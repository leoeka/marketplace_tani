<?php

namespace Tests\Feature;

use App\Support\MachineTranslator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MachineTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'export.machine.enabled' => true,
            'export.machine.api_key' => 'test-key',
            'export.machine.monthly_char_limit' => 1000000,
            'export.machine_locales' => ['ja'],
            'export.locales' => ['en', 'id', 'zh', 'ar', 'ja'],
            'export.locale_attributes.ja' => ['lang' => 'ja', 'dir' => 'ltr', 'label' => '日本語'],
        ]);
    }

    /** Google palsu: setiap teks diberi awalan "JA:" agar mudah dikenali. */
    private function fakeGoogle(): void
    {
        Http::fake([
            'translation.googleapis.com/*' => function (Request $request) {
                return Http::response(['data' => ['translations' => array_map(
                    fn ($text) => ['translatedText' => 'JA:'.$text],
                    $request['q']
                )]]);
            },
        ]);
    }

    public function test_machine_locale_is_translated_and_cached(): void
    {
        $this->fakeGoogle();
        $english = trans('export.navigation.home', [], 'en');

        app(MachineTranslator::class)->load('ja');

        $this->assertSame('JA:'.$english, app('translator')->get('export.navigation.home', [], 'ja'));

        $sent = count(Http::recorded());
        $this->assertGreaterThan(0, $sent);
        $this->assertGreaterThan(0, DB::table('machine_translations')->where('locale', 'ja')->count());

        // Pemuatan kedua memakai cache, tidak memanggil Google lagi.
        app(MachineTranslator::class)->buildTree('ja');
        $this->assertCount($sent, Http::recorded());
    }

    public function test_placeholders_are_protected_and_kept(): void
    {
        $this->fakeGoogle();

        app(MachineTranslator::class)->load('ja');

        $line = app('translator')->get('export.product_card.image_alt', [], 'ja');
        $this->assertStringContainsString(':name', $line);
        $this->assertStringNotContainsString('<span', $line);
        $this->assertSame('JA:'.trans('export.product_card.image_alt', [], 'en'), $line);
    }

    public function test_translation_that_loses_a_placeholder_falls_back_to_english(): void
    {
        Http::fake([
            'translation.googleapis.com/*' => fn (Request $request) => Http::response(['data' => ['translations' => array_map(
                fn () => ['translatedText' => 'X'],
                $request['q']
            )]]),
        ]);

        app(MachineTranslator::class)->load('ja');

        $english = trans('export.product_card.image_alt', [], 'en');
        $this->assertSame($english, app('translator')->get('export.product_card.image_alt', [], 'ja'));
        $this->assertSame('X', app('translator')->get('export.navigation.home', [], 'ja'));
        $this->assertDatabaseMissing('machine_translations', ['locale' => 'ja', 'source_hash' => sha1($english)]);
    }

    public function test_excluded_texts_stay_english(): void
    {
        $this->fakeGoogle();

        app(MachineTranslator::class)->load('ja');

        $this->assertSame(
            trans('export.logistics.disclaimer', [], 'en'),
            app('translator')->get('export.logistics.disclaimer', [], 'ja')
        );
    }

    public function test_dotted_validation_keys_keep_their_structure(): void
    {
        $this->fakeGoogle();

        app(MachineTranslator::class)->load('ja');

        $validation = app('translator')->get('export.logistics.validation', [], 'ja');
        $this->assertIsArray($validation);
        $this->assertArrayHasKey('mode.required', $validation);
    }

    public function test_monthly_limit_stops_google_calls_and_shows_english(): void
    {
        $this->fakeGoogle();
        config(['export.machine.monthly_char_limit' => 10]);

        app(MachineTranslator::class)->load('ja');

        Http::assertNothingSent();
        $this->assertSame(
            trans('export.navigation.home', [], 'en'),
            app('translator')->get('export.navigation.home', [], 'ja')
        );
    }

    public function test_usage_is_recorded_per_month(): void
    {
        $this->fakeGoogle();

        app(MachineTranslator::class)->load('ja');

        $this->assertGreaterThan(0, app(MachineTranslator::class)->usedThisMonth());
        $this->assertDatabaseHas('machine_translation_usages', ['period' => now()->format('Y-m')]);
    }

    public function test_google_failure_shows_english_and_stores_nothing(): void
    {
        Http::fake(['translation.googleapis.com/*' => Http::response('error', 500)]);

        app(MachineTranslator::class)->load('ja');

        $this->assertSame(
            trans('export.navigation.home', [], 'en'),
            app('translator')->get('export.navigation.home', [], 'ja')
        );
        $this->assertDatabaseCount('machine_translations', 0);
    }

    public function test_without_api_key_nothing_is_sent(): void
    {
        Http::fake();
        config(['export.machine.api_key' => null]);

        $this->assertFalse(app(MachineTranslator::class)->enabled());

        app(MachineTranslator::class)->load('ja');

        Http::assertNothingSent();
    }

    public function test_visitor_can_pick_a_machine_language_and_page_renders(): void
    {
        $this->fakeGoogle();

        $this->get(route('export.locale', 'ja'))->assertRedirect();
        $this->get(route('export.home'))->assertOk()->assertSee('JA:', false);

        $this->assertSame('ja', app()->getLocale());
    }

    public function test_machine_locale_not_in_list_is_not_found(): void
    {
        config(['export.locales' => ['en', 'id', 'zh', 'ar']]);

        $this->get(route('export.locale', 'ja'))->assertNotFound();
    }

    public function test_browser_language_suggests_a_machine_language_with_translated_banner(): void
    {
        $this->fakeGoogle();

        $this->withHeader('Accept-Language', 'ja-JP,ja;q=0.9')
            ->get(route('export.home'))
            ->assertOk()
            ->assertSee('JA:', false);

        $this->assertSame('en', app()->getLocale());
    }

    public function test_indonesian_is_a_stored_language(): void
    {
        Http::fake();

        $this->get(route('export.locale', 'id'))->assertRedirect();
        $this->get(route('export.home'))->assertOk();

        $this->assertSame('id', app()->getLocale());
        $this->assertSame('Beranda', __('export.navigation.home'));
        Http::assertNothingSent();
    }
}