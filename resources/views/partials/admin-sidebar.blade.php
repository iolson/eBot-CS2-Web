<nav class="space-y-1">
    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Match Management') }}</p>

    <a href="{{ url('/admin/matchs') }}"
       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->is('admin/matchs*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        {{ __('Matches') }}
    </a>

    <a href="{{ url('/admin/servers') }}"
       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->is('admin/servers*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        {{ __('Servers') }}
    </a>

    <a href="{{ url('/admin/teams') }}"
       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->is('admin/teams*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        {{ __('Teams') }}
    </a>

    <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Organization') }}</p>

    <a href="{{ url('/admin/seasons') }}"
       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->is('admin/seasons*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        {{ __('Seasons') }}
    </a>

    <a href="{{ url('/admin/advertising') }}"
       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->is('admin/advertising*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        {{ __('Advertising') }}
    </a>

    <a href="{{ url('/admin/configs') }}"
       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->is('admin/configs*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        {{ __('Configs') }}
    </a>

    <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('System') }}</p>

    <a href="{{ url('/admin/users') }}"
       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->is('admin/users*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
        {{ __('Users') }}
    </a>
</nav>
