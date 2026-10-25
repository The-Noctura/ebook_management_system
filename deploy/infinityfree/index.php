<?php

/*
 * Pengganti public/index.php untuk InfinityFree.
 *
 * Seluruh aplikasi Laravel berada di folder `app_core` DI DALAM htdocs (InfinityFree
 * menghapus file yang diunggah di luar htdocs). Folder app_core dikunci oleh
 * app_core/.htaccess dan aturan di .htaccess root.
 * Lihat docs/PANDUAN-INFINITYFREE.md.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$core = __DIR__ . '/app_core';

if (file_exists($maintenance = $core . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $core . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $core . '/bootstrap/app.php';

// "public" Laravel = folder ini (htdocs), tempat build/ dan index.php berada.
// Tanpa ini @vite mencari build/manifest.json di app_core/public dan gagal.
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
