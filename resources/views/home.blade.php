<x-layouts.app :title="__('Home')">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Live & Recent Matches') }}</h1>

        @forelse($matches as $match)
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 hover:border-gray-600 transition-colors">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <x-match-status-badge :match="$match" />
                        <span class="text-white font-medium">
                            {{ $match->teamA?->name ?? '?' }}
                            <span class="font-mono text-yellow-400 mx-2">{{ $match->score_a }} – {{ $match->score_b }}</span>
                            {{ $match->teamB?->name ?? '?' }}
                        </span>
                        @if($match->season)
                            <span class="text-xs text-gray-400">{{ $match->season->name }}</span>
                        @endif
                    </div>
                    <a href="{{ route('matchs.show', $match) }}"
                       class="text-sm text-yellow-400 hover:text-yellow-300">{{ __('Details') }} →</a>
                </div>
            </div>
        @empty
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-8 text-center text-gray-500">
                {{ __('No active matches.') }}
            </div>
        @endforelse

        <div class="flex items-center gap-4 pt-2">
            <a href="{{ route('matchs.index') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('All Matches') }}
            </a>
            <a href="{{ route('matchs.archived') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Archives') }}
            </a>
            <a href="{{ route('stats.index') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Statistics') }}
            </a>
        </div>
    </div>
</x-layouts.app>
