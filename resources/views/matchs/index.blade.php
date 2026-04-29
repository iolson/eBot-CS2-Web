<x-layouts.app :title="__('Matches')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ __('Matches') }}</h1>
            <a href="{{ route('matchs.archived') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Archives') }}
            </a>
        </div>

        {{-- Event filter --}}
        @if(session('selected_event_id'))
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-400">{{ __('Filtered by event') }}</span>
                <a href="{{ route('events.index') }}" class="text-xs text-yellow-400 hover:text-yellow-300">{{ __('Change') }}</a>
            </div>
        @endif

        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Match') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Score') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Event') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($matches as $match)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 font-medium text-white">
                                {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-white">{{ $match->score_a }} – {{ $match->score_b }}</td>
                            <td class="px-4 py-3"><x-match-status-badge :match="$match" /></td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $match->event?->name ?? '–' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('matchs.show', $match) }}"
                                   class="rounded bg-gray-700 hover:bg-gray-600 px-2 py-1 text-xs text-gray-300">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('No matches found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $matches->links() }}
    </div>
</x-layouts.app>
