<x-layouts.stream :title="($match->teamA?->name ?? '?') . ' vs ' . ($match->teamB?->name ?? '?')">
    <div class="min-h-screen bg-gray-900 text-white p-4 space-y-4">

        {{-- Match Header --}}
        <div class="text-center">
            <div class="text-5xl font-mono font-bold text-yellow-400">
                {{ $match->score_a }} &ndash; {{ $match->score_b }}
            </div>
            <div class="mt-2 text-xl font-semibold">
                {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
            </div>
            <div class="mt-1">
                <x-match-status-badge :match="$match" />
            </div>
        </div>

        {{-- Per-Map Breakdown --}}
        @foreach($match->maps as $map)
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-medium text-gray-300">{{ $map->map_name }}</span>
                    <span class="font-mono text-yellow-400 font-bold">{{ $map->score_1 }} – {{ $map->score_2 }}</span>
                </div>
            </div>
        @endforeach

        {{-- Live indicator --}}
        @if($match->isLive())
            <div class="text-center">
                <span class="inline-flex items-center gap-2 text-sm text-blue-300">
                    <span class="inline-block h-2 w-2 rounded-full bg-blue-400 animate-pulse"></span>
                    {{ __('Live') }}
                </span>
            </div>
        @endif

        <div class="text-center">
            <a href="{{ route('matchs.show', $match) }}"
               class="text-sm text-yellow-400 hover:text-yellow-300">{{ __('Full Stats') }} →</a>
        </div>
    </div>
</x-layouts.stream>
