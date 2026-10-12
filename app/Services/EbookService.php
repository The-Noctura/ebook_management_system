<?php

namespace App\Services;

use App\Exceptions\DuplicateEbookException;
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
            'ebooks/user-'.$user->id,
            $file,
            Str::uuid().'.pdf'
        );

        if ($path === false) {
            throw new RuntimeException('Gagal menyimpan file ke storage.');
        }

        // 5. Simpan baris ebooks dalam transaksi.
        try {
            return DB::transaction(fn () => Ebook::create([
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
