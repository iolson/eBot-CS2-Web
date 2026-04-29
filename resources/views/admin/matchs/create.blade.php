<x-layouts.admin :title="__('Create Match')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Create Match') }}</h1>
                <p class="mt-1 text-sm text-gray-400">{{ __('Set up a new match between two teams.') }}</p>
            </div>
            <a href="{{ route('admin.matchs.index') }}"
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

        <form method="POST" action="{{ route('admin.matchs.store') }}" class="space-y-6">
            @csrf

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
                                <option value="{{ $team->id }}" {{ old('team_a') == $team->id ? 'selected' : '' }}>
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
                                <option value="{{ $team->id }}" {{ old('team_b') == $team->id ? 'selected' : '' }}>
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
                    {{-- Event --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="event_id">
                            {{ __('Event') }} <span class="text-gray-500 font-normal">({{ __('optional') }})</span>
                        </label>
                        <select id="event_id" name="event_id"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            <option value="">{{ __('No Event') }}</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
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
                                <option value="{{ $server->id }}" {{ old('server_id') == $server->id ? 'selected' : '' }}>
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
                            {{ __('Max Rounds (per side)') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="max_round" name="max_round"
                                class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                            <option value="12" selected>MR12</option>
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
                            <option value="0" {{ old('map_selection_mode', '0') == '0' ? 'selected' : '' }}>{{ __('Random') }}</option>
                            <option value="1" {{ old('map_selection_mode') == '1' ? 'selected' : '' }}>{{ __('Vote') }}</option>
                            <option value="2" {{ old('map_selection_mode') == '2' ? 'selected' : '' }}>{{ __('Pick') }}</option>
                        </select>
                        @error('map_selection_mode')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.matchs.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('Create Match') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
