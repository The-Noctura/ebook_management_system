<?php

use App\Exceptions\DuplicateEbookException;
use App\Exceptions\EbookFileMissingException;
use App\Exceptions\EbookLimitReachedException;
use App\Models\Ebook;
use App\Models\User;
use App\Services\EbookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

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

test('it rejects an upload when the user already has 10 ebooks', function () {
  Storage::fake('local');
  $service = new EbookService;
  $user = User::create([
    'name' => 'Full Collection Owner',
    'email' => 'full-collection@example.com',
    'password' => 'secret123',
  ]);

  foreach (range(1, 10) as $number) {
    Ebook::create([
      'user_id' => $user->id,
      'title' => "Existing ebook {$number}",
      'file_path' => "ebooks/existing-{$number}.pdf",
      'file_hash' => hash('sha256', "existing-{$number}"),
    ]);
  }

  $file = UploadedFile::fake()->createWithContent('new-book.pdf', "%PDF-1.4\nNew book\n%%EOF");

  try {
    $service->store($user, $file);
    $this->fail('An 11th ebook must be rejected.');
  } catch (EbookLimitReachedException $exception) {
    expect($exception->getMessage())->toBe('Koleksi sudah penuh (maksimal 10 PDF). Hapus salah satu untuk menambah.');
  }

  $this->assertDatabaseCount('ebooks', 10);
  expect(Storage::disk('local')->allFiles('ebooks'))->toBe([]);
});

test('it allows an upload when the user has 9 ebooks', function () {
  Storage::fake('local');
  $service = new EbookService;
  $user = User::create([
    'name' => 'Almost Full Owner',
    'email' => 'almost-full@example.com',
    'password' => 'secret123',
  ]);

  foreach (range(1, 9) as $number) {
    Ebook::create([
      'user_id' => $user->id,
      'title' => "Existing ebook {$number}",
      'file_path' => "ebooks/existing-{$number}.pdf",
      'file_hash' => hash('sha256', "almost-full-{$number}"),
    ]);
  }

  $file = UploadedFile::fake()->createWithContent('tenth-book.pdf', "%PDF-1.4\nTenth book\n%%EOF");
  $ebook = $service->store($user, $file);

  $this->assertModelExists($ebook);
  $this->assertDatabaseCount('ebooks', 10);
  expect(Storage::disk('local')->exists($ebook->file_path))->toBeTrue();
});

test('it trims a supplied title and limits it to 255 characters', function () {
  Storage::fake('local');
  $service = new EbookService;
  $user = User::create([
    'name' => 'Title Owner',
    'email' => 'title-owner@example.com',
    'password' => 'secret123',
  ]);
  $file = UploadedFile::fake()->createWithContent('title.pdf', "%PDF-1.4\nTitle test\n%%EOF");
  $title = str_repeat('T', 260);

  $ebook = $service->store($user, $file, '  ' . $title . '  ');

  expect($ebook->title)->toBe(str_repeat('T', 255));
});

test('it allows different users to store identical PDF contents', function () {
  Storage::fake('local');
  $service = new EbookService;
  $firstUser = User::create([
    'name' => 'First Owner',
    'email' => 'first-owner@example.com',
    'password' => 'secret123',
  ]);
  $secondUser = User::create([
    'name' => 'Second Owner',
    'email' => 'second-owner@example.com',
    'password' => 'secret123',
  ]);
  $content = "%PDF-1.4\nSame content\n%%EOF";

  $firstEbook = $service->store($firstUser, UploadedFile::fake()->createWithContent('first.pdf', $content));
  $secondEbook = $service->store($secondUser, UploadedFile::fake()->createWithContent('second.pdf', $content));

  expect($secondEbook->file_hash)->toBe($firstEbook->file_hash)
    ->and($secondEbook->user_id)->toBe($secondUser->id)
    ->and($secondEbook->id)->not->toBe($firstEbook->id);
});

test('it removes the stored file when creating the ebook row fails', function () {
  Storage::fake('local');
  $service = new EbookService;
  $user = User::create([
    'name' => 'Rollback Owner',
    'email' => 'rollback-owner@example.com',
    'password' => 'secret123',
  ]);
  $file = UploadedFile::fake()->createWithContent('rollback.pdf', "%PDF-1.4\nRollback test\n%%EOF");

  Ebook::creating(function () {
    throw new RuntimeException('Simulated database failure.');
  });

  try {
    $service->store($user, $file);
    $this->fail('The storage/database failure should be rethrown.');
  } catch (RuntimeException $exception) {
    expect($exception->getMessage())->toBe('Simulated database failure.');
  }

  expect(Storage::disk('local')->allFiles('ebooks'))->toBe([]);
  $this->assertDatabaseCount('ebooks', 0);
});

test('it treats an ebook owned by another user as not found when changing read status', function () {
  $service = new EbookService;
  $owner = User::create([
    'name' => 'Owner',
    'email' => 'owner@example.com',
    'password' => 'secret123',
  ]);
  $otherUser = User::create([
    'name' => 'Other User',
    'email' => 'other-user@example.com',
    'password' => 'secret123',
  ]);
  $ebook = Ebook::create([
    'user_id' => $owner->id,
    'title' => 'Private PDF',
    'file_path' => 'ebooks/private.pdf',
    'file_hash' => str_repeat('c', 64),
  ]);

  try {
    $service->setReadStatus($otherUser, $ebook->id, true);
    $this->fail('An ebook owned by another user must not be found.');
  } catch (ModelNotFoundException $exception) {
    expect($exception->getModel())->toBe(Ebook::class);
  }
});

test('it calls the matching status procedure in a transaction and returns the refreshed ebook', function (bool $initialStatus, bool $requestedStatus, string $procedure) {
  $service = new EbookService;
  $user = User::create([
    'name' => 'Status Owner',
    'email' => 'status-owner@example.com',
    'password' => 'secret123',
  ]);
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Status PDF',
    'file_path' => 'ebooks/status.pdf',
    'file_hash' => str_repeat('e', 64),
    'is_read' => $initialStatus,
  ]);

  DB::shouldReceive('transaction')
    ->once()
    ->andReturnUsing(fn(Closure $callback) => $callback());
  DB::shouldReceive('statement')
    ->once()
    ->with("CALL {$procedure}(?, ?)", [$user->id, $ebook->id])
    ->andReturnUsing(function () use ($ebook, $requestedStatus): int {
      return Ebook::query()
        ->whereKey($ebook->id)
        ->update(['is_read' => $requestedStatus]);
    });

  $updatedEbook = $service->setReadStatus($user, $ebook->id, $requestedStatus);

  expect($updatedEbook->is_read)->toBe($requestedStatus)
    ->and($updatedEbook->is($ebook))->toBeTrue();
})->with([
  'mark as read' => [false, true, 'sp_mark_ebook_read'],
  'mark as unread' => [true, false, 'sp_mark_ebook_unread'],
]);

test('it throws a file-missing exception when an owned ebook has no stored PDF', function () {
  Storage::fake('local');
  $service = new EbookService;
  $user = User::create([
    'name' => 'Missing File Owner',
    'email' => 'missing-file-owner@example.com',
    'password' => 'secret123',
  ]);
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Missing PDF',
    'file_path' => 'ebooks/missing.pdf',
    'file_hash' => str_repeat('d', 64),
  ]);

  try {
    $service->streamFile($user, $ebook->id);
    $this->fail('A missing storage object must raise EbookFileMissingException.');
  } catch (EbookFileMissingException $exception) {
    expect($exception->ebookId)->toBe($ebook->id);
  }
});
