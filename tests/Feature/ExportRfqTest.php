<?php

namespace Tests\Feature;

use App\Mail\ExportRfqNotification;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductExportDetail;
use App\Models\Rfq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class ExportRfqTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_english_rfq_form_with_only_export_visible_products(): void
    {
        $visibleProduct = $this->createExportProduct();
        $hiddenProduct = $this->createExportProduct(['show_in_export' => false], ['name_en' => 'Hidden Export Item']);

        $this->get(route('export.rfq.create'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee($visibleProduct->exportDetails->name_en)
            ->assertDontSee('Hidden Export Item')
            ->assertSee('Request a Quote');
    }

    public function test_valid_rfq_is_saved_with_all_submitted_values_and_redirects_to_the_thank_you_page(): void
    {
        $product = $this->createExportProduct();

        $this->post(route('export.rfq.store'), $this->validPayload([
            'product_id' => $product->id,
            'quantity' => 1250,
            'quantity_unit' => 'kg',
            'wants_sample' => '1',
            'asks_moq' => '1',
            'incoterms' => 'CIF',
            'message' => 'Please share export specifications.',
        ]))
            ->assertRedirect(route('export.rfq.thanks'));

        $rfq = Rfq::query()->firstOrFail();
        $this->assertSame('Alex Buyer', $rfq->buyer_name);
        $this->assertSame('Global Foods Ltd', $rfq->company);
        $this->assertSame('Singapore', $rfq->country);
        $this->assertSame('buyer@example.test', $rfq->email);
        $this->assertSame('+65 1234 5678', $rfq->phone);
        $this->assertSame($product->id, $rfq->product_id);
        $this->assertSame('Export Product Name', $rfq->product_name);
        $this->assertSame(1250, $rfq->quantity);
        $this->assertSame('kg', $rfq->quantity_unit);
        $this->assertTrue($rfq->wants_sample);
        $this->assertTrue($rfq->asks_moq);
        $this->assertSame('CIF', $rfq->incoterms);
        $this->assertSame('Please share export specifications.', $rfq->message);
        $this->assertSame('new', $rfq->status);
        $this->assertNull($rfq->handled_at);

        $this->get(route('export.rfq.thanks'))
            ->assertOk()
            ->assertSee('Thank you, we will contact you by email.')
            ->assertDontSee('Alex Buyer')
            ->assertDontSee('Global Foods Ltd');
    }

    public function test_rfq_uses_a_product_name_snapshot(): void
    {
        $product = $this->createExportProduct();
        $this->post(route('export.rfq.store'), $this->validPayload(['product_id' => $product->id]));

        $product->exportDetails()->update(['name_en' => 'Renamed Export Product']);

        $this->assertSame('Export Product Name', Rfq::query()->firstOrFail()->product_name);
    }

    public function test_visible_product_is_prefilled_and_non_export_product_prefill_is_ignored(): void
    {
        $visibleProduct = $this->createExportProduct();
        $hiddenProduct = $this->createExportProduct(['show_in_export' => false], ['name_en' => 'Hidden Export Item']);

        $this->get(route('export.rfq.create', ['product' => $visibleProduct->slug]))
            ->assertOk()
            ->assertSee('value="'.$visibleProduct->id.'" selected', false);

        $this->get(route('export.rfq.create', ['product' => $hiddenProduct->slug]))
            ->assertOk()
            ->assertDontSee('Hidden Export Item')
            ->assertSee('<option value="">General inquiry</option>', false);
    }

    public function test_non_export_visible_product_id_is_ignored_when_submitted(): void
    {
        $hiddenProduct = $this->createExportProduct(['show_in_export' => false]);

        $this->post(route('export.rfq.store'), $this->validPayload(['product_id' => $hiddenProduct->id]))
            ->assertRedirect(route('export.rfq.thanks'));

        $this->assertDatabaseHas('rfqs', [
            'product_id' => null,
            'product_name' => null,
        ]);
    }

    #[DataProvider('invalidRfqs')]
    public function test_invalid_rfq_fields_are_rejected(array $changes, string $invalidField): void
    {
        $this->from(route('export.rfq.create'))
            ->post(route('export.rfq.store'), $this->validPayload($changes))
            ->assertSessionHasErrors($invalidField);

        $this->assertDatabaseCount('rfqs', 0);
    }

    /** @return array<string, array{0: array<string, mixed>, 1: string}> */
    public static function invalidRfqs(): array
    {
        return [
            'full name required' => [['buyer_name' => ''], 'buyer_name'],
            'company required' => [['company' => ''], 'company'],
            'country required' => [['country' => ''], 'country'],
            'email required' => [['email' => ''], 'email'],
            'email must be valid' => [['email' => 'not-an-email'], 'email'],
            'quantity requires unit' => [['quantity' => '10'], 'quantity_unit'],
            'invalid incoterms' => [['incoterms' => 'DDP'], 'incoterms'],
            'message length' => [['message' => str_repeat('a', 2001)], 'message'],
        ];
    }

    public function test_filled_honeypot_is_silently_ignored_and_shows_thank_you(): void
    {
        $this->post(route('export.rfq.store'), ['fax_number' => '+1 555 0100'])
            ->assertRedirect(route('export.rfq.thanks'));

        $this->assertDatabaseCount('rfqs', 0);
    }

    public function test_sixth_rfq_submission_from_one_ip_is_rate_limited(): void
    {
        for ($submission = 1; $submission <= 5; $submission++) {
            $this->post(route('export.rfq.store'), $this->validPayload())
                ->assertRedirect(route('export.rfq.thanks'));
        }

        $this->post(route('export.rfq.store'), $this->validPayload())
            ->assertTooManyRequests();

        $this->assertDatabaseCount('rfqs', 5);
    }

    public function test_admin_notification_is_sent_to_the_configured_address(): void
    {
        Mail::fake();
        config(['export.rfq_notification_email' => 'admin@example.test']);

        $this->post(route('export.rfq.store'), $this->validPayload());

        Mail::assertSent(ExportRfqNotification::class, function (ExportRfqNotification $mail): bool {
            return $mail->hasTo('admin@example.test')
                && $mail->rfq->company === 'Global Foods Ltd'
                && str_contains($mail->envelope()->subject, 'Global Foods Ltd')
                && str_contains($mail->envelope()->subject, 'General inquiry');
        });
    }

    public function test_admin_notification_is_skipped_when_no_recipient_is_configured(): void
    {
        Mail::fake();
        config(['export.rfq_notification_email' => null]);

        $this->post(route('export.rfq.store'), $this->validPayload())
            ->assertRedirect(route('export.rfq.thanks'));

        Mail::assertNothingOutgoing();
        $this->assertDatabaseCount('rfqs', 1);
    }

    public function test_mail_delivery_failure_does_not_discard_the_rfq_or_change_the_response(): void
    {
        config(['export.rfq_notification_email' => 'admin@example.test']);
        Mail::shouldReceive('to')
            ->once()
            ->with('admin@example.test')
            ->andThrow(new RuntimeException('Mail transport unavailable.'));

        $this->post(route('export.rfq.store'), $this->validPayload())
            ->assertRedirect(route('export.rfq.thanks'));

        $this->assertDatabaseCount('rfqs', 1);
    }

    public function test_admin_can_filter_search_and_update_rfq_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $newRfq = Rfq::factory()->create([
            'buyer_name' => 'Ada Buyer',
            'company' => 'Pacific Produce',
            'created_at' => now()->subMinutes(5),
        ]);
        Rfq::factory()->create([
            'buyer_name' => 'Ben Buyer',
            'company' => 'Other Imports',
            'status' => 'handled',
            'handled_at' => now()->subDay(),
            'created_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.rfqs.index'))
            ->assertOk()
            ->assertSee('Ada Buyer')
            ->assertSee('Ben Buyer')
            ->assertSee(__('dashboard.menu.new_count', ['count' => 1], 'id'));

        $this->get(route('admin.rfqs.index', ['status' => 'new', 'search' => 'Pacific']))
            ->assertOk()
            ->assertSee('Ada Buyer')
            ->assertDontSee('Ben Buyer');

        $this->get(route('admin.rfqs.index', ['status' => 'handled']))
            ->assertOk()
            ->assertSee('Ben Buyer')
            ->assertDontSee('Ada Buyer');

        $this->get(route('admin.rfqs.show', $newRfq))
            ->assertOk()
            ->assertSee('mailto:'.$newRfq->email, false)
            ->assertSee('Ada Buyer')
            ->assertSee('Pertanyaan umum');

        $this->post(route('admin.rfqs.mark-handled', $newRfq))
            ->assertRedirect();
        $this->assertSame('handled', $newRfq->fresh()->status);
        $this->assertNotNull($newRfq->fresh()->handled_at);

        $this->post(route('admin.rfqs.mark-new', $newRfq))
            ->assertRedirect();
        $this->assertSame('new', $newRfq->fresh()->status);
        $this->assertNull($newRfq->fresh()->handled_at);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(__('admin.dashboard.new_rfqs', [], 'id'))
            ->assertSee('>1</p>', false);
    }

    public function test_admin_detail_escapes_html_in_buyer_submitted_text(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post(route('export.rfq.store'), $this->validPayload([
            'buyer_name' => '<strong>Buyer</strong>',
            'company' => '<img src=x onerror=alert(1)>',
            'message' => '<script>alert("x")</script>',
        ]));
        $rfq = Rfq::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.rfqs.show', $rfq))
            ->assertOk()
            ->assertSee('&lt;strong&gt;Buyer&lt;/strong&gt;', false)
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
            ->assertSee('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("x")</script>', false);
    }

    public function test_guest_is_redirected_to_login_for_admin_rfqs(): void
    {
        $rfq = Rfq::factory()->create();

        $this->get(route('admin.rfqs.index'))
            ->assertRedirect(route('login'));
        $this->get(route('admin.rfqs.show', $rfq))
            ->assertRedirect(route('login'));
    }

    public function test_farmer_and_customer_receive_forbidden_for_all_admin_rfq_routes(): void
    {
        $rfq = Rfq::factory()->create();

        foreach (['farmer', 'customer'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('admin.rfqs.index'))
                ->assertForbidden();
            $this->get(route('admin.rfqs.show', $rfq))
                ->assertForbidden();
            $this->post(route('admin.rfqs.mark-handled', $rfq))
                ->assertForbidden();
            $this->post(route('admin.rfqs.mark-new', $rfq))
                ->assertForbidden();
        }
    }

    /** @return array<string, mixed> */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'buyer_name' => 'Alex Buyer',
            'company' => 'Global Foods Ltd',
            'country' => 'Singapore',
            'email' => 'buyer@example.test',
            'phone' => '+65 1234 5678',
            'product_id' => '',
            'quantity' => '',
            'quantity_unit' => '',
            'incoterms' => 'TBD',
            'message' => '',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $productAttributes
     * @param  array<string, mixed>  $detailsAttributes
     */
    private function createExportProduct(
        array $productAttributes = [],
        array $detailsAttributes = [],
    ): Product {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);
        $product = Product::factory()->for($farmer, 'farmer')->create(array_merge([
            'status' => 'approved',
            'show_in_export' => true,
        ], $productAttributes));
        ProductExportDetail::factory()->for($product)->create(array_merge([
            'name_en' => 'Export Product Name',
            'description_en' => 'A complete English description.',
        ], $detailsAttributes));

        return $product->load('exportDetails');
    }
}