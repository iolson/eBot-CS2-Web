<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eBot CS2 — Install</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-900 text-gray-100 antialiased flex items-center justify-center py-12 px-4">

<div class="w-full max-w-xl">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-yellow-400">eBot CS2 Web</h1>
        <p class="mt-1 text-gray-400">Installation Wizard</p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="mb-6 rounded-md bg-red-900/50 border border-red-700 p-4">
            <p class="text-sm font-medium text-red-300 mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-400">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('install.store') }}" class="space-y-6">
        @csrf

        {{-- Database --}}
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-lg font-semibold text-white mb-4">Database</h2>
            <p class="text-xs text-gray-400 mb-4">Must be the same database used by the eBot Node.js server.</p>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-400 mb-1">Host</label>
                    <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Port</label>
                    <input type="number" name="db_port" value="{{ old('db_port', '3306') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-1">Database Name</label>
                <input type="text" name="db_name" value="{{ old('db_name', 'ebotv3') }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Username</label>
                    <input type="text" name="db_user" value="{{ old('db_user', 'root') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Password</label>
                    <input type="password" name="db_pass"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
            </div>
        </div>

        {{-- eBot Server --}}
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-lg font-semibold text-white mb-4">eBot Node.js Server</h2>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-1">WebSocket URL</label>
                <input type="url" name="ebot_url" value="{{ old('ebot_url', 'http://localhost:12360') }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                <p class="text-xs text-gray-500 mt-1">URL of your running eBot Node.js server.</p>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-400 mb-1">JWT Secret Key</label>
                <input type="text" name="ebot_secret" value="{{ old('ebot_secret', \Illuminate\Support\Str::random(48)) }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm font-mono text-white focus:outline-none focus:border-yellow-500">
                <p class="text-xs text-gray-500 mt-1">Must match <code class="text-yellow-400">websocket_secret_key</code> in the eBot Node.js config.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Display Mode</label>
                <select name="ebot_mode"
                        class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                    <option value="net" {{ old('ebot_mode', 'net') === 'net' ? 'selected' : '' }}>net — hides server IPs (public)</option>
                    <option value="lan" {{ old('ebot_mode') === 'lan' ? 'selected' : '' }}>lan — shows server IPs (internal)</option>
                </select>
            </div>
        </div>

        {{-- Admin Account --}}
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h2 class="text-lg font-semibold text-white mb-4">Admin Account</h2>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Username</label>
                    <input type="text" name="admin_username" value="{{ old('admin_username') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Email Address</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Password</label>
                    <input type="password" name="admin_password"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Confirm Password</label>
                    <input type="password" name="admin_password_confirmation"
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                </div>
            </div>
        </div>

        <button type="submit"
                class="w-full bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold py-3 rounded-lg transition-colors">
            Install eBot CS2 Web
        </button>
    </form>

    <p class="text-center text-xs text-gray-600 mt-6">
        You can also run <code class="text-gray-400">php artisan ebot:install</code> from the command line.
    </p>
</div>

</body>
</html>
