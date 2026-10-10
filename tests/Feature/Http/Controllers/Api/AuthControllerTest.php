<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('logs in and returns a bearer token with public user data', function () {
  $user = User::query()->create([
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
  ]);

  $response = $this->postJson('/api/login', [
    'email' => '  USER@example.com ',
    'password' => 'rahasia123',
  ]);

  $token = $response->json('data.token');

  $response
    ->assertOk()
    ->assertExactJson([
      'data' => [
        'token' => $token,
        'token_type' => 'Bearer',
        'user' => [
          'id' => $user->id,
          'name' => 'Noctura',
          'email' => 'user@example.com',
        ],
      ],
    ]);

  $this->assertDatabaseHas('personal_access_tokens', [
    'tokenable_id' => $user->id,
    'name' => 'android',
  ]);

  $this->withToken($token)
    ->getJson('/api/user')
    ->assertOk()
    ->assertJsonPath('id', $user->id);
});

it('uses the supplied device name for the personal access token', function () {
  $user = User::query()->create([
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
  ]);

  $this->postJson('/api/login', [
    'email' => 'user@example.com',
    'password' => 'rahasia123',
    'device_name' => 'android-pixel',
  ])->assertOk();

  $this->assertDatabaseHas('personal_access_tokens', [
    'tokenable_id' => $user->id,
    'name' => 'android-pixel',
  ]);
});

it('returns the same generic error for invalid login credentials', function () {
  User::query()->create([
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
  ]);

  $wrongPasswordResponse = $this->postJson('/api/login', [
    'email' => 'user@example.com',
    'password' => 'wrong-password',
  ]);

  $wrongPasswordResponse
    ->assertUnauthorized()
    ->assertExactJson([
      'message' => 'Email atau password salah.',
      'code' => 'invalid_credentials',
    ]);

  $unknownEmailResponse = $this->postJson('/api/login', [
    'email' => 'unknown@example.com',
    'password' => 'wrong-password',
  ]);

  $unknownEmailResponse
    ->assertUnauthorized()
    ->assertExactJson($wrongPasswordResponse->json());

  $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('validates required login fields and device name', function () {
  $response = $this->postJson('/api/login', []);

  $response
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['email', 'password']);

  $invalidDeviceNameResponse = $this->postJson('/api/login', [
    'email' => 'user@example.com',
    'password' => 'rahasia123',
    'device_name' => str_repeat('d', 256),
  ]);

  $invalidDeviceNameResponse
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['device_name']);

  $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('limits login attempts to five per normalized email and ip address', function () {
  $credentials = [
    'email' => 'user@example.com',
    'password' => 'wrong-password',
  ];

  for ($attempt = 0; $attempt < 5; $attempt++) {
    $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
      ->postJson('/api/login', $credentials)
      ->assertUnauthorized();
  }

  $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
    ->postJson('/api/login', [
      'email' => 'another@example.com',
      'password' => 'wrong-password',
    ])
    ->assertUnauthorized();

  $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.11'])
    ->postJson('/api/login', $credentials)
    ->assertUnauthorized();

  $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
    ->postJson('/api/login', [
      'email' => '  USER@example.com ',
      'password' => 'wrong-password',
    ])
    ->assertTooManyRequests()
    ->assertExactJson([
      'message' => 'Terlalu banyak percobaan login. Silakan coba lagi nanti.',
      'code' => 'too_many_attempts',
    ]);
});

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

it('logs the user out and revokes the active token', function () {
  $user = User::query()->create([
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
  ]);

  $token = $user->createToken('android')->plainTextToken;

  $this->withToken($token)
    ->postJson('/api/logout')
    ->assertOk()
    ->assertExactJson([
      'message' => 'Logout berhasil.',
    ]);

  $this->assertDatabaseCount('personal_access_tokens', 0);
});

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
