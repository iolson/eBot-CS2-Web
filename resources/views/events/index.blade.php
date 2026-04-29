<x-layouts.app :title="__('Events')">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Events') }}</h1>

        @if($activeEvents->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('Active Events') }}</h2>
                <div class="space-y-2">
                    @foreach($activeEvents as $event)
                        <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-white">{{ $event->name }}</span>
                                @if($event->event)
                                    <span class="ml-2 text-xs text-gray-400">{{ $event->event }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('events.select', $event) }}"
                                   class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-3 py-1.5 text-xs font-medium text-white">
                                    {{ __('View Matches') }}
                                </a>
                                <a href="{{ route('events.select', [$event, 'site' => 'archived']) }}"
                                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">
                                    {{ __('Archives') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($pastEvents->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('Past Events') }}</h2>
                <div class="space-y-2">
                    @foreach($pastEvents as $event)
                        <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-gray-300">{{ $event->name }}</span>
                                @if($event->event)
                                    <span class="ml-2 text-xs text-gray-500">{{ $event->event }}</span>
                                @endif
                            </div>
                            <a href="{{ route('events.select', [$event, 'site' => 'archived']) }}"
                               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-3 py-1.5 text-xs text-gray-300">
                                {{ __('View Archives') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($activeEvents->isEmpty() && $pastEvents->isEmpty())
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-8 text-center text-gray-500">
                {{ __('No events found.') }}
            </div>
        @endif
    </div>
</x-layouts.app>
