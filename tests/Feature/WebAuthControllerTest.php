<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registers a user with a normalized email and hashed password', function () {
  $response = $this->post('/register', [
    'name' => 'Noctura',
    'email' => '  USER@example.com ',
    'password' => 'rahasia123',
    'password_confirmation' => 'rahasia123',
  ]);

  $user = User::query()->where('email', 'user@example.com')->firstOrFail();

  $response
    ->assertRedirect('/login')
    ->assertSessionHas('status', 'Akun berhasil dibuat. Silakan masuk.');

  expect(Hash::check('rahasia123', $user->password))->toBeTrue();
});

it('returns validation errors and does not create a user when registration fields are missing', function () {
  $response = $this->post('/register', []);

  $response->assertSessionHasErrors(['name', 'email', 'password']);

  $this->assertDatabaseCount('users', 0);
});

it('logs in with an email that has different casing and surrounding spaces', function () {
  $user = User::query()->create([
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
  ]);

  $response = $this->post('/login', [
    'email' => '  USER@example.com ',
    'password' => 'rahasia123',
  ]);

  $response->assertRedirect('/ebooks');
  $this->assertAuthenticatedAs($user);
});

it('returns to the login form when the password is incorrect', function () {
  User::query()->create([
    'name' => 'Noctura',
    'email' => 'user@example.com',
    'password' => 'rahasia123',
  ]);

  $response = $this->post('/login', [
    'email' => 'user@example.com',
    'password' => 'wrong-password',
  ]);

  $response
    ->assertRedirect()
    ->assertSessionHasErrors(['email' => 'Email atau password salah.']);

  $this->assertGuest();
});
