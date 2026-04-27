<x-layouts.admin :title="__('Toornament Import')">
    <div class="space-y-6">
        <h1 class="text-xl font-bold text-white">{{ __('Toornament Import') }}</h1>

        @if(! $configured)
            <div class="rounded-lg bg-yellow-900/50 border border-yellow-700 p-4 text-sm text-yellow-300">
                {{ __('Toornament integration is not configured. Add TOORNAMENT_ID, TOORNAMENT_SECRET, and TOORNAMENT_API_KEY to your .env file.') }}
            </div>
        @else
            @if(isset($apiError))
                <div class="rounded-lg bg-red-900/50 border border-red-700 p-4 text-sm text-red-300">
                    {{ __('API Error') }}: {{ $apiError }}
                </div>
            @endif

            {{-- Tournament list --}}
            @if(! empty($tournaments))
                <div class="overflow-x-auto rounded-lg border border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Tournament') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Browse') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                            @foreach($tournaments as $t)
                                <tr class="hover:bg-gray-800 {{ ($tournamentId === ($t['id'] ?? '')) ? 'bg-yellow-900/20' : '' }}">
                                    <td class="px-4 py-3 font-medium text-white">{{ $t['name'] ?? $t['id'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.toornament.index', ['id' => $t['id']]) }}"
                                           class="rounded bg-gray-700 hover:bg-gray-600 px-2 py-1 text-xs text-gray-300">
                                            {{ __('Matches') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Match list for selected tournament --}}
            @if($tournamentId && ! empty($matches))
                <div>
                    <h2 class="text-lg font-semibold text-white mb-3">{{ __('Tournament Matches') }}</h2>
                    <div class="overflow-x-auto rounded-lg border border-gray-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Match') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Import') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                                @foreach($matches as $m)
                                    <tr class="hover:bg-gray-800">
                                        <td class="px-4 py-3 text-white">
                                            {{ $m['opponents'][0]['participant']['name'] ?? '?' }}
                                            vs
                                            {{ $m['opponents'][1]['participant']['name'] ?? '?' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $m['status'] ?? '–' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            @foreach($m['games'] ?? [] as $gi => $game)
                                                <button type="button"
                                                        onclick="importMatch('{{ $tournamentId }}', '{{ $m['id'] }}', {{ $gi + 1 }})"
                                                        class="rounded bg-yellow-700 hover:bg-yellow-600 px-2 py-1 text-xs text-white ml-1">
                                                    {{ __('Game') }} {{ $gi + 1 }}
                                                </button>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @push('scripts')
    <script>
    function importMatch(tournamentId, matchId, gameId) {
        fetch('{{ route('admin.toornament.import') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ toornamentId: tournamentId, toornamentMatchId: matchId, gameId: gameId }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) {
                window.location.href = '{{ url('/admin/matchs') }}/' + data.matchId + '/edit';
            } else if (data.matchId) {
                window.location.href = '{{ url('/admin/matchs') }}/' + data.matchId + '/edit';
            } else {
                alert('Import failed: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(e => alert('Network error: ' + e.message));
    }
    </script>
    @endpush
</x-layouts.admin>
