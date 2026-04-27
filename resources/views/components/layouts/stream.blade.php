<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'eBot CS2 Stream Overlay' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="text-gray-100" style="background-color: transparent;">
    {{-- Socket.IO Configuration (public/read-only for stream overlays) --}}
    <script>
        window.ebotConfig = {
            websocketUrl: @json(config('ebot.websocket_url')),
            jwtToken: 'Bearer {{ $jwtToken ?? '' }}',
            isAdmin: false,
        };
    </script>

    {{ $slot }}

    @stack('scripts')
</body>
</html>
