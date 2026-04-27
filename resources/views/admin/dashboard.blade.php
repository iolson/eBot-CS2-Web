<x-layouts.admin :title="__('Dashboard')">
    <div class="p-6">
        <h1 class="text-2xl font-bold text-white mb-4">{{ __('Dashboard') }}</h1>
        <p class="text-gray-400">{{ __('Welcome back, :name', ['name' => auth()->user()->getDisplayName()]) }}</p>
    </div>
</x-layouts.admin>
