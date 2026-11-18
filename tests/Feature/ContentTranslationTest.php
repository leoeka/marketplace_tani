<?php

namespace Tests\Feature;

use App\Support\MachineTranslator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContentTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        MachineTranslator::flushMemo();

        config([
            'export.machine.enabled' => true,
            'export.machine.api_key' => 'test-key',
            'export.machine.monthly_char_limit' => 1000000,
            'export.machine.content' => true,
            'export.machine_locales' => ['ja'],
            'export.locales' => ['en', 'id', 'zh', 'ar', 'ja'],
        ]);

        Http::fake([
            'translation.googleapis.com/*' => fn (Request $request) => Http::response(['data' => ['translations' => array_map(
                fn ($text) => ['translatedText' => 'T:'.$text],
                $request['q']
            )]]),
        ]);
    }

    public function test_indonesian_content_is_translated_with_indonesian_as_source(): void
    {
        $result = app(MachineTranslator::class)->content('Tomat Merah Segar', 'en');

        $this->assertSame('T:Tomat Merah Segar', $result);
        Http::assertSent(fn (Request $r) => $r['source'] === 'id' && $r['target'] === 'en');
    }

    public function test_content_is_cached_and_not_sent_twice(): void
    {
        $mt = app(MachineTranslator::class);
        $mt->content('Beras Panen Baru', 'ja');
        MachineTranslator::flushMemo();
        $mt->content('Beras Panen Baru', 'ja');

        Http::assertSentCount(1);
    }

    public function test_many_texts_use_one_google_call(): void
    {
        app(MachineTranslator::class)->primeContent(['Cabai', 'Tomat', 'Beras'], 'en');

        Http::assertSentCount(1);
    }

    public function test_indonesian_visitors_see_original_content(): void
    {
        $this->assertSame('Tomat Merah Segar', app(MachineTranslator::class)->content('Tomat Merah Segar', 'id'));
        Http::assertNothingSent();
    }

    public function test_content_stays_original_without_key_or_when_switched_off(): void
    {
        config(['export.machine.api_key' => null]);
        $this->assertSame('Tomat', app(MachineTranslator::class)->content('Tomat', 'en'));

        config(['export.machine.api_key' => 'k', 'export.machine.content' => false]);
        $this->assertSame('Tomat', app(MachineTranslator::class)->content('Tomat', 'en'));

        Http::assertNothingSent();
    }

    public function test_line_breaks_survive_translation(): void
    {
        $result = app(MachineTranslator::class)->content("Baris satu\nBaris dua", 'en');

        $this->assertSame("T:Baris satu\nBaris dua", $result);
    }
}