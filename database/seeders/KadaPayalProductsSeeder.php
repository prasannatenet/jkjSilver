<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KadaPayalProductsSeeder extends Seeder
{
    public function run(): void
    {
        $silverJewellery = Category::firstOrCreate(
            ['slug' => 'silver-jewellery'],
            ['name' => 'Silver Jewellery', 'parent_id' => null],
        );

        $kadaPayal = Category::firstOrCreate(
            ['slug' => 'kada-payal'],
            ['name' => 'Kada Payal'],
        );

        if ($kadaPayal->parent_id !== $silverJewellery->id) {
            $kadaPayal->update(['parent_id' => $silverJewellery->id]);
        }

        $products = [
            [
                'name' => 'Floral Design Heavy Weight Silver Kada Payal',
                'sku' => 'JKJ-KP-001',
                'base_price' => 17842,
                'sale_price' => 15284,
                'short_description' => 'Elegant traditional kada-style payal in 92.5 sterling silver, with floral detailing and musical ghungroo bells for festive and wedding wear.',
                'description' => <<<'DESCRIPTION'
Add a royal touch to your traditional look with this beautiful heavy kada payal made from 92.5% pure silver. Crafted with care, it features a thick kada-style band with a unique floral pattern engraved along the edges. Hanging silver ghungroo bells add a classic musical charm, perfect for weddings, festivals, and special occasions.

Made for women who love both tradition and style, this silver payal is strong, durable, and designed to stand out. It fits comfortably around the ankle and has a secure clasp. The 92.5% silver purity ensures lasting shine and quality.

Product Features:
- Made from 92.5 sterling silver
- Heavy kada design with floral detailing
- Dangling ghungroos that make a soft sound while walking
- Perfect for weddings, festive wear, or gifting
- Secure and comfortable clasp fitting
- Crafted for elegance, tradition, and strength
DESCRIPTION,
            ],
            [
                'name' => "Antique Silver Kada Payal for Women's with Layered Ghungroos",
                'sku' => 'JKJ-KP-002',
                'base_price' => 16259,
                'sale_price' => 15486,
                'short_description' => 'Handcrafted 92.5 sterling silver kada payal with layered ghungroos and floral-peacock motifs for a rich ethnic look.',
                'description' => <<<'DESCRIPTION'
Bring home the charm of royal Rajasthan with this beautifully handcrafted silver kada payal for women. Made from high-quality silver, this kada payal features a bold bangle-style design with multiple layers of tiny silver ghungroos that create a soft jingling sound with every step. The kada is carved with floral and peacock patterns, giving it a rich ethnic look perfect for weddings, festivals, or traditional attire. The design offers a blend of strength and elegance.

Product Specifications:
- Material: 92.5 sterling silver
- Quantity: 1 pair (2 pieces)
- Ghungroo: Multiple silver-toned hanging bells
- Design: Traditional handcrafted motifs on kada
DESCRIPTION,
            ],
            [
                'name' => 'Silver Ghungroo Kada Payal for Women',
                'sku' => 'JKJ-KP-003',
                'base_price' => 18957,
                'sale_price' => 16268,
                'short_description' => 'Beautifully handcrafted silver kada payal with hanging ghungroos, perfect for traditional and festive wear.',
                'description' => <<<'DESCRIPTION'
Add grace to your ankles with this elegant silver kada payal featuring multiple hanging ghungroos. The kada is designed with traditional patterns that highlight Indian craftsmanship. Made from high-quality 92.5 sterling silver, this payal jingles sweetly with every step.

Specifications:
- Material: 92.5 sterling silver
- Quantity: 1 pair (2 pieces)
- Ghungroo: Multiple silver-toned hanging bells
- Design: Traditional handcrafted motifs on kada
DESCRIPTION,
            ],
            [
                'name' => 'Antique Silver Kada Payal with Tiny Ghungroos',
                'sku' => 'JKJ-KP-004',
                'base_price' => 17957,
                'sale_price' => 16682,
                'short_description' => 'Vintage-inspired silver kada payal with delicate vine engraving and tiny bells for a graceful sound.',
                'description' => <<<'DESCRIPTION'
This royal antique silver kada payal for women showcases a beautifully engraved surface with fine detailing. The kada features numerous tiny ghungroos that produce a soft, melodious chime. Crafted from 92.5 silver, it offers a timeless ethnic look ideal for weddings and special occasions.

Specifications:
- Material: Premium 92.5 sterling silver
- Quantity: Set of 2
- Bells: Small, multi-layered ghungroos
- Design: Antique finish with vine engraving
DESCRIPTION,
            ],
            [
                'name' => 'Royal Charm Silver Kada Payal with Colorful Ghungroos',
                'sku' => 'JKJ-KP-005',
                'base_price' => 19257,
                'sale_price' => 15282,
                'short_description' => 'Exquisite silver kada payal with three-ghungroo sets, including colorful red and green accents, for a royal festive touch.',
                'description' => <<<'DESCRIPTION'
Enhance your ethnic wear with this royal charm silver kada payal featuring a unique three-ghungroo design on each side. With two bells made of pure silver and one crafted from red and green colored glass, the payal jingles with elegance and style. Designed for traditional occasions, this 92.5 silver kada blends heritage with modern aesthetics.

Specifications:
- Material: 92.5 sterling silver
- Quantity: 1 pair (2 payals)
- Ghungroo: 3 per cluster - 2 silver, 1 red/green glass
- Design: Traditional band with floral motifs and glass charm
DESCRIPTION,
            ],
            [
                'name' => 'Royal Silver Zig-Zag Pattern Kada Payal for Women',
                'sku' => 'JKJ-KP-006',
                'base_price' => 16992,
                'sale_price' => 15124,
                'short_description' => 'Stylish silver kada payal featuring a detailed zig-zag pattern and royal shine, suited to modern and traditional outfits.',
                'description' => <<<'DESCRIPTION'
Elevate your style with this premium silver kada payal designed with a stunning zig-zag pattern that exudes elegance and strength. Unlike traditional payals with hanging ghungroos, this design keeps it clean and classy while making a statement. Crafted from authentic 92.5 sterling silver, this kada payal sits gracefully on the ankle and is suitable for parties, weddings, or everyday elegance. Its knotted ponytail-inspired texture gives it a royal, contemporary edge.

Specifications:
- Material: 92.5 certified sterling silver
- Quantity: 1 piece (single kada payal)
- Design: Zig-zag/knotted pony pattern across the kada
- Style: No ghungroos; detailed, minimal design
- Weight: Approximately 35-40 grams
- Finish: Polished silver with oxidized detailing

This kada payal is ideal for women who love to blend tradition with modernity and makes a thoughtful gift for weddings, anniversaries, or personal collections.
DESCRIPTION,
            ],
            [
                'name' => 'Royal Peacock Silver Kada Payal',
                'sku' => 'JKJ-KP-007',
                'base_price' => 17984,
                'sale_price' => 15246,
                'short_description' => 'Royal silver kada payal with single ghungroos, dotted detailing, and an intricate peacock-shaped clasp.',
                'description' => <<<'DESCRIPTION'
Step into elegance with this Royal Peacock Silver Kada Payal, designed to add timeless charm to your ankles. Single hanging ghungroos placed all around create a subtle, graceful chime. A unique dotted pattern etched on the kada adds texture, while the detailed peacock-shaped lock makes this payal a distinctive piece.

Crafted in 92.5 sterling silver, this kada payal balances tradition with trend and suits festive and modern occasions.

Specifications:
- Material: 92.5 pure sterling silver
- Quantity: Set of 2 (one pair)
- Ghungroos: Single-line silver bells placed evenly around the kada
- Design: Dotted texture with an elegant finish
- Lock style: Intricately crafted peacock-shaped clasp
DESCRIPTION,
            ],
            [
                'name' => "Plain Silver Kada Payal for Women's",
                'sku' => 'JKJ-KP-008',
                'base_price' => 14732,
                'sale_price' => 13251,
                'short_description' => 'Elegant polished silver kada payal with evenly spaced single ghungroos for a royal, minimal look.',
                'description' => <<<'DESCRIPTION'
This royal plain kada payal is crafted in pure 92.5 sterling silver and features a clean, sleek finish. Single ghungroos are placed delicately around the kada, giving it a rich and graceful appeal. Perfect for women who love understated traditional beauty with a modern twist.

Specifications:
- Material: 92.5 sterling silver
- Quantity: 1 pair (2 pieces)
- Ghungroos: Evenly spaced single silver bells
- Design: Plain kada with a polished finish
DESCRIPTION,
            ],
            [
                'name' => 'Spiral Silver Kada Payal for Women',
                'sku' => 'JKJ-KP-009',
                'base_price' => 13861,
                'sale_price' => 12280,
                'short_description' => 'Classy spiral-pattern silver kada payal without ghungroos, for women who prefer a simple royal touch.',
                'description' => <<<'DESCRIPTION'
Step into elegance with this Royal Spiral Silver Kada Payal, a blend of simplicity and grace. Crafted from 92.5 sterling silver, this kada payal features a smooth spiral pattern that wraps around your ankle in a clean, stylish form. With no hanging ghungroos, it offers a silent, elegant charm that suits both modern and ethnic outfits. The minimalist design is ideal for daily wear or special occasions.

Specifications:
- Material: 92.5 hallmarked sterling silver
- Quantity: 1 pair (2 pieces)
- Design: Simple spiral engraving with a polished finish
- Style: Kada-style payal without bells

This kada payal is a great choice for women who love sleek accessories with a royal traditional touch.
DESCRIPTION,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    ...$product,
                    'category_id' => $kadaPayal->id,
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
