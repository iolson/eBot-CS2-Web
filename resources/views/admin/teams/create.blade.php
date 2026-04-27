<x-layouts.admin :title="__('Create Team')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Create Team') }}</h1>
                <p class="mt-1 text-sm text-gray-400">{{ __('Add a new team to the system.') }}</p>
            </div>
            <a href="{{ route('admin.teams.index') }}"
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

        <form method="POST" action="{{ route('admin.teams.store') }}" class="space-y-6">
            @csrf

            {{-- Team Details --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Team Details') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-300" for="name">
                            {{ __('Name') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name') }}"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none"
                               required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Shorthandle --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="shorthandle">
                            {{ __('Shorthandle') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="shorthandle" name="shorthandle"
                               value="{{ old('shorthandle') }}"
                               maxlength="10"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none font-mono"
                               required>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Max 10 characters') }}</p>
                        @error('shorthandle')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Flag --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="flag">
                            {{ __('Country Flag') }} <span class="text-gray-500 font-normal">({{ __('2-char code, optional') }})</span>
                        </label>
                        <input type="text" id="flag" name="flag"
                               value="{{ old('flag') }}"
                               maxlength="2"
                               placeholder="US"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none font-mono uppercase">
                        @error('flag')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Link --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-300" for="link">
                            {{ __('Website') }} <span class="text-gray-500 font-normal">({{ __('URL, optional') }})</span>
                        </label>
                        <input type="url" id="link" name="link"
                               value="{{ old('link') }}"
                               placeholder="https://"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('link')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Seasons --}}
            @if($seasons->isNotEmpty())
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Seasons') }}</h2>
                    <p class="text-xs text-gray-500">{{ __('Select the seasons this team participates in.') }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($seasons as $season)
                            <label class="flex items-center gap-3 cursor-pointer rounded-lg bg-gray-700/50 border border-gray-600 px-3 py-2 hover:border-yellow-600/50">
                                <input type="checkbox"
                                       name="seasons[]"
                                       value="{{ $season->id }}"
                                       {{ in_array($season->id, old('seasons', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-gray-900">
                                <span class="text-sm text-gray-300">{{ $season->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('seasons')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.teams.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('Create Team') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
