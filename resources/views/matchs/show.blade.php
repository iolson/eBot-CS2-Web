<x-layouts.app :title="($match->teamA?->name ?? '?') . ' vs ' . ($match->teamB?->name ?? '?')">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-white">
                        {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
                    </h1>
                    <x-match-status-badge :match="$match" />
                </div>
                <p class="mt-1 text-4xl font-mono font-bold text-yellow-400">
                    {{ $match->score_a }} &ndash; {{ $match->score_b }}
                </p>
                @if($match->season)
                    <p class="mt-1 text-sm text-gray-400">{{ $match->season->name }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stream.show', $match) }}"
                   class="rounded-lg bg-blue-700 hover:bg-blue-600 px-4 py-2 text-sm font-medium text-white">
                    {{ __('Stream View') }}
                </a>
                <a href="{{ route('matchs.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        {{-- Maps / Per-Map Stats --}}
        @forelse($match->maps as $map)
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-gray-800 border-b border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-white">{{ $map->map_name }}</h2>
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-yellow-400 text-lg font-bold">{{ $map->score_1 }} – {{ $map->score_2 }}</span>
                        @if($map->hasDemoFile())
                            <a href="{{ route('matchs.demo', $map) }}"
                               class="rounded bg-indigo-700 hover:bg-indigo-600 px-3 py-1 text-xs text-white">
                                {{ __('Download Demo') }}
                            </a>
                        @endif
                    </div>
                </div>

                @if($map->players->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800/70">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Player') }}</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Team') }}</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">K</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">D</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">A</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">HS%</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">K/D</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($map->players->sortByDesc('nb_kill') as $player)
                                    <tr class="hover:bg-gray-800/60">
                                        <td class="px-4 py-2 font-medium text-white">
                                            <a href="{{ route('stats.player', $player->steamid) }}"
                                               class="hover:text-yellow-400">{{ $player->pseudo }}</a>
                                        </td>
                                        <td class="px-4 py-2 text-center text-gray-400">{{ $player->team }}</td>
                                        <td class="px-4 py-2 text-center text-white">{{ $player->nb_kill }}</td>
                                        <td class="px-4 py-2 text-center text-gray-300">{{ $player->death }}</td>
                                        <td class="px-4 py-2 text-center text-gray-300">{{ $player->assist }}</td>
                                        <td class="px-4 py-2 text-center text-gray-300">{{ $player->getHsPercent() }}%</td>
                                        <td class="px-4 py-2 text-center font-mono text-gray-300">{{ $player->getKdRatio() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-8 text-center text-gray-500">
                {{ __('No map data available yet.') }}
            </div>
        @endforelse

        {{-- Heatmap --}}
        @if($hasHeatmap)
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">{{ __('Heatmap') }}</h2>
                <p class="text-sm text-gray-400">{{ __('Heatmap data available.') }}</p>
            </div>
        @endif

        {{-- Export Links --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500 uppercase tracking-wider">{{ __('Export') }}:</span>
            <a href="{{ route('matchs.export.players', $match) }}"
               class="rounded bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">JSON Players</a>
            <a href="{{ route('matchs.export.rounds', $match) }}"
               class="rounded bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">JSON Rounds</a>
            <a href="{{ route('matchs.export.kills', $match) }}"
               class="rounded bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">JSON Kills</a>
            <a href="{{ route('matchs.export.estats', $match) }}"
               class="rounded bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">JSON eStats</a>
        </div>

    </div>
</x-layouts.app>
