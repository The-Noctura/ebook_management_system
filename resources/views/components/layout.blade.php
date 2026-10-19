@props(['title' => config('app.name', 'Laravel'), 'bare' => false])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css'])

    <style>
        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            line-height: 1.5;
        }

        .page-content {
            width: min(72rem, calc(100% - 2rem));
            margin-inline: auto;
            padding-block: 2rem;
        }
    </style>
</head>

{{-- Warna latar dan teks mengikuti token Design System (terang/gelap otomatis) --}}

<body class="bg-background text-text">
    @if ($bare)
        {{-- Halaman yang mengatur lebar dan jaraknya sendiri (mis. daftar ebook) --}}
        {{ $slot }}
    @else
        <main class="page-content">
            {{ $slot }}
        </main>
    @endif
</body>

</html>
