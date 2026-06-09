<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_product_with_auto_generated_barcode_sku_and_image(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'owner']);
        $category = Category::query()->create([
            'name' => 'Cups',
            'sku_prefix' => 'CUP',
            'description' => 'Drinkware',
        ]);

        AppSetting::current()->update([
            'barcode_prefix' => '88',
            'barcode_random_length' => 8,
        ]);

        $response = $this->actingAs($owner)->post('/products', [
            'name' => 'Paper Cups',
            'category_id' => $category->id,
            'cost' => 1.25,
            'selling_price' => 2.25,
            'quantity' => 20,
            'min_stock' => 5,
            'description' => 'Disposable cups',
            'image' => UploadedFile::fake()->createWithContent(
                'cups.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aW8sAAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertSessionHasNoErrors();
        $product = Product::query()->where('name', 'Paper Cups')->firstOrFail();

        $this->assertNotEmpty($product->barcode);
        $this->assertTrue(str_starts_with($product->barcode, '88'));
        $this->assertSame('CUP-0001', $product->sku);
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_invalid_product_upload_is_rejected(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'owner']);
        $category = Category::query()->create([
            'name' => 'Cups',
            'sku_prefix' => 'CUP',
            'description' => 'Drinkware',
        ]);

        $response = $this->from('/products/create')->actingAs($owner)->post('/products', [
            'name' => 'Unsafe Upload',
            'category_id' => $category->id,
            'cost' => 1.25,
            'selling_price' => 2.25,
            'quantity' => 20,
            'min_stock' => 5,
            'description' => 'Should fail',
            'image' => UploadedFile::fake()->createWithContent(
                'payload.svg',
                '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
            ),
        ]);

        $response->assertRedirect('/products/create');
        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('products', ['name' => 'Unsafe Upload']);
    }

    public function test_stock_out_updates_quantity_and_creates_a_transaction(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $product = Product::query()->create([
            'name' => 'Soap',
            'sku' => 'SOAP-1',
            'barcode' => '10001',
            'cost' => 2.50,
            'quantity' => 8,
            'min_stock' => 2,
        ]);

        $response = $this->actingAs($owner)->post('/transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 3,
        ]);

        $response->assertSessionHasNoErrors();
        $product->refresh();

        $this->assertSame(5, $product->quantity);
        $this->assertDatabaseHas('stock_transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity_before' => 8,
            'quantity_after' => 5,
        ]);
    }

    public function test_stock_out_cannot_make_inventory_negative(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $product = Product::query()->create([
            'name' => 'Cleaner',
            'sku' => 'CLN-1',
            'barcode' => '10002',
            'cost' => 5.50,
            'quantity' => 2,
            'min_stock' => 1,
        ]);

        $response = $this->from('/products/' . $product->id)->actingAs($owner)->post('/transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 3,
        ]);

        $response->assertRedirect('/products/' . $product->id);
        $response->assertSessionHasErrors('quantity');
    }

    public function test_owner_can_update_shared_scanner_settings(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->put('/settings', [
            'scanner_mode' => 'keyboard_wedge',
            'auto_submit_on_enter' => '1',
            'default_post_scan_behavior' => 'open_product_actions',
            'default_stock_action' => 'in',
            'label_size_default' => 'large',
            'barcode_prefix' => '77',
            'barcode_random_length' => 9,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('app_settings', [
            'scanner_mode' => 'keyboard_wedge',
            'default_stock_action' => 'in',
            'label_size_default' => 'large',
            'barcode_prefix' => '77',
            'barcode_random_length' => 9,
        ]);
    }

    public function test_scan_stock_action_returns_to_scan_page(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $product = Product::query()->create([
            'name' => 'Soap',
            'sku' => 'SOAP-2',
            'barcode' => '10003',
            'cost' => 2.50,
            'quantity' => 8,
            'min_stock' => 2,
        ]);

        $response = $this->actingAs($owner)->post('/transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 1,
            'return_to_scan' => '1',
        ]);

        $response->assertRedirect('/scan');
    }

    public function test_owner_can_manage_categories_and_cannot_delete_used_category(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)->post('/categories', [
            'name' => 'Beverages',
            'sku_prefix' => 'BEV',
            'description' => 'Drinks section',
        ])->assertSessionHasNoErrors();

        $category = Category::query()->where('name', 'Beverages')->firstOrFail();

        $this->actingAs($owner)->put('/categories/' . $category->id, [
            'name' => 'Hot Beverages',
            'sku_prefix' => 'HOT',
            'description' => 'Tea and coffee',
        ])->assertSessionHasNoErrors();

        Product::query()->create([
            'name' => 'Coffee',
            'sku' => 'COF-1',
            'barcode' => '11001',
            'category_id' => $category->id,
            'cost' => 3.50,
            'quantity' => 10,
            'min_stock' => 2,
        ]);

        $this->actingAs($owner)->delete('/categories/' . $category->id)
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Hot Beverages',
            'sku_prefix' => 'HOT',
        ]);
    }

    public function test_owner_can_import_products_csv_and_generated_barcodes_and_skus_are_assigned(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        AppSetting::current()->update([
            'barcode_prefix' => '55',
            'barcode_random_length' => 8,
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'products.csv',
            implode("\n", [
                'name,category,cost,selling_price,quantity,min_stock,description',
                'Notebook,Stationery,4.50,6.00,12,3,College notebook',
                'Pen,Stationery,0.75,1.50,30,5,Blue ink pen',
            ])
        );

        $response = $this->actingAs($owner)->post('/imports/products', [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', ['name' => 'Notebook']);
        $this->assertDatabaseHas('products', ['name' => 'Pen']);

        $product = Product::query()->where('name', 'Notebook')->firstOrFail();
        $this->assertTrue(str_starts_with($product->barcode, '55'));
        $this->assertTrue(str_starts_with($product->sku, 'STAT-'));
    }

    public function test_owner_can_export_products_and_transactions_csv(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $product = Product::query()->create([
            'name' => 'Cable',
            'sku' => 'CBL-1',
            'barcode' => '12001',
            'cost' => 6.00,
            'quantity' => 15,
            'min_stock' => 4,
        ]);

        $this->actingAs($owner)->post('/transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
        ])->assertSessionHasNoErrors();

        $productsExport = $this->actingAs($owner)->get('/exports/products');
        $productsExport->assertOk();
        $productsExport->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('name,sku,barcode,category,cost,selling_price,quantity,min_stock,description', $productsExport->streamedContent());

        $transactionsExport = $this->actingAs($owner)->get('/exports/transactions');
        $transactionsExport->assertOk();
        $transactionsExport->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('time,product,sku,type,quantity,before,after,user,note', $transactionsExport->streamedContent());
        $this->assertStringContainsString('Cable', $transactionsExport->streamedContent());
    }

    public function test_low_stock_alerts_are_created_once_and_resolved_after_restock(): void
    {
        Notification::fake();

        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner@example.com',
        ]);

        $product = Product::query()->create([
            'name' => 'Battery',
            'sku' => 'BAT-1',
            'barcode' => '13001',
            'cost' => 2.00,
            'quantity' => 5,
            'min_stock' => 3,
        ]);

        $this->actingAs($owner)->post('/transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
        ])->assertSessionHasNoErrors();

        $alert = LowStockAlert::query()->where('product_id', $product->id)->where('status', 'active')->first();
        $this->assertNotNull($alert);

        $this->actingAs($owner)->post('/transactions', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, LowStockAlert::query()->where('product_id', $product->id)->where('status', 'active')->count());

        $this->actingAs($owner)->post('/transactions', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
            'unit_cost' => 2.10,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('low_stock_alerts', [
            'id' => $alert->id,
            'status' => 'resolved',
        ]);
    }

    public function test_settings_changes_affect_new_product_barcode_generation(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)->put('/settings', [
            'scanner_mode' => 'keyboard_wedge',
            'auto_submit_on_enter' => '1',
            'default_post_scan_behavior' => 'open_product_actions',
            'default_stock_action' => 'out',
            'label_size_default' => 'small',
            'barcode_prefix' => '91',
            'barcode_random_length' => 7,
        ])->assertSessionHasNoErrors();

        $category = Category::query()->create([
            'name' => 'Accessories',
            'sku_prefix' => 'ACC',
            'description' => 'Computer accessories',
        ]);

        $this->actingAs($owner)->post('/products', [
            'name' => 'Mouse Pad',
            'category_id' => $category->id,
            'cost' => 3.25,
            'selling_price' => 5.00,
            'quantity' => 9,
            'min_stock' => 2,
            'description' => 'Desk accessory',
        ])->assertSessionHasNoErrors();

        $product = Product::query()->where('name', 'Mouse Pad')->firstOrFail();

        $this->assertSame(9, strlen($product->barcode));
        $this->assertTrue(str_starts_with($product->barcode, '91'));
        $this->assertSame('ACC-0001', $product->sku);
    }

    public function test_product_sku_sequence_increments_within_each_category(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $category = Category::query()->create([
            'name' => 'Electronics',
            'sku_prefix' => 'ELC',
            'description' => 'Devices',
        ]);

        $this->actingAs($owner)->post('/products', [
            'name' => 'Wireless Mouse',
            'category_id' => $category->id,
            'cost' => 10,
            'selling_price' => 15,
            'quantity' => 5,
            'min_stock' => 1,
        ])->assertSessionHasNoErrors();

        $this->actingAs($owner)->post('/products', [
            'name' => 'Wireless Keyboard',
            'category_id' => $category->id,
            'cost' => 20,
            'selling_price' => 30,
            'quantity' => 4,
            'min_stock' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', ['name' => 'Wireless Mouse', 'sku' => 'ELC-0001']);
        $this->assertDatabaseHas('products', ['name' => 'Wireless Keyboard', 'sku' => 'ELC-0002']);
    }

    public function test_owner_password_must_meet_stronger_security_rules(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->from('/users')->actingAs($owner)->post('/users', [
            'name' => 'Weak Password User',
            'username' => 'weak-user',
            'email' => 'weak@example.com',
            'role' => 'staff',
            'is_active' => '1',
            'password' => 'weak123',
        ]);

        $response->assertRedirect('/users');
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['username' => 'weak-user']);
    }
}
