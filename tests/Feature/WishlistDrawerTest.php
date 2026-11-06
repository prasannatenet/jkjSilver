<?php

use App\Models\Product;

it('opens a right-side wishlist drawer from the navbar and shows guest wishlist products', function () {
    $product = Product::create([
        'name' => 'Guest Wishlist Necklace',
        'slug' => 'guest-wishlist-necklace',
        'base_price' => 1250,
        'primary_image' => 'products/wishlist-necklace.jpg',
        'is_active' => true,
    ]);

    $this->withSession(['guest_wishlist' => [$product->id]])
        ->get('/')
        ->assertOk()
        ->assertSee('aria-controls="wishlist-drawer"', false)
        ->assertSee('class="wishlist-drawer"', false)
        ->assertSee('Guest Wishlist Necklace')
        ->assertSee('storage/products/wishlist-necklace.jpg')
        ->assertSee('Open wishlist, 1 item');
});

it('shows an empty state when the wishlist has no saved products', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Your wishlist is empty.')
        ->assertSee('Open wishlist, 0 items');
});
