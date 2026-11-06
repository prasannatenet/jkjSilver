@foreach ($products as $product)
    @php
        $isWishlisted = (!empty($product['id']) && in_array((int) $product['id'], $wishlistProductIds ?? [], true))
            || (!empty($product['wishlist_key']) && in_array($product['wishlist_key'], $wishlistFallbackKeys ?? [], true));
    @endphp
    <div class="swiper-slide">
        <article class="product-card">
            <div class="product-image">
                <a class="product-card-image-link" href="{{ route('product.show', ['slug' => $product['slug']]) }}"
                    aria-label="View {{ $product['name'] }}">
                    <img
                        src="{{ $product['image'] }}"
                        alt="{{ $product['name'] }}"
                        @if (!empty($product['hover_image'])) data-hover-image="{{ $product['hover_image'] }}" @endif
                        loading="lazy">
                </a>
                @if (!empty($product['id']))
                    <button class="add-to-cart" type="button"
                        data-cart-add-url="{{ route('storefront.cart.add') }}"
                        data-product-id="{{ $product['id'] }}">ADD TO CART</button>
                @else
                    <a class="add-to-cart" href="{{ url('/product/' . $product['slug']) }}">ADD TO CART</a>
                @endif
                <button class="add-to-wishlist" type="button"
                    data-product-id="{{ $product['id'] ?? '' }}"
                    data-wishlist-key="{{ !empty($product['id']) ? 'product-' . $product['id'] : ($product['wishlist_key'] ?? '') }}"
                    data-wishlist-url="{{ route('storefront.wishlist.toggle') }}"
                    aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}"
                    @if (empty($product['id']) && empty($product['wishlist_key'])) disabled @endif>
                    <span class="material-symbols-outlined" aria-hidden="true">{{ $isWishlisted ? 'favorite' : 'favorite_border' }}</span>
                </button>
            </div>
            <h3>
                <a href="{{ url('/product/' . $product['slug']) }}">{{ $product['name'] }}</a>
            </h3>
            <div class="product-pricing">
                <span class="current-price">₹ {{ number_format((float) $product['price'], 2) }}</span>
                @if ((float) $product['regular_price'] > (float) $product['price'])
                    <del class="original-price">₹ {{ number_format((float) $product['regular_price'], 2) }}</del>
                @endif
                @if ($product['reviews_count'] > 0)
                    <span class="product-rating">
                        <span class="rating-star" aria-hidden="true">★</span>
                        ({{ number_format((float) $product['rating'], 1) }} | {{ $product['reviews_count'] }})
                    </span>
                @endif
            </div>
        </article>
    </div>
@endforeach
