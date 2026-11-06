<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

it('removes a guest shopping bag item and refreshes the drawer', function () {
    $product = Product::create([
        'name' => 'Guest Remove Necklace',
        'slug' => 'guest-remove-necklace',
        'base_price' => 1250,
        'is_active' => true,
    ]);

    $response = $this->withSession([
        'guest_cart' => [[
            'product_id' => $product->id,
            'product_variation_id' => null,
            'quantity' => 2,
            'price' => 1250,
        ]],
    ])
        ->deleteJson(route('storefront.cart.remove'), [
            'product_id' => $product->id,
            'product_variation_id' => null,
        ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('count', 0);

    expect($response->json('drawer_html'))->toContain('Your shopping bag is empty.');
    expect((array) session('guest_cart'))->toBeEmpty();
});

it('removes only the signed-in customer shopping bag item', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::create([
        'name' => 'Signed In Remove Necklace',
        'slug' => 'signed-in-remove-necklace',
        'base_price' => 1250,
        'is_active' => true,
    ]);
    $cartItem = Cart::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 1250,
    ]);
    $otherCartItem = Cart::create([
        'user_id' => $otherUser->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 1250,
    ]);

    $this->actingAs($user)
        ->deleteJson(route('storefront.cart.remove'), ['cart_id' => $cartItem->id])
        ->assertOk()
        ->assertJsonPath('count', 0);

    $this->assertDatabaseMissing('carts', ['id' => $cartItem->id]);
    $this->assertDatabaseHas('carts', ['id' => $otherCartItem->id]);
});

it('shows a checkout link in the shopping bag drawer', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('class="cart-drawer-checkout" href="' . route('checkout.index') . '"', false);
});

it('requires sign-in before opening checkout', function () {
    $this->get(route('checkout.index'))
        ->assertRedirect(route('login'));
});

it('loads the signed-in customer shopping bag in checkout', function () {
    $user = User::factory()->create();
    $product = Product::create([
        'name' => 'Checkout Bag Necklace',
        'slug' => 'checkout-bag-necklace',
        'base_price' => 1250,
        'is_active' => true,
    ]);
    Cart::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 1250,
    ]);

    $this->actingAs($user)
        ->get(route('checkout.index'))
        ->assertOk()
        ->assertSee('Checkout Bag Necklace')
        ->assertSee('Checkout');
});
