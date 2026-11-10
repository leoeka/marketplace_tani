<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\MarketPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketPriceArticleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_market_prices(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'commodity' => 'Cabai Merah',
            'price' => 42000,
            'unit' => 'kg',
            'source' => 'Pasar Induk',
            'recorded_on' => now()->toDateString(),
        ];

        $this->actingAs($admin)
            ->post(route('admin.market-prices.store'), $payload)
            ->assertRedirect(route('admin.market-prices.index'));

        $price = MarketPrice::query()->firstOrFail();
        $this->assertSame('Cabai Merah', $price->commodity);

        $payload['price'] = 45000;
        $payload['source'] = 'Badan Pangan';
        $this->put(route('admin.market-prices.update', $price), $payload)
            ->assertRedirect(route('admin.market-prices.index'));

        $this->assertDatabaseHas('market_prices', [
            'id' => $price->id,
            'price' => 45000,
            'source' => 'Badan Pangan',
        ]);
        $this->assertSame(now()->toDateString(), $price->fresh()->recorded_on->toDateString());

        $this->delete(route('admin.market-prices.destroy', $price))
            ->assertRedirect(route('admin.market-prices.index'));

        $this->assertDatabaseMissing('market_prices', ['id' => $price->id]);
    }

    public function test_admin_can_create_update_and_delete_articles_with_unique_slugs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'title' => 'Tips Pertanian Modern',
            'excerpt' => 'Ringkasan tips.',
            'body' => "Paragraf pertama.\n\nParagraf kedua.",
            'status' => 'draft',
        ];

        $this->actingAs($admin)
            ->post(route('admin.articles.store'), $payload)
            ->assertRedirect();
        $firstArticle = Article::query()->firstOrFail();

        $this->post(route('admin.articles.store'), $payload)
            ->assertRedirect();
        $secondArticle = Article::query()->where('id', '!=', $firstArticle->id)->firstOrFail();

        $this->assertSame('tips-pertanian-modern', $firstArticle->slug);
        $this->assertSame('tips-pertanian-modern-2', $secondArticle->slug);
        $this->assertNull($firstArticle->published_at);

        $payload['status'] = 'published';
        $this->put(route('admin.articles.update', $firstArticle), $payload)
            ->assertRedirect(route('admin.articles.index'));
        $this->assertSame(now()->toDateString(), $firstArticle->fresh()->published_at->toDateString());

        $this->delete(route('admin.articles.destroy', $secondArticle))
            ->assertRedirect(route('admin.articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $secondArticle->id]);
    }

    public function test_farmer_and_customer_are_forbidden_from_3c_admin_pages(): void
    {
        foreach (['farmer', 'customer'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('admin.market-prices.index'))
                ->assertForbidden();
            $this->get(route('admin.articles.index'))->assertForbidden();
            $this->post(route('admin.market-prices.store'), [])->assertForbidden();
            $this->post(route('admin.articles.store'), [])->assertForbidden();
        }
    }

    public function test_future_market_price_date_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.market-prices.create'))
            ->post(route('admin.market-prices.store'), [
                'commodity' => 'Beras',
                'price' => 15000,
                'unit' => 'kg',
                'source' => 'Pasar Induk',
                'recorded_on' => now()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('recorded_on');

        $this->assertDatabaseCount('market_prices', 0);
    }

    public function test_market_prices_show_latest_per_commodity_and_correct_changes(): void
    {
        foreach ([
            ['Naik', 100, 120],
            ['Turun', 200, 100],
            ['Tetap', 90, 90],
        ] as [$commodity, $oldPrice, $newPrice]) {
            MarketPrice::factory()->create([
                'commodity' => $commodity,
                'price' => $oldPrice,
                'unit' => 'kg',
                'recorded_on' => now()->subDays(2)->toDateString(),
            ]);
            MarketPrice::factory()->create([
                'commodity' => $commodity,
                'price' => $newPrice,
                'unit' => 'kg',
                'recorded_on' => now()->subDay()->toDateString(),
            ]);
        }
        MarketPrice::factory()->create([
            'commodity' => 'Tanpa Pembanding',
            'price' => 3000,
            'unit' => 'kg',
            'recorded_on' => now()->toDateString(),
        ]);

        $response = $this->get(route('marketplace.market-prices.index'))
            ->assertOk()
            ->assertSee('↑ 20,00%')
            ->assertSee('↓ 50,00%')
            ->assertSee('→ 0,00%')
            ->assertSee('Tanpa Pembanding')
            ->assertSee(__('marketplace.market_prices.no_comparison', [], 'id'));

        $rows = $response->viewData('rows')->keyBy(fn(array $row): string => $row['latest']->commodity);
        $this->assertSame(120, $rows['Naik']['latest']->price);
        $this->assertNull($rows['Tanpa Pembanding']['change_percent']);
    }

    public function test_market_price_units_are_compared_separately_and_history_is_limited_to_ten(): void
    {
        foreach (range(0, 11) as $daysAgo) {
            MarketPrice::factory()->create([
                'commodity' => 'Beras',
                'price' => 10000 + $daysAgo,
                'unit' => 'kg',
                'source' => 'Sumber kg ' . $daysAgo,
                'recorded_on' => now()->subDays($daysAgo)->toDateString(),
            ]);
        }
        MarketPrice::factory()->create([
            'commodity' => 'Beras',
            'price' => 10000000,
            'unit' => 'ton',
            'recorded_on' => now()->toDateString(),
        ]);

        $response = $this->get(route('marketplace.market-prices.index', ['q' => 'Beras']))
            ->assertOk()
            ->assertSee('Beras');
        $rows = $response->viewData('rows');

        $this->assertCount(2, $rows);
        $kilogramRow = $rows->firstWhere('latest.unit', 'kg');
        $tonRow = $rows->firstWhere('latest.unit', 'ton');
        $this->assertCount(10, $kilogramRow['history']);
        $this->assertNull($tonRow['change_percent']);
        $this->assertSame('Sumber kg 0', $kilogramRow['history']->first()->source);

        $this->get(route('marketplace.market-prices.index', ['q' => 'TidakAda']))
            ->assertOk()
            ->assertSee(__('marketplace.market_prices.empty_search', [], 'id'))
            ->assertDontSee('Beras');
    }

    public function test_draft_articles_are_hidden_and_published_article_text_is_escaped(): void
    {
        $draft = Article::factory()->create([
            'title' => 'Artikel Draf Tersembunyi',
            'slug' => 'artikel-draf-tersembunyi',
            'status' => 'draft',
            'published_at' => null,
        ]);
        $published = Article::factory()->create([
            'title' => 'Tips Panen Modern',
            'slug' => 'tips-panen-modern',
            'status' => 'published',
            'published_at' => now(),
            'body' => "Paragraf aman.\n\n<script>alert('teks')</script>",
        ]);

        $this->get(route('marketplace.articles.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);

        $this->get(route('marketplace.articles.show', $draft->slug))->assertNotFound();

        $this->get(route('marketplace.articles.show', $published->slug))
            ->assertOk()
            ->assertSee('Paragraf aman.')
            ->assertSee('&lt;script&gt;alert(&#039;teks&#039;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert', false);
    }

    public function test_market_price_and_education_navigation_link_to_their_pages(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="' . route('marketplace.market-prices.index') . '"', false)
            ->assertSee('href="' . route('marketplace.articles.index') . '"', false);
    }
}
