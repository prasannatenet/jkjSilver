<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ToeRingProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Parent Category
        $silverJewellery = Category::firstOrCreate(
            ['slug' => 'silver-jewellery'],
            ['name' => 'Silver Jewellery', 'parent_id' => null]
        );

        // Child Category
        $toeRing = Category::firstOrCreate(
            ['slug' => 'toe-ring'],
            ['name' => 'Toe Ring', 'parent_id' => $silverJewellery->id]
        );

        if ($toeRing->parent_id != $silverJewellery->id) {
            $toeRing->update(['parent_id' => $silverJewellery->id]);
        }

        // All 15 Toe Ring Products
        $products = [];

        // Product 1
        $products[] = [
            'name' => 'Floral Curve Silver Toe Rings for Women',
            'sku' => 'JKJ-TR-001',
            'base_price' => 1299.00,
            'sale_price' => 1048.00,
            'short_description' => 'Stylish pure silver toe rings with elegant curved design and floral texture—lightweight and timeless.',
            'description' => 'Add a graceful touch to your toes with these elegant curve silver toe rings, crafted from pure 92.5 silver. The design features soft curves and light floral etching, making them a perfect daily wear accessory for modern women who love tradition with style.',
            'specifications' => [
                'Material' => 'Pure 92.5 Sterling Silver',
                'Quantity' => '1 Pair (2 toe rings)',
                'Design' => 'Soft curved structure with floral texture',
                'Weight' => 'Approx. 6 grams',
                'Size' => 'Adjustable fit',
            ],
        ];

        // Product 2
        $products[] = [
            'name' => 'Twisted Vine Pure Silver Toe Rings',
            'sku' => 'JKJ-TR-002',
            'base_price' => 1162.00,
            'sale_price' => 964.00,
            'short_description' => 'Lightweight twisted vine-style silver toe rings, ideal for festive and daily wear.',
            'description' => 'Inspired by nature, these toe rings showcase a twisted vine-like pattern, etched finely on sterling silver. Delicate and lightweight, they add subtle charm to your feet. Ideal for daily use, gifting, or traditional occasions.',
            'specifications' => [
                'Material' => '92.5 Silver',
                'Quantity' => 'Pair of 2',
                'Design' => 'Twisted vine detailing with smooth finish',
                'Weight' => 'Approx. 5.5 grams',
                'Size' => 'Adjustable',
            ],
        ];

      
        // Product 3
        $products[] = [
            'name' => 'Minimal Leaf Motif Silver Toe Ring For Women',
            'sku' => 'JKJ-TR-003',
            'base_price' => 1182.00,
            'sale_price' => 1022.00,
            'short_description' => 'Minimalist toe rings with small silver leaves, perfect for subtle ethnic styling.',
            'description' => 'These silver toe rings are adorned with fine leaf motifs, bringing elegance and simplicity together. Crafted in pure silver, the design wraps gently around the toe, giving a clean yet detailed look ideal for light occasions and daily comfort.',
            'specifications' => [
                'Silver Type' => '92.5 Sterling Silver',
                'Quantity' => '2 toe rings',
                'Design' => 'Leaf patterned detailing',
                'Weight' => 'Approx. 5-6 grams',
                'Size' => 'Adjustable',
            ],
        ];

        // Product 4
        $products[] = [
            'name' => 'Silver Toe Rings with Petal Wrap Design',
            'sku' => 'JKJ-TR-004',
            'base_price' => 1289.00,
            'sale_price' => 1068.00,
            'short_description' => 'Chic silver toe rings with a petal-inspired wrap-around look—simple yet stylish.',
            'description' => 'These toe rings feature overlapping petal shapes that wrap gracefully around the toe. The light floral touch and lightweight sterling silver make them a stylish choice for both casual and traditional wear.',
            'specifications' => [
                'Material' => '92.5 Pure Silver',
                'Quantity' => 'Set of 2',
                'Design' => 'Overlapping petal look with light texture',
                'Weight' => 'Approx. 6 grams',
                'Size' => 'Flexible and adjustable',
            ],
        ];

        // Product 5
        $products[] = [
            'name' => 'Dotted Silver Floral Toe Rings (Pair)',
            'sku' => 'JKJ-TR-005',
            'base_price' => 1362.00,
            'sale_price' => 1076.00,
            'short_description' => 'Classic dotted design with floral circles on lightweight silver toe rings.',
            'description' => 'Crafted in pure silver, these toe rings feature small dotted circles arranged in a floral pattern. The subtle symmetry and shine make them suitable for everyday elegance and cultural events alike.',
            'specifications' => [
                'Material' => '92.5 Hallmarked Silver',
                'Quantity' => '2 toe rings',
                'Design' => 'Dotted floral circles',
                'Weight' => 'Approx. 5 grams',
                'Size' => 'One size adjustable',
            ],
        ];

        // Product 6
        $products[] = [
            'name' => 'Majestic Peacock Design Silver Toe Rings',
            'sku' => 'JKJ-TR-006',
            'base_price' => 1362.00,
            'sale_price' => 1142.00,
            'short_description' => 'Exquisite peacock-designed silver toe rings that reflect traditional Indian charm.',
            'description' => 'Bring tradition to your toes with this majestic pair of toe rings featuring a detailed peacock motif. Handcrafted in 92.5 sterling silver, these rings capture the beauty of Indian artistry with a graceful and symbolic design.',
            'specifications' => [
                'Material' => '92.5 Sterling Silver',
                'Quantity' => '1 Pair',
                'Design' => 'Engraved peacock with floral tail detailing',
                'Weight' => 'Approx. 6 grams',
                'Fit' => 'Adjustable band',
            ],
        ];

        // Product 7
        $products[] = [
            'name' => 'Floral Silver Toe Rings with Green Diamonds',
            'sku' => 'JKJ-TR-007',
            'base_price' => 1244.00,
            'sale_price' => 1072.00,
            'short_description' => 'Stylish toe rings with floral center and 4 small green diamonds—vibrant and classy.',
            'description' => 'These toe rings feature a charming floral centerpiece inlaid with 4 small sparkling green diamond stones. The bright pop of color on pure silver adds a festive and vibrant touch to your look.',
            'specifications' => [
                'Material' => '92.5 Silver with Green CZ stones',
                'Quantity' => 'Pair (2 rings)',
                'Design' => 'Floral base with 4 green diamond accents',
                'Weight' => 'Approx. 6.5 grams',
                'Size' => 'Adjustable fit',
            ],
        ];

        // Product 8
        $products[] = [
            'name' => 'Pink Diamonds Floral Silver Toe Rings',
            'sku' => 'JKJ-TR-008',
            'base_price' => 1262.00,
            'sale_price' => 1082.00,
            'short_description' => 'Elegant floral toe rings with sparkling pink diamond accents—light, pretty, and perfect for festive styling.',
            'description' => 'Crafted in pure silver, these toe rings showcase a floral design in the center adorned with 4 tiny pink diamonds. The color blend of silver and pink makes them an eye-catching pair for special occasions.',
            'specifications' => [
                'Material' => '92.5 Sterling Silver',
                'Quantity' => '1 Pair',
                'Design' => 'Central floral pattern with 4 pink CZ stones',
                'Weight' => 'Approx. 6 grams',
                'Fit' => 'Adjustable',
            ],
        ];

        // Product 9
        $products[] = [
            'name' => 'Engraved Lotus Toe Rings in Silver',
            'sku' => 'JKJ-TR-009',
            'base_price' => 1162.00,
            'sale_price' => 1024.00,
            'short_description' => 'Elegant lotus-inspired silver toe rings with etched petal details and light floral beauty.',
            'description' => 'These toe rings are inspired by the lotus flower, representing purity and grace. The petals are finely engraved on a round band, and the lightweight design ensures daily comfort with traditional charm.',
            'specifications' => [
                'Silver Type' => '92.5 Sterling Silver',
                'Quantity' => 'Set of 2',
                'Design' => 'Lotus flower engraved on band',
                'Weight' => 'Approx. 5 grams',
                'Fit' => 'Adjustable',
            ],
        ];

        // Product 10
        $products[] = [
            'name' => 'Radiant Mandala Pattern Silver Toe Rings',
            'sku' => 'JKJ-TR-010',
            'base_price' => 1328.00,
            'sale_price' => 921.00,
            'short_description' => 'Mandalic floral toe rings in pure silver—sacred geometry meets subtle beauty.',
            'description' => 'These toe rings feature a delicate mandala-style floral design engraved on the surface, offering a perfect blend of symmetry and spirituality. Lightweight and comfortable, they are great for mindful gifting.',
            'specifications' => [
                'Material' => '92.5 Silver',
                'Quantity' => 'Pair',
                'Design' => 'Mandala floral geometry',
                'Weight' => 'Approx. 5.5 grams',
                'Fit' => 'One size fits all',
            ],
        ];

        // Product 11
        $products[] = [
            'name' => 'Silver Toe Rings with Rosebud Design',
            'sku' => 'JKJ-TR-011',
            'base_price' => 1221.00,
            'sale_price' => 932.00,
            'short_description' => 'Charming toe rings with mini rosebud motifs, crafted in shining silver.',
            'description' => 'Celebrate romantic elegance with these rosebud-style toe rings. Each piece is shaped with a tiny rose design, giving your toes a soft and feminine charm. Great for weddings or daily love-inspired fashion.',
            'specifications' => [
                'Silver Type' => '92.5 Silver',
                'Quantity' => '2 rings',
                'Design' => 'Mini rosebud with light floral curve',
                'Weight' => 'Approx. 5 grams',
                'Fit' => 'Adjustable',
            ],
        ];

        // Product 12
        $products[] = [
            'name' => 'Vintage Mesh Pattern Silver Toe Rings',
            'sku' => 'JKJ-TR-012',
            'base_price' => 1162.00,
            'sale_price' => 984.00,
            'short_description' => 'Vintage mesh-style floral toe rings in pure silver with a timeless look.',
            'description' => 'Crafted with intricate mesh patterns resembling blooming petals, these vintage-style toe rings are perfect for those who love antique silver jewelry. They are lightweight and breathable, suitable for long wear.',
            'specifications' => [
                'Material' => '92.5 Sterling Silver',
                'Quantity' => 'Pair',
                'Design' => 'Mesh floral with open pattern',
                'Size' => 'Adjustable',
            ],
        ];

        // Product 13
        $products[] = [
            'name' => 'Bell-Tip Silver Toe Rings with Petal Edges',
            'sku' => 'JKJ-TR-013',
            'base_price' => 1092.00,
            'sale_price' => 876.00,
            'short_description' => 'Toe rings with a smooth bell-like dome and floral-edged design—elegant and ethnic.',
            'description' => 'These rings feature a rounded bell dome at the top with delicate petal-shaped edges around it. The soft jingle of the dome and lightweight silver make them stand out without being too flashy.',
            'specifications' => [
                'Silver Type' => '92.5 Hallmarked',
                'Quantity' => 'Set of 2',
                'Design' => 'Bell tip with floral edge',
                'Weight' => 'Approx. 6 grams',
                'Fit' => 'One size fits all',
            ],
        ];

        // Product 14
        $products[] = [
            'name' => 'Silver Toe Rings with Double Petal Bands',
            'sku' => 'JKJ-TR-014',
            'base_price' => 1362.00,
            'sale_price' => 924.00,
            'short_description' => 'Elegant toe rings with double-layer petal bands for enhanced floral charm.',
            'description' => 'Double-layered petals wrap around your toe in these unique silver rings. The layered look creates depth while maintaining a delicate feminine style. Perfect for modern ethnic wardrobes.',
            'specifications' => [
                'Material' => '92.5 Silver',
                'Quantity' => '2 rings',
                'Design' => 'Dual petal wrap band',
                'Size' => 'Adjustable',
            ],
        ];

        // Product 15
        $products[] = [
            'name' => 'Circular Bloom Silver Toe Ring for Women',
            'sku' => 'JKJ-TR-015',
            'base_price' => 1134.00,
            'sale_price' => 988.00,
            'short_description' => 'Circular bloom toe rings in shiny silver with light floral etching—subtle and refined.',
            'description' => 'Inspired by a blooming flower, these toe rings have a circular bloom shape with fine lines and petal curves. Lightweight, adjustable, and highly polished, they offer everyday elegance in sterling silver.',
            'specifications' => [
                'Material' => '92.5 Sterling Silver',
                'Quantity' => 'Pair',
                'Design' => 'Circular floral with etched bloom',
                'Weight' => 'Approx. 5 grams',
                'Fit' => 'Flexible',
            ],
        ];


        // Insert or Update Products
        foreach ($products as $product) {
            $specifications = $product['specifications'];
            $description = $product['description'];
            $description .= '<h3>Product Specifications</h3><ul>';
            foreach ($specifications as $label => $value) {
                $description .= "<li><strong>$label:</strong> " . e($value) . "</li>";
            }
            $description .= '</ul>';
            unset($product['specifications']);
            $product['description'] = $description;

            Product::updateOrCreate(
                ['sku' => $product['sku']],
                array_merge($product, [
                    'category_id' => $toeRing->id,
                    'material_type' => 'silver',
                    'product_type' => 'simple',
                    'is_active' => true,
                    'stock' => 0,
                    'manage_stock' => false,
                    'audience' => 'women',
                    'slug' => Str::slug($product['name']),
                ])
            );
        }

        $this->command->info('Successfully created/updated 15 Toe Ring products.');
    }
}