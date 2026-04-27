<x-layouts.admin :title="__('Edit Match')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Edit Match') }}</h1>
                <p class="mt-1 text-sm text-gray-400">
                    {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
                </p>
            </div>
            <a href="{{ route('admin.matchs.show', $match) }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Cancel') }}
            </a>
        </div>

        {{-- Validation Error Summary --}}
        @if($errors->any())
            <div class="rounded-lg bg-red-900/50 border border-red-700 p-4">
                <p class="text-sm font-medium text-red-300 mb-2">{{ __('Please fix the following errors:') }}</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-sm text-red-400">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.matchs.update', $match) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Teams --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Teams') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Team A --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="team_a">
                            {{ __('Team A') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="team_a" name="team_a"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            <option value="">{{ __('Select Team A') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}"
                                    {{ old('team_a', $match->team_a) == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('team_a')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Team B --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="team_b">
                            {{ __('Team B') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="team_b" name="team_b"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            <option value="">{{ __('Select Team B') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}"
                                    {{ old('team_b', $match->team_b) == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('team_b')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Match Settings --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Match Settings') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Season --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="season_id">
                            {{ __('Season') }} <span class="text-gray-500 font-normal">({{ __('optional') }})</span>
                        </label>
                        <select id="season_id" name="season_id"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            <option value="">{{ __('No Season') }}</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}"
                                    {{ old('season_id', $match->season_id) == $season->id ? 'selected' : '' }}>
                                    {{ $season->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('season_id')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Server --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="server_id">
                            {{ __('Server') }} <span class="text-gray-500 font-normal">({{ __('optional') }})</span>
                        </label>
                        <select id="server_id" name="server_id"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            <option value="">{{ __('No Server') }}</option>
                            @foreach($servers as $server)
                                <option value="{{ $server->id }}"
                                    {{ old('server_id', $match->server_id) == $server->id ? 'selected' : '' }}>
                                    {{ $server->ip }}
                                </option>
                            @endforeach
                        </select>
                        @error('server_id')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Max Round --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="max_round">
                            {{ __('Max Rounds') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="max_round" name="max_round"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            @foreach([15, 25, 30] as $rounds)
                                <option value="{{ $rounds }}"
                                    {{ old('max_round', $match->max_round) == $rounds ? 'selected' : '' }}>
                                    {{ $rounds }}
                                </option>
                            @endforeach
                        </select>
                        @error('max_round')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Map Selection Mode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="map_selection_mode">
                            {{ __('Map Selection Mode') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="map_selection_mode" name="map_selection_mode"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            <option value="0" {{ old('map_selection_mode', $match->map_selection_mode) == '0' ? 'selected' : '' }}>{{ __('Random') }}</option>
                            <option value="1" {{ old('map_selection_mode', $match->map_selection_mode) == '1' ? 'selected' : '' }}>{{ __('Vote') }}</option>
                            <option value="2" {{ old('map_selection_mode', $match->map_selection_mode) == '2' ? 'selected' : '' }}>{{ __('Pick') }}</option>
                        </select>
                        @error('map_selection_mode')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.matchs.show', $match) }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
