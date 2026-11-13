<?php

namespace Tests\Feature;

use App\Support\ExportCurrency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportLocaleCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_switch_export_language_and_arabic_is_right_to_left(): void
    {
        $this->from(route('export.home'))
            ->get(route('export.locale', 'ar'))
            ->assertRedirect(route('export.home'));

        $this->get(route('export.home'))
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false);
    }

    public function test_chinese_is_available_and_marketplace_stays_indonesian(): void
    {
        $this->from(route('export.home'))->get(route('export.locale', 'zh'));

        $this->get(route('export.home'))
            ->assertOk()
            ->assertSee('<html lang="zh-CN" dir="ltr">', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="id"', false);
    }

    public function test_unknown_locale_is_not_found(): void
    {
        $this->get(route('export.locale', 'xx'))->assertNotFound();
    }

    public function test_currency_without_a_configured_rate_is_unavailable(): void
    {
        config()->set('export.currencies.EUR.rate', null);

        $this->get(route('export.currency', 'EUR'))->assertNotFound();
    }

    public function test_currency_with_a_configured_rate_can_be_selected(): void
    {
        config()->set('export.currencies.EUR.rate', 0.9);

       $this->from(route('export.logistics.index'))
            ->get(route('export.currency', 'eur'))
            ->assertRedirect(route('export.logistics.index'))
            ->assertSessionHas('export_currency', 'EUR');
    }

    public function test_conversion_uses_only_the_configured_rate(): void
    {
        config()->set('export.currencies.SGD.rate', 1.3);
        $this->withSession(['export_currency' => 'SGD']);

        $converted = ExportCurrency::fromUsd('1,250.00');

        $this->assertSame('SGD', $converted['currency']);
        $this->assertSame('1,625.00', $converted['amount']);
        $this->assertSame('1.3', $converted['rate_label']);
    }

    public function test_conversion_falls_back_to_usd_when_the_rate_is_missing(): void
    {
        config()->set('export.currencies.SGD.rate', null);
        $this->withSession(['export_currency' => 'SGD']);

        $converted = ExportCurrency::fromUsd('300.00');

        $this->assertSame('USD', $converted['currency']);
        $this->assertSame('300.00', $converted['amount']);
    }
}