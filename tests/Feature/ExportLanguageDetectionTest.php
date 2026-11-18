<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportLanguageDetectionTest extends TestCase
{
    use RefreshDatabase;

    private function visit(string $acceptLanguage)
    {
        return $this->withHeader('Accept-Language', $acceptLanguage)->get(route('export.home'));
    }

    public function test_default_is_english_without_browser_language(): void
    {
        $this->get(route('export.home'))->assertOk()->assertDontSee('切换');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_mandarin_browser_gets_suggestion_but_page_stays_english(): void
    {
        $this->visit('zh-CN,zh;q=0.9')->assertOk()->assertSee('切换');

        $this->assertSame('en', app()->getLocale());
    }

    public function test_regional_variant_matches_available_locale(): void
    {
        $this->visit('zh-TW,zh;q=0.8')->assertOk()->assertSee('切换');
    }

    public function test_arabic_browser_gets_arabic_suggestion(): void
    {
        $this->visit('ar-SA,ar;q=0.9')->assertOk()->assertSee('تبديل');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_unavailable_browser_language_shows_no_suggestion(): void
    {
        $this->visit('th-TH,th;q=0.9')->assertOk()->assertDontSee('切换')->assertDontSee('تبديل');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_english_browser_shows_no_suggestion(): void
    {
        $this->visit('en-US,en;q=0.9')->assertOk()->assertDontSee('切换');
    }

    public function test_manual_choice_beats_browser_language(): void
    {
        $this->withSession(['export_locale' => 'zh'])->visit('ar-SA,ar;q=0.9')->assertOk();

        $this->assertSame('zh', app()->getLocale());
    }

    public function test_keep_english_remembers_choice_and_hides_banner(): void
    {
        $this->withHeader('Accept-Language', 'zh-CN')->get(route('export.locale', 'en'))->assertRedirect();

        $this->withHeader('Accept-Language', 'zh-CN')->get(route('export.home'))
            ->assertOk()
            ->assertDontSee('切换');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_switch_link_changes_language(): void
    {
        $this->withHeader('Accept-Language', 'zh-CN')->get(route('export.locale', 'zh'))->assertRedirect();
        $this->get(route('export.home'))->assertOk();

        $this->assertSame('zh', app()->getLocale());
    }

    public function test_auto_mode_switches_directly_and_remembers(): void
    {
        config(['export.lang_detect' => 'auto']);

        $this->visit('ar-SA,ar;q=0.9')->assertOk()->assertDontSee('تبديل');

        $this->assertSame('ar', app()->getLocale());
        $this->assertSame('ar', session('export_locale'));
    }

    public function test_off_mode_ignores_browser_language(): void
    {
        config(['export.lang_detect' => 'off']);

        $this->visit('zh-CN')->assertOk()->assertDontSee('切换');
        $this->assertSame('en', app()->getLocale());
    }
}