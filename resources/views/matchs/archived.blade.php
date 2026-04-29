<x-layouts.app :title="__('Archived Matches')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ __('Archived Matches') }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('events.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Filter by Event') }}
                </a>
                <a href="{{ route('matchs.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Current Matches') }}
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Match') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Result') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Event') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($matches as $match)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-400">{{ $match->id }}</td>
                            <td class="px-4 py-3 font-medium text-white">
                                {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-white">{{ $match->score_a }} – {{ $match->score_b }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $match->season?->name ?? '–' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('matchs.show', $match) }}"
                                   class="rounded bg-gray-700 hover:bg-gray-600 px-2 py-1 text-xs text-gray-300">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('No archived matches found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $matches->links() }}
    </div>
</x-layouts.app>
