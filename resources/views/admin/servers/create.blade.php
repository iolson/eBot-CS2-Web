<x-layouts.admin :title="__('Add Server')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Add Server') }}</h1>
                <p class="mt-1 text-sm text-gray-400">{{ __('Register one or more CS2 game servers.') }}</p>
            </div>
            <a href="{{ route('admin.servers.index') }}"
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

        <form method="POST" action="{{ route('admin.servers.store') }}" class="space-y-6">
            @csrf

            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Server Details') }}</h2>

                {{-- IP:Port --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300" for="ip_port">
                        {{ __('IP:Port') }} <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="ip_port" name="ip_port"
                           value="{{ old('ip_port') }}"
                           placeholder="192.168.1.1:27015 or 192.168.1.1-10:27015-27020"
                           class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none font-mono">
                    <p class="mt-1.5 text-xs text-gray-500">
                        {{ __('Single server: ') }}<code class="text-gray-400">192.168.1.1:27015</code>
                        &nbsp;&mdash;&nbsp;
                        {{ __('Batch creation: ') }}<code class="text-gray-400">192.168.1.1-10:27015-27020</code>
                        {{ __('(creates servers .1 through .10 on ports 27015&ndash;27020)') }}
                    </p>
                    @error('ip_port')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- RCON --}}
                <div>
                    <label class="block text-sm font-medium text-gray-300" for="rcon">
                        {{ __('RCON Password') }} <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="rcon" name="rcon"
                           value="{{ old('rcon') }}"
                           autocomplete="off"
                           class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none font-mono">
                    @error('rcon')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.servers.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('Add Server') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
