<x-layouts.app :title="__('Gun Round Stats')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ __('Gun Round Statistics') }}</h1>
            <a href="{{ route('stats.index') }}" class="text-sm text-yellow-400 hover:text-yellow-300">← {{ __('Back') }}</a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Team Win') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Rounds') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('CT Wins') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('T Wins') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($gunRoundStats as $stat)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 font-medium text-white">{{ $stat->team_win }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $stat->rounds_played }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $stat->ct_wins }}</td>
                            <td class="px-4 py-3 text-center text-gray-300">{{ $stat->t_wins }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('No data yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
