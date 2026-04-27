<x-layouts.admin :title="__('Archived Matches')">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Archived Matches') }}</h1>
                <p class="mt-1 text-sm text-gray-400">{{ __('All completed and archived matches.') }}</p>
            </div>
            <a href="{{ route('admin.matchs.index') }}"
               class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                {{ __('Active Matches') }}
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Match') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Final Score') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Season') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($matches as $match)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-400">{{ $match->id }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.matchs.show', $match) }}"
                                   class="font-medium text-white hover:text-yellow-400">
                                    {{ $match->teamA?->name ?? '?' }} vs {{ $match->teamB?->name ?? '?' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-300">
                                {{ $match->score_a }} &ndash; {{ $match->score_b }}
                            </td>
                            <td class="px-4 py-3 text-gray-400">
                                {{ $match->season?->name ?? '–' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                {{ __('No archived matches found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $matches->links() }}
    </div>
</x-layouts.admin>
