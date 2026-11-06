<?php

namespace App\Support;

class StorefrontFallbackProducts
{
    public static function bestSellers(): array
    {
        return [
            [
                'wishlist_key' => 'sample-best-seller-1',
                'name' => 'Twilight Spark Silver Necklace',
                'slug' => 'twilight-spark-silver-necklace',
                'image' => asset('frontend/images/product-1.jpeg'),
                'hover_image' => asset('frontend/images/product-1-hover.jpeg'),
                'price' => 1808,
                'regular_price' => 2750,
                'rating' => 5,
                'reviews_count' => 1,
            ],
            [
                'wishlist_key' => 'sample-best-seller-2',
                'name' => 'Enchanted Stardust Silver Earrings',
                'slug' => 'enchanted-stardust-silver-earrings',
                'image' => asset('frontend/images/product-1.jpeg'),
                'hover_image' => asset('frontend/images/product-1-hover.jpeg'),
                'price' => 2249,
                'regular_price' => 2750,
                'rating' => 5,
                'reviews_count' => 1,
            ],
            [
                'wishlist_key' => 'sample-best-seller-3',
                'name' => 'Serene Splendor Silver Earrings',
                'slug' => 'serene-splendor-silver-earrings',
                'image' => asset('frontend/images/product-1.jpeg'),
                'hover_image' => asset('frontend/images/product-1-hover.jpeg'),
                'price' => 1901,
                'regular_price' => 2750,
                'rating' => 5,
                'reviews_count' => 1,
            ],
        ];
    }

    public static function newArrivals(): array
    {
        return [
            [
                'wishlist_key' => 'sample-new-arrival-1',
                'name' => 'Twilight Spark Silver Necklace',
                'slug' => 'twilight-spark-silver-necklace',
                'image' => asset('frontend/images/silver-1.webp'),
                'hover_image' => asset('frontend/images/silver-2.webp'),
                'price' => 1808,
                'regular_price' => 2750,
                'rating' => 5,
                'reviews_count' => 1,
            ],
            [
                'wishlist_key' => 'sample-new-arrival-2',
                'name' => 'Twilight Spark Silver Necklace',
                'slug' => 'twilight-spark-silver-necklace',
                'image' => asset('frontend/images/silver-2.webp'),
                'hover_image' => asset('frontend/images/silver-1.webp'),
                'price' => 1808,
                'regular_price' => 2750,
                'rating' => 5,
                'reviews_count' => 1,
            ],
            [
                'wishlist_key' => 'sample-new-arrival-3',
                'name' => 'Twilight Spark Silver Necklace',
                'slug' => 'twilight-spark-silver-necklace',
                'image' => asset('frontend/images/silver-6.webp'),
                'hover_image' => asset('frontend/images/silver-1.webp'),
                'price' => 1808,
                'regular_price' => 2750,
                'rating' => 5,
                'reviews_count' => 1,
            ],
            [
                'wishlist_key' => 'sample-new-arrival-4',
                'name' => 'Twilight Spark Silver Necklace',
                'slug' => 'twilight-spark-silver-necklace',
                'image' => asset('frontend/images/silver-4.webp'),
                'hover_image' => asset('frontend/images/silver-2.webp'),
                'price' => 1808,
                'regular_price' => 2750,
                'rating' => 5,
                'reviews_count' => 1,
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        foreach (array_merge(self::bestSellers(), self::newArrivals()) as $product) {
            if ($product['wishlist_key'] === $key) {
                return $product;
            }
        }

        return null;
    }

    public static function keys(): array
    {
        return array_column(array_merge(self::bestSellers(), self::newArrivals()), 'wishlist_key');
    }
}
