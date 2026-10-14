<?php

use App\Models\Ebook;
use App\Models\User;
use App\Services\EbookService;
use Illuminate\Support\Facades\DB;

$ebook = Ebook::firstOrFail();
$user = User::findOrFail($ebook->user_id);
$service = new EbookService();

$hitungRiwayat = fn() => DB::table('ebook_status_history')
  ->where('ebook_id', $ebook->id)
  ->count();

// Keadaan awal, dibaca langsung dari database.
$awal = DB::table('ebooks')->where('id', $ebook->id)->first();
$riwayatAwal = $hitungRiwayat();
$targetBaru = ! (bool) $awal->is_read;

echo "Ebook ID {$ebook->id} | is_read awal: " . var_export((bool) $awal->is_read, true) . " | riwayat awal: {$riwayatAwal}\n";

try {
  DB::transaction(function () use ($service, $user, $ebook, $targetBaru, $hitungRiwayat) {
    $hasil = $service->setReadStatus($user, $ebook->id, $targetBaru);

    echo "Di dalam transaksi | is_read: " . var_export($hasil->is_read, true) . " | riwayat: " . $hitungRiwayat() . "\n";

    throw new RuntimeException('Error buatan untuk uji ROLLBACK');
  });
} catch (RuntimeException $e) {
  echo "Error tertangkap: " . $e->getMessage() . "\n";
}

// Keadaan akhir, dibaca ulang langsung dari database.
$akhir = DB::table('ebooks')->where('id', $ebook->id)->first();
$riwayatAkhir = $hitungRiwayat();

echo "Setelah ROLLBACK | is_read: " . var_export((bool) $akhir->is_read, true) . " | riwayat: {$riwayatAkhir}\n";

$statusKembali = (bool) $akhir->is_read === (bool) $awal->is_read;
$riwayatSama = $riwayatAkhir === $riwayatAwal;
$updatedAtSama = (string) $akhir->updated_at === (string) $awal->updated_at;

echo "is_read kembali ke awal : " . ($statusKembali ? 'LULUS' : 'GAGAL') . "\n";
echo "riwayat tidak bertambah : " . ($riwayatSama ? 'LULUS' : 'GAGAL') . "\n";
echo "updated_at tidak berubah: " . ($updatedAtSama ? 'LULUS' : 'GAGAL') . "\n";
