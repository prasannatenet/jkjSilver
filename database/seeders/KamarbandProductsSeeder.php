<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KamarbandProductsSeeder extends Seeder
{
    public function run(): void
    {
        $silverJewellery = Category::firstOrCreate(
            ['slug' => 'silver-jewellery'],
            ['name' => 'Silver Jewellery', 'parent_id' => null],
        );

        $kamarband = Category::firstOrCreate(
            ['slug' => 'kamarband'],
            ['name' => 'Kamarband'],
        );

        if ($kamarband->parent_id !== $silverJewellery->id) {
            $kamarband->update(['parent_id' => $silverJewellery->id]);
        }

        $products = [
            [
                'name' => 'Floral Style Silver Kamarband with Hanging Ghungroos',
                'sku' => 'JKJ-KB-001',
                'base_price' => 13265,
                'sale_price' => 12654,
                'short_description' => 'A royal-style silver kamarband with detailed floral motifs and hanging ghungroos—perfect for weddings and festive occasions.',
                'description' => <<<'DESCRIPTION'
Elevate your traditional look with this beautifully crafted royal silver kamarband. The main silver strip is intricately engraved with round floral designs, each of which connects to delicately dangling ghungroos via fine silver chains. This piece is a celebration of feminine grace and heritage, ideal for bridal wear and classical dance outfits. Crafted from pure 92.5 sterling silver, this kamarband wraps the waist elegantly and adds a rhythmic charm to your movements.

Specifications:
- Material: 92.5 hallmarked sterling silver
- Design: Continuous round floral motifs on a silver strip
- Ghungroos: Multiple hanging bells attached with fine silver chains
- Closure: Adjustable silver clasp for a comfortable fit
- Weight: Approximately 120-130 grams
- Quantity: 1 waist chain (kamarband)
DESCRIPTION,
            ],
            [
                'name' => 'Silver Floral Chain Kamarband with Ghungroo Drops',
                'sku' => 'JKJ-KB-002',
                'base_price' => 11265,
                'sale_price' => 10234,
                'short_description' => 'Traditional antique silver kamarband with circular floral motifs and dancing ghungroo chains for timeless ethnic style.',
                'description' => <<<'DESCRIPTION'
This antique silver floral chain kamarband is the definition of tradition and beauty. It features an ornate silver strip with engraved round floral designs, each flower adorned with soft-sounding ghungroos connected through silver chains. The hanging bells create a gentle jingle with every step, making this an eye-catching waist accessory. Made in 92.5 sterling silver, it blends antique charm with fine detailing, making it ideal for brides, dancers, and festive outfits.

Specifications:
- Material: Pure 92.5 sterling silver
- Design: Carved floral motifs with chain-linked ghungroo detailing
- Ghungroos: Bell-shaped silver drops connected to each floral unit
- Closure: Adjustable length with silver hook
- Weight: Approximately 110-115 grams
- Quantity: 1 kamarband piece
DESCRIPTION,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    ...$product,
                    'category_id' => $kamarband->id,
                    'audience' => 'women',
                    'material_type' => 'silver',
                    'slug' => Str::slug($product['name']),
                    'is_active' => true,
                    'product_type' => 'simple',
                    'stock' => 0,
                    'manage_stock' => false,
                ],
            );
        }
    }
}
