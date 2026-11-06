<?php

use App\Models\Product;

it('loads best sellers in twelve-product batches', function () {
    foreach (range(1, 25) as $number) {
        Product::create([
            'name' => "Batched Product {$number}",
            'slug' => "batched-product-{$number}",
            'base_price' => 1000 + $number,
            'primary_image' => "products/{$number}.jpg",
            'is_active' => true,
        ]);
    }

    Product::create([
        'name' => 'Batched Product Without Admin Image',
        'slug' => 'batched-product-without-admin-image',
        'base_price' => 1000,
        'is_active' => true,
    ]);

    $html = $this->get('/')
        ->assertOk()
        ->getContent();
    $bestSellersSection = substr($html, strpos($html, 'id="best-sellers-title"'));
    $bestSellersSection = substr(
        $bestSellersSection,
        0,
        strpos($bestSellersSection, 'id="new-arrivals-title"')
    );

    expect(substr_count($bestSellersSection, 'class="swiper-slide"'))->toBe(12)
        ->and($bestSellersSection)->toContain('Batched Product 25')
        ->and($bestSellersSection)->not->toContain('Batched Product Without Admin Image')
        ->not->toContain('Batched Product 13');

    $secondPage = $this->getJson(route('storefront.best-sellers.more', ['page' => 2]))
        ->assertOk()
        ->assertJsonPath('has_more', true);

    expect($secondPage->json('html'))
        ->toContain('Batched Product 13')
        ->not->toContain('Batched Product Without Admin Image')
        ->not->toContain('batched-product-25"');

    $thirdPage = $this->getJson(route('storefront.best-sellers.more', ['page' => 3]))
        ->assertOk()
        ->assertJsonPath('has_more', false);

    expect($thirdPage->json('html'))->toContain('Batched Product 1');
});

it('rejects invalid best sellers page numbers', function () {
    $this->getJson(route('storefront.best-sellers.more', ['page' => 1]))
        ->assertUnprocessable();
});
