<?php

use App\Exceptions\EbookFileMissingException;
use App\Models\Ebook;
use App\Models\User;
use App\Services\EbookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

$disk = Storage::disk(config('filesystems.ebook_disk', 'local'));
$service = new EbookService();

// Pilih ebook pertama yang filenya benar-benar ada di disk.
$ebook = Ebook::all()->first(fn($e) => $disk->exists($e->file_path));

if ($ebook === null) {
  throw new RuntimeException('Tidak ada ebook dengan file di disk. Jalankan siapkan_ebook_uji.php terlebih dahulu.');
}

$pemilik = User::findOrFail($ebook->user_id);
$lapor = function (string $nama, bool $lulus): void {
  echo str_pad($nama, 55) . ($lulus ? 'LULUS' : 'GAGAL') . "\n";
};

echo "Ebook ID {$ebook->id} | pemilik user ID {$pemilik->id} | file: {$ebook->file_path}\n\n";

// Kasus 1: pemilik membuka file yang ada.
$response = $service->streamFile($pemilik, $ebook->id);

ob_start();
$response->sendContent();
$isi = ob_get_clean();

$lapor('1a. Status 200', $response->getStatusCode() === 200);
$lapor('1b. Content-Type application/pdf', str_contains((string) $response->headers->get('Content-Type'), 'application/pdf'));
$lapor('1c. Content-Disposition diawali inline', str_starts_with((string) $response->headers->get('Content-Disposition'), 'inline'));
$lapor('1d. Isi diawali %PDF', str_starts_with($isi, '%PDF'));
$lapor('1e. Ukuran isi sama dengan ukuran file di disk', strlen($isi) === $disk->size($ebook->file_path));

// Kasus 2: user lain mencoba membuka ebook tersebut.
$userLain = User::create([
  'name' => 'Pengguna Uji',
  'email' => uniqid('uji_') . '@contoh.test',
  'password' => Hash::make('rahasia123'),
]);

try {
  $service->streamFile($userLain, $ebook->id);
  $lapor('2. Ebook user lain ditolak (ModelNotFoundException)', false);
} catch (ModelNotFoundException $e) {
  $lapor('2. Ebook user lain ditolak (ModelNotFoundException)', true);
} finally {
  $userLain->delete();
}

// Kasus 3: baris ebook ada, tetapi file hilang dari disk.
$cadangan = $ebook->file_path . '.bak';
$disk->move($ebook->file_path, $cadangan);

try {
  $service->streamFile($pemilik, $ebook->id);
  $lapor('3. File hilang menghasilkan EbookFileMissingException', false);
} catch (EbookFileMissingException $e) {
  $lapor('3. File hilang menghasilkan EbookFileMissingException', $e->ebookId === $ebook->id);
} finally {
  $disk->move($cadangan, $ebook->file_path);
}

$lapor('3b. File sudah dikembalikan ke lokasi semula', $disk->exists($ebook->file_path));
