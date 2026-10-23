<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh flex items-center justify-center bg-background px-4 sm:px-6">
    <div class="w-full max-w-sm animate-enter">
        {{-- Empty state pattern (Design System 7.7) --}}
        <div class="flex flex-col items-center gap-4 rounded-lg bg-primary-tint p-6 lg:flex-row">
            <svg class="size-16 shrink-0 text-primary lg:size-20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 8v4M12 16h.01" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="text-center lg:text-left">
                <h1 class="text-title-lg font-semibold text-text">404</h1>
                <h2 class="mt-1 text-title-md font-medium text-text">Halaman Tidak Ditemukan</h2>
                <p class="mt-2 text-body text-text-muted">Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
            </div>
        </div>

        {{-- Primary button (Design System 7.1) --}}
        <a href="/" class="mt-6 block w-full group inline-flex min-h-12 items-center justify-center gap-2 rounded-full border border-transparent bg-primary px-6 text-label font-medium text-on-primary lg:min-h-10 lg:px-5 hover:bg-primary-hover active:bg-primary-pressed transition duration-100 ease-standard cursor-pointer active:scale-[0.97] motion-reduce:active:scale-100">
            <svg class="size-5 transition-transform duration-200 ease-standard group-hover:-rotate-90 motion-reduce:transform-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>