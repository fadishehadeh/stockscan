<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        AppSetting::current();

        $owner = User::query()->updateOrCreate(
            ['username' => 'owner'],
            ['name' => 'Store Owner', 'email' => 'owner@example.com', 'role' => 'owner', 'is_active' => true, 'password' => Hash::make('password')]
        );

        $staff = User::query()->updateOrCreate(
            ['username' => 'staff'],
            ['name' => 'Stock Staff', 'email' => 'staff@example.com', 'role' => 'staff', 'is_active' => true, 'password' => Hash::make('password')]
        );

        User::query()->updateOrCreate(
            ['username' => 'pmanager'],
            ['name' => 'Purchase Manager', 'email' => 'pmanager@example.com', 'role' => 'purchase_manager', 'is_active' => true, 'password' => Hash::make('password')]
        );

        $categories = collect([
            ['name' => 'Beverages', 'sku_prefix' => 'BEV', 'description' => 'Drinks and bottled items'],
            ['name' => 'Snacks', 'sku_prefix' => 'SNK', 'description' => 'Quick retail snacks'],
            ['name' => 'Cleaning', 'sku_prefix' => 'CLN', 'description' => 'Cleaning and supply products'],
            ['name' => 'Dairy', 'sku_prefix' => 'DRY', 'description' => 'Milk, yogurt, and chilled goods'],
            ['name' => 'Stationery', 'sku_prefix' => 'STY', 'description' => 'Office and school supplies'],
            ['name' => 'Personal Care', 'sku_prefix' => 'PCR', 'description' => 'Toiletries and hygiene products'],
        ])->mapWithKeys(fn (array $category) => [
            $category['name'] => Category::query()->updateOrCreate(['name' => $category['name']], $category),
        ]);

        $products = [
            [
                'name' => 'Mineral Water 1.5L',
                'sku' => 'BEV-001',
                'barcode' => '628000100001',
                'category_id' => $categories['Beverages']->id,
                'cost' => 0.45,
                'selling_price' => 0.75,
                'quantity' => 32,
                'min_stock' => 10,
                'description' => 'Everyday bottled water.',
            ],
            [
                'name' => 'Potato Chips Large',
                'sku' => 'SNK-002',
                'barcode' => '628000100002',
                'category_id' => $categories['Snacks']->id,
                'cost' => 0.60,
                'selling_price' => 1.25,
                'quantity' => 18,
                'min_stock' => 12,
                'description' => 'Salted family-size chips.',
            ],
            [
                'name' => 'Floor Cleaner 1L',
                'sku' => 'CLN-003',
                'barcode' => '628000100003',
                'category_id' => $categories['Cleaning']->id,
                'cost' => 1.80,
                'selling_price' => 2.75,
                'quantity' => 6,
                'min_stock' => 5,
                'description' => 'Citrus scent floor cleaner.',
            ],
            [
                'name' => 'Orange Juice 1L',
                'sku' => 'BEV-004',
                'barcode' => '628000100004',
                'category_id' => $categories['Beverages']->id,
                'cost' => 1.10,
                'selling_price' => 1.85,
                'quantity' => 9,
                'min_stock' => 10,
                'description' => 'Chilled orange juice carton.',
            ],
            [
                'name' => 'Cola Can 330ml',
                'sku' => 'BEV-005',
                'barcode' => '628000100005',
                'category_id' => $categories['Beverages']->id,
                'cost' => 0.35,
                'selling_price' => 0.75,
                'quantity' => 48,
                'min_stock' => 20,
                'description' => 'Single-serve soft drink cans.',
            ],
            [
                'name' => 'Chocolate Biscuits',
                'sku' => 'SNK-006',
                'barcode' => '628000100006',
                'category_id' => $categories['Snacks']->id,
                'cost' => 0.55,
                'selling_price' => 1.10,
                'quantity' => 7,
                'min_stock' => 8,
                'description' => 'Chocolate-filled biscuit packs.',
            ],
            [
                'name' => 'Salted Peanuts',
                'sku' => 'SNK-007',
                'barcode' => '628000100007',
                'category_id' => $categories['Snacks']->id,
                'cost' => 0.80,
                'selling_price' => 1.50,
                'quantity' => 0,
                'min_stock' => 6,
                'description' => 'Roasted salted peanut snack.',
            ],
            [
                'name' => 'Dish Soap 750ml',
                'sku' => 'CLN-008',
                'barcode' => '628000100008',
                'category_id' => $categories['Cleaning']->id,
                'cost' => 1.25,
                'selling_price' => 2.10,
                'quantity' => 4,
                'min_stock' => 5,
                'description' => 'Lemon dishwashing liquid.',
            ],
            [
                'name' => 'Laundry Detergent 2L',
                'sku' => 'CLN-009',
                'barcode' => '628000100009',
                'category_id' => $categories['Cleaning']->id,
                'cost' => 3.90,
                'selling_price' => 5.95,
                'quantity' => 11,
                'min_stock' => 4,
                'description' => 'Liquid detergent for daily washing.',
            ],
            [
                'name' => 'Full Cream Milk 1L',
                'sku' => 'DRY-010',
                'barcode' => '628000100010',
                'category_id' => $categories['Dairy']->id,
                'cost' => 0.95,
                'selling_price' => 1.45,
                'quantity' => 5,
                'min_stock' => 8,
                'description' => 'Fresh milk carton.',
            ],
            [
                'name' => 'Greek Yogurt Cup',
                'sku' => 'DRY-011',
                'barcode' => '628000100011',
                'category_id' => $categories['Dairy']->id,
                'cost' => 0.70,
                'selling_price' => 1.25,
                'quantity' => 14,
                'min_stock' => 6,
                'description' => 'Single serving yogurt cup.',
            ],
            [
                'name' => 'A4 Copy Paper',
                'sku' => 'STY-012',
                'barcode' => '628000100012',
                'category_id' => $categories['Stationery']->id,
                'cost' => 3.20,
                'selling_price' => 4.80,
                'quantity' => 22,
                'min_stock' => 8,
                'description' => '500-sheet office paper ream.',
            ],
            [
                'name' => 'Blue Ballpoint Pens',
                'sku' => 'STY-013',
                'barcode' => '628000100013',
                'category_id' => $categories['Stationery']->id,
                'cost' => 0.18,
                'selling_price' => 0.45,
                'quantity' => 3,
                'min_stock' => 10,
                'description' => 'Standard blue office pens.',
            ],
            [
                'name' => 'Hand Soap 500ml',
                'sku' => 'PCR-014',
                'barcode' => '628000100014',
                'category_id' => $categories['Personal Care']->id,
                'cost' => 1.05,
                'selling_price' => 1.95,
                'quantity' => 13,
                'min_stock' => 5,
                'description' => 'Liquid hand soap pump bottle.',
            ],
            [
                'name' => 'Shampoo 400ml',
                'sku' => 'PCR-015',
                'barcode' => '628000100015',
                'category_id' => $categories['Personal Care']->id,
                'cost' => 2.60,
                'selling_price' => 4.25,
                'quantity' => 2,
                'min_stock' => 4,
                'description' => 'Daily care shampoo bottle.',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::query()->updateOrCreate(['sku' => $productData['sku']], $productData);

            if ($product->stockTransactions()->doesntExist()) {
                StockTransaction::query()->create([
                    'product_id' => $product->id,
                    'user_id' => $owner->id,
                    'type' => 'in',
                    'quantity' => $product->quantity,
                    'unit_cost' => $product->cost,
                    'note' => 'Initial stock load',
                    'quantity_before' => 0,
                    'quantity_after' => $product->quantity,
                    'created_at' => now()->subDays(4),
                    'updated_at' => now()->subDays(4),
                ]);
            }
        }

        $chips = Product::query()->where('sku', 'SNK-002')->first();

        if ($chips && $chips->stockTransactions()->count() === 1) {
            StockTransaction::query()->create([
                'product_id' => $chips->id,
                'user_id' => $staff->id,
                'type' => 'out',
                'quantity' => 4,
                'unit_cost' => null,
                'note' => 'Shelf sale',
                'quantity_before' => 18,
                'quantity_after' => 14,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]);

            $chips->update(['quantity' => 14]);
        }

        $extraMovements = [
            ['sku' => 'BEV-004', 'user_id' => $staff->id, 'type' => 'out', 'quantity' => 3, 'before' => 12, 'after' => 9, 'note' => 'Morning shelf refill and sales', 'days' => 1],
            ['sku' => 'SNK-007', 'user_id' => $staff->id, 'type' => 'out', 'quantity' => 6, 'before' => 6, 'after' => 0, 'note' => 'Sold out on front shelf', 'days' => 0],
            ['sku' => 'DRY-010', 'user_id' => $staff->id, 'type' => 'out', 'quantity' => 4, 'before' => 9, 'after' => 5, 'note' => 'Evening chilled sales', 'days' => 0],
            ['sku' => 'STY-013', 'user_id' => $owner->id, 'type' => 'out', 'quantity' => 7, 'before' => 10, 'after' => 3, 'note' => 'Bulk office request', 'days' => 2],
            ['sku' => 'PCR-015', 'user_id' => $staff->id, 'type' => 'out', 'quantity' => 2, 'before' => 4, 'after' => 2, 'note' => 'Weekend sales', 'days' => 1],
        ];

        foreach ($extraMovements as $movement) {
            $product = Product::query()->where('sku', $movement['sku'])->first();

            if (! $product) {
                continue;
            }

            $existing = StockTransaction::query()
                ->where('product_id', $product->id)
                ->where('type', $movement['type'])
                ->where('quantity_before', $movement['before'])
                ->where('quantity_after', $movement['after'])
                ->exists();

            if (! $existing) {
                StockTransaction::query()->create([
                    'product_id' => $product->id,
                    'user_id' => $movement['user_id'],
                    'type' => $movement['type'],
                    'quantity' => $movement['quantity'],
                    'unit_cost' => null,
                    'note' => $movement['note'],
                    'quantity_before' => $movement['before'],
                    'quantity_after' => $movement['after'],
                    'created_at' => now()->subDays($movement['days']),
                    'updated_at' => now()->subDays($movement['days']),
                ]);
            }
        }

        LowStockAlert::query()->delete();

        Product::query()
            ->whereColumn('quantity', '<=', 'min_stock')
            ->get()
            ->each(function (Product $product) use ($owner) {
                LowStockAlert::query()->create([
                    'product_id' => $product->id,
                    'created_by_user_id' => $owner->id,
                    'status' => 'active',
                    'threshold' => $product->min_stock,
                    'quantity_when_triggered' => $product->quantity,
                    'notified_at' => now()->subHours(6),
                ]);
            });
    }
}
