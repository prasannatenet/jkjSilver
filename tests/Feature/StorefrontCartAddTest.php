<?php

use App\Models\Cart;
use App\Models\Product;
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
