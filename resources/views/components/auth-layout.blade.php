@props(['title' => config('app.name')])
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    {{-- Mobile (< 1024): pita di atas, form di bawah. Desktop: dua kolom (dokumen 06 bagian 8.1) --}}
    <div class="grid min-h-dvh grid-rows-[auto_1fr] lg:grid-cols-2 lg:grid-rows-1">
        <header class="topbar bg-primary text-on-primary lg:flex lg:flex-col lg:justify-center lg:p-8">
            <div
                class="mx-auto flex min-h-16 w-full max-w-sm items-center px-4 sm:px-6 lg:mx-0 lg:block lg:min-h-0 lg:max-w-none lg:px-0">
                <p class="text-title-md font-semibold lg:text-title-lg">{{ config('app.name') }}</p>
                <p class="mt-2 hidden max-w-sm text-body lg:block">Satu koleksi PDF pribadi yang dapat dibuka dari
                    browser mana pun.</p>
            </div>
        </header>
        <main class="mx-auto w-full max-w-sm p-4 sm:p-6 lg:self-center lg:p-4">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
