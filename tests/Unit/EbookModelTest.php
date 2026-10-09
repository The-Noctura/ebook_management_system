<?php

use App\Models\Ebook;
use App\Models\EbookStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('ebook casts is_read to boolean and has user and status history relationships', function () {
  $user = User::create([
    'name' => 'Jane Doe',
    'email' => 'jane@example.com',
    'password' => 'secret123',
  ]);

  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Sample PDF',
    'file_path' => 'ebooks/user-1/sample.pdf',
    'file_hash' => str_repeat('a', 64),
    'is_read' => true,
  ]);

  expect($ebook->is_read)->toBeTrue()
    ->and($ebook->user)->toBeInstanceOf(User::class)
    ->and($ebook->user->is($user))->toBeTrue();

  $history = EbookStatusHistory::create([
    'ebook_id' => $ebook->id,
    'old_status' => false,
    'new_status' => true,
    'changed_at' => now(),
  ]);

  expect($ebook->fresh()->statusHistory)->toHaveCount(1)
    ->and($ebook->fresh()->statusHistory->first()->is($history))->toBeTrue();
});

test('user exposes ebooks relationship', function () {
  $user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'secret123',
  ]);

  Ebook::create([
    'user_id' => $user->id,
    'title' => 'Another PDF',
    'file_path' => 'ebooks/user-2/another.pdf',
    'file_hash' => str_repeat('b', 64),
    'is_read' => false,
  ]);

  expect($user->fresh()->ebooks)->toHaveCount(1)
    ->and($user->fresh()->ebooks->first()->title)->toBe('Another PDF');
});

test('user can create a Sanctum personal access token', function () {
  $user = User::create([
    'name' => 'Token User',
    'email' => 'token@example.com',
    'password' => 'secret123',
  ]);

  $token = $user->createToken('android');

  expect($token->plainTextToken)->not->toBeEmpty()
    ->and($token->accessToken->name)->toBe('android');

  $this->assertDatabaseHas('personal_access_tokens', [
    'id' => $token->accessToken->getKey(),
    'tokenable_type' => User::class,
    'tokenable_id' => $user->id,
    'name' => 'android',
  ]);
});
