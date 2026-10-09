<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registers a user and returns only public account data', function () {
  $response = $this->postJson('/api/register', [
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
    'password_confirmation' => 'rahasia123',
  ]);

  $user = User::query()->where('email', 'user@example.com')->firstOrFail();

  $response
    ->assertCreated()
    ->assertExactJson([
      'data' => [
        'id' => $user->id,
        'name' => 'Noctura',
        'email' => 'user@example.com',
      ],
    ]);

  $this->assertDatabaseHas('users', [
    'id' => $user->id,
    'name' => 'Noctura',
    'email' => 'user@example.com',
  ]);
  $this->assertDatabaseCount('personal_access_tokens', 0);
  expect(Hash::check('rahasia123', $user->password))->toBeTrue();
});

it('stores the email in lowercase and trimmed', function () {
  $response = $this->postJson('/api/register', [
    'name' => 'Noctura',
    'email' => '  User@Example.COM ',
    'password' => 'rahasia123',
    'password_confirmation' => 'rahasia123',
  ]);

  $response
    ->assertCreated()
    ->assertJsonPath('data.email', 'user@example.com');

  $this->assertDatabaseHas('users', ['email' => 'user@example.com']);
});

it('returns 422 when registration fields are missing', function () {
  $response = $this->postJson('/api/register', []);

  $response
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['name', 'email', 'password']);

  $this->assertDatabaseCount('users', 0);
});

it('returns 422 when a registration field violates its rules', function (array $overrides, string $field) {
  $payload = array_merge([
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
    'password_confirmation' => 'rahasia123',
  ], $overrides);

  $response = $this->postJson('/api/register', $payload);

  $response
    ->assertUnprocessable()
    ->assertJsonValidationErrors([$field])
    ->assertJsonPath("errors.{$field}.0", fn(string $message): bool => $message !== '');

  $this->assertDatabaseCount('users', 0);
})->with([
  'name must be a string' => [['name' => []], 'name'],
  'name cannot exceed 100 characters' => [['name' => str_repeat('N', 101)], 'name'],
  'email must be valid' => [['email' => 'not-an-email'], 'email'],
  'email cannot exceed 255 characters' => [['email' => 'a@' . str_repeat('b', 250) . '.com'], 'email'],
  'password must be at least 8 characters' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
  'password confirmation must match' => [['password_confirmation' => 'different123'], 'password'],
]);

it('returns 422 when the email is already registered', function () {
  User::query()->create([
    'name' => 'Existing User',
    'email' => 'user@example.com',
    'password' => 'existing-password',
  ]);

  $response = $this->postJson('/api/register', [
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
    'password_confirmation' => 'rahasia123',
  ]);

  $response
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['email'])
    ->assertJsonPath('errors.email.0', fn(string $message): bool => $message !== '');

  $this->assertDatabaseCount('users', 1);
});

it('treats emails that differ only by case as already registered', function () {
  User::query()->create([
    'name' => 'Existing User',
    'email' => 'user@example.com',
    'password' => 'existing-password',
  ]);

  $response = $this->postJson('/api/register', [
    'name' => 'Noctura',
    'email' => 'USER@example.com',
    'password' => 'rahasia123',
    'password_confirmation' => 'rahasia123',
  ]);

  $response
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['email']);

  $this->assertDatabaseCount('users', 1);
});
