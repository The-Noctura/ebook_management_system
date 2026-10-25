#!/usr/bin/env bash
# Menyusun folder siap-unggah untuk InfinityFree.
# Jalankan dari ROOT proyek (Git Bash atau WSL di Windows), SETELAH:
#   composer install --no-dev --optimize-autoloader
#   npm run build
# Hasil: ./deploy_out/htdocs  -> unggah ISINYA ke folder htdocs di InfinityFree.
set -euo pipefail

OUT="${1:-deploy_out}/htdocs"
CORE="$OUT/app_core"

[ -f artisan ] || { echo "Jalankan dari root proyek (folder yang berisi file artisan)."; exit 1; }
[ -d vendor ] || { echo "Folder vendor belum ada. Jalankan: composer install --no-dev --optimize-autoloader"; exit 1; }
[ -f public/build/manifest.json ] || { echo "public/build belum ada. Jalankan: npm run build"; exit 1; }
[ ! -d vendor/pestphp ] || echo "PERINGATAN: vendor berisi paket dev (pestphp). Jalankan ulang: composer install --no-dev --optimize-autoloader"

rm -rf "$OUT"
mkdir -p "$CORE"

# --- isi aplikasi -> htdocs/app_core
cp -r app bootstrap config routes vendor "$CORE"/
mkdir -p "$CORE/resources" && cp -r resources/views "$CORE/resources/views"
cp artisan composer.json "$CORE"/

# Cache bootstrap dari komputer dev menyebut paket dev dan jalur lokal: hapus, dibuat ulang otomatis.
find "$CORE/bootstrap/cache" -type f -name '*.php' -delete

# Folder tulis kosong (jangan membawa PDF uji, sesi, atau view hasil kompilasi dari komputer dev)
mkdir -p "$CORE/storage/app/private" "$CORE/storage/framework/cache/data" \
         "$CORE/storage/framework/sessions" "$CORE/storage/framework/views" "$CORE/storage/logs"

# --- konfigurasi
cp deploy/infinityfree/htaccess-app_core "$CORE/.htaccess"
cp deploy/infinityfree/env.infinityfree.example "$CORE/.env"

# --- isi "public" -> htdocs
cp deploy/infinityfree/index.php "$OUT/index.php"
cp deploy/infinityfree/htaccess-root "$OUT/.htaccess"
cp -r public/build "$OUT/build"
[ -f public/favicon.ico ] && cp public/favicon.ico "$OUT/"
[ -f public/robots.txt ] && cp public/robots.txt "$OUT/"

echo
echo "Selesai: $OUT"
echo "Jumlah file : $(find "$OUT" -type f | wc -l)"
echo "Ukuran total: $(du -sh "$OUT" | cut -f1)"
BIG="$(find "$OUT" -type f -size +9M)"
if [ -n "$BIG" ]; then
  echo
  echo "PERINGATAN: file berikut lebih dari 9 MB (batas InfinityFree 10 MB per file):"
  echo "$BIG"
fi
echo
echo "Berikutnya: isi $CORE/.env (nilai bertanda ISI), lalu unggah isi htdocs lewat FTP."
