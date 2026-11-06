<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PayalProductsSeeder extends Seeder
{
    public function run(): void
    {
        $silverJewellery = Category::firstOrCreate(
            ['slug' => 'silver-jewellery'],
            ['name' => 'Silver Jewellery', 'parent_id' => null],
        );

        $payal = Category::firstOrCreate(
            ['slug' => 'payal'],
            ['name' => 'Payal'],
        );

        if ($payal->parent_id !== $silverJewellery->id) {
            $payal->update(['parent_id' => $silverJewellery->id]);
        }

        $products = [
            [
                'name' => 'Bridal Grand Pure Silver Payal',
                'sku' => 'JKJ-PYL-001',
                'audience' => 'women',
                'base_price' => 14699,
                'sale_price' => 13689,
                'short_description' => 'Heavy bridal silver payal with detailed patterns and sparkling pink and green diamond-style stones.',
                'description' => <<<'DESCRIPTION'
This grand silver payal is perfect for brides and wedding occasions. Designed with thick silver artistry, it features an intricate floral and vine pattern with pink and green stone embellishments. The multiple layers of silver and traditional ghungroos bring rich elegance to every step.

Specifications:
- Silver type: 92.5 hallmarked silver
- Stones: Pink and green synthetic diamonds
- Design: Traditional heavy carving with layered ghungroos
- Quantity: 1 pair (2 pieces)
DESCRIPTION,
            ],
            [
                'name' => 'Heavy Silver Payal with Colored Stone',
                'sku' => 'JKJ-PYL-002',
                'audience' => 'women',
                'base_price' => 12382,
                'sale_price' => 11289,
                'short_description' => 'Peacock-themed silver payal in a heavy design with colorful pink-green stones and bold ghungroos.',
                'description' => <<<'DESCRIPTION'
Inspired by the royal peacock design, this payal is a celebration piece crafted in pure silver. Embellished with shimmering stones in pink and green, and featuring a bold ghungroo fringe, this heavy payal is a must-have for bridal and traditional celebrations.

Specifications:
- Silver type: 92.5 sterling silver
- Design: Carved peacock motifs and floral vines
- Stones: Colored synthetic pink and green
- Quantity: Pair of 2
- Weight: Approximately 88 grams
DESCRIPTION,
            ],
            [
                'name' => 'Temple Art Silver Payal For Weddings',
                'sku' => 'JKJ-PYL-003',
                'audience' => 'women',
                'base_price' => 13289,
                'sale_price' => 11824,
                'short_description' => 'Temple-style silver payal with traditional elements and colorful diamond-style insets.',
                'description' => <<<'DESCRIPTION'
Channel divine elegance with this temple-inspired silver payal, featuring goddess motifs, deep floral carvings, and delicate pink and green accents. A grand ghungroo line enhances its rhythmic beauty for festive occasions.

Specifications:
- Silver purity: 92.5%
- Motif: Temple style with deity carvings
- Ghungroos: Bold and well-aligned
- Quantity: 2 pieces
DESCRIPTION,
            ],
            [
                'name' => 'Antique Layered Silver Payal for Womens',
                'sku' => 'JKJ-PYL-004',
                'audience' => 'women',
                'base_price' => 11299,
                'sale_price' => 10119,
                'short_description' => 'Antique-finish heavy silver payal with a multilayer design and colored stone accents.',
                'description' => <<<'DESCRIPTION'
This antique-style silver payal features a thick multilayer design enriched with tiny floral patterns and colorful stones in pink and green. The ghungroos create a bold sound, making it perfect for traditional dance or bridal wear.

Specifications:
- Silver type: Pure 92.5 silver
- Layer: Triple-layer chain design
- Stones: Micro pink and green cut stones
- Quantity: 2
DESCRIPTION,
            ],
            [
                'name' => 'Classic Floral Style Silver Payal For Wedding',
                'sku' => 'JKJ-PYL-005',
                'audience' => 'women',
                'base_price' => 13699,
                'sale_price' => 12689,
                'short_description' => 'Floral kada-style silver payal with dazzling pink and green stones for grand looks.',
                'description' => <<<'DESCRIPTION'
This bold kada-style silver payal wraps around the ankle with carved round floral disks, each centered with colored stones. Perfect for wedding lehengas and sarees.

Specifications:
- Silver type: 92.5 certified
- Stones: Pink and green diamond-cut look
- Design: Rigid kada with flexible joinery
- Quantity: Set of 2
DESCRIPTION,
            ],
            [
                'name' => 'Bridal Silver Payal with Heavy Bell',
                'sku' => 'JKJ-PYL-006',
                'audience' => 'women',
                'base_price' => 13699,
                'sale_price' => 12832,
                'short_description' => 'Thick silver bridal payal with rich bell patterns and hanging stone beads.',
                'description' => <<<'DESCRIPTION'
Designed for wedding-day glamour, this payal features multiple carved bells combined with drops of pink and green stones. Every step echoes bridal beauty and traditional melody.

Specifications:
- Silver purity: 92.5 sterling silver
- Ghungroos: Multi-bell arrangement
- Stones: Hanging pink-green drop stones
- Quantity: 2
DESCRIPTION,
            ],
            [
                'name' => 'Festival Pure Silver Payal For Women',
                'sku' => 'JKJ-PYL-007',
                'audience' => 'women',
                'base_price' => 14699,
                'sale_price' => 13689,
                'short_description' => 'Disc-pattern silver payal with colorful stone centerpieces and heavy bell accents.',
                'description' => <<<'DESCRIPTION'
Celebrate festivals in full style with this disc-pattern silver payal. Each floral disc has a radiant pink or green stone at the center, all connected to heavy bells for a festive sound.

Specifications:
- Silver type: 92.5 hallmark
- Design: Disc and chain design
- Stones: Alternating pink and green centers
- Quantity: 1 pair
DESCRIPTION,
            ],
            [
                'name' => 'Vintage Pure Silver Bridal Payal',
                'sku' => 'JKJ-PYL-008',
                'audience' => 'women',
                'base_price' => 11882,
                'sale_price' => 10622,
                'short_description' => 'Vintage-style silver payal rich in curves, carvings, and multicolor sparkle.',
                'description' => <<<'DESCRIPTION'
This payal is a vintage treasure featuring detailed silver work and a balanced blend of pink and green mini-stones. The wide base sits comfortably on the ankle, ideal for bridal functions.

Specifications:
- Silver type: 92.5
- Stones: Alternating dual-color
- Ghungroos: Bold and antique-finish
- Quantity: 2
DESCRIPTION,
            ],
            [
                'name' => 'Ethnic Marwari Style Payal with Diamond Touch',
                'sku' => 'JKJ-PYL-009',
                'audience' => 'women',
                'base_price' => 13700,
                'sale_price' => 12323,
                'short_description' => 'Inspired by Marwari tradition, this heavy silver payal glitters with pink and green highlights.',
                'description' => <<<'DESCRIPTION'
Designed with Rajasthan's regal charm, this Marwari-style silver payal features multi-petal floral designs with shining pink-green stone dots and thick ghungroo strings.

Specifications:
- Silver type: 92.5 purity
- Stones: Miniature pink and green cut stones
- Quantity: 2
DESCRIPTION,
            ],
            [
                'name' => 'Bridal Pure Silver Payal with Royal Colors',
                'sku' => 'JKJ-PYL-010',
                'audience' => 'women',
                'base_price' => 12399,
                'sale_price' => 11492,
                'short_description' => 'Heavily decorated royal silver payal with colored floral diamonds for grand weddings.',
                'description' => <<<'DESCRIPTION'
Crafted for queens, this payal has bold floral patterns, each filled with pink and green stones and surrounded by thick, round bells. It is an excellent choice for weddings and classical occasions.

Specifications:
- Silver type: 92.5 hallmarked
- Design: Royal floral arrangement
- Quantity: 1 pair
DESCRIPTION,
            ],
            [
                'name' => 'Light Silver Floral Payal For Girls',
                'sku' => 'JKJ-PYL-011',
                'audience' => 'girl',
                'base_price' => 8764,
                'sale_price' => 7345,
                'short_description' => 'Light and lovely floral payal with colorful enamel accents and hanging bells.',
                'description' => <<<'DESCRIPTION'
This elegant payal features enamel-filled silver flowers in vibrant shades, each connected with fine chains and silver ghungroos. Lightweight and youthful, it is ideal for festive and daily use.

Specifications:
- Silver type: 92.5
- Floral enamel: Multicolor (red, green, blue)
- Ghungroos: Small hanging bells
- Quantity: 2 pieces
DESCRIPTION,
            ],
            [
                'name' => 'Colorful Flower Drop Silver Payal',
                'sku' => 'JKJ-PYL-012',
                'audience' => 'both',
                'base_price' => 8456,
                'sale_price' => 7658,
                'short_description' => 'Chain-style silver payal with delicate hanging floral drops and bells.',
                'description' => <<<'DESCRIPTION'
Featuring silver chains with tiny colored floral pendants and jingling bells, this lightweight payal adds joyful color to your look. Ideal for girls and young women.

Specifications:
- Silver purity: 92.5
- Flowers: Hanging design with pastel enamel
- Ghungroos: Dainty and bright-sounding
- Quantity: Pair of 2
DESCRIPTION,
            ],
            [
                'name' => 'Rainbow Floral Silver Payal for Girls',
                'sku' => 'JKJ-PYL-013',
                'audience' => 'girl',
                'base_price' => 6858,
                'sale_price' => 5789,
                'short_description' => 'Charming lightweight silver payal with rainbow floral charms and mini bells.',
                'description' => <<<'DESCRIPTION'
With multi-hued flowers strung along a sleek chain, this silver payal dances with vibrant charm. Each floral unit hangs freely with a soft ghungroo below, creating gentle music as you walk.

Specifications:
- Silver type: Pure 92.5
- Colors: Rainbow floral accents
- Bells: Soft-tone ghungroos
- Quantity: 2 payals
DESCRIPTION,
            ],
            [
                'name' => 'Pink-Green Floral Charm Light Silver Payal',
                'sku' => 'JKJ-PYL-014',
                'audience' => 'women',
                'base_price' => 7865,
                'sale_price' => 6899,
                'short_description' => 'Minimal silver payal with alternating pink and green floral drops and chain bells.',
                'description' => <<<'DESCRIPTION'
Celebrate minimal beauty with this pink and green-themed floral silver payal. Each flower charm hangs via a silver loop, ending with mini ghungroos for rhythmic sound.

Specifications:
- Silver type: 92.5
- Floral charms: Pink and green alternates
- Chains: Flexible silver chain base
- Quantity: Pair
- Weight: 36 grams
DESCRIPTION,
            ],
            [
                'name' => 'Pure Silver Payal For Girls & Womens',
                'sku' => 'JKJ-PYL-015',
                'audience' => 'both',
                'base_price' => 6878,
                'sale_price' => 5890,
                'short_description' => 'Perfect daily-wear silver payal with colorful hanging flowers and bells.',
                'description' => <<<'DESCRIPTION'
Lightweight and playful, this silver payal includes multicolor hanging floral charms, each paired with a tiny ghungroo. A soft-toned, elegant piece for everyday wear or small celebrations.

Specifications:
- Silver type: Hallmarked 92.5
- Design: Multicolor flower hangings
- Quantity: Set of 2
- Weight: 32-34 grams
DESCRIPTION,
            ],
            [
                'name' => 'Antique Silver Chain Payal for Girls',
                'sku' => 'JKJ-PYL-016',
                'audience' => 'girl',
                'base_price' => 8942,
                'sale_price' => 7543,
                'short_description' => 'Simple, stylish antique silver chain payal with a thin, graceful design—perfect for ethnic wear.',
                'description' => <<<'DESCRIPTION'
Designed for elegance and simplicity, this pure silver payal features a sleek chain style with a subtle antique finish. Lightweight and classy, it is ideal for daily wear or ethnic outfits. Crafted in 92.5 sterling silver, this payal adds a soft charm to your ankles.

Specifications:
- Material: 92.5 sterling silver
- Design: Thin antique silver chain
- Weight: Approximately 22-24 grams
- Quantity: 1 pair (2 payals)
- Ideal for: Girls and women
DESCRIPTION,
            ],
            [
                'name' => 'Chain Style Silver Payal for Festivals',
                'sku' => 'JKJ-PYL-017',
                'audience' => 'women',
                'base_price' => 7689,
                'sale_price' => 6890,
                'short_description' => 'A minimal silver chain payal with a classic touch for everyday and festive looks.',
                'description' => <<<'DESCRIPTION'
This thin silver payal is delicately handcrafted in a traditional chain pattern that offers versatility with ethnic and casual outfits. The antique finish gives it a timeless look while remaining light on the feet. Made from genuine 92.5 silver, it is a graceful addition to any jewellery box.

Specifications:
- Material: 92.5 sterling silver
- Design: Chain style with antique finish
- Weight: Approximately 23-25 grams
- Quantity: 1 pair
- Ideal for: Weddings and daily ethnic wear
DESCRIPTION,
            ],
            [
                'name' => 'Antique Silver Chain Payal for Women',
                'sku' => 'JKJ-PYL-018',
                'audience' => 'both',
                'base_price' => 8224,
                'sale_price' => 6889,
                'short_description' => 'Beautiful thin chain payal in antique silver finish, perfect for subtle traditional style.',
                'description' => <<<'DESCRIPTION'
Crafted for elegance and comfort, this thin chain payal has a lightly textured antique design that suits all ages. Whether for a festive occasion or a family gathering, this piece adds a soft jingle and traditional charm to your look.

Specifications:
- Material: 92.5 sterling silver
- Design: Simple chain with a traditional finish
- Weight: Approximately 22 grams
- Quantity: 1 pair
- Suitable for: Girls and women
DESCRIPTION,
            ],
            [
                'name' => 'Traditional Thin Silver Chain Payal',
                'sku' => 'JKJ-PYL-019',
                'audience' => 'women',
                'base_price' => 7998,
                'sale_price' => 6899,
                'short_description' => 'Classic and elegant silver chain payal made for wedding and festive ethnic wear.',
                'description' => <<<'DESCRIPTION'
This pure silver chain payal offers a beautiful balance of elegance and tradition. Designed with a medium-thin chain in antique style, this pair fits comfortably and looks graceful with traditional attire. The high-quality 92.5 silver ensures durability and purity.

Specifications:
- Material: Pure 92.5 sterling silver
- Design: Thin chain style with antique texture
- Weight: Approximately 23 grams
- Quantity: 1 pair
- Ideal for: Ethnic and festive wear
DESCRIPTION,
            ],
            [
                'name' => 'Ethnic Silver Payal for Girls & Women',
                'sku' => 'JKJ-PYL-020',
                'audience' => 'both',
                'base_price' => 7823,
                'sale_price' => 6784,
                'short_description' => 'Antique chain-style silver payal with a simple and sleek look for traditional occasions.',
                'description' => <<<'DESCRIPTION'
This pair of silver payals comes in a lightweight chain design, ideal for young girls or women who love simple yet stylish jewellery. The antique finish complements ethnic outfits like sarees, lehengas, or salwars.

Specifications:
- Material: 92.5 sterling silver
- Design: Medium-thin chain with vintage finish
- Weight: Approximately 24 grams
- Quantity: 1 pair
- Suitable for: Festivals and weddings
DESCRIPTION,
            ],
            [
                'name' => 'Peacock Motif Silver Payal For Girls',
                'sku' => 'JKJ-PYL-021',
                'audience' => 'girl',
                'base_price' => 8990,
                'sale_price' => 7354,
                'short_description' => 'Unique peacock design on the lock side of a silver payal adds tradition and charm.',
                'description' => <<<'DESCRIPTION'
This stunning silver payal features a detailed peacock motif on the lock side, giving it a regal and festive appearance. Made in pure sterling silver, the chain itself is fine and antique-styled, while the peacock adds an elegant focal point. Perfect for weddings or cultural events.

Specifications:
- Material: 92.5 sterling silver
- Design: Thin antique chain with peacock-shaped lock detailing
- Weight: Approximately 25 grams
- Quantity: 1 pair
- Highlight: Intricate peacock design
DESCRIPTION,
            ],
            [
                'name' => 'Silver Butterfly Payal with Delicate Chain Series',
                'sku' => 'JKJ-PYL-022',
                'audience' => 'girl',
                'base_price' => 7896,
                'sale_price' => 6897,
                'short_description' => 'Beautifully crafted silver payal with a series of butterflies for a whimsical ethnic look.',
                'description' => <<<'DESCRIPTION'
Let your style take flight with this elegant silver payal featuring a sequence of tiny butterfly motifs across a delicate antique chain. Made from 92.5 sterling silver, this design is charming and playful, perfect for young girls and festive wear.

Specifications:
- Material: Pure 92.5 silver
- Design: Chain payal with a series of butterfly elements
- Weight: Approximately 26 grams
- Quantity: 1 pair
- Ideal for: Girls, festive looks, mehendi, and haldi
DESCRIPTION,
            ],
            [
                'name' => 'Floral Silver Payal with Pink & Green Stones',
                'sku' => 'JKJ-PYL-023',
                'audience' => 'women',
                'base_price' => 6889,
                'sale_price' => 5896,
                'short_description' => 'Thin chain payal adorned with pink and green floral stone designs—vibrant and elegant.',
                'description' => <<<'DESCRIPTION'
Add color to your traditional outfit with this pure silver payal featuring small floral motifs studded with pink and green stones. Set on a fine antique-style chain, it adds shimmer and tradition to any ethnic ensemble.

Specifications:
- Material: 92.5 sterling silver
- Design: Floral motifs with colored stones on a thin chain
- Stones: Pink and green
- Weight: Approximately 27 grams
- Quantity: 1 pair
- Suitable for: Weddings, sangeet, and cultural events
DESCRIPTION,
            ],
            [
                'name' => 'Peacock Design Silver Payal for Girls',
                'sku' => 'JKJ-PYL-024',
                'audience' => 'girl',
                'base_price' => 8964,
                'sale_price' => 7582,
                'short_description' => 'Peacock-inspired silver payal with colorful stones—a festive and royal ankle accessory.',
                'description' => <<<'DESCRIPTION'
This vibrant payal features a beautiful peacock motif enhanced with multicolored stones, creating a royal and eye-catching appearance. Made from pure sterling silver, this elegant piece rests on a thin antique-style chain and is perfect for brides or cultural dancers.

Specifications:
- Material: 92.5 sterling silver
- Design: Peacock design with colorful stonework on chain
- Weight: Approximately 28 grams
- Quantity: 1 pair
- Ideal for: Weddings, temple festivals, and cultural occasions
DESCRIPTION,
            ],
            [
                'name' => 'Pure Silver Chain Payal for Festivals',
                'sku' => 'JKJ-PYL-025',
                'audience' => 'both',
                'base_price' => 8235,
                'sale_price' => 7256,
                'short_description' => 'Sleek silver payal in antique finish with timeless style for daily or festive wear.',
                'description' => <<<'DESCRIPTION'
Made for simplicity and elegance, this antique silver payal features a clean chain design that can be worn daily or on special occasions. Lightweight and finely crafted in pure 92.5 silver, it is a lovely gift for women and girls.

Specifications:
- Material: 92.5 sterling silver
- Design: Thin antique-style chain
- Weight: Approximately 23 grams
- Quantity: 1 pair
DESCRIPTION,
            ],
            [
                'name' => 'Lightweight Silver Chain Payal for Girls',
                'sku' => 'JKJ-PYL-026',
                'audience' => 'girl',
                'base_price' => 8948,
                'sale_price' => 7549,
                'short_description' => 'Charming and minimal silver payal in antique chain design, crafted for young girls.',
                'description' => <<<'DESCRIPTION'
This simple yet pretty silver payal is perfect for girls who love tradition with a touch of grace. The antique-finish chain is not too thick, making it easy and light to wear all day long.

Specifications:
- Material: Pure 92.5 silver
- Design: Lightweight antique chain
- Weight: Approximately 22 grams
- Quantity: 1 pair
DESCRIPTION,
            ],
            [
                'name' => 'Ethnic Silver Payal for Festivals',
                'sku' => 'JKJ-PYL-027',
                'audience' => 'both',
                'base_price' => 8568,
                'sale_price' => 7348,
                'short_description' => 'Thin silver chain payal with traditional charm, ideal for festivals and family functions.',
                'description' => <<<'DESCRIPTION'
Add a subtle sparkle to your ankles with this fine chain silver payal. Featuring a gentle antique polish, this piece complements lehengas, anarkalis, and sarees beautifully. A timeless gift for women and young girls alike.

Specifications:
- Material: 92.5 sterling silver
- Design: Medium-thin chain style
- Weight: Approximately 23 grams
- Quantity: 1 pair
DESCRIPTION,
            ],
            [
                'name' => 'Simple Antique Chain Payal with Ethnic Touch',
                'sku' => 'JKJ-PYL-028',
                'audience' => 'women',
                'base_price' => 7865,
                'sale_price' => 6558,
                'short_description' => 'Basic silver chain payal in antique look—elegant and easy to wear with ethnic dresses.',
                'description' => <<<'DESCRIPTION'
Crafted in pure 92.5 silver, this basic chain-style payal carries a traditional charm and lightweight design. Ideal for those who love a graceful and uncomplicated look for weddings and festivals.

Specifications:
- Material: 92.5 sterling silver
- Design: Classic antique silver chain
- Weight: Approximately 22 grams
- Quantity: 1 pair
DESCRIPTION,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    ...$product,
                    'category_id' => $payal->id,
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
