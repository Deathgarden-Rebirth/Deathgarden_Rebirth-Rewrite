<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Deathgarden Rebirth' }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-gray-950 font-sans text-white antialiased">
{{ $slot }}
</body>
<footer>
    @stack('js')
</footer>
</html>