<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;

it('saves and removes guest wishlist products across page requests', function () {
    $product = Product::create([
        'name' => 'Persistent Guest Wishlist Product',
        'slug' => 'persistent-guest-wishlist-product',
        'base_price' => 1250,
        'primary_image' => 'products/persistent-wishlist.jpg',
        'is_active' => true,
    ]);

    $toggleUrl = route('storefront.wishlist.toggle');

    $this->postJson($toggleUrl, ['product_id' => $product->id])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('in_wishlist', true)
        ->assertJsonPath('count', 1);

    $this->get('/')
        ->assertOk()
        ->assertSee('data-product-id="' . $product->id . '"', false)
        ->assertSee('aria-pressed="true"', false)
        ->assertSee('Persistent Guest Wishlist Product');

    $this->postJson($toggleUrl, ['product_id' => $product->id])
        ->assertOk()
        ->assertJsonPath('in_wishlist', false)
        ->assertJsonPath('count', 0);

    $this->get('/')
        ->assertOk()
        ->assertSee('aria-pressed="false"', false)
        ->assertSee('Your wishlist is empty.');
});

it('allows fallback homepage products to be wishlisted across page requests', function () {
    $toggleUrl = route('storefront.wishlist.toggle');

    $this->postJson($toggleUrl, ['fallback_key' => 'sample-best-seller-1'])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('wishlist_key', 'sample-best-seller-1')
        ->assertJsonPath('in_wishlist', true)
        ->assertJsonPath('count', 1);

    $this->get('/')
        ->assertOk()
        ->assertSee('data-wishlist-key="sample-best-seller-1"', false)
        ->assertSee('aria-pressed="true"', false)
        ->assertSee('data-wishlist-key="sample-best-seller-1"', false)
        ->assertSee('Twilight Spark Silver Necklace');

    $this->postJson($toggleUrl, ['fallback_key' => 'sample-best-seller-1'])
        ->assertOk()
        ->assertJsonPath('in_wishlist', false)
        ->assertJsonPath('count', 0);

    $this->get('/')
        ->assertOk()
        ->assertSee('Your wishlist is empty.');
});

it('saves wishlist products to the signed-in user account', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name' => 'Account Wishlist Product',
        'slug' => 'account-wishlist-product',
        'base_price' => 900,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->postJson(route('storefront.wishlist.toggle'), ['product_id' => $product->id])
        ->assertOk()
        ->assertJsonPath('in_wishlist', true)
        ->assertJsonPath('count', 1);

    expect(Wishlist::query()
        ->where('user_id', $user->id)
        ->where('product_id', $product->id)
        ->exists())->toBeTrue();

    $this->get('/')
        ->assertOk()
        ->assertSee('aria-pressed="true"', false)
        ->assertSee('Account Wishlist Product');
});

it('rejects inactive products in the wishlist toggle', function () {
    $product = Product::create([
        'name' => 'Inactive Wishlist Product',
        'slug' => 'inactive-wishlist-product',
        'base_price' => 500,
        'is_active' => false,
    ]);

    $this->postJson(route('storefront.wishlist.toggle'), ['product_id' => $product->id])
        ->assertNotFound();
});
