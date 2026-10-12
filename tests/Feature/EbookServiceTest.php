<?php

use App\Exceptions\DuplicateEbookException;
use App\Models\Ebook;
use App\Models\User;
use App\Services\EbookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('it stores an ebook using the uploaded filename as the default title', function () {
  $service = new EbookService;
  Storage::fake('local');

  $user = User::create([
    'name' => 'Alice Example',
    'email' => 'alice@example.com',
    'password' => 'secret123',
  ]);
  $file = UploadedFile::fake()->create('my-book.pdf', 1024, 'application/pdf');

  $ebook = $service->store($user, $file);

  expect($ebook)
    ->toBeInstanceOf(Ebook::class)
    ->and($ebook->user_id)->toBe($user->id)
    ->and($ebook->title)->toBe('my-book')
    ->and($ebook->file_hash)->toBe(hash_file('sha256', $file->getRealPath()))
    ->and($ebook->file_path)->toStartWith('ebooks/user-' . $user->id . '/')
    ->and(Storage::disk('local')->exists($ebook->file_path))->toBeTrue();
});

test('it throws a duplicate exception when the same file already exists for the same user', function () {
  $service = new EbookService;
  Storage::fake('local');

  $user = User::create([
    'name' => 'Bob Example',
    'email' => 'bob@example.com',
    'password' => 'secret123',
  ]);
  $content = 'duplicate-content-' . random_int(1000, 9999);
  $tempFile = tempnam(sys_get_temp_dir(), 'ebook_service_test');
  file_put_contents($tempFile, $content);

  $hash = hash('sha256', $content);

  Ebook::create([
    'user_id' => $user->id,
    'title' => 'Existing Book',
    'file_path' => 'ebooks/user-' . $user->id . '/existing.pdf',
    'file_hash' => $hash,
  ]);

  $duplicateFile = new UploadedFile($tempFile, 'another-name.pdf', 'application/pdf', null, true);

  $this->expectException(DuplicateEbookException::class);
  $this->expectExceptionMessage('PDF ini sudah ada di koleksi Anda.');

  $service->store($user, $duplicateFile);
});
