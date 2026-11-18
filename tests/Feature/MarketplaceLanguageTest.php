<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketplaceLanguageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Tes lain memaksa sesi Indonesia; di sini kita menguji perilaku bawaan.
        $this->flushSession();
        Cache::flush();

        config([
            'export.machine.enabled' => true,
            'export.machine.api_key' => 'test-key',
            'export.machine.monthly_char_limit' => 1000000,
            'export.machine_locales' => ['ja'],
            'export.locales' => ['en', 'id', 'zh', 'ar', 'ja'],
            'export.lang_detect' => 'suggest',
            'export.locale_attributes.ja' => ['lang' => 'ja', 'dir' => 'ltr', 'label' => 'æ—¥æœ¬èªž'],
        ]);
    }

    private function fakeGoogle(): void
    {
        Http::fake([
            'translation.googleapis.com/*' => fn (Request $request) => Http::response(['data' => ['translations' => array_map(
                fn ($text) => ['translatedText' => 'JA:'.$text],
                $request['q']
            )]]),
        ]);
    }

    public function test_marketplace_is_english_by_default(): void
    {
        Http::fake();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee(__('marketplace.hero.title', [], 'en'));

        $this->assertSame('en', app()->getLocale());
        Http::assertNothingSent();
    }

    public function test_visitor_can_choose_indonesian(): void
    {
        $this->get(route('site.locale', 'id'))->assertRedirect();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="id"', false)
            ->assertSee(__('marketplace.hero.title', [], 'id'));
    }

    public function test_unknown_locale_is_not_found(): void
    {
        $this->get(route('site.locale', 'xx'))->assertNotFound();
    }

    public function test_indonesian_browser_gets_a_suggestion_but_page_stays_english(): void
    {
        $this->withHeader('Accept-Language', 'id-ID,id;q=0.9')
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Bahasa browser Anda adalah Bahasa Indonesia', false)
            ->assertSee('<html lang="en"', false);

        $this->assertSame('en', app()->getLocale());
    }

    public function test_manual_choice_beats_browser_language_and_hides_banner(): void
    {
        $this->get(route('site.locale', 'en'));

        $this->withHeader('Accept-Language', 'id-ID,id;q=0.9')
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('Bahasa browser Anda', false);
    }

    public function test_auto_mode_switches_directly(): void
    {
        config(['export.lang_detect' => 'auto']);

        $this->withHeader('Accept-Language', 'id')->get(route('home'))->assertOk();

        $this->assertSame('id', app()->getLocale());
        $this->assertSame('id', session('locale'));
    }

    public function test_off_mode_ignores_browser_language(): void
    {
        config(['export.lang_detect' => 'off']);

        $this->withHeader('Accept-Language', 'id')->get(route('home'))
            ->assertOk()
            ->assertDontSee('Bahasa browser Anda', false);

        $this->assertSame('en', app()->getLocale());
    }

    public function test_machine_language_translates_marketplace_texts(): void
    {
        $this->fakeGoogle();

        $this->get(route('site.locale', 'ja'))->assertRedirect();
        $this->get(route('home'))->assertOk()->assertSee('JA:', false);

        $this->assertSame('ja', app()->getLocale());
        $this->assertDatabaseHas('machine_translations', ['locale' => 'ja']);
    }

    public function test_stored_language_without_marketplace_file_uses_google(): void
    {
        if (is_file(lang_path('zh/marketplace.php'))) {
            $this->markTestSkipped('lang/zh/marketplace.php sudah ada, memakai file tersimpan.');
        }

        $this->fakeGoogle();

        $this->get(route('site.locale', 'zh'));
        $this->get(route('home'))->assertOk()->assertSee('JA:', false);
    }

    public function test_without_api_key_a_machine_language_falls_back_to_english_texts(): void
    {
        Http::fake();
        config(['export.machine.api_key' => null]);

        $this->get(route('site.locale', 'ja'));
        $this->get(route('home'))->assertOk()->assertSee(__('marketplace.hero.title', [], 'en'));

        Http::assertNothingSent();
    }

    public function test_login_page_is_english_by_default_and_follows_the_choice(): void
    {
        $this->get(route('login'))->assertOk()->assertSee(__('auth-ui.login_title', [], 'en'));

        $this->get(route('site.locale', 'id'));
        $this->get(route('login'))->assertOk()->assertSee(__('auth-ui.login_title', [], 'id'));
    }

    public function test_language_switcher_lists_available_languages(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('site.locale', 'id'), false)
            ->assertSee(route('site.locale', 'ja'), false);
    }

    public function test_farmer_dashboard_stays_indonesian_for_now(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer)
            ->withSession(['locale' => 'en'])
            ->get(route('farmer.dashboard'));

        $this->assertSame('id', app()->getLocale());
    }

    public function test_export_website_keeps_its_own_language(): void
    {
        $this->withSession(['locale' => 'id'])
            ->get(route('export.home'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);
    }
}
