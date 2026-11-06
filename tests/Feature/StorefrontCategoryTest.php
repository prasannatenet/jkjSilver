<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;

function storefrontCategoryProduct(Category $category, string $name, float $price, ?Brand $brand = null): Product
{
    return Product::create([
        'name' => $name,
        'slug' => str()->slug($name),
        'category_id' => $category->id,
        'brand_id' => $brand?->id,
        'base_price' => $price,
        'is_active' => true,
    ]);
}

it('shows products from a category and its subcategories', function () {
    $category = Category::create(['name' => 'Necklaces']);
    $subcategory = Category::create([
        'name' => 'Pendants',
        'parent_id' => $category->id,
    ]);
    $otherCategory = Category::create(['name' => 'Bracelets']);

    storefrontCategoryProduct($category, 'Necklace Product', 500);
    storefrontCategoryProduct($subcategory, 'Pendant Product', 250);
    storefrontCategoryProduct($otherCategory, 'Bracelet Product', 700);

    $this->get(route('storefront.category.show', $category->slug))
        ->assertOk()
        ->assertSee('Necklace Product')
        ->assertSee('Pendant Product')
        ->assertDontSee('Bracelet Product')
        ->assertSee('Filter products')
        ->assertSee('Price');
});

it('shows products with stored images first and wires wishlist and cart controls', function () {
    $category = Category::create(['name' => 'Earrings']);
    $withoutImage = storefrontCategoryProduct($category, 'Earlier Plain Earring', 500);
    $withImage = storefrontCategoryProduct($category, 'Later Image Earring', 600);
    ProductImage::create([
        'product_id' => $withImage->id,
        'path' => 'products/later-image-earring.jpg',
        'is_primary' => true,
    ]);

    $response = $this->withSession([
        'guest_wishlist' => [$withoutImage->id],
    ])->get(route('storefront.category.show', $category->slug))
        ->assertOk()
        ->assertSee('data-cart-add-url="' . route('storefront.cart.add') . '"', false)
        ->assertSee('data-wishlist-url="' . route('storefront.wishlist.toggle') . '"', false)
        ->assertSee('aria-pressed="true"', false)
        ->assertSee('data-product-id="' . $withImage->id . '"', false);

    preg_match_all(
        '/<article class="product-card category-product-card">.*?<\/article>/s',
        $response->getContent(),
        $cards
    );

    expect($cards[0][0])
        ->toContain('Later Image Earring')
        ->and($cards[0][1])
        ->toContain('Earlier Plain Earring');
});

it('filters category products by subcategory, brand, and displayed price', function () {
    $category = Category::create(['name' => 'Rings']);
    $subcategory = Category::create([
        'name' => 'Silver Rings',
        'parent_id' => $category->id,
    ]);
    $brand = Brand::create(['name' => 'Maanyas']);
    $otherBrand = Brand::create(['name' => 'Other Brand']);

    storefrontCategoryProduct($category, 'Gold Ring', 900, $brand);
    storefrontCategoryProduct($subcategory, 'Silver Ring', 400, $brand);
    storefrontCategoryProduct($subcategory, 'Other Silver Ring', 300, $otherBrand);

    $this->get(route('storefront.category.show', [
        'slug' => $subcategory->slug,
        'brand' => $brand->id,
        'min_price' => 350,
        'max_price' => 450,
    ]))
        ->assertOk()
        ->assertSee('Silver Ring')
        ->assertDontSee('Other Silver Ring');
});

it('provides an all-categories page for the jewellery navigation link', function () {
    $category = Category::create(['name' => 'Necklaces']);
    Category::create([
        'name' => 'Pendants',
        'parent_id' => $category->id,
    ]);

    $this->get(route('storefront.categories'))
        ->assertOk()
        ->assertSee('Shop by Category')
        ->assertSee('Necklaces')
        ->assertSee('/category/necklaces')
        ->assertSee('Pendants')
        ->assertSee('/category/pendants');
});

it('shows all active products and filters the all-products page by category, brand, and price', function () {
    $category = Category::create(['name' => 'Necklaces']);
    $subcategory = Category::create([
        'name' => 'Pendants',
        'parent_id' => $category->id,
    ]);
    $otherCategory = Category::create(['name' => 'Bracelets']);
    $brand = Brand::create(['name' => 'Maanyas']);
    $otherBrand = Brand::create(['name' => 'Other Brand']);

    storefrontCategoryProduct($subcategory, 'Filtered Pendant', 400, $brand);
    storefrontCategoryProduct($subcategory, 'Expensive Pendant', 900, $brand);
    storefrontCategoryProduct($otherCategory, 'Other Brand Bracelet', 350, $otherBrand);
    $inactive = storefrontCategoryProduct($category, 'Inactive Necklace', 250, $brand);
    $inactive->update(['is_active' => false]);

    $this->get(route('storefront.products'))
        ->assertOk()
        ->assertSee('All Jewellery')
        ->assertSee('Filtered Pendant')
        ->assertSee('Expensive Pendant')
        ->assertSee('Other Brand Bracelet')
        ->assertDontSee('Inactive Necklace')
        ->assertSee('Filter by category')
        ->assertSee('Filter by brand');

    $this->get(route('storefront.products', ['category' => $subcategory->id]))
        ->assertOk()
        ->assertSee('Filtered Pendant')
        ->assertSee('Expensive Pendant');

    $this->get(route('storefront.products', [
        'category' => $subcategory->id,
        'brand' => $brand->id,
    ]))
        ->assertOk()
        ->assertSee('Filtered Pendant')
        ->assertSee('Expensive Pendant');

    $this->get(route('storefront.products', [
        'category' => $subcategory->id,
        'min_price' => 350,
    ]))
        ->assertOk()
        ->assertSee('Filtered Pendant')
        ->assertDontSee('Other Brand Bracelet');

    $this->get(route('storefront.products', [
        'category' => $subcategory->id,
        'brand' => $brand->id,
        'min_price' => 350,
        'max_price' => 450,
    ]))
        ->assertOk()
        ->assertSee('Filtered Pendant')
        ->assertDontSee('Expensive Pendant')
        ->assertDontSee('Other Brand Bracelet');
});
