<x-layouts.app :title="__('Global Stats')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ __('Global Player Stats') }}</h1>
            <a href="{{ route('stats.index') }}" class="text-sm text-yellow-400 hover:text-yellow-300">← {{ __('Back') }}</a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Player') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">K</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">D</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">A</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">HS</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">HS%</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">K/D</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($players as $i => $player)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-2 text-gray-500">{{ $players->firstItem() + $i }}</td>
                            <td class="px-4 py-2 font-medium text-white">
                                <a href="{{ route('stats.player', $player->steamid) }}"
                                   class="hover:text-yellow-400">{{ $player->name }}</a>
                            </td>
                            <td class="px-4 py-2 text-center text-white">{{ $player->total_kills }}</td>
                            <td class="px-4 py-2 text-center text-gray-300">{{ $player->total_deaths }}</td>
                            <td class="px-4 py-2 text-center text-gray-300">{{ $player->total_assists }}</td>
                            <td class="px-4 py-2 text-center text-gray-300">{{ $player->total_hs }}</td>
                            <td class="px-4 py-2 text-center text-gray-300">
                                @if($player->total_kills > 0)
                                    {{ round($player->total_hs / $player->total_kills * 100) }}%
                                @else
                                    0%
                                @endif
                            </td>
                            <td class="px-4 py-2 text-center font-mono text-gray-300">
                                @if($player->total_deaths > 0)
                                    {{ round($player->total_kills / $player->total_deaths, 2) }}
                                @else
                                    {{ $player->total_kills }}.00
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">{{ __('No stats yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $players->links() }}
    </div>
</x-layouts.app>
