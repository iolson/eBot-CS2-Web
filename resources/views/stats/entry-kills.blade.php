<x-layouts.app :title="__('Entry Kills')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ __('Entry Kills') }}</h1>
            <a href="{{ route('stats.index') }}" class="text-sm text-yellow-400 hover:text-yellow-300">← {{ __('Back') }}</a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Player') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Entry Kills') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($entryStats as $i => $stat)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-white">
                                @if($stat->killer_id)
                                    <a href="{{ route('stats.player', $stat->killer_id) }}"
                                       class="hover:text-yellow-400">{{ $stat->name ?? $stat->killer_id }}</a>
                                @else
                                    {{ $stat->name ?? '–' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-white">{{ $stat->entry_kills }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">{{ __('No data yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
