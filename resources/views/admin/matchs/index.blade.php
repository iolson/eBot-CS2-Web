<x-layouts.admin :title="__('Matches')">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-white">{{ __('Matches') }}</h1>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.matchs.archive-all') }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs font-medium text-gray-300">
                        {{ __('Archive All Finished') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.matchs.start-all') }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-green-700 hover:bg-green-600 px-3 py-1.5 text-xs font-medium text-white">
                        {{ __('Start All') }}
                    </button>
                </form>
                <a href="{{ route('admin.matchs.archived') }}" class="rounded-md bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">
                    {{ __('Archives') }}
                </a>
                <a href="{{ route('admin.matchs.create') }}" class="rounded-md bg-yellow-600 hover:bg-yellow-500 px-3 py-1.5 text-xs font-semibold text-white">
                    + {{ __('New Match') }}
                </a>
            </div>
        </div>

        @if(session('info'))
            <div class="rounded-md bg-blue-900/50 border border-blue-700 p-3 text-sm text-blue-300">{{ session('info') }}</div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Match') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Score') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Server') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($matches as $match)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-400">{{ $match->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-white">
                                    {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
                                </div>
                                @if($match->season)
                                    <div class="text-xs text-gray-400">{{ $match->season->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-white">{{ $match->score_a }} – {{ $match->score_b }}</td>
                            <td class="px-4 py-3">
                                <x-match-status-badge :match="$match" />
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $match->server?->ip ?? '–' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    @if($match->status === \App\Models\Matchs::STATUS_NOT_STARTED)
                                        <form method="POST" action="{{ route('admin.matchs.start', $match) }}">@csrf
                                            <button class="rounded bg-green-700 hover:bg-green-600 px-2 py-1 text-xs text-white">{{ __('Start') }}</button>
                                        </form>
                                    @elseif($match->isLive())
                                        <form method="POST" action="{{ route('admin.matchs.stop', $match) }}">@csrf
                                            <button class="rounded bg-red-700 hover:bg-red-600 px-2 py-1 text-xs text-white">{{ __('Stop') }}</button>
                                        </form>
                                    @elseif($match->status === \App\Models\Matchs::STATUS_END_MATCH)
                                        <form method="POST" action="{{ route('admin.matchs.archive', $match) }}">@csrf
                                            <button class="rounded bg-gray-600 hover:bg-gray-500 px-2 py-1 text-xs text-white">{{ __('Archive') }}</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.matchs.show', $match) }}"
                                       class="rounded bg-gray-700 hover:bg-gray-600 px-2 py-1 text-xs text-gray-300">{{ __('View') }}</a>

                                    @if(! $match->isLive())
                                        <a href="{{ route('admin.matchs.edit', $match) }}"
                                           class="rounded bg-yellow-700 hover:bg-yellow-600 px-2 py-1 text-xs text-white">{{ __('Edit') }}</a>
                                        @if(! $match->isArchived())
                                            <form method="POST" action="{{ route('admin.matchs.destroy', $match) }}"
                                                  onsubmit="return confirm('{{ __('Delete this match?') }}')">
                                                @csrf @method('DELETE')
                                                <button class="rounded bg-red-900 hover:bg-red-800 px-2 py-1 text-xs text-red-300">{{ __('Delete') }}</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">{{ __('No matches found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $matches->links() }}
    </div>
</x-layouts.admin>
