<?php

use App\Models\Category;

it('shows all backend categories in the homepage showcase', function () {
    $parent = Category::create([
        'name' => 'Necklaces',
        'position' => 1,
        'image' => 'categories/necklaces.jpg',
    ]);

    Category::create([
        'name' => 'Pendants',
        'parent_id' => $parent->id,
        'position' => 1,
    ]);

    Category::create([
        'name' => 'Bracelets',
        'position' => 2,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Shop By Category')
        ->assertSee('Necklaces')
        ->assertSee('Pendants')
        ->assertSee('Bracelets')
        ->assertSee('storage/categories/necklaces.jpg')
        ->assertSee('frontend/images/cat_01.jpg')
        ->assertSee('/category/necklaces')
        ->assertSee('/category/pendants');
});
