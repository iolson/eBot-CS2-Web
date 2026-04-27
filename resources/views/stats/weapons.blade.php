<x-layouts.app :title="__('Weapon Statistics')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ __('Weapon Statistics') }}</h1>
            <a href="{{ route('stats.index') }}" class="text-sm text-yellow-400 hover:text-yellow-300">← {{ __('Back') }}</a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Weapon') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Kills') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Headshots') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('HS%') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($weaponStats as $stat)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 font-medium text-white">{{ $stat->weapon }}</td>
                            <td class="px-4 py-3 text-center text-white">{{ number_format($stat->total_kills) }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ number_format($stat->headshots) }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">
                                @if($stat->total_kills > 0)
                                    {{ round($stat->headshots / $stat->total_kills * 100) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('No weapon data yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
