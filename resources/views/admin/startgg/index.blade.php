<x-layouts.admin :title="__('start.gg Import')">
    <div class="space-y-6">
        <h1 class="text-xl font-bold text-white">{{ __('start.gg Import') }}</h1>

        @if(! $configured)
            <div class="rounded-lg bg-yellow-900/50 border border-yellow-700 p-4 text-sm text-yellow-300">
                {{ __('start.gg integration is not configured. Add STARTGG_TOKEN to your .env file.') }}
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
                                <tr class="hover:bg-gray-800 {{ ($tournamentSlug === ($t['slug'] ?? '')) ? 'bg-yellow-900/20' : '' }}">
                                    <td class="px-4 py-3 font-medium text-white">{{ $t['name'] ?? $t['slug'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.startgg.index', ['tournament' => $t['slug']]) }}"
                                           class="rounded bg-gray-700 hover:bg-gray-600 px-2 py-1 text-xs text-gray-300">
                                            {{ __('Events') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Event list for selected tournament --}}
            @if($tournamentSlug && ! empty($events))
                <div>
                    <h2 class="text-lg font-semibold text-white mb-3">{{ __('Events') }}</h2>
                    <div class="overflow-x-auto rounded-lg border border-gray-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Event') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Entrants') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Browse') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                                @foreach($events as $event)
                                    <tr class="hover:bg-gray-800 {{ ($eventId === ($event['id'] ?? null)) ? 'bg-yellow-900/20' : '' }}">
                                        <td class="px-4 py-3 font-medium text-white">{{ $event['name'] ?? '?' }}</td>
                                        <td class="px-4 py-3 text-gray-400">{{ $event['numEntrants'] ?? '–' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.startgg.index', ['tournament' => $tournamentSlug, 'event' => $event['id']]) }}"
                                               class="rounded bg-gray-700 hover:bg-gray-600 px-2 py-1 text-xs text-gray-300">
                                                {{ __('Sets') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Set list for selected event --}}
            @if($eventId && ! empty($sets))
                <div>
                    <h2 class="text-lg font-semibold text-white mb-3">{{ __('Sets') }}</h2>
                    <div class="overflow-x-auto rounded-lg border border-gray-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Round') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Match') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Import') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                                @foreach($sets as $set)
                                    <tr class="hover:bg-gray-800">
                                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $set['fullRoundText'] ?? '–' }}</td>
                                        <td class="px-4 py-3 text-white">
                                            {{ $set['slots'][0]['entrant']['name'] ?? '?' }}
                                            vs
                                            {{ $set['slots'][1]['entrant']['name'] ?? '?' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-400 text-xs">
                                            @php
                                                $stateMap = [1 => 'Not Started', 2 => 'Started', 3 => 'Completed'];
                                            @endphp
                                            {{ $stateMap[$set['state'] ?? 0] ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button"
                                                    onclick="importSet('{{ $eventId }}', '{{ $set['id'] }}')"
                                                    class="rounded bg-yellow-700 hover:bg-yellow-600 px-2 py-1 text-xs text-white">
                                                {{ __('Import') }}
                                            </button>
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
    function importSet(eventId, setId) {
        fetch('{{ route('admin.startgg.import') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ eventId: eventId, setId: setId }),
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
