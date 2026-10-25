<?php

use App\Models\Ebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function apiEbookTestUser(string $email = 'reader@example.test'): User
{
  return User::create([
    'name' => 'Ebook Reader',
    'email' => $email,
    'password' => 'password123',
  ]);
}

test('returns 401 when an unauthenticated request lists ebooks', function () {
  $this->getJson('/api/ebooks')
    ->assertUnauthorized()
    ->assertExactJson([
      'message' => 'Sesi tidak valid. Silakan login kembali.',
      'code' => 'unauthenticated',
    ]);
});

test('lists only the authenticated users ebooks with collection metadata', function () {
  $user = apiEbookTestUser();
  $anotherUser = apiEbookTestUser('another@example.test');

  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Owned book',
    'file_path' => 'ebooks/owned.pdf',
    'file_hash' => str_repeat('a', 64),
    'is_read' => true,
  ]);

  Ebook::create([
    'user_id' => $anotherUser->id,
    'title' => 'Private book',
    'file_path' => 'ebooks/private.pdf',
    'file_hash' => str_repeat('b', 64),
    'is_read' => true,
  ]);

  DB::shouldReceive('selectOne')
    ->once()
    ->with('SELECT fn_count_read_ebooks(?) AS read_count', [$user->id])
    ->andReturn((object) ['read_count' => '1']);

  $this->actingAs($user)
    ->getJson('/api/ebooks')
    ->assertOk()
    ->assertJsonPath('data.0.id', $ebook->id)
    ->assertJsonPath('data.0.title', 'Owned book')
    ->assertJsonPath('data.0.is_read', true)
    ->assertJsonPath('meta.total', 1)
    ->assertJsonPath('meta.read_count', 1)
    ->assertJsonMissing(['title' => 'Private book']);
});

test('returns 422 when an ebook upload is missing or is not a PDF', function () {
  $user = apiEbookTestUser();

  $this->actingAs($user, 'sanctum')
    ->postJson('/api/ebooks')
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['file'])
    ->assertJsonPath('errors.file.0', 'File PDF wajib diisi.');

  $this->actingAs($user, 'sanctum')
    ->postJson('/api/ebooks', [
      'file' => UploadedFile::fake()->create('notes.txt', 20, 'text/plain'),
    ])
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['file']);

  $this->assertDatabaseCount('ebooks', 0);
});

test('stores a valid PDF and returns the public ebook resource', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = apiEbookTestUser();
  $file = UploadedFile::fake()->createWithContent('api-book.pdf', "%PDF-1.4\nAPI test PDF\n%%EOF");

  $response = $this->actingAs($user, 'sanctum')
    ->postJson('/api/ebooks', [
      'file' => $file,
      'title' => '  API Book  ',
    ]);

  $ebook = Ebook::query()->sole();

  $response
    ->assertCreated()
    ->assertJsonPath('data.id', $ebook->id)
    ->assertJsonPath('data.title', 'API Book')
    ->assertJsonPath('data.is_read', false)
    ->assertJsonMissingPath('data.file_path')
    ->assertJsonMissingPath('data.file_hash');

  $this->assertModelExists($ebook);
  expect(Storage::disk('local')->exists($ebook->file_path))->toBeTrue();
});

test('returns 409 when the uploaded PDF already belongs to the user', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = apiEbookTestUser();
  $content = "%PDF-1.4\nRepeated PDF\n%%EOF";
  $existing = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Existing PDF',
    'file_path' => 'ebooks/existing.pdf',
    'file_hash' => hash('sha256', $content),
  ]);

  $this->actingAs($user, 'sanctum')
    ->postJson('/api/ebooks', [
      'file' => UploadedFile::fake()->createWithContent('copy.pdf', $content),
    ])
    ->assertConflict()
    ->assertExactJson([
      'message' => 'PDF ini sudah ada di koleksi Anda.',
      'code' => 'duplicate_ebook',
      'data' => ['existing_id' => $existing->id],
    ]);

  $this->assertDatabaseCount('ebooks', 1);
});

test('returns 409 when uploading an 11th ebook', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = apiEbookTestUser();
  foreach (range(1, 10) as $number) {
    Ebook::create([
      'user_id' => $user->id,
      'title' => "Existing ebook {$number}",
      'file_path' => "ebooks/existing-{$number}.pdf",
      'file_hash' => hash('sha256', "api-existing-{$number}"),
    ]);
  }

  $this->actingAs($user, 'sanctum')
    ->postJson('/api/ebooks', [
      'file' => UploadedFile::fake()->createWithContent('new-book.pdf', "%PDF-1.4\nNew book\n%%EOF"),
    ])
    ->assertConflict()
    ->assertExactJson([
      'message' => 'Koleksi sudah penuh (maksimal 10 PDF). Hapus salah satu untuk menambah.',
      'code' => 'ebook_limit_reached',
    ]);

  $this->assertDatabaseCount('ebooks', 10);
  expect(Storage::disk('local')->allFiles('ebooks'))->toBe([]);
});

test('deleting an ebook from a full collection allows another upload', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = apiEbookTestUser();
  $ebookToDelete = null;
  foreach (range(1, 10) as $number) {
    $ebook = Ebook::create([
      'user_id' => $user->id,
      'title' => "Existing ebook {$number}",
      'file_path' => "ebooks/user-{$user->id}/existing-{$number}.pdf",
      'file_hash' => hash('sha256', "api-full-collection-{$number}"),
    ]);

    if ($number === 1) {
      $ebookToDelete = $ebook;
    }
  }
  Storage::disk('local')->put($ebookToDelete->file_path, "%PDF-1.4\nExisting book\n%%EOF");

  $this->actingAs($user, 'sanctum')
    ->deleteJson('/api/ebooks/' . $ebookToDelete->id)
    ->assertOk()
    ->assertExactJson(['message' => 'Ebook berhasil dihapus.']);

  $this->assertDatabaseCount('ebooks', 9);
  expect(Storage::disk('local')->exists($ebookToDelete->file_path))->toBeFalse();

  $this->actingAs($user, 'sanctum')
    ->postJson('/api/ebooks', [
      'file' => UploadedFile::fake()->createWithContent('replacement-book.pdf', "%PDF-1.4\nReplacement book\n%%EOF"),
    ])
    ->assertCreated();

  $this->assertDatabaseCount('ebooks', 10);
});

test('streams an owned PDF inline and hides ebooks owned by another user', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = apiEbookTestUser();
  $anotherUser = apiEbookTestUser('another@example.test');
  $ownedEbook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'A\B / Book',
    'file_path' => 'ebooks/owned.pdf',
    'file_hash' => str_repeat('c', 64),
  ]);
  $privateEbook = Ebook::create([
    'user_id' => $anotherUser->id,
    'title' => 'Private Book',
    'file_path' => 'ebooks/private.pdf',
    'file_hash' => str_repeat('d', 64),
  ]);
  Storage::disk('local')->put($ownedEbook->file_path, "%PDF-1.4\nOwned\n%%EOF");
  Storage::disk('local')->put($privateEbook->file_path, "%PDF-1.4\nPrivate\n%%EOF");

  $this->actingAs($user, 'sanctum')
    ->get('/api/ebooks/' . $ownedEbook->id . '/file')
    ->assertOk()
    ->assertHeader('Content-Type', 'application/pdf')
    ->assertHeader('Content-Disposition', 'inline; filename="A-B - Book.pdf"');

  $this->actingAs($user, 'sanctum')
    ->getJson('/api/ebooks/' . $privateEbook->id . '/file')
    ->assertNotFound()
    ->assertExactJson([
      'message' => 'Ebook tidak ditemukan.',
      'code' => 'ebook_not_found',
    ]);
});

test('returns 404 when an ebook row exists but its PDF is missing from storage', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = apiEbookTestUser();
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Missing PDF',
    'file_path' => 'ebooks/missing.pdf',
    'file_hash' => str_repeat('e', 64),
  ]);

  $this->actingAs($user, 'sanctum')
    ->getJson('/api/ebooks/' . $ebook->id . '/file')
    ->assertNotFound()
    ->assertExactJson([
      'message' => 'File ebook tidak tersedia.',
      'code' => 'file_missing',
    ]);
});

test('rejects invalid read-status input and leaves the ebook unchanged', function () {
  $user = apiEbookTestUser();
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Unread PDF',
    'file_path' => 'ebooks/unread.pdf',
    'file_hash' => str_repeat('f', 64),
  ]);

  $this->actingAs($user, 'sanctum')
    ->patchJson('/api/ebooks/' . $ebook->id, ['is_read' => 'not-a-boolean'])
    ->assertUnprocessable()
    ->assertJsonValidationErrors(['is_read']);

  expect($ebook->fresh()->is_read)->toBeFalse();
});

test('returns the existing resource when the requested read status is unchanged', function () {
  $user = apiEbookTestUser();
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Unread PDF',
    'file_path' => 'ebooks/unread.pdf',
    'file_hash' => str_repeat('1', 64),
    'is_read' => false,
  ]);

  $this->actingAs($user, 'sanctum')
    ->patchJson('/api/ebooks/' . $ebook->id, ['is_read' => false])
    ->assertOk()
    ->assertJsonPath('data.id', $ebook->id)
    ->assertJsonPath('data.is_read', false);

  expect($ebook->fresh()->is_read)->toBeFalse();
});
