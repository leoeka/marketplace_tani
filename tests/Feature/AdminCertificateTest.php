<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_certificate_and_associate_products(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct();
        $firstFile = UploadedFile::fake()->createWithContent('organic.pdf', "%PDF-1.4\nCertificate document");

        $this->actingAs($admin)
            ->post(route('admin.certificates.store'), [
                'name' => 'Organic',
                'file' => $firstFile,
                'product_ids' => [$product->id],
            ])
            ->assertRedirect(route('admin.certificates.index'))
            ->assertSessionHas('success', 'Sertifikat berhasil ditambahkan.');

        $certificate = Certificate::query()->firstOrFail();
        Storage::disk('public')->assertExists($certificate->file_path);
        $this->assertDatabaseHas('product_certificate', [
            'certificate_id' => $certificate->id,
            'product_id' => $product->id,
        ]);

        $oldFilePath = $certificate->file_path;
        $secondFile = UploadedFile::fake()->createWithContent('organic-updated.pdf', "%PDF-1.4\nUpdated certificate document");

        $this->put(route('admin.certificates.update', $certificate), [
            'name' => 'Organic Standard',
            'file' => $secondFile,
            'product_ids' => [],
        ])->assertRedirect(route('admin.certificates.index'));

        $certificate->refresh();
        $this->assertSame('Organic Standard', $certificate->name);
        Storage::disk('public')->assertMissing($oldFilePath);
        Storage::disk('public')->assertExists($certificate->file_path);
        $this->assertDatabaseMissing('product_certificate', [
            'certificate_id' => $certificate->id,
            'product_id' => $product->id,
        ]);

        $filePath = $certificate->file_path;
        $this->delete(route('admin.certificates.destroy', $certificate))
            ->assertRedirect(route('admin.certificates.index'));

        $this->assertDatabaseMissing('certificates', ['id' => $certificate->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_admin_must_upload_a_supported_file_when_creating_a_certificate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.certificates.create'))
            ->post(route('admin.certificates.store'), [
                'name' => 'Organic',
                'file' => UploadedFile::fake()->create('certificate.txt', 10, 'text/plain'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('certificates', 0);
    }

    /** @return array<string, array<int, string>> */
    public static function nonAdminRoles(): array
    {
        return [
            'farmer' => ['farmer'],
            'customer' => ['customer'],
        ];
    }

    #[DataProvider('nonAdminRoles')]
    public function test_non_admin_roles_receive_forbidden_from_certificate_management(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('admin.certificates.index'))
            ->assertForbidden();
    }

    private function createProduct(): Product
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);

        return Product::factory()->for($farmer, 'farmer')->create([
            'status' => 'approved',
            'show_in_export' => true,
        ]);
    }
}