<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;

it('adds homepage products to the guest shopping bag', function () {
    $product = Product::create([
        'name' => 'Homepage Cart Product',
        'slug' => 'homepage-cart-product',
        'base_price' => 1499,
        'primary_image' => 'products/homepage-cart-product.jpg',
        'is_active' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('data-cart-add-url="' . route('storefront.cart.add') . '"', false)
        ->assertSee('data-product-id="' . $product->id . '"', false);

    $response = $this->postJson(route('storefront.cart.add'), [
        'product_id' => $product->id,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('count', 1);

    expect($response->json('drawer_html'))->toContain('Homepage Cart Product');

    expect((array) session('guest_cart'))->toHaveCount(1)
        ->and((int) session('guest_cart.0.product_id'))->toBe($product->id)
        ->and((int) session('guest_cart.0.quantity'))->toBe(1)
        ->and((float) session('guest_cart.0.price'))->toBe(1499.0)
        ->and(Cart::query()->count())->toBe(0);
});

it('adds homepage products to the signed-in customer cart', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name' => 'Signed In Homepage Product',
        'slug' => 'signed-in-homepage-product',
        'base_price' => 2299,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->postJson(route('storefront.cart.add'), [
            'product_id' => $product->id,
        ])
        ->assertOk()
        ->assertJsonPath('count', 1);

    $this->assertDatabaseHas('carts', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 2299,
    ]);
});

it('adds the selected product variation and requested quantity from product details', function () {
    $product = Product::create([
        'name' => 'Product Detail Variation',
        'slug' => 'product-detail-variation',
        'base_price' => 1000,
        'product_type' => 'variable',
        'is_active' => true,
    ]);
    $firstVariation = ProductVariation::create([
        'product_id' => $product->id,
        'sku' => 'DETAIL-VARIANT-1',
        'price' => 1000,
        'stock' => 8,
        'attributes' => ['Size' => 'Small'],
        'is_active' => true,
    ]);
    $selectedVariation = ProductVariation::create([
        'product_id' => $product->id,
        'sku' => 'DETAIL-VARIANT-2',
        'price' => 1500,
        'stock' => 4,
        'attributes' => ['Size' => 'Large'],
        'is_active' => true,
    ]);

    $this->get(route('product.show', $product->slug))
        ->assertOk()
        ->assertSee('data-cart-add-url="' . route('storefront.cart.add') . '"', false)
        ->assertSee('data-variation-id="' . $firstVariation->id . '"', false);

    $this->postJson(route('storefront.cart.add'), [
        'product_id' => $product->id,
        'product_variation_id' => $selectedVariation->id,
        'quantity' => 2,
    ])
        ->assertOk()
        ->assertJsonPath('count', 2);

    expect(session('guest_cart'))->toHaveCount(1)
        ->and((int) session('guest_cart.0.product_variation_id'))->toBe($selectedVariation->id)
        ->and((int) session('guest_cart.0.quantity'))->toBe(2)
        ->and((float) session('guest_cart.0.price'))->toBe(1500.0);
});

it('rejects a selected variation that does not belong to the product', function () {
    $product = Product::create([
        'name' => 'Product With No Selected Option',
        'slug' => 'product-with-no-selected-option',
        'base_price' => 1000,
        'is_active' => true,
    ]);
    $otherProduct = Product::create([
        'name' => 'Different Product',
        'slug' => 'different-product',
        'base_price' => 2000,
        'product_type' => 'variable',
        'is_active' => true,
    ]);
    $variation = ProductVariation::create([
        'product_id' => $otherProduct->id,
        'sku' => 'OTHER-PRODUCT-VARIATION',
        'price' => 2000,
        'stock' => 3,
        'is_active' => true,
    ]);

    $this->postJson(route('storefront.cart.add'), [
        'product_id' => $product->id,
        'product_variation_id' => $variation->id,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('product_variation_id');

    expect((array) session('guest_cart', []))->toBeEmpty();
});

it('rejects adding an inactive product to the shopping bag', function () {
    $product = Product::create([
        'name' => 'Inactive Homepage Product',
        'slug' => 'inactive-homepage-product',
        'base_price' => 999,
        'is_active' => false,
    ]);

    $this->postJson(route('storefront.cart.add'), [
        'product_id' => $product->id,
    ])->assertNotFound();

    expect((array) session('guest_cart', []))->toBeEmpty();
});
