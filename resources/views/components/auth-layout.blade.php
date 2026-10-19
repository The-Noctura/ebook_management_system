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
    <div class="grid min-h-dvh lg:grid-cols-2">
        <div class="topbar bg-primary px-4 py-3 text-on-primary lg:flex lg:flex-col lg:justify-center lg:p-8">
            <p class="text-title-md font-semibold lg:text-title-lg">{{ config('app.name') }}</p>
            <p class="mt-2 hidden max-w-sm text-body lg:block">Satu koleksi PDF pribadi yang dapat dibuka dari Web maupun
                Android.</p>
        </div>
        <main class="mx-auto w-full max-w-sm p-4 lg:self-center">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
