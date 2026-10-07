@props(['title' => config('app.name', 'Laravel')])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <style>
        :root {
            color-scheme: light;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1f2937;
            background-color: #f8fafc;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

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

<body>
    <main class="page-content">
        {{ $slot }}
    </main>
</body>

</html>
