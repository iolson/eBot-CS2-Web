<x-layouts.admin :title="ucfirst(basename(request()->route()->getName()))">
    <div class="p-6">
        <p class="text-gray-400">{{ request()->route()->getName() }}</p>
    </div>
</x-layouts.admin>
