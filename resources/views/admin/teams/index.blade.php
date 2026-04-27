<x-layouts.admin :title="__('Teams')">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-white">{{ __('Teams') }}</h1>
            <a href="{{ route('admin.teams.create') }}"
               class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                + {{ __('New Team') }}
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Shorthandle') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Flag') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($teams as $team)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-400">{{ $team->id }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ $team->name }}</td>
                            <td class="px-4 py-3 font-mono text-gray-300">{{ $team->shorthandle ?? '–' }}</td>
                            <td class="px-4 py-3 font-mono text-gray-300 uppercase">{{ $team->flag ?? '–' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    <a href="{{ route('admin.teams.show', $team) }}"
                                       class="rounded bg-gray-700 hover:bg-gray-600 px-2 py-1 text-xs text-gray-300">
                                        {{ __('View') }}
                                    </a>
                                    <a href="{{ route('admin.teams.edit', $team) }}"
                                       class="rounded bg-yellow-700 hover:bg-yellow-600 px-2 py-1 text-xs text-white">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.teams.destroy', $team) }}"
                                          onsubmit="return confirm('{{ __('Delete this team?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded bg-red-900 hover:bg-red-800 px-2 py-1 text-xs text-red-300">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                {{ __('No teams found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $teams->links() }}
    </div>
</x-layouts.admin>
