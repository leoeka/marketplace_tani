<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_adds_five_categories_once_and_keeps_existing_ones(): void
    {
        Category::factory()->create(['name' => 'Sayur', 'slug' => 'sayur']);

        $this->seed(CategorySeeder::class);
        $this->seed(CategorySeeder::class);

        $this->assertSame(
            ['beras-merah', 'cengkeh', 'jagung', 'kopi', 'vanili'],
            Category::query()->whereIn('slug', ['kopi', 'vanili', 'cengkeh', 'beras-merah', 'jagung'])->orderBy('slug')->pluck('slug')->all(),
        );
        $this->assertSame(6, Category::query()->count());
    }

    public function test_marketplace_home_lists_the_five_categories(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Kopi')
            ->assertSee('Vanili')
            ->assertSee('Cengkeh')
            ->assertSee('Beras merah')
            ->assertSee('Jagung');
    }
}
