<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Mode dua domain: marketplace dan website ekspor di domain berbeda, satu aplikasi. */
class SplitDomainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        foreach (['MARKETPLACE_DOMAIN' => 'nusaresource.com', 'EXPORT_DOMAIN' => 'vienusantaraglobal.com'] as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach (['MARKETPLACE_DOMAIN', 'EXPORT_DOMAIN'] as $key) {
            unset($_ENV[$key], $_SERVER[$key]);
            putenv($key);
        }
    }

    public function test_each_domain_serves_its_own_website(): void
    {
        $this->get('http://vienusantaraglobal.com/')
            ->assertOk()
            ->assertSee('PT. VIE NUSANTARA GLOBAL')
            ->assertSee('<html lang="en" dir="ltr">', false);

        $this->get('http://nusaresource.com/')
            ->assertOk()
            ->assertSee('NUSA RESOURCE')
            ->assertSee('<html lang="id"', false);
    }

    public function test_route_helper_builds_links_across_domains(): void
    {
        $this->assertSame('vienusantaraglobal.com', parse_url(route('export.home'), PHP_URL_HOST));
        $this->assertSame('nusaresource.com', parse_url(route('marketplace.products.index'), PHP_URL_HOST));
        $this->assertSame('/produk', parse_url(route('marketplace.products.index'), PHP_URL_PATH));
    }

    public function test_export_home_links_menu_and_products_to_the_marketplace_domain(): void
    {
        $this->get('http://vienusantaraglobal.com/')
            ->assertOk()
            ->assertSee('http://nusaresource.com/produk', false);
    }

    public function test_marketplace_pages_do_not_exist_on_the_export_domain_and_vice_versa(): void
    {
        $this->get('http://vienusantaraglobal.com/produk')->assertNotFound();
        $this->get('http://vienusantaraglobal.com/admin/dashboard')->assertNotFound();
        $this->get('http://nusaresource.com/rfq')->assertNotFound();
    }

    public function test_www_redirects_to_the_bare_domain(): void
    {
        $this->get('http://www.nusaresource.com/produk')
            ->assertStatus(301)
            ->assertRedirect('http://nusaresource.com/produk');

        $this->get('http://www.vienusantaraglobal.com/rfq')
            ->assertStatus(301)
            ->assertRedirect('http://vienusantaraglobal.com/rfq');
    }

    public function test_export_language_switch_stays_on_the_export_domain(): void
    {
        $this->get('http://vienusantaraglobal.com/locale/id')
            ->assertRedirect('http://vienusantaraglobal.com');
    }
}
