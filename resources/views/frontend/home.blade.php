@extends('layouts.frontend')

@section('content')
    @include('layouts.heroSection')


    {{-- All Category --}}
    @if (($categories ?? collect())->isNotEmpty())
        <div class="container">
            <section class="category-section" aria-labelledby="category-title">
                <div class="section-heading">
                    <p class="section-eyebrow">A little something for every style</p>
                    <h2 id="category-title">Shop By Category</h2>
                </div>

                <div class="category-slider swiper">
                    <div class="category-grid swiper-wrapper">
                    @foreach ($categories as $category)
                        <div class="swiper-slide">
                            <a class="category-card"
                                href="{{ url('/category/' . $category->slug) }}"
                                aria-label="Shop {{ $category->name }}">
                                <img
                                    src="{{ $category->image ? asset('storage/' . ltrim($category->image, '/')) : asset('frontend/images/cat_01.jpg') }}"
                                    alt=""
                                    loading="lazy">
                                <span class="category-card-title">
                                    {{ $category->name }}
                                    <span class="category-card-arrow" aria-hidden="true">→</span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                    </div>
                </div>
            </section>
        </div>
    @endif


    {{-- Product Slider --}}
    @if ($bestSellers->isNotEmpty())
    <section class="bestSellers mt-5" aria-labelledby="best-sellers-title">
        <div class="section-heading">
            <p class="section-eyebrow">A little something for every style</p>
            <h2 id="best-sellers-title">Best Sellers</h2>
        </div>
        <div class="product-slider-section mt-2 mb-5">
            <div class="swiper productSwiper"
                data-load-more-url="{{ route('storefront.best-sellers.more') }}">
                <div class="swiper-wrapper">
                    @include('frontend.partials.best-seller-card', ['products' => $bestSellers])
                </div>
            </div>

            <div class="product-slider-controls">
                <div class="product-scrollbar" role="progressbar" aria-label="Product slider progress" aria-valuemin="0"
                    aria-valuemax="100" aria-valuenow="0">
                    <span class="product-scrollbar-progress"></span>
                </div>
                <div class="product-navigation">
                    <button class="product-prev" type="button" aria-label="Previous products">
                        <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                    </button>
                    <button class="product-next" type="button" aria-label="Next products">
                        <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    @endif


    {{-- New Arrivals --}}
    <section class="pt-5 pb-5" aria-labelledby="new-arrivals-title">
        <div class="container">
            <div class="section-heading">
                <p class="section-eyebrow">A little something for every style</p>
                <h2 id="new-arrivals-title">New Arrivals</h2>
            </div>
            <div class="new-arrivals-slider swiper">
                <div class="swiper-wrapper">
                    @foreach ($newArrivals as $product)
                        <div class="swiper-slide">
                            <article class="product-card">
                                <div class="product-image large">
                                    <a class="product-card-image-link" href="{{ route('product.show', ['slug' => $product['slug']]) }}"
                                        aria-label="View {{ $product['name'] }}">
                                        <img
                                            src="{{ $product['image'] }}"
                                            alt="{{ $product['name'] }}"
                                            @if ($product['hover_image']) data-hover-image="{{ $product['hover_image'] }}" @endif
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
                                        aria-pressed="{{ (!empty($product['id']) && in_array((int) $product['id'], $wishlistProductIds ?? [], true)) || (!empty($product['wishlist_key']) && in_array($product['wishlist_key'], $wishlistFallbackKeys ?? [], true)) ? 'true' : 'false' }}"
                                        @if (empty($product['id']) && empty($product['wishlist_key'])) disabled @endif>
                                        <span class="material-symbols-outlined" aria-hidden="true">{{ ((!empty($product['id']) && in_array((int) $product['id'], $wishlistProductIds ?? [], true)) || (!empty($product['wishlist_key']) && in_array($product['wishlist_key'], $wishlistFallbackKeys ?? [], true))) ? 'favorite' : 'favorite_border' }}</span>
                                    </button>
                                    <a class="add-to-cart-round" href="{{ url('/product/' . $product['slug']) }}"
                                        aria-label="View {{ $product['name'] }}">
                                        <span class="material-symbols-outlined" aria-hidden="true">local_mall</span>
                                    </a>
                                </div>
                                <h3><a href="{{ route('product.show', ['slug' => $product['slug']]) }}">{{ $product['name'] }}</a></h3>
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
                </div>
                <div class="product-navigation new-arrivals-navigation">
                    <button class="new-arrivals-prev" type="button" aria-label="Previous new arrivals">
                        <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                    </button>
                    <button class="new-arrivals-next" type="button" aria-label="Next new arrivals">
                        <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </section>



    <section class="pt-5 pb-5" aria-label="Discover our silver jewellery">
        <img class="home-ad-banner" src="{{ asset('frontend/images/section-ad.webp') }}"
            alt="Discover the beauty and stories of hidden treasures in handcrafted silver jewellery"
            loading="lazy">
    </section>



    @include('frontend.partials.handpicked-products')

    <!-- GIRD SECTION -->
    <section class="silver-wave-section pt-5 pb-5">
        <div class="section-heading">
            <h2>A SILVER WAVE RISES </h2>
        </div>
        <div class="silver-wave-grid">
            <!-- Top Left Image -->
            <div class="wave-item wave-img wave-one">
                <img src="{{ asset('frontend/images/silver-1.webp') }}" alt="Silver Jewellery" loading="lazy">
            </div>
            <!-- Center Left Image -->
            <div class="wave-item wave-img wave-two">
                <img src="{{ asset('frontend/images/silver-2.webp') }}" alt="Silver Collection" loading="lazy">
                <a href="#" class="wave-link">SHOP NOW</a>
            </div>
            <!-- Center Text -->
            <div class="wave-item wave-content">
                <div>
                    <p>
                        At Mannyas, we believe that true jewellery curation
                        leaves no shortcuts.
                    </p>

                    <p>
                        Every necklace, ring, and bracelet reflects the beauty of fine craftsmanship, timeless elegance,
                        and thoughtful design — created to add a touch of sophistication to every moment.
                    </p>
                </div>
            </div>
            <!-- Top Right Video -->
            <div class="wave-item wave-video wave-video-top">
                <video autoplay muted loop playsinline>
                    <source src="{{ asset('frontend/images/silver-video-1.mp4') }}" type="video/mp4">
                </video>
                <a href="#" class="wave-link">SHOP NOW</a>
            </div>

            <!-- Bottom Left Video -->
            <div class="wave-item wave-video wave-video-bottom">
                <video autoplay muted loop playsinline>
                    <source src="{{ asset('frontend/images/silver-video-2.mp4') }}" type="video/mp4">
                </video>
                <a href="#" class="wave-link">SHOP NOW</a>
            </div>

            <!-- Bottom Center Left Image -->
            <div class="wave-item wave-img wave-three">
                <img src="{{ asset('frontend/images/silver-3.webp') }}" alt="Silver Coins Collection" loading="lazy">
                <div class="wave-overlay">
                    <h3>COINS COLLECTION</h3>
                    <a href="#">SHOP NOW</a>
                </div>
            </div>

            <!-- Bottom Center Right Image -->
            <div class="wave-item wave-img wave-four">
                <img src="{{ asset('frontend/images/silver-4.webp') }}" alt="Silver Art Collection" loading="lazy">
            </div>

            <!-- Bottom Right Image -->
            <div class="wave-item wave-img wave-five">
                <img src="{{ asset('frontend/images/silver-5.webp') }}" alt="Silver Decor" loading="lazy">
                <a href="#" class="wave-link">SHOP NOW</a>
            </div>
        </div>
    </section>

    <!-- Own The Look -->
    <section class="own-the-look pt-6 pb-6">
        <div class="look-section">
            <!-- Payel Image -->
            <div class="look-image" id="payel-image">
                <div class="look-photo">
                    <img src="{{ asset('frontend/images/hotspot-image.jpg') }}" alt="Model wearing a silver anklet" loading="lazy">
                    <div class="look-hotspots" aria-label="Product hotspots">
                        <button class="look-hotspot" type="button" data-product-index="0" data-x="89" data-y="94"
                            aria-label="View product 1"></button>
                        <button class="look-hotspot" type="button" data-product-index="1" data-x="69" data-y="81"
                            aria-label="View product 2"></button>
                    </div>
                </div>
            </div>

            <div class="right-section">
                <div class="section-heading">
                    <h2>Own The Look </h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni atque dolor dicta ducimus sapiente
                        adipisci.</p>

                    <div class="tabs" role="tablist" aria-label="Own the Look categories">
                        <button class="look-tab is-active" id="payel-tab" type="button" role="tab"
                            aria-controls="payal-products" aria-selected="true" data-look-tab="payel">Payel</button>
                        <button class="look-tab" id="toe-ring-tab" type="button" role="tab"
                            aria-controls="toeRing-products" aria-selected="false" data-look-tab="toeRing">Toe
                            Rings</button>
                    </div>
                </div>
                <!-- Payel Products -->
                <div class="look-products" id="payal-products" role="tabpanel" aria-labelledby="payel-tab">
                    <div class="swiper lookSwiper">
                        <div class="swiper-wrapper">
                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/silver-1.webp') }}" alt="Silver anklet"
                                            data-hover-image="{{ asset('frontend/images/silver-2.webp') }}" loading="lazy">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Anklet</h3>
                                </div>
                            </div>
                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/silver-2.webp') }}" alt="Silver anklet"
                                            data-hover-image="{{ asset('frontend/images/silver-3.webp') }}" loading="lazy">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Anklet</h3>
                                </div>
                            </div>

                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/silver-3.webp') }}" alt="Silver anklet"
                                            data-hover-image="{{ asset('frontend/images/silver-4.webp') }}" loading="lazy">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Anklet</h3>
                                </div>
                            </div>

                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/silver-4.webp') }}" alt="Silver anklet"
                                            data-hover-image="{{ asset('frontend/images/silver-1.webp') }}" loading="lazy">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Anklet</h3>
                                    <!-- <div class="product-pricing">
                            <span class="current-price">₹ 1,808.00</span>
                            <del class="original-price">₹ 2,750.00</del>
                            <span class="product-rating"><span class="rating-star" aria-hidden="true">★</span>
                                (5.0
                                |
                                1)</span>
                        </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product-navigation look-navigation">
                        <button class="look-prev" type="button" aria-label="Previous products">
                            <span class="material-symbols-outlined">
                                chevron_left
                            </span>
                        </button>
                        <button class="look-next" type="button" aria-label="Next products">
                            <span class="material-symbols-outlined">
                                chevron_right
                            </span>
                        </button>
                    </div>
                </div>
                <!-- Toe Rings Products -->
                <div class="look-products" id="toeRing-products" role="tabpanel" aria-labelledby="toe-ring-tab" hidden>
                    <div class="swiper lookSwiper">
                        <div class="swiper-wrapper">
                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/toering_01.webp') }}" alt="Silver toe ring" loading="lazy"
                                            decoding="async" data-hover-image="{{ asset('frontend/images/toering_02.webp') }}">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Toe Ring</h3>
                                </div>
                            </div>
                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/toering_02.webp') }}" alt="Silver toe ring" loading="lazy"
                                            decoding="async" data-hover-image="{{ asset('frontend/images/toering_03.webp') }}">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Toe Ring</h3>
                                </div>
                            </div>

                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/toering_03.webp') }}" alt="Silver toe ring" loading="lazy"
                                            decoding="async" data-hover-image="{{ asset('frontend/images/toering_04.webp') }}">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Toe Ring</h3>
                                </div>
                            </div>

                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/toering_04.webp') }}" alt="Silver toe ring" loading="lazy"
                                            decoding="async" data-hover-image="{{ asset('frontend/images/toering_05.webp') }}">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Toe Ring</h3>
                                </div>
                            </div>

                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/toering_05.webp') }}" alt="Silver toe ring" loading="lazy"
                                            decoding="async" data-hover-image="{{ asset('frontend/images/toering_06.webp') }}">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Toe Ring</h3>
                                </div>
                            </div>

                            <!-- Product card -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <div class="product-image large">
                                        <img src="{{ asset('frontend/images/toering_06.webp') }}" alt="Silver toe ring" loading="lazy"
                                            decoding="async" data-hover-image="{{ asset('frontend/images/toering_01.webp') }}">
                                        <button class="add-to-cart">ADD TO CART</button>
                                        <button class="add-to-wishlist" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined" aria-hidden="true">favorite</span>
                                        </button>
                                        <button class="add-to-cart-round" type="button"
                                            aria-label="Add product to wishlist" aria-pressed="false">
                                            <span class="material-symbols-outlined">
                                                local_mall
                                            </span>
                                        </button>
                                    </div>
                                    <h3>Silver Toe Ring</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product-navigation look-navigation">
                        <button class="look-prev" type="button" aria-label="Previous products">
                            <span class="material-symbols-outlined">
                                chevron_left
                            </span>
                        </button>
                        <button class="look-next" type="button" aria-label="Next products">
                            <span class="material-symbols-outlined">
                                chevron_right
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
