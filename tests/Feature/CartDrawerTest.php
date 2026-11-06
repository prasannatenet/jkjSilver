<?php

use App\Models\Product;

it('opens the shopping bag drawer and shows guest cart contents', function () {
    $product = Product::create([
        'name' => 'Guest Cart Necklace',
        'slug' => 'guest-cart-necklace',
        'base_price' => 1250,
        'primary_image' => 'products/cart-necklace.jpg',
        'is_active' => true,
    ]);

    $this->withSession([
        'guest_cart' => [[
            'product_id' => $product->id,
            'product_variation_id' => null,
            'quantity' => 2,
            'price' => 1250,
        ]],
    ])
        ->get('/')
        ->assertOk()
        ->assertSee('aria-controls="cart-drawer"', false)
        ->assertSee('class="cart-drawer"', false)
        ->assertSee('Guest Cart Necklace')
        ->assertSee('storage/products/cart-necklace.jpg')
        ->assertSee('Qty: 2')
        ->assertSee('₹ 2,500.00')
        ->assertSee('data-cart-remove-url="' . route('storefront.cart.remove') . '"', false)
        ->assertSee('data-product-id="' . $product->id . '"', false)
        ->assertSee('Open shopping bag, 2 items');
});

it('shows an empty state when the shopping bag has no items', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Your shopping bag is empty.')
        ->assertSee('Open shopping bag, 0 items');
});
