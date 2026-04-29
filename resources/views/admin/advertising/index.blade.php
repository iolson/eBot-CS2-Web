<x-layouts.admin :title="__('Advertising')">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-white">{{ __('Advertising') }}</h1>
            <a href="{{ route('admin.advertising.create') }}"
               class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                + {{ __('New Ad') }}
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Event') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Message') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Active') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($ads as $ad)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-400">{{ $ad->id }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $ad->event?->name ?? '–' }}</td>
                            <td class="px-4 py-3 text-gray-300 max-w-xs truncate">
                                {{ \Illuminate\Support\Str::limit($ad->message, 50) }}
                            </td>
                            <td class="px-4 py-3">
                                @if($ad->active)
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-green-900 text-green-300">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-gray-700 text-gray-400">
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1 flex-wrap">
                                    <a href="{{ route('admin.advertising.edit', $ad) }}"
                                       class="rounded bg-yellow-700 hover:bg-yellow-600 px-2 py-1 text-xs text-white">
                                        {{ __('Edit') }}
                                    </a>

                                    {{-- Deactivate / Activate Toggle --}}
                                    <form method="POST" action="{{ route('admin.advertising.deactivate', $ad) }}">
                                        @csrf
                                        <button type="submit"
                                                class="rounded bg-gray-600 hover:bg-gray-500 px-2 py-1 text-xs text-gray-300">
                                            {{ $ad->active ? __('Deactivate') : __('Activate') }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.advertising.destroy', $ad) }}"
                                          onsubmit="return confirm('{{ __('Delete this ad?') }}')">
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
                                {{ __('No ads found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $ads->links() }}
    </div>
</x-layouts.admin>
