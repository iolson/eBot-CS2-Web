<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'eBot CS2 Widget' }}</title>

    @vite(['resources/css/app.css'])

    <style>
        body {
            background: transparent;
            font-size: 12px;
        }
        .table th, .table td {
            line-height: 12px;
        }
    </style>

    @stack('head')
</head>
<body class="text-gray-100">
    <div class="p-2">
        {{ $slot }}
    </div>

    <div class="text-right text-xs text-gray-500 px-2 py-1">
        Powered by <a href="https://www.esport-tools.net/ebot" target="_blank" class="hover:text-gray-400">eBot</a>
    </div>

    @stack('scripts')
</body>
</html>
