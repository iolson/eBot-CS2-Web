<x-layouts.widget :title="__('Match Players')">
    <div class="bg-gray-900 text-white p-3 text-xs space-y-3">
        <div class="flex items-center justify-between font-semibold">
            <span>{{ $match->teamA?->name ?? '?' }}</span>
            <span class="font-mono text-yellow-400">{{ $match->score_a }} – {{ $match->score_b }}</span>
            <span>{{ $match->teamB?->name ?? '?' }}</span>
        </div>

        @foreach($match->maps as $map)
            @if($map->players->isNotEmpty())
                <div class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">{{ $map->map_name }}</div>
                <table class="w-full">
                    <thead>
                        <tr class="text-gray-500">
                            <th class="text-left py-0.5">{{ __('Player') }}</th>
                            <th class="text-center py-0.5">K</th>
                            <th class="text-center py-0.5">D</th>
                            <th class="text-center py-0.5">A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($map->players->sortByDesc('nb_kill') as $player)
                            <tr class="border-t border-gray-800">
                                <td class="py-0.5 text-white">{{ $player->pseudo }}</td>
                                <td class="py-0.5 text-center text-white">{{ $player->nb_kill }}</td>
                                <td class="py-0.5 text-center text-gray-400">{{ $player->death }}</td>
                                <td class="py-0.5 text-center text-gray-400">{{ $player->assist }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </div>
</x-layouts.widget>
