@extends('layouts.frontend')

@section('title', (!empty($filters['q']) ? 'Search: ' . $filters['q'] : ($category?->name ?? 'All Jewellery')) . ' | ' . (optional($appSetting)->site_name ?: 'Maanyas'))

@section('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/storefront-category.min.css') }}">
@endsection

@section('content')
<main class="category-results-page">
    <div class="category-results-container">
        <nav class="category-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('storefront') }}">Home</a>
            <span aria-hidden="true">/</span>
            @if ($isAllProducts)
                <span>All Jewellery</span>
            @else
                <a href="{{ route('storefront.products') }}">All Jewellery</a>
                <span aria-hidden="true">/</span>
                <span>{{ $category->name }}</span>
            @endif
        </nav>

        <header class="category-page-heading">
            <p>{{ $isAllProducts ? 'Explore the complete collection' : 'Explore the collection' }}</p>
            <h1>{{ $category?->name ?? 'All Jewellery' }}</h1>
            @if ($category?->description)
                <p class="category-description">{{ $category->description }}</p>
            @endif
        </header>

        <div class="category-layout">
            <aside class="category-filter" aria-labelledby="category-filter-title">
                <h2 id="category-filter-title">Filter products</h2>
                @if ($errors->any())
                    <div class="category-filter-errors" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <form method="GET" action="{{ $category ? url('/category/' . $category->slug) : route('storefront.products') }}">
                @if (!empty($filters['q']))
                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                @endif
                <fieldset class="category-filter-group">
                    <legend>Categories</legend>
                    @if ($isAllProducts)
                        <select name="category" aria-label="Filter by category">
                            <option value="">All categories</option>
                            @foreach ($sidebarCategoryOptions as $option)
                                <option value="{{ $option['category']->id }}" @selected((string) ($filters['category'] ?? '') === (string) $option['category']->id)>
                                    {{ str_repeat('— ', $option['depth']) }}{{ $option['category']->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <nav class="category-filter-list" aria-label="Browse categories">
                            @foreach ($sidebarCategoryOptions as $option)
                                <a
                                    href="{{ url('/category/' . $option['category']->slug) }}"
                                    class="{{ $category->is($option['category']) ? 'is-current' : '' }}"
                                    style="--category-depth: {{ $option['depth'] }}"
                                    @if ($category->is($option['category'])) aria-current="page" @endif>
                                    {{ $option['category']->name }}
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </fieldset>
                    @if ($brands->isNotEmpty())
                        <fieldset class="category-filter-group">
                            <legend>Brand</legend>
                            <select name="brand" aria-label="Filter by brand">
                                <option value="">All brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected((string) ($filters['brand'] ?? '') === (string) $brand->id)>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </fieldset>
                    @endif

                    <fieldset class="category-filter-group">
                        <legend>Price</legend>
                        <div class="category-price-fields">
                            <label>
                                Min (₹)
                                <input type="number" name="min_price" min="0" step="1" value="{{ $filters['min_price'] ?? '' }}" placeholder="{{ (int) ($priceBounds->min_price ?? 0) }}">
                            </label>
                            <label>
                                Max (₹)
                                <input type="number" name="max_price" min="0" step="1" value="{{ $filters['max_price'] ?? '' }}" placeholder="{{ (int) ($priceBounds->max_price ?? 0) }}">
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="category-filter-group">
                        <legend>Sort by</legend>
                        <select name="sort" aria-label="Sort products">
                            <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest</option>
                            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: low to high</option>
                            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: high to low</option>
                        </select>
                    </fieldset>

                    <div class="category-filter-actions">
                        <button class="category-filter-submit" type="submit">Apply filters</button>
                        <a class="category-filter-reset" href="{{ $category ? url('/category/' . $category->slug) : route('storefront.products') }}">Clear filters</a>
                    </div>
                </form>
            </aside>

            <section aria-label="{{ $category?->name ?? 'All Jewellery' }} products">
                <div class="category-products-heading">
                    <p>
                        @if (!empty($filters['q']))
                            <strong>{{ $products->total() }}</strong> {{ Str::plural('result', $products->total()) }} for “{{ $filters['q'] }}”
                        @else
                            <strong>{{ $products->total() }}</strong> {{ Str::plural('product', $products->total()) }}
                        @endif
                    </p>
                </div>

                <div class="category-product-grid">
                    @forelse ($products as $product)
                        @php
                            $isWishlisted = collect($wishlistProductIds ?? [])
                                ->map(fn ($id): int => (int) $id)
                                ->contains($product['id']);
                        @endphp
                        <article class="product-card category-product-card">
                            <div class="product-image category-product-image">
                                <a class="product-card-image-link" href="{{ route('product.show', ['slug' => $product['slug']]) }}" aria-label="View {{ $product['name'] }}">
                                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                                </a>
                                <button class="add-to-cart" type="button" data-cart-add-url="{{ route('storefront.cart.add') }}" data-product-id="{{ $product['id'] }}">ADD TO CART</button>
                                <button class="add-to-wishlist" type="button"
                                    data-product-id="{{ $product['id'] }}"
                                    data-wishlist-key="product-{{ $product['id'] }}"
                                    data-wishlist-url="{{ route('storefront.wishlist.toggle') }}"
                                    aria-label="{{ $isWishlisted ? 'Remove' : 'Add' }} {{ $product['name'] }} {{ $isWishlisted ? 'from' : 'to' }} wishlist"
                                    aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}">
                                    <span class="material-symbols-outlined" aria-hidden="true">{{ $isWishlisted ? 'favorite' : 'favorite_border' }}</span>
                                </button>
                            </div>
                            <h3><a href="{{ route('product.show', ['slug' => $product['slug']]) }}">{{ $product['name'] }}</a></h3>
                            <div class="product-pricing category-product-price">
                                <span class="current-price">₹ {{ number_format((float) $product['price'], 2) }}</span>
                                @if ((float) $product['regular_price'] > (float) $product['price'])
                                    <del class="original-price">₹ {{ number_format((float) $product['regular_price'], 2) }}</del>
                                @endif
                                @if ($product['reviews_count'] > 0)
                                    <span class="product-rating"><span class="rating-star" aria-hidden="true">★</span> {{ number_format($product['rating'], 1) }} ({{ $product['reviews_count'] }})</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="category-no-products">No products match these filters. Try changing your selection.</p>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="category-pagination">{{ $products->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</main>
@endsection
