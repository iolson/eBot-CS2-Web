<x-layouts.app title="Home">
    <div class="text-center py-12">
        <h1 class="text-4xl font-bold text-white mb-4">eBot CS2</h1>
        <p class="text-gray-400 text-lg mb-8">{{ __('Match management for Counter-Strike 2') }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto">
            <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">{{ __('Current Matches') }}</h2>
                <p class="text-gray-400 text-sm">{{ __('View live matches in progress') }}</p>
            </div>

            <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">{{ __('Archives') }}</h2>
                <p class="text-gray-400 text-sm">{{ __('Browse completed match history') }}</p>
            </div>

            <div class="rounded-lg bg-gray-800 border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-2">{{ __('Statistics') }}</h2>
                <p class="text-gray-400 text-sm">{{ __('Player and map statistics') }}</p>
            </div>
        </div>

        <p class="mt-8 text-xs text-gray-600">{{ config('ebot.version') }}</p>
    </div>
</x-layouts.app>
