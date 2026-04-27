<x-layouts.admin :title="__('Users')">
    <div class="space-y-4">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-white">{{ __('Users') }}</h1>
            <a href="{{ route('admin.users.create') }}"
               class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                + {{ __('New User') }}
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Username') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Super Admin') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Active') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-800">
                            <td class="px-4 py-3 text-gray-400">{{ $user->id }}</td>
                            <td class="px-4 py-3 font-medium text-white">
                                {{ $user->username }}
                                @if($user->id === auth()->id())
                                    <span class="ml-1 text-xs text-yellow-500">({{ __('you') }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-300">{{ $user->email_address }}</td>
                            <td class="px-4 py-3 text-gray-300">
                                {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: '–' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_super_admin)
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-yellow-900 text-yellow-300">
                                        {{ __('Super Admin') }}
                                    </span>
                                @else
                                    <span class="text-gray-600 text-xs">–</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_active)
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
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="rounded bg-yellow-700 hover:bg-yellow-600 px-2 py-1 text-xs text-white">
                                        {{ __('Edit') }}
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('{{ __('Delete this user?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded bg-red-900 hover:bg-red-800 px-2 py-1 text-xs text-red-300">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="rounded bg-gray-800 px-2 py-1 text-xs text-gray-600 cursor-not-allowed"
                                              title="{{ __('Cannot delete yourself') }}">
                                            {{ __('Delete') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                {{ __('No users found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
</x-layouts.admin>
