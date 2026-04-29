@php use App\Models\Matchs; @endphp
<x-layouts.admin :title="__('Match Details')">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-white">
                        {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
                    </h1>
                    <x-match-status-badge :match="$match" />
                </div>
                <p class="mt-1 text-3xl font-mono font-bold text-yellow-400">
                    {{ $match->score_a }} &ndash; {{ $match->score_b }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(! $match->isLive())
                    <a href="{{ route('admin.matchs.edit', $match) }}"
                       class="rounded-lg bg-yellow-700 hover:bg-yellow-600 px-4 py-2 text-sm font-medium text-white">
                        {{ __('Edit') }}
                    </a>
                @endif
                <a href="{{ route('admin.matchs.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">{{ __('Match Info') }}</h2>
            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('ID') }}</dt>
                    <dd class="mt-1 text-sm font-mono text-white">{{ $match->id }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1"><x-match-status-badge :match="$match" /></dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Server') }}</dt>
                    <dd class="mt-1 text-sm text-white font-mono">{{ $match->server?->ip ?? '–' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Event') }}</dt>
                    <dd class="mt-1 text-sm text-white">{{ $match->event?->name ?? '–' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Max Rounds') }}</dt>
                    <dd class="mt-1 text-sm text-white">{{ $match->max_round }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Map Selection') }}</dt>
                    <dd class="mt-1 text-sm text-white">
                        @php
                            $modes = [0 => __('Random'), 1 => __('Vote'), 2 => __('Pick')];
                        @endphp
                        {{ $modes[$match->map_selection_mode] ?? $match->map_selection_mode }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Action Buttons --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">{{ __('Actions') }}</h2>
            <div class="flex flex-wrap items-center gap-2">

                {{-- Not Started: Start --}}
                @if($match->status === Matchs::STATUS_NOT_STARTED)
                    <form method="POST" action="{{ route('admin.matchs.start', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-green-700 hover:bg-green-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Start') }}
                        </button>
                    </form>
                @endif

                {{-- Live: Stop, Stop Back, Pause/Unpause --}}
                @if($match->isLive())
                    <form method="POST" action="{{ route('admin.matchs.stop', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-red-700 hover:bg-red-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Stop') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.matchs.stop-back', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-orange-800 hover:bg-orange-700 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Stop Back') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.matchs.pause-unpause', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-yellow-700 hover:bg-yellow-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Pause / Unpause') }}
                        </button>
                    </form>
                @endif

                {{-- Warmup/Side statuses: Force Start --}}
                @if(in_array($match->status, [
                    Matchs::STATUS_WU_1_SIDE,
                    Matchs::STATUS_WU_2_SIDE,
                    Matchs::STATUS_FIRST_SIDE,
                    Matchs::STATUS_SECOND_SIDE,
                ]))
                    <form method="POST" action="{{ route('admin.matchs.force-start', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-blue-700 hover:bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Force Start') }}
                        </button>
                    </form>
                @endif

                {{-- Warmup Knife: Force Knife --}}
                @if($match->status === Matchs::STATUS_WU_KNIFE)
                    <form method="POST" action="{{ route('admin.matchs.force-knife', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-blue-700 hover:bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Force Knife') }}
                        </button>
                    </form>
                @endif

                {{-- Knife Round: Force Knife End, Pass Knife --}}
                @if($match->status === Matchs::STATUS_KNIFE)
                    <form method="POST" action="{{ route('admin.matchs.force-knife-end', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-blue-700 hover:bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Force Knife End') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.matchs.pass-knife', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-indigo-700 hover:bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Pass Knife') }}
                        </button>
                    </form>
                @endif

                {{-- End Match: Archive --}}
                @if($match->status === Matchs::STATUS_END_MATCH)
                    <form method="POST" action="{{ route('admin.matchs.archive', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-gray-600 hover:bg-gray-500 px-4 py-2 text-sm font-semibold text-white">
                            {{ __('Archive') }}
                        </button>
                    </form>
                @endif

                {{-- Always: Duplicate --}}
                <form method="POST" action="{{ route('admin.matchs.duplicate', $match) }}">
                    @csrf
                    <button type="submit"
                            class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm font-medium text-gray-300">
                        {{ __('Duplicate') }}
                    </button>
                </form>

                {{-- Toornament Export (only for linked matches) --}}
                @if($match->identifier_id)
                    <form method="POST" action="{{ route('admin.toornament.export', $match) }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-indigo-700 hover:bg-indigo-600 px-4 py-2 text-sm font-medium text-white">
                            {{ __('Export to Toornament') }}
                        </button>
                    </form>
                @endif

                {{-- Not live, not archived, not started: Reset --}}
                @if(! $match->isLive() && ! $match->isArchived() && $match->status !== Matchs::STATUS_NOT_STARTED)
                    <form method="POST" action="{{ route('admin.matchs.reset', $match) }}"
                          onsubmit="return confirm('{{ __('Reset this match to not started?') }}')">
                        @csrf
                        <button type="submit"
                                class="rounded-lg bg-red-900 hover:bg-red-800 px-4 py-2 text-sm font-medium text-red-300">
                            {{ __('Reset') }}
                        </button>
                    </form>
                @endif

            </div>
        </div>

        {{-- Score Edit Form --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">{{ __('Edit Score') }}</h2>
            <form method="POST" action="{{ route('admin.matchs.edit-score', $match) }}" class="flex flex-wrap items-end gap-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-300" for="score_a">
                        {{ $match->teamA?->name ?? __('Team A') }}
                    </label>
                    <input type="number" id="score_a" name="score_a" min="0"
                           value="{{ old('score_a', $match->score_a) }}"
                           class="mt-1 block w-24 rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                    @error('score_a')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="pb-2.5 text-gray-400 font-bold text-lg">&ndash;</div>
                <div>
                    <label class="block text-sm font-medium text-gray-300" for="score_b">
                        {{ $match->teamB?->name ?? __('Team B') }}
                    </label>
                    <input type="number" id="score_b" name="score_b" min="0"
                           value="{{ old('score_b', $match->score_b) }}"
                           class="mt-1 block w-24 rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                    @error('score_b')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('Update Score') }}
                </button>
            </form>
        </div>

        {{-- Maps Table --}}
        @if($match->maps && $match->maps->isNotEmpty())
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">{{ __('Maps') }}</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Map') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ $match->teamA?->name ?? __('Team A') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ $match->teamB?->name ?? __('Team B') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                            @foreach($match->maps as $map)
                                <tr class="hover:bg-gray-800">
                                    <td class="px-4 py-3 font-medium text-white">{{ $map->map_name }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-300">{{ $map->score_1 }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-300">{{ $map->score_2 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</x-layouts.admin>
