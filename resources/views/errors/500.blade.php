<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-dvh flex items-center justify-center bg-background px-4 sm:px-6">
    <div class="w-full max-w-sm animate-enter">
        {{-- Error box pattern (Design System 7.8) --}}
        <div class="flex animate-enter items-start gap-3 rounded-md bg-primary-tint px-4 py-3 text-error">
            <svg class="size-6 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                aria-hidden="true">
                <path d="M12 9v3.75M12 17.25h.008v.008H12V17.25Z" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9Z" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
            <div>
                <h2 class="text-title-md font-semibold text-error">Kesalahan Server</h2>
                <p class="mt-1 text-body text-error">Terjadi kesalahan pada server. Silakan coba lagi beberapa saat
                    lagi.</p>
            </div>
        </div>

        {{-- Button group: Secondary + Primary (Design System 7.1) --}}
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            {{-- Secondary button: Coba Lagi --}}
            <button onclick="window.location.reload()"
                class="group inline-flex min-h-12 items-center justify-center gap-2 rounded-full border border-outline-strong bg-transparent px-6 text-label font-medium text-text lg:min-h-10 lg:px-5 hover:border-ink hover:bg-ink hover:text-on-ink transition duration-100 ease-standard cursor-pointer active:scale-[0.97] motion-reduce:active:scale-100">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path d="M21 12a9 9 0 0 1-9 9M21 12a9 9 0 0 1-6.3-15.6L21 12" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M21 12a9 9 0 0 0-9-9M21 12a9 9 0 0 0-6.3 15.6L21 12" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Coba Lagi
            </button>

            {{-- Primary button: Kembali ke Beranda --}}
            <a href="/"
                class="group inline-flex min-h-12 items-center justify-center gap-2 rounded-full border border-transparent bg-primary px-6 text-label font-medium text-on-primary lg:min-h-10 lg:px-5 hover:bg-primary-hover active:bg-primary-pressed transition duration-100 ease-standard cursor-pointer active:scale-[0.97] motion-reduce:active:scale-100">
                <svg class="size-5 transition-transform duration-200 ease-standard group-hover:-rotate-90 motion-reduce:transform-none"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>
