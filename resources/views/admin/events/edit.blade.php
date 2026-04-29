<x-layouts.admin :title="__('Edit Event')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Edit Event') }}</h1>
                <p class="mt-1 text-sm text-gray-400">{{ $event->name }}</p>
            </div>
            <a href="{{ route('admin.events.index') }}"
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

        <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Basic Info') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-300" for="name">
                            {{ __('Name') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $event->name) }}"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none"
                               required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Event --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="event">
                            {{ __('Event') }} <span class="text-gray-500 font-normal">({{ __('optional') }})</span>
                        </label>
                        <input type="text" id="event" name="event"
                               value="{{ old('event', $event->event) }}"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('event')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Link --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="link">
                            {{ __('Link') }} <span class="text-gray-500 font-normal">({{ __('URL, optional') }})</span>
                        </label>
                        <input type="url" id="link" name="link"
                               value="{{ old('link', $event->link) }}"
                               placeholder="https://"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('link')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Logo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="logo">
                            {{ __('Logo') }} <span class="text-gray-500 font-normal">({{ __('URL, optional') }})</span>
                        </label>
                        <input type="url" id="logo" name="logo"
                               value="{{ old('logo', $event->logo) }}"
                               placeholder="https://"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('logo')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Dates & Status --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Dates & Status') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Start Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="start">
                            {{ __('Start Date') }} <span class="text-gray-500 font-normal">({{ __('optional') }})</span>
                        </label>
                        <input type="date" id="start" name="start"
                               value="{{ old('start', $event->start ? \Carbon\Carbon::parse($event->start)->format('Y-m-d') : '') }}"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('start')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="end">
                            {{ __('End Date') }} <span class="text-gray-500 font-normal">({{ __('optional') }})</span>
                        </label>
                        <input type="date" id="end" name="end"
                               value="{{ old('end', $event->end ? \Carbon\Carbon::parse($event->end)->format('Y-m-d') : '') }}"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('end')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="active" value="1"
                                   {{ old('active', $event->active) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-gray-900">
                            <span class="text-sm font-medium text-gray-300">{{ __('Active') }}</span>
                        </label>
                        @error('active')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.events.index') }}"
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
