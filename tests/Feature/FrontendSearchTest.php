<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

test('search suggestions include matching categories and active products', function () {
    $category = Category::create(['name' => 'Silver Rings']);
    $product = Product::create([
        'name' => 'Silver Ring',
        'slug' => Str::slug('Silver Ring').'-'.uniqid(),
        'category_id' => $category->id,
        'base_price' => 1000,
        'product_type' => 'simple',
        'is_active' => true,
        'manage_stock' => true,
        'stock' => 10,
    ]);
    Product::create([
        'name' => 'Silver Ring Draft',
        'slug' => Str::slug('Silver Ring Draft').'-'.uniqid(),
        'base_price' => 1000,
        'product_type' => 'simple',
        'is_active' => false,
        'manage_stock' => true,
        'stock' => 10,
    ]);

    $this->getJson(route('search.suggestions', ['q' => 'Silver']))
        ->assertOk()
        ->assertJsonPath('categories.0.name', $category->name)
        ->assertJsonPath('categories.0.url', route('storefront.category.show', $category->slug))
        ->assertJsonPath('products.0.name', $product->name)
        ->assertJsonPath('products.0.url', route('product.show', $product->slug))
        ->assertJsonMissing(['name' => 'Silver Ring Draft']);
});

test('search suggestions require at least four characters', function () {
    $this->getJson(route('search.suggestions', ['q' => 'rin']))
        ->assertStatus(422)
        ->assertJsonValidationErrors('q');

    $this->getJson(route('search.suggestions', ['q' => '    ']))
        ->assertStatus(422)
        ->assertJsonValidationErrors('q');
});

test('search submits a product query to the catalogue', function () {
    Product::create([
        'name' => 'Silver Ring',
        'slug' => Str::slug('Silver Ring').'-'.uniqid(),
        'base_price' => 1000,
        'product_type' => 'simple',
        'is_active' => true,
        'manage_stock' => true,
        'stock' => 10,
    ]);
    Product::create([
        'name' => 'Gold Necklace',
        'slug' => Str::slug('Gold Necklace').'-'.uniqid(),
        'base_price' => 2000,
        'product_type' => 'simple',
        'is_active' => true,
        'manage_stock' => true,
        'stock' => 10,
    ]);

    $this->get(route('search', ['q' => 'silver ring']))
        ->assertRedirect(route('storefront.products', ['q' => 'silver ring']));

    $this->get(route('storefront.products', ['q' => 'silver ring']))
        ->assertOk()
        ->assertSee('Silver Ring')
        ->assertSee('result for')
        ->assertDontSee('Gold Necklace');
});
