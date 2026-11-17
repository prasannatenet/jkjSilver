<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PendantsProductsSeeder extends Seeder
{
    public function run(): void
    {
        $silverJewellery = Category::firstOrCreate(
            ['slug' => 'silver-jewellery'],
            ['name' => 'Silver Jewellery', 'parent_id' => null]
        );

        $pendants = Category::firstOrCreate(
            ['slug' => 'pendants'],
            ['name' => 'Pendants', 'parent_id' => $silverJewellery->id]
        );

        if ($pendants->parent_id !== $silverJewellery->id) {
            $pendants->update(['parent_id' => $silverJewellery->id]);
        }

        $products = [
            [
                'name' => 'Divine Shiv Murti Silver Pendant with Rudraksh',
                'sku' => 'JKJ-RP-001',
                'base_price' => 4566.00,
                'sale_price' => 3689.00,
                'short_description' => 'A circular silver pendant featuring a 3D Lord Shiva murti, encircled with rudraksh beads and layered silver chains for a powerful spiritual look.',
                'description' => 'Channel the energy of Lord Shiva with this exquisitely crafted circular silver pendant. At the heart of the pendant lies a detailed 3D Shiva murti, symbolizing peace and divine power. Surrounding the murti is a fine ring of small, original rudraksh beads, believed to enhance spiritual energy. This rudraksh circle is further adorned by three concentric layers of intricate silver chains that add depth and movement to the piece. Made from 92.5 sterling silver, this pendant is a symbol of devotion and timeless craftsmanship.',
                'material_type' => 'silver',
                'category_id' => $pendants->id,
                'product_type' => 'simple',
                'is_active' => true,
                'stock' => 0,
                'manage_stock' => false,
                'slug' => Str::slug('Divine Shiv Murti Silver Pendant with Rudraksh'),
            ],
            [
                'name' => 'Shree Ram 3D Silver Pendant',
                'sku' => 'JKJ-RP-002',
                'base_price' => 4281.00,
                'sale_price' => 3344.00,
                'short_description' => 'A vertical rectangular silver pendant showcasing a full 3D idol of Shree Ram, bordered with a silver chain accented with sparkling small diamonds.',
                'description' => 'Celebrate the purity and valor of Lord Ram with this finely detailed rectangular silver pendant. The pendant features a full 3D sculpture of Shree Ram, standing tall in divine posture. Framed by a sleek silver border, the rectangle is enhanced by a single silver chain that wraps around the frame, inlaid with tiny shimmering diamonds. This pendant is perfect for spiritual wearers looking for elegance and symbolism in one statement piece. Crafted in hallmarked 92.5 sterling silver, it carries both religious significance and luxurious design.',
                'material_type' => 'silver',
                'category_id' => $pendants->id,
                'product_type' => 'simple',
                'is_active' => true,
                'stock' => 0,
                'manage_stock' => false,
                'slug' => Str::slug('Shree Ram 3D Silver Pendant'),
            ],
            [
                'name' => 'Hanuman Ji Silver Pendant with Diamond Detailing',
                'sku' => 'JKJ-RP-003',
                'base_price' => 4392.00,
                'sale_price' => 3498.00,
                'short_description' => 'Circular silver pendant featuring a detailed Hanuman ji face engraved in silver and highlighted with sparkling diamond accents.',
                'description' => 'Invoke courage and strength with this circular pendant featuring a finely detailed silver face of Lord Hanuman. The face is raised and sculpted in silver with expressive eyes, symbolizing protection and unwavering devotion. The outer layer of the pendant is decorated with subtle small diamonds that enhance the divine presence with a sparkling aura. Made from 92.5 sterling silver, this pendant is a spiritual charm and a standout accessory for daily wear or religious events.',
                'material_type' => 'silver',
                'category_id' => $pendants->id,
                'product_type' => 'simple',
                'is_active' => true,
                'stock' => 0,
                'manage_stock' => false,
                'slug' => Str::slug('Hanuman Ji Silver Pendant with Diamond Detailing'),
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                array_merge($product, [
                    'category_id' => $pendants->id,
                    'material_type' => 'silver',
                    'slug' => $product['slug'],
                    'is_active' => true,
                    'product_type' => 'simple',
                    'stock' => 0,
                    'manage_stock' => false,
                ])
            );
        }

        $this->command->info('✅ 3 Pendant products created.');
    }
}