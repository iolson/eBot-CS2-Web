<x-layouts.admin :title="__('Servers')">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-white">{{ __('Servers') }}</h1>
            <a href="{{ route('admin.servers.create') }}"
               class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                + {{ __('New Server') }}
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('IP:Port') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('RCON') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Matches') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($servers as $server)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-400">{{ $server->id }}</td>
                            <td class="px-4 py-3 font-mono font-medium text-white">{{ $server->ip }}</td>
                            <td class="px-4 py-3 font-mono text-gray-500">****</td>
                            <td class="px-4 py-3 text-gray-300">{{ $server->matches_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($server->matches_count == 0)
                                        <form method="POST" action="{{ route('admin.servers.destroy', $server) }}"
                                              onsubmit="return confirm('{{ __('Delete this server?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded bg-red-900 hover:bg-red-800 px-2 py-1 text-xs text-red-300">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-600">{{ __('In use') }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                {{ __('No servers configured.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $servers->links() }}
    </div>
</x-layouts.admin>
