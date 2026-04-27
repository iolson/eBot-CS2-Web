<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'eBot CS2' }} - eBot CS2</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('head')
</head>
<body class="min-h-screen bg-gray-900 text-gray-100 antialiased">
    {{-- Socket.IO Configuration --}}
    <script>
        window.ebotConfig = {
            websocketUrl: @json(config('ebot.websocket_url')),
            jwtToken: 'Bearer {{ $jwtToken ?? '' }}',
            refreshTime: {{ config('ebot.refresh_time') }},
            mode: @json(config('ebot.mode')),
        };
    </script>

    {{-- Navigation --}}
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-white">eBot CS2</a>
                    <div class="hidden md:flex items-center gap-4">
                        <a href="{{ url('/') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Home') }}</a>
                        <a href="{{ url('/matchs/current') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Current Matches') }}</a>
                        <a href="{{ url('/matchs/archived') }}" class="text-gray-300 hover:text-white text-sm">{{ __('Archives') }}</a>
                        @auth
                            @if(auth()->user()->is_admin ?? false)
                                <a href="{{ url('/admin') }}" class="text-yellow-400 hover:text-yellow-300 text-sm font-medium">{{ __('Administration') }}</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Language Switcher --}}
                    <x-language-switcher />

                    {{-- Auth Links --}}
                    @auth
                        <span class="text-gray-400 text-sm">{{ auth()->user()->username }}</span>
                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-white text-sm">{{ __('Logout') }}</button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="text-gray-400 hover:text-white text-sm">{{ __('Login') }}</a>
                    @endauth
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

    {{-- Page Content --}}
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-700 mt-auto">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-xs text-gray-500">
                &copy; <a href="https://www.esport-tools.net/ebot" target="_blank" class="hover:text-gray-400">eSport-tools</a> 2012-{{ date('Y') }}
                - {{ config('ebot.version') }}
                - By <a href="https://twitter.com/deStrO_BE" target="_blank" class="hover:text-gray-400">deStrO</a>
                - Powered by <a href="https://laravel.com" target="_blank" class="hover:text-gray-400">Laravel</a>
                &amp; <a href="https://tailwindcss.com" target="_blank" class="hover:text-gray-400">Tailwind CSS</a>
                - <a href="https://github.com/deStrO/eBot-CSGO" target="_blank" class="hover:text-gray-400">GitHub</a>
            </p>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
