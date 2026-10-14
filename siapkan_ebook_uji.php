<?php

use App\Models\User;
use App\Services\EbookService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$user = User::firstOrFail();

// PDF minimal untuk uji. Komentar acak di akhir membuat hash selalu berbeda,
// sehingga tidak ditolak sebagai duplikat bila skrip dijalankan ulang.
$temp = tempnam(sys_get_temp_dir(), 'pdf');
file_put_contents(
  $temp,
  "%PDF-1.4\n"
    . "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
    . "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
    . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\n"
    . "trailer<</Root 1 0 R>>\n%%EOF\n% " . uniqid()
);

$file = new UploadedFile($temp, 'Ebook Uji.pdf', 'application/pdf', null, true);
$ebook = (new EbookService())->store($user, $file);

$disk = Storage::disk(config('filesystems.ebook_disk', 'local'));

echo "Ebook ID {$ebook->id} dibuat untuk user ID {$user->id}\n";
echo "file_path: {$ebook->file_path}\n";
echo "File ada di disk: " . ($disk->exists($ebook->file_path) ? 'YA' : 'TIDAK') . "\n";

@unlink($temp);
