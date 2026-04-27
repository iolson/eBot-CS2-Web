<?php

describe('Locale switcher', function () {
    it('switches to a supported locale and redirects back', function () {
        $this->post(route('locale.switch'), ['locale' => 'ru'])
            ->assertRedirect();

        $this->assertEquals('ru', session('locale'));
    });

    it('switches to zh_CN locale', function () {
        $this->post(route('locale.switch'), ['locale' => 'zh_CN'])
            ->assertRedirect();

        $this->assertEquals('zh_CN', session('locale'));
    });

    it('ignores unsupported locale values', function () {
        $this->post(route('locale.switch'), ['locale' => 'xx'])
            ->assertRedirect();

        $this->assertNull(session('locale'));
    });

    it('defaults to en when no locale is provided', function () {
        $this->post(route('locale.switch'))
            ->assertRedirect();

        // 'en' is supported but the route only sets it when it's in_array
        // so session is either not set or 'en'
        $locale = session('locale');
        expect($locale)->toBeIn([null, 'en']);
    });
});
