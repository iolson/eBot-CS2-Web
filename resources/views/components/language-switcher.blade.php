@php
    $languages = [
        'en'    => 'EN',
        'ru'    => 'RU',
        'zh_CN' => 'CN',
    ];
    $currentLocale = app()->getLocale();
@endphp

<div class="flex items-center gap-1">
    @foreach($languages as $code => $label)
        <form method="POST" action="{{ route('locale.switch') }}" class="inline">
            @csrf
            <input type="hidden" name="locale" value="{{ $code }}">
            <button type="submit"
                    class="px-1.5 py-0.5 text-xs rounded {{ $currentLocale === $code ? 'bg-gray-600 text-white' : 'text-gray-500 hover:text-gray-300' }}">
                {{ $label }}
            </button>
        </form>
    @endforeach
</div>
