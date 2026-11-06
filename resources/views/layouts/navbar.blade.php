<header>
    <div class="announcement" aria-label="Special offer">
        <div class="announcement-track">
            <div class="announcement-message">
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Every Piece Reflects Our Passion for Perfection</p>
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Trusted Quality &amp; Timely Delivery</p>
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Premium 925 Sterling Silver Guaranteed</p>
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Every Piece Reflects Our Passion for Perfection</p>
            </div>
            <div class="announcement-message" aria-hidden="true">
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Every Piece Reflects Our Passion for Perfection</p>
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Trusted Quality &amp; Timely Delivery</p>
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Premium 925 Sterling Silver Guaranteed</p>
                <p><span><img src="{{ asset('frontend/images/Diwali_Diya_1.png') }}" alt=""></span>Every Piece Reflects Our Passion for Perfection</p>
            </div>
        </div>
    </div>

    <div class="main-header">
        <button class="category-toggle" type="button" aria-label="Show categories" aria-expanded="false"
            aria-controls="category-nav">
            <span class="hamburger-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>
        <a class="brand" href="{{ url('/') }}" aria-label="{{ optional($appSetting)->site_name ?: 'Maanyas' }} home">
            <img src="{{ optional($appSetting)->logo_path ? asset('storage/' . $appSetting->logo_path) : asset('frontend/images/logo-jk-320.jpeg') }}"
                alt="{{ optional($appSetting)->site_name ?: 'Maanyas' }}">
        </a>
        <form class="search" role="search" action="{{ route('search') }}" method="GET" autocomplete="off">
            <input type="search" name="q" placeholder="What are you looking for?" aria-label="Search products"
                aria-autocomplete="list" aria-expanded="false" aria-controls="search-suggestions"
                data-category-names='@json(($searchCategoryNames ?? collect())->all())'
                data-suggestions-url="{{ route('search.suggestions') }}">
            <div class="search-suggestions" id="search-suggestions" role="listbox" aria-label="Search suggestions" hidden></div>
            <button type="submit" aria-label="Search">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="7.2" stroke="currentColor" stroke-width="2"></circle>
                    <path d="m16.2 16.2 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
            </button>
        </form>

        <div class="header-actions">
            <button class="icon-link wishlist-drawer-trigger" type="button"
                aria-label="Open wishlist, {{ $headerWishlistCount ?? 0 }} {{ ($headerWishlistCount ?? 0) === 1 ? 'item' : 'items' }}"
                aria-controls="wishlist-drawer" aria-expanded="false">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M20.8 4.7a5.5 5.5 0 0 0-7.8 0L12 5.8l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21.4l7.8-7.8 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"></path>
                </svg>
                <span class="wishlist-count">{{ $headerWishlistCount ?? 0 }}</span>
            </button>
            <a class="action-link location" href="#">
                <svg width="23" height="25" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 10c0 5-7 12-7 12S5 15 5 10a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="2"></path>
                    <circle cx="12" cy="10" r="2.3" stroke="currentColor" stroke-width="2"></circle>
                </svg>
                <span>Find a Store</span>
            </a>
            @auth
                <div class="account-menu">
                    <a class="action-link" href="{{ route('storefront') }}" aria-haspopup="true"
                        aria-controls="account-dropdown">
                        <svg width="23" height="25" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"></circle>
                            <path d="M3.5 21a8.5 8.5 0 0 1 17 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                        <span>{{ auth()->user()->name }}</span>
                    </a>
                    <div class="account-dropdown" id="account-dropdown">
                        <a href="{{ route('account.profile') }}">Profile</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a class="action-link" href="{{ route('login') }}">
                    <svg width="23" height="25" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"></circle>
                        <path d="M3.5 21a8.5 8.5 0 0 1 17 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                    <span>Sign in/ Register</span>
                </a>
            @endauth
            <button class="icon-link bag-link cart-drawer-trigger" type="button"
                aria-label="Open shopping bag, {{ $headerCartCount ?? 0 }} items"
                aria-controls="cart-drawer" aria-expanded="false">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4.5 8.5h15l-1.2 12h-12l-1.8-12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                    <path d="M9 9V6a3 3 0 0 1 6 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
                <span class="wishlist-count cart-count">{{ $headerCartCount ?? 0 }}</span>
            </button>
        </div>
    </div>

    <nav class="category-nav" id="category-nav" aria-label="Jewellery categories">
        <a class="category-link" href="{{ route('storefront.products') }}">All Jewellery</a>
        @foreach ($navigationCategories ?? collect() as $category)
            <a class="category-link" href="{{ url('/category/' . $category->slug) }}">{{ $category->name }}</a>
        @endforeach
    </nav>
</header>

<button class="wishlist-drawer-backdrop" type="button" aria-label="Close wishlist" tabindex="-1" hidden></button>
<aside class="wishlist-drawer" id="wishlist-drawer" aria-labelledby="wishlist-drawer-title"
    aria-hidden="true" inert>
    <div class="wishlist-drawer-header">
        <h2 id="wishlist-drawer-title">My Wishlist <span>({{ $headerWishlistCount ?? 0 }})</span></h2>
        <button class="wishlist-drawer-close" type="button" aria-label="Close wishlist">
            <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
    </div>
    <div class="wishlist-drawer-content">
        @forelse ($headerWishlistProducts ?? collect() as $product)
            <a class="wishlist-drawer-item" data-product-id="{{ $product['id'] ?? '' }}" data-wishlist-key="{{ $product['wishlist_key'] ?? 'product-' . $product['id'] }}" href="{{ url('/product/' . $product['slug']) }}">
                <img src="{{ $product['image'] }}" alt="" loading="lazy">
                <span class="wishlist-drawer-item-details">
                    <span class="wishlist-drawer-item-name">{{ $product['name'] }}</span>
                    <span class="wishlist-drawer-item-price">₹ {{ number_format((float) $product['price'], 2) }}</span>
                </span>
                <span class="material-symbols-outlined wishlist-drawer-item-arrow" aria-hidden="true">arrow_forward</span>
            </a>
        @empty
            <p class="wishlist-drawer-empty">Your wishlist is empty.</p>
        @endforelse
    </div>
</aside>
<div class="wishlist-toast" role="status" aria-live="polite"></div>

<button class="cart-drawer-backdrop" type="button" aria-label="Close shopping bag" tabindex="-1" hidden></button>
<aside class="cart-drawer" id="cart-drawer" aria-labelledby="cart-drawer-title" aria-hidden="true" inert>
    <div class="wishlist-drawer-header">
        <div class="cart-drawer-heading">
            <h2 id="cart-drawer-title">Shopping Bag <span>({{ $headerCartCount ?? 0 }})</span></h2>
            <a class="cart-drawer-checkout" href="{{ route('checkout.index') }}">Checkout</a>
        </div>
        <button class="cart-drawer-close wishlist-drawer-close" type="button" aria-label="Close shopping bag">
            <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
    </div>
    <div class="cart-drawer-content-wrapper">
        @include('layouts.partials.cart-drawer-content')
    </div>
</aside>