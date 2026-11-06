<?php

use App\Models\Product;

it('shows the latest active admin products in new arrivals', function () {
    Product::create([
        'name' => 'Older Arrival Product',
        'slug' => 'older-arrival-product',
        'base_price' => 1000,
        'primary_image' => 'products/older.jpg',
        'is_active' => true,
    ]);

    Product::create([
        'name' => 'Latest Arrival Product',
        'slug' => 'latest-arrival-product',
        'base_price' => 2000,
        'primary_image' => 'products/latest.jpg',
        'is_active' => true,
    ]);

    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('Latest Arrival Product')
        ->assertSee('Older Arrival Product')
        ->assertSee('storage/products/latest.jpg')
        ->assertSee('storage/products/older.jpg')
        ->assertSee('href="' . route('product.show', ['slug' => 'latest-arrival-product']) . '"', false);

    $newArrivalsSection = substr(
        $response->getContent(),
        strpos($response->getContent(), 'id="new-arrivals-title"')
    );
    $newArrivalsSection = substr($newArrivalsSection, 0, strpos($newArrivalsSection, 'home-ad-banner'));
    expect($newArrivalsSection)->not->toContain('silver-1.webp');

    $newArrivalsSection = substr(
        $response->getContent(),
        strpos($response->getContent(), 'id="new-arrivals-title"')
    );

    expect($newArrivalsSection)->toContain('Latest Arrival Product')
        ->and(strpos($newArrivalsSection, 'Latest Arrival Product'))
        ->toBeLessThan(strpos($newArrivalsSection, 'Older Arrival Product'));
});

it('shows the supplied default new-arrival products when no active products exist', function () {
    $html = $this->get('/')
        ->assertOk()
        ->assertSee('New Arrivals')
        ->assertSee('Twilight Spark Silver Necklace')
        ->assertSee('₹ 1,808.00')
        ->getContent();

    expect($html)
        ->toContain('frontend/images/silver-1.webp')
        ->toContain('frontend/images/silver-2.webp')
        ->toContain('frontend/images/silver-6.webp')
        ->toContain('frontend/images/silver-4.webp')
        ->not->toContain('id="handpicked-title"')
        ->toContain('data-wishlist-key="sample-new-arrival-1"');
});

it('only shows products with an image in new arrivals', function () {
    Product::create([
        'name' => 'Product Without Image',
        'slug' => 'product-without-image',
        'base_price' => 1000,
        'is_active' => true,
    ]);

    Product::create([
        'name' => 'Product With Image',
        'slug' => 'product-with-image',
        'base_price' => 2000,
        'primary_image' => 'products/with-image.jpg',
        'is_active' => true,
    ]);

    $html = $this->get('/')
        ->assertOk()
        ->getContent();
    $newArrivalsSection = substr(
        $html,
        strpos($html, 'id="new-arrivals-title"')
    );
    $newArrivalsSection = substr($newArrivalsSection, 0, strpos($newArrivalsSection, 'home-ad-banner'));

    expect($newArrivalsSection)
        ->toContain('Product With Image')
        ->toContain('storage/products/with-image.jpg')
        ->not->toContain('Product Without Image');
});

it('shows only products with uploaded images in handpicked favourites', function () {
    Product::create([
        'name' => 'Handpicked Without Image',
        'slug' => 'handpicked-without-image',
        'base_price' => 1000,
        'is_active' => true,
    ]);

    Product::create([
        'name' => 'Handpicked With Image',
        'slug' => 'handpicked-with-image',
        'base_price' => 2000,
        'primary_image' => 'products/handpicked.jpg',
        'is_active' => true,
    ]);

    $html = $this->get('/')
        ->assertOk()
        ->getContent();
    $handpickedStart = strpos($html, 'id="handpicked-title"');
    $handpickedSection = substr($html, strrpos(substr($html, 0, $handpickedStart), '<section'));
    $handpickedSection = substr($handpickedSection, 0, strpos($handpickedSection, '</section>') + strlen('</section>'));

    expect($handpickedSection)
        ->toContain('Handpicked With Image')
        ->toContain('storage/products/handpicked.jpg')
        ->not->toContain('Handpicked Without Image');
});
