<x-layouts.admin :title="__('Dashboard')">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Dashboard') }}</h1>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Live Matches') }}</p>
                <p class="mt-2 text-3xl font-bold text-blue-400">{{ $liveMatches->count() }}</p>
            </div>
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Pending Matches') }}</p>
                <p class="mt-2 text-3xl font-bold text-yellow-400">{{ $pendingCount }}</p>
            </div>
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Servers') }}</p>
                <p class="mt-2 text-3xl font-bold text-green-400">{{ $serverCount }}</p>
            </div>
        </div>

        {{-- Live Matches --}}
        @if($liveMatches->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-white mb-3">{{ __('Live Now') }}</h2>
                <div class="space-y-2">
                    @foreach($liveMatches as $match)
                        <div class="bg-gray-800 border border-blue-500/30 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-300">
                                    <span class="inline-block h-2 w-2 rounded-full bg-blue-400 animate-pulse"></span>
                                    LIVE
                                </span>
                                <span class="text-white font-medium">
                                    {{ $match->teamA?->name ?? '?' }} {{ $match->score_a }} – {{ $match->score_b }} {{ $match->teamB?->name ?? '?' }}
                                </span>
                                @if($match->server)
                                    <span class="text-xs text-gray-400">{{ $match->server->ip }}</span>
                                @endif
                            </div>
                            <a href="{{ route('admin.matchs.show', $match) }}"
                               class="text-sm text-yellow-400 hover:text-yellow-300">{{ __('Manage') }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <p class="text-sm text-gray-400">{{ __('Welcome back, :name', ['name' => auth()->user()->getDisplayName()]) }}</p>
    </div>
</x-layouts.admin>
