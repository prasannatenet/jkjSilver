<?php

use App\Models\Product;

it('shows active admin products in the homepage product slider', function () {
    $product = Product::create([
        'name' => 'Dynamic Slider Necklace',
        'slug' => 'dynamic-slider-necklace',
        'base_price' => 2750,
        'sale_price' => 1808,
        'primary_image' => 'products/necklace.jpg',
        'is_active' => true,
    ]);

    Product::create([
        'name' => 'Inactive Slider Product',
        'slug' => 'inactive-slider-product',
        'base_price' => 1000,
        'is_active' => false,
    ]);

    Product::create([
        'name' => 'Slider Product Without Admin Image',
        'slug' => 'slider-product-without-admin-image',
        'base_price' => 1000,
        'is_active' => true,
    ]);

    $html = $this->get('/')
        ->assertOk()
        ->assertSee('Best Sellers')
        ->assertSee('Dynamic Slider Necklace')
        ->assertSee('storage/products/necklace.jpg')
        ->assertSee('data-wishlist-key="product-' . $product->id . '"', false)
        ->assertSee('₹ 1,808.00')
        ->assertDontSee('Inactive Slider Product')
        ->getContent();

    $bestSellersSection = substr($html, strpos($html, 'id="best-sellers-title"'));
    $bestSellersSection = substr($bestSellersSection, 0, strpos($bestSellersSection, 'id="new-arrivals-title"'));
    expect($bestSellersSection)
        ->not->toContain('Twilight Spark Silver Necklace')
        ->not->toContain('Slider Product Without Admin Image');
});

it('does not show sample products in best sellers when no admin product has an image', function () {
    Product::create([
        'name' => 'Product Missing Admin Image',
        'slug' => 'product-missing-admin-image',
        'base_price' => 1000,
        'is_active' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('id="best-sellers-title"', false)
        ->assertDontSee('data-wishlist-key="sample-best-seller-1"', false);
});

it('shows active admin products in the handpicked section', function () {
    $product = Product::create([
        'name' => 'Handpicked Admin Necklace',
        'slug' => 'handpicked-admin-necklace',
        'base_price' => 2750,
        'primary_image' => 'products/handpicked.jpg',
        'is_active' => true,
    ]);

    Product::create([
        'name' => 'Inactive Handpicked Product',
        'slug' => 'inactive-handpicked-product',
        'base_price' => 1000,
        'is_active' => false,
    ]);

    $html = $this->get('/')
        ->assertOk()
        ->getContent();
    $handpickedSection = substr($html, strpos($html, 'id="handpicked-title"'));
    $handpickedSection = substr($handpickedSection, 0, strpos($handpickedSection, '<!-- GIRD SECTION -->'));

    expect($handpickedSection)
        ->toContain('Handpicked Admin Necklace')
        ->toContain('storage/products/handpicked.jpg')
        ->toContain('href="' . route('product.show', ['slug' => 'handpicked-admin-necklace']) . '"')
        ->toContain('data-wishlist-key="product-' . $product->id . '"')
        ->not->toContain('Inactive Handpicked Product');
});

it('loads the correct public banner image for the homepage ad section', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('class="home-ad-banner" src="' . asset('frontend/images/section-ad.webp') . '"', false)
        ->assertSee('alt="Discover the beauty and stories of hidden treasures in handcrafted silver jewellery"', false);
});

it('loads the silver wave section media from public frontend assets', function () {
    $html = $this->get('/')
        ->assertOk()
        ->getContent();

    $silverWaveSection = substr($html, strpos($html, 'silver-wave-section'));
    $silverWaveSection = substr($silverWaveSection, 0, strpos($silverWaveSection, '<!-- Own The Look -->'));

    expect($silverWaveSection)
        ->toContain(asset('frontend/images/silver-1.webp'))
        ->toContain(asset('frontend/images/silver-2.webp'))
        ->toContain(asset('frontend/images/silver-3.webp'))
        ->toContain(asset('frontend/images/silver-4.webp'))
        ->toContain(asset('frontend/images/silver-5.webp'))
        ->toContain(asset('frontend/images/silver-video-1.mp4'))
        ->toContain(asset('frontend/images/silver-video-2.mp4'))
        ->not->toContain('assets/images/');
});

it('hides best sellers when there are no active products with admin images', function () {
    Product::create([
        'name' => 'Inactive Slider Product',
        'slug' => 'inactive-slider-product',
        'base_price' => 1000,
        'is_active' => false,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('id="best-sellers-title"', false)
        ->assertDontSee('data-wishlist-key="sample-best-seller-1"', false);
});
