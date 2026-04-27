<x-layouts.admin :title="__('Team Profile')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ $team->name }}</h1>
                @if($team->shorthandle)
                    <p class="mt-1 text-sm font-mono text-gray-400">{{ $team->shorthandle }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.teams.edit', $team) }}"
                   class="rounded-lg bg-yellow-700 hover:bg-yellow-600 px-4 py-2 text-sm font-medium text-white">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('admin.teams.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        {{-- Team Info --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">{{ __('Team Info') }}</h2>
            <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Name') }}</dt>
                    <dd class="mt-1 text-sm text-white">{{ $team->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Shorthandle') }}</dt>
                    <dd class="mt-1 text-sm font-mono text-white">{{ $team->shorthandle ?? '–' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Country Flag') }}</dt>
                    <dd class="mt-1 text-sm font-mono text-white uppercase">{{ $team->flag ?? '–' }}</dd>
                </div>
                @if($team->link)
                    <div class="sm:col-span-3">
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Website') }}</dt>
                        <dd class="mt-1 text-sm">
                            <a href="{{ $team->link }}" target="_blank" rel="noopener noreferrer"
                               class="text-yellow-400 hover:text-yellow-300 break-all">
                                {{ $team->link }}
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Seasons --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">{{ __('Seasons') }}</h2>
            @if($team->seasons->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($team->seasons as $season)
                        <span class="inline-flex items-center rounded-md bg-gray-700 border border-gray-600 px-3 py-1 text-sm text-gray-300">
                            {{ $season->name }}
                            @if($season->active)
                                <span class="ml-2 inline-block h-1.5 w-1.5 rounded-full bg-green-400"></span>
                            @endif
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('This team is not assigned to any seasons.') }}</p>
            @endif
        </div>
    </div>
</x-layouts.admin>
