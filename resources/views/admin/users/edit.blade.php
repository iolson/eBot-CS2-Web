<x-layouts.admin :title="__('Edit User')">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">{{ __('Edit User') }}</h1>
                <p class="mt-1 text-sm text-gray-400">{{ $user->username }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
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

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Account Details --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Account Details') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="username">
                            {{ __('Username') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="username" name="username"
                               value="{{ old('username', $user->username) }}"
                               autocomplete="username"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none"
                               required>
                        @error('username')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="email_address">
                            {{ __('Email Address') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="email" id="email_address" name="email_address"
                               value="{{ old('email_address', $user->email_address) }}"
                               autocomplete="email"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none"
                               required>
                        @error('email_address')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- First Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="first_name">
                            {{ __('First Name') }}
                        </label>
                        <input type="text" id="first_name" name="first_name"
                               value="{{ old('first_name', $user->first_name) }}"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('first_name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="last_name">
                            {{ __('Last Name') }}
                        </label>
                        <input type="text" id="last_name" name="last_name"
                               value="{{ old('last_name', $user->last_name) }}"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('last_name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Password --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Change Password') }}</h2>
                <p class="text-xs text-gray-500">{{ __('Leave blank to keep the current password.') }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="password">
                            {{ __('New Password') }}
                        </label>
                        <input type="password" id="password" name="password"
                               autocomplete="new-password"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                        @error('password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300" for="password_confirmation">
                            {{ __('Confirm New Password') }}
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               autocomplete="new-password"
                               class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- Roles & Status --}}
            <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('Roles & Status') }}</h2>

                <div class="space-y-3">
                    {{-- Super Admin --}}
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_super_admin" value="1"
                                   {{ old('is_super_admin', $user->is_super_admin) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-gray-900">
                            <div>
                                <span class="text-sm font-medium text-gray-300">{{ __('Super Admin') }}</span>
                                <p class="text-xs text-gray-500">{{ __('Grants full access to all admin features.') }}</p>
                            </div>
                        </label>
                        @error('is_super_admin')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div>
                        <label class="flex items-center gap-3 {{ $user->id === auth()->id() ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                   class="h-4 w-4 rounded border-gray-600 bg-gray-700 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-gray-900">
                            <div>
                                <span class="text-sm font-medium text-gray-300">{{ __('Active') }}</span>
                                <p class="text-xs text-gray-500">
                                    @if($user->id === auth()->id())
                                        {{ __('You cannot deactivate your own account.') }}
                                    @else
                                        {{ __('Inactive users cannot log in.') }}
                                    @endif
                                </p>
                            </div>
                        </label>
                        {{-- Hidden fallback for disabled checkbox of current user --}}
                        @if($user->id === auth()->id())
                            <input type="hidden" name="is_active" value="1">
                        @endif
                        @error('is_active')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-lg bg-gray-700 hover:bg-gray-600 px-4 py-2 text-sm text-gray-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="rounded-lg bg-yellow-600 hover:bg-yellow-500 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
