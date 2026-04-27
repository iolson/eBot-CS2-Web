<x-layouts.app :title="__('Seasons')">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Seasons') }}</h1>

        @if($activeSeasons->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('Active Seasons') }}</h2>
                <div class="space-y-2">
                    @foreach($activeSeasons as $season)
                        <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-white">{{ $season->name }}</span>
                                @if($season->event)
                                    <span class="ml-2 text-xs text-gray-400">{{ $season->event }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('seasons.select', $season) }}"
                                   class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white">
                                    {{ __('View Matches') }}
                                </a>
                                <a href="{{ route('seasons.select', [$season, 'site' => 'archived']) }}"
                                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">
                                    {{ __('Archives') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($pastSeasons->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('Past Seasons') }}</h2>
                <div class="space-y-2">
                    @foreach($pastSeasons as $season)
                        <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-gray-300">{{ $season->name }}</span>
                                @if($season->event)
                                    <span class="ml-2 text-xs text-gray-500">{{ $season->event }}</span>
                                @endif
                            </div>
                            <a href="{{ route('seasons.select', [$season, 'site' => 'archived']) }}"
                               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">
                                {{ __('View Archives') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($activeSeasons->isEmpty() && $pastSeasons->isEmpty())
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-8 text-center text-gray-500">
                {{ __('No seasons found.') }}
            </div>
        @endif
    </div>
</x-layouts.app>
