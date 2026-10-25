<?php

use App\Models\Ebook;
use App\Models\EbookStatusHistory;
use App\Models\User;
use App\Services\EbookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

// Mode hosting yang menonaktifkan procedure, trigger, dan function (EBOOK_SQL_OBJECTS=false).
beforeEach(function () {
  config(['ebook.sql_objects' => false]);
  Storage::fake('local');
});

function noSqlUser(string $email = 'nosql@example.com'): User
{
  return User::create(['name' => 'No Sql', 'email' => $email, 'password' => 'secret123']);
}

function noSqlEbook(User $user, bool $isRead = false, string $hash = 'a'): Ebook
{
  return Ebook::create([
    'user_id' => $user->id,
    'title' => 'Buku Uji',
    'file_path' => 'ebooks/user-' . $user->id . '/' . $hash . '.pdf',
    'file_hash' => str_repeat($hash, 64),
    'is_read' => $isRead,
  ]);
}

test('marking an ebook as read updates it and records exactly one history row', function () {
  $user = noSqlUser();
  $ebook = noSqlEbook($user);

  $result = (new EbookService)->setReadStatus($user, $ebook->id, true);

  expect($result->is_read)->toBeTrue();

  $history = EbookStatusHistory::where('ebook_id', $ebook->id)->get();
  expect($history)->toHaveCount(1)
    ->and($history->first()->old_status)->toBeFalse()
    ->and($history->first()->new_status)->toBeTrue();
});

test('marking an ebook with the same status adds no history', function () {
  $user = noSqlUser();
  $ebook = noSqlEbook($user, true);

  (new EbookService)->setReadStatus($user, $ebook->id, true);

  expect(EbookStatusHistory::where('ebook_id', $ebook->id)->count())->toBe(0);
});

test('another user cannot change the status of an ebook they do not own', function () {
  $owner = noSqlUser();
  $other = noSqlUser('other@example.com');
  $ebook = noSqlEbook($owner);

  (new EbookService)->setReadStatus($other, $ebook->id, true);
})->throws(ModelNotFoundException::class);

test('deleting an ebook also removes its status history and its file', function () {
  $user = noSqlUser();
  $ebook = noSqlEbook($user);
  Storage::disk('local')->put($ebook->file_path, '%PDF-1.4 uji');

  (new EbookService)->setReadStatus($user, $ebook->id, true);
  expect(EbookStatusHistory::where('ebook_id', $ebook->id)->count())->toBe(1);

  (new EbookService)->destroy($user, $ebook->id);

  expect(Ebook::find($ebook->id))->toBeNull()
    ->and(EbookStatusHistory::where('ebook_id', $ebook->id)->count())->toBe(0)
    ->and(Storage::disk('local')->exists($ebook->file_path))->toBeFalse();
});

test('the collection page counts read ebooks in PHP without calling the SQL function', function () {
  $user = noSqlUser();
  noSqlEbook($user, true, 'a');
  noSqlEbook($user, false, 'b');

  $this->actingAs($user)
    ->get('/ebooks')
    ->assertOk()
    ->assertSee('1 dari 2 sudah dibaca');
});

test('the upload limit label follows EBOOK_MAX_UPLOAD_KB', function () {
  config(['ebook.max_upload_kb' => 9216, 'ebook.max_upload_label' => '9 MB']);

  $this->actingAs(noSqlUser())
    ->get('/ebooks/create')
    ->assertOk()
    ->assertSee('Hanya PDF, maksimal 9 MB.');
});
