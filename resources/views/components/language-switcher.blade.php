@php
    $languages = [
        'en' => 'English',
        'ru' => 'Russian',
        'cn' => 'Chinese',
    ];
    $currentLocale = app()->getLocale();
@endphp

<div class="flex items-center gap-1">
    @foreach($languages as $code => $name)
        <form method="POST" action="{{ url('/switch/lang/' . $code) }}" class="inline">
            @csrf
            <input type="hidden" name="referer" value="{{ request()->path() }}">
            <button type="submit"
                    class="px-1.5 py-0.5 text-xs rounded {{ $currentLocale === $code ? 'bg-gray-600 text-white' : 'text-gray-500 hover:text-gray-300' }}"
                    title="{{ $name }}">
                {{ strtoupper($code) }}
            </button>
        </form>
    @endforeach
</div>
