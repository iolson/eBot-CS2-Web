<x-layouts.app :title="$stats->name ?? $steamid">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ $stats->name ?? $steamid }}</h1>
            <a href="{{ route('stats.global') }}" class="text-sm text-yellow-400 hover:text-yellow-300">← {{ __('Back') }}</a>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('Kills') }}</p>
                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($stats->total_kills) }}</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('Deaths') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-300">{{ number_format($stats->total_deaths) }}</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('HS%') }}</p>
                <p class="mt-2 text-3xl font-bold text-red-400">
                    @if($stats->total_kills > 0)
                        {{ round($stats->total_hs / $stats->total_kills * 100) }}%
                    @else
                        0%
                    @endif
                </p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-xs uppercase tracking-wider text-gray-400">{{ __('K/D') }}</p>
                <p class="mt-2 text-3xl font-bold text-yellow-400">
                    @if($stats->total_deaths > 0)
                        {{ round($stats->total_kills / $stats->total_deaths, 2) }}
                    @else
                        {{ $stats->total_kills }}.00
                    @endif
                </p>
            </div>
        </div>

        <p class="text-xs text-gray-500 font-mono">{{ $steamid }}</p>

        {{-- Recent Maps --}}
        @if($recentMaps->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('Recent Maps') }}</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Match') }}</th>
                                <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">K</th>
                                <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">D</th>
                                <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">A</th>
                                <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-400">HS%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                            @foreach($recentMaps as $entry)
                                <tr class="hover:bg-gray-800">
                                    <td class="px-4 py-2 text-white">
                                        @if($entry->map && $entry->map->match)
                                            <a href="{{ route('matchs.show', $entry->map->match) }}"
                                               class="hover:text-yellow-400">
                                                {{ $entry->map->match->teamA?->name ?? '?' }}
                                                vs
                                                {{ $entry->map->match->teamB?->name ?? '?' }}
                                            </a>
                                        @else
                                            –
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center text-white">{{ $entry->nb_kill }}</td>
                                    <td class="px-4 py-2 text-center text-gray-300">{{ $entry->death }}</td>
                                    <td class="px-4 py-2 text-center text-gray-300">{{ $entry->assist }}</td>
                                    <td class="px-4 py-2 text-center text-gray-300">{{ $entry->getHsPercent() }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
