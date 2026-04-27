<x-layouts.admin :title="__('Create Config')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Create Config') }}</h1>
                <p class="mt-1 text-sm text-gray-400">{{ __('Add a new server configuration preset.') }}</p>
            </div>
            <a href="{{ route('admin.configs.index') }}"
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

        <form method="POST" action="{{ route('admin.configs.store') }}" class="space-y-6">
            @csrf

            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Config Details') }}</h2>

                {{-- Name --}}
                <div>
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

                {{-- Content --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300" for="content">
                        {{ __('Content') }} <span class="text-red-400">*</span>
                    </label>
                    <textarea id="content" name="content"
                              rows="10"
                              class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none font-mono text-sm resize-y"
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.configs.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('Create Config') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
