<x-layouts.app :title="__('Statistics')">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Statistics') }}</h1>

        {{-- Overview Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('Live Matches') }}</p>
                <p class="mt-2 text-3xl font-bold text-blue-400">{{ $liveCount }}</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('Finished') }}</p>
                <p class="mt-2 text-3xl font-bold text-green-400">{{ $finishedCount }}</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('Total Kills') }}</p>
                <p class="mt-2 text-3xl font-bold text-yellow-400">{{ number_format($totalKills) }}</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('Headshots') }}</p>
                <p class="mt-2 text-3xl font-bold text-red-400">{{ number_format($totalHS) }}</p>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('stats.global') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Global Player Stats') }}
            </a>
            <a href="{{ route('stats.maps') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Map Statistics') }}
            </a>
            <a href="{{ route('stats.weapons') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Weapon Statistics') }}
            </a>
            <a href="{{ route('stats.entry-kills') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Entry Kills') }}
            </a>
            <a href="{{ route('stats.gunround') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Gun Round') }}
            </a>
        </div>
    </div>
</x-layouts.app>
