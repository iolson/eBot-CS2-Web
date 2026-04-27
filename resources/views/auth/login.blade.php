<x-layouts.app :title="__('Sign In')">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-white">eBot CS2</h1>
                <p class="mt-2 text-sm text-gray-400">{{ __('Sign in to your account') }}</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6 bg-gray-800/50 border border-gray-700 rounded-xl p-8">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300">
                            {{ __('Username') }}
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:ring-yellow-500 focus:outline-none @error('username') border-red-500 @enderror"
                        >
                        @error('username')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">
                            {{ __('Password') }}
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="mt-1 block w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-4 py-2.5 focus:border-yellow-500 focus:ring-yellow-500 focus:outline-none"
                        >
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="rounded bg-gray-700 border-gray-600 text-yellow-500">
                        <label for="remember" class="ml-2 text-sm text-gray-400">{{ __('Remember me') }}</label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 rounded-lg bg-yellow-600 hover:bg-yellow-500 text-white font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                    {{ __('Sign In') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
