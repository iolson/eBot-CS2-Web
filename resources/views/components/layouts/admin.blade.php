<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} - eBot CS2 Admin</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('head')
</head>
<body class="min-h-screen bg-gray-900 text-gray-100 antialiased">
    {{-- Socket.IO Configuration (admin gets full access) --}}
    <script>
        window.ebotConfig = {
            websocketUrl: @json(config('ebot.websocket_url')),
            jwtToken: 'Bearer {{ $jwtToken ?? '' }}',
            refreshTime: {{ config('ebot.refresh_time') }},
            mode: @json(config('ebot.mode')),
            isAdmin: true,
        };
    </script>

    {{-- Admin Navigation --}}
    <nav class="bg-gray-800 border-b border-yellow-600/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="{{ url('/admin') }}" class="text-xl font-bold text-yellow-400">eBot CS2 Admin</a>
                    <div class="hidden md:flex items-center gap-4">
                        <a href="{{ url('/admin/matchs') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Matches') }}</a>
                        <a href="{{ url('/admin/servers') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Servers') }}</a>
                        <a href="{{ url('/admin/teams') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Teams') }}</a>
                        <a href="{{ url('/admin/events') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Events') }}</a>
                        <a href="{{ url('/admin/configs') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Configs') }}</a>
                        <a href="{{ url('/admin/advertising') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Advertising') }}</a>
                        <a href="{{ url('/admin/users') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Users') }}</a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Connection Status Indicator --}}
                    <div id="connection-status" class="flex items-center gap-1.5">
                        <span class="inline-block h-2 w-2 rounded-full bg-gray-500" id="ws-indicator"></span>
                        <span class="text-xs text-gray-500" id="ws-status-text">{{ __('Disconnected') }}</span>
                    </div>

                    {{-- Language Switcher --}}
                    <x-language-switcher />

                    <a href="{{ url('/') }}" class="text-gray-400 hover:text-white text-sm">{{ __('Back to site') }}</a>
                    <span class="text-gray-400 text-sm">{{ auth()->user()->username ?? '' }}</span>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white text-sm">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md bg-green-900/50 border border-green-700 p-4">
                <p class="text-sm text-green-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md bg-red-900/50 border border-red-700 p-4">
                <p class="text-sm text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- WebSocket Support Warning --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4 hidden" id="websocket-warning">
        <div class="rounded-md bg-yellow-900/50 border border-yellow-700 p-4">
            <p class="text-sm text-yellow-300">{{ __('Your browser does not support WebSocket connections. Without WebSockets you cannot control and manage matches.') }}</p>
        </div>
    </div>

    {{-- Admin Content with Sidebar --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-6">
            {{-- Sidebar --}}
            <aside class="hidden lg:block w-56 shrink-0">
                @include('partials.admin-sidebar')
            </aside>

            {{-- Main Content --}}
            <main class="flex-1 min-w-0">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="border-t border-gray-700 mt-auto">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-xs text-gray-500">
                &copy; <a href="https://www.esport-tools.net/ebot" target="_blank" class="hover:text-gray-400">eSport-tools</a> 2012-{{ date('Y') }}
                - {{ config('ebot.version') }}
                - By <a href="https://twitter.com/deStrO_BE" target="_blank" class="hover:text-gray-400">deStrO</a>
                - Powered by <a href="https://laravel.com" target="_blank" class="hover:text-gray-400">Laravel</a>
                - <a href="https://github.com/deStrO/eBot-CSGO" target="_blank" class="hover:text-gray-400">GitHub</a>
            </p>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
