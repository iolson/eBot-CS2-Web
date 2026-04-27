<x-layouts.widget :title="__('Live Stats')">
    <div class="bg-gray-900 text-white p-3 text-xs space-y-3">
        @forelse($matches as $match)
            <div class="border border-gray-700 rounded p-2 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 text-blue-300">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        LIVE
                    </span>
                    <x-match-status-badge :match="$match" />
                </div>
                <div class="flex items-center justify-between font-semibold">
                    <span>{{ $match->teamA?->name ?? '?' }}</span>
                    <span class="font-mono text-yellow-400">{{ $match->score_a }} – {{ $match->score_b }}</span>
                    <span>{{ $match->teamB?->name ?? '?' }}</span>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-2">{{ __('No live matches.') }}</p>
        @endforelse
    </div>
</x-layouts.widget>
