<?php

use App\Models\Category;

it('shows backend categories and nested subcategories in the navbar', function () {
    $rings = Category::create([
        'name' => 'Rings',
        'position' => 1,
    ]);

    $silverRings = Category::create([
        'name' => 'Silver Rings',
        'parent_id' => $rings->id,
        'position' => 1,
    ]);

    Category::create([
        'name' => 'Toe Rings',
        'parent_id' => $silverRings->id,
        'position' => 2,
    ]);

    Category::create([
        'name' => 'Earrings',
        'position' => 2,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Rings')
        ->assertSee('Silver Rings')
        ->assertSee('Toe Rings')
        ->assertSee('Earrings')
        ->assertSee('/category/rings')
        ->assertSee('/category/silver-rings')
        ->assertSee('data-category-names=', false)
        ->assertSee('Rings')
        ->assertSee('Silver Rings')
        ->assertSee('Toe Rings')
        ->assertSee('Earrings');
});
