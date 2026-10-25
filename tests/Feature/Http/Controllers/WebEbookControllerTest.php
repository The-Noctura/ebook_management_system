<?php

use App\Models\Ebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function webEbookTestUser(string $email = 'reader@example.test'): User
{
  return User::create([
    'name' => 'Ebook Reader',
    'email' => $email,
    'password' => 'password123',
  ]);
}

test('redirects guests from the ebook collection and upload form', function () {
  $this->get('/ebooks')->assertRedirect('/login');
  $this->get('/ebooks/create')->assertRedirect('/login');
});

test('renders the empty collection without querying the read-count function', function () {
  $user = webEbookTestUser();

  DB::shouldReceive('selectOne')->never();

  $this->actingAs($user)
    ->get('/ebooks')
    ->assertOk()
    ->assertSee('Koleksi Anda masih kosong')
    ->assertSee('Tambah PDF Pertama ke Koleksi');
});

test('renders only the users ebooks and their read summary', function () {
  $user = webEbookTestUser();
  $anotherUser = webEbookTestUser('another@example.test');
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => '<script>alert("ebook")</script>',
    'file_path' => 'ebooks/visible.pdf',
    'file_hash' => str_repeat('a', 64),
    'is_read' => true,
  ]);
  Ebook::create([
    'user_id' => $anotherUser->id,
    'title' => 'Private title',
    'file_path' => 'ebooks/private.pdf',
    'file_hash' => str_repeat('b', 64),
  ]);

  DB::shouldReceive('selectOne')
    ->once()
    ->with('SELECT fn_count_read_ebooks(?) AS total', [$user->id])
    ->andReturn((object) ['total' => '1']);

  $this->actingAs($user)
    ->get('/ebooks')
    ->assertOk()
    ->assertSee('1 dari 1 sudah dibaca')
    ->assertSee('id="ebook-' . $ebook->id . '"', false)
    ->assertSee('&lt;script&gt;alert(&quot;ebook&quot;)&lt;/script&gt;', false)
    ->assertDontSee('<script>alert("ebook")</script>', false)
    ->assertDontSee('Private title');
});

test('renders the upload form for an authenticated user', function () {
  $this->actingAs(webEbookTestUser())
    ->get('/ebooks/create')
    ->assertOk()
    ->assertSee('Tambah PDF ke Koleksi')
    ->assertSee('enctype="multipart/form-data"', false)
    ->assertSee('Hanya PDF, maksimal 20 MB.');
});

test('returns validation errors when the uploaded file is missing or is not a PDF', function () {
  $user = webEbookTestUser();

  $this->actingAs($user)
    ->from('/ebooks/create')
    ->post('/ebooks')
    ->assertRedirect('/ebooks/create')
    ->assertSessionHasErrors(['file' => 'Pilih file PDF terlebih dahulu.']);

  $this->actingAs($user)
    ->from('/ebooks/create')
    ->post('/ebooks', [
      'file' => UploadedFile::fake()->create('notes.txt', 20, 'text/plain'),
    ])
    ->assertRedirect('/ebooks/create')
    ->assertSessionHasErrors('file');

  $this->assertDatabaseCount('ebooks', 0);
});

test('stores a valid PDF and redirects to its new collection card', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = webEbookTestUser();
  $file = UploadedFile::fake()->createWithContent('web-book.pdf', "%PDF-1.4\nWeb test PDF\n%%EOF");

  $response = $this->actingAs($user)
    ->post('/ebooks', [
      'file' => $file,
      'title' => '  Web Book  ',
    ]);

  $ebook = Ebook::query()->sole();

  $response
    ->assertRedirect(route('ebooks.index', ['baru' => $ebook->id]) . '#ebook-' . $ebook->id)
    ->assertSessionHas('success', '“Web Book” ditambahkan ke koleksi.');

  $this->assertModelExists($ebook);
  expect($ebook->title)->toBe('Web Book')
    ->and(Storage::disk('local')->exists($ebook->file_path))->toBeTrue();
});

test('returns to the upload form with the existing ebook id for duplicate PDFs', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = webEbookTestUser();
  $content = "%PDF-1.4\nExisting web PDF\n%%EOF";
  $existing = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Existing Web PDF',
    'file_path' => 'ebooks/existing.pdf',
    'file_hash' => hash('sha256', $content),
  ]);

  $this->actingAs($user)
    ->from('/ebooks/create')
    ->post('/ebooks', [
      'file' => UploadedFile::fake()->createWithContent('copy.pdf', $content),
    ])
    ->assertRedirect('/ebooks/create')
    ->assertSessionHasErrors(['file' => 'PDF ini sudah ada di koleksi Anda.'])
    ->assertSessionHas('existing_id', $existing->id);

  $this->assertDatabaseCount('ebooks', 1);
});

test('returns the collection limit error when uploading an 11th ebook', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = webEbookTestUser();
  foreach (range(1, 10) as $number) {
    Ebook::create([
      'user_id' => $user->id,
      'title' => "Existing ebook {$number}",
      'file_path' => "ebooks/existing-{$number}.pdf",
      'file_hash' => hash('sha256', "web-existing-{$number}"),
    ]);
  }

  $this->actingAs($user)
    ->from('/ebooks/create')
    ->post('/ebooks', [
      'file' => UploadedFile::fake()->createWithContent('new-book.pdf', "%PDF-1.4\nNew book\n%%EOF"),
    ])
    ->assertRedirect('/ebooks/create')
    ->assertSessionHasErrors(['file' => 'Koleksi sudah penuh (maksimal 10 PDF). Hapus salah satu untuk menambah.']);

  $this->assertDatabaseCount('ebooks', 10);
  expect(Storage::disk('local')->allFiles('ebooks'))->toBe([]);
});

test('returns 404 when a user tries to open another users PDF', function () {
  $user = webEbookTestUser();
  $anotherUser = webEbookTestUser('another@example.test');
  $ebook = Ebook::create([
    'user_id' => $anotherUser->id,
    'title' => 'Private PDF',
    'file_path' => 'ebooks/private.pdf',
    'file_hash' => str_repeat('c', 64),
  ]);

  $this->actingAs($user)
    ->get('/ebooks/' . $ebook->id . '/file')
    ->assertNotFound();
});

test('redirects to the collection with an error when the stored PDF is missing', function () {
  Storage::fake('local');
  config(['filesystems.ebook_disk' => 'local']);

  $user = webEbookTestUser();
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Missing PDF',
    'file_path' => 'ebooks/missing.pdf',
    'file_hash' => str_repeat('d', 64),
  ]);

  $this->actingAs($user)
    ->get('/ebooks/' . $ebook->id . '/file')
    ->assertRedirect(route('ebooks.index'))
    ->assertSessionHas('error', 'File PDF tidak tersedia.');
});

test('rejects invalid read-status input and preserves the existing status', function () {
  $user = webEbookTestUser();
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Unread PDF',
    'file_path' => 'ebooks/unread.pdf',
    'file_hash' => str_repeat('e', 64),
  ]);

  $this->actingAs($user)
    ->from('/ebooks')
    ->patch('/ebooks/' . $ebook->id, ['is_read' => 'invalid'])
    ->assertRedirect('/ebooks')
    ->assertSessionHasErrors(['is_read' => 'Status baca tidak valid.']);

  expect($ebook->fresh()->is_read)->toBeFalse();
});

test('redirects to the ebook card after an unchanged read-status update', function () {
  $user = webEbookTestUser();
  $ebook = Ebook::create([
    'user_id' => $user->id,
    'title' => 'Unread PDF',
    'file_path' => 'ebooks/unread.pdf',
    'file_hash' => str_repeat('f', 64),
    'is_read' => false,
  ]);

  $this->actingAs($user)
    ->patch('/ebooks/' . $ebook->id, ['is_read' => false])
    ->assertRedirect(route('ebooks.index') . '#ebook-' . $ebook->id)
    ->assertSessionHas('success', '“Unread PDF” ditandai belum dibaca.');

  expect($ebook->fresh()->is_read)->toBeFalse();
});
