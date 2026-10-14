<?php

namespace App\Services;

use App\Exceptions\DuplicateEbookException;
use App\Exceptions\EbookFileMissingException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Ebook;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class EbookService
{
  /**
   * @return \Illuminate\Filesystem\FilesystemAdapter
   */
  private function disk()
  {
    return Storage::disk(config('filesystems.ebook_disk', 'local'));
  }

  public function store(User $user, UploadedFile $file, ?string $title = null): Ebook
  {
    // 1. Hash SHA-256 isi file.
    $hash = hash_file('sha256', $file->getRealPath());

    // 2. Penghalang awal: user sudah punya file dengan hash sama?
    $existingId = Ebook::where('user_id', $user->id)
      ->where('file_hash', $hash)
      ->value('id');

    if ($existingId !== null) {
      throw new DuplicateEbookException((int) $existingId);
    }

    // 3. Judul: dari input, atau nama file tanpa ekstensi.
    $title = trim((string) $title);
    if ($title === '') {
      $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }
    $title = Str::limit($title, 255, '');

    // 4. Simpan file ke ebooks/user-{id}/{uuid}.pdf.
    $path = $this->disk()->putFileAs(
      'ebooks/user-' . $user->id,
      $file,
      Str::uuid() . '.pdf'
    );

    if ($path === false) {
      throw new RuntimeException('Gagal menyimpan file ke storage.');
    }

    // 5. Simpan baris ebooks dalam transaksi.
    try {
      return DB::transaction(fn() => Ebook::create([
        'user_id' => $user->id,
        'title' => $title,
        'file_path' => $path,
        'file_hash' => $hash,
      ]));
    } catch (Throwable $e) {
      // DB::transaction sudah melakukan ROLLBACK. Sekarang compensating action:
      $this->deleteFileQuietly($path);

      // 6. Duplicate key dari unique index (misalnya dua upload bersamaan).
      if ($e instanceof QueryException && ($e->errorInfo[1] ?? null) === 1062) {
        $existingId = Ebook::where('user_id', $user->id)
          ->where('file_hash', $hash)
          ->value('id');

        if ($existingId !== null) {
          throw new DuplicateEbookException((int) $existingId);
        }
      }

      throw $e;
    }
  }

  /**
   * Mengubah status baca ebook milik user.
   *
   * @throws \Illuminate\Database\Eloquent\ModelNotFoundException bila ebook tidak ada atau bukan milik user
   */
  public function setReadStatus(User $user, int $ebookId, bool $isRead): Ebook
  {
    // 1. Cari ebook milik user. Ebook milik user lain diperlakukan sama dengan tidak ada.
    $ebook = Ebook::where('user_id', $user->id)->findOrFail($ebookId);

    // 2. Idempotent: status sudah sama, procedure tidak dipanggil.
    if ($ebook->is_read === $isRead) {
      return $ebook;
    }

    // 3. Status berbeda: panggil procedure di dalam transaksi.
    //    Trigger AFTER UPDATE mencatat riwayat; error apa pun memicu ROLLBACK otomatis.
    $procedure = $isRead ? 'sp_mark_ebook_read' : 'sp_mark_ebook_unread';

    DB::transaction(function () use ($procedure, $user, $ebook) {
      DB::statement("CALL {$procedure}(?, ?)", [$user->id, $ebook->id]);
    });

    // 4. Procedure mengubah baris langsung di MySQL, sehingga model di memori sudah usang.
    return $ebook->fresh();
  }

  /**
   * Mengalirkan PDF milik user ke klien (Web atau API).
   *
   * @throws \Illuminate\Database\Eloquent\ModelNotFoundException bila ebook tidak ada atau bukan milik user
   * @throws EbookFileMissingException bila baris ebook ada tetapi file tidak ada di disk
   */
  public function streamFile(User $user, int $ebookId): StreamedResponse
  {
    // 1. Kepemilikan: ebook milik user lain diperlakukan sama dengan tidak ada.
    $ebook = Ebook::where('user_id', $user->id)->findOrFail($ebookId);

    // 2. File harus ada di disk (lokal atau R2).
    $disk = $this->disk();

    if (! $disk->exists($ebook->file_path)) {
      Log::warning('File ebook hilang dari storage', [
        'ebook_id' => $ebook->id,
        'file_path' => $ebook->file_path,
      ]);

      throw new EbookFileMissingException($ebook->id);
    }

    // 3. Nama file untuk header: judul tanpa karakter garis miring, ditambah ekstensi .pdf.
    $filename = preg_replace('/[\\\\\/]+/', '-', $ebook->title) . '.pdf';

    // 4. Stream dengan Content-Disposition: inline.
    //    Content-Type dan Content-Length diisi otomatis oleh Laravel.
    return $disk->response(
      $ebook->file_path,
      $filename,
      ['Content-Type' => 'application/pdf'],
      'inline'
    );
  }

  private function deleteFileQuietly(string $path): void
  {
    try {
      if (! $this->disk()->delete($path)) {
        Log::error('File yatim: penghapusan mengembalikan false', ['file_path' => $path]);
      }
    } catch (Throwable $e) {
      Log::error('File yatim: penghapusan gagal', [
        'file_path' => $path,
        'error' => $e->getMessage(),
      ]);
    }
  }
}
