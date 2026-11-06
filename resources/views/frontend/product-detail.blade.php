@extends('layouts.frontend')

@php
    $currentPrice = $defaultVariation?->effectivePrice() ?? $product->effectivePrice();
    $regularPrice = $defaultVariation
        ? (float) $defaultVariation->price
        : $product->regularPrice();
    $discountPercentage = $defaultVariation
        ? $defaultVariation->discountPercentage()
        : $product->discountPercentage();
    $currentSku = $defaultVariation?->sku ?: $product->sku;
    $currentStock = $defaultVariation
        ? (int) $defaultVariation->stock
        : ($product->manage_stock ? (int) $product->total_stock : 1);
    $currentDescription = $defaultVariation?->description ?: $product->description;
    $imageUrl = static fn (?string $path): ?string => $path
        ? asset('storage/' . ltrim($path, '/'))
        : null;
    $imagesForVariation = function ($variation = null) use ($product, $imageUrl): array {
        if ($variation && $variation->images->isNotEmpty()) {
            $paths = $variation->images
                ->sortByDesc('is_primary')
                ->pluck('path');
        } else {
            $paths = $product->images
                ->sortByDesc('is_primary')
                ->pluck('path');

            if ($product->primary_image) {
                $paths->prepend($product->primary_image);
            }
        }

        return $paths
            ->filter()
            ->unique()
            ->map(fn ($path): array => [
                'url' => $imageUrl($path),
                'alt' => $product->name,
            ])
            ->values()
            ->all();
    };
    $galleryImages = $imagesForVariation($defaultVariation);
    $hasGalleryImages = $galleryImages !== [];
    $placeholderImage = asset('frontend/images/product-1.jpeg');
    $productVideos = $product->videos->sortByDesc('is_primary')->values();
    $activeProductVideo = $hasGalleryImages ? null : $productVideos->first();
    if ($galleryImages === []) {
        $galleryImages = [['url' => $placeholderImage, 'alt' => $product->name]];
    }
    $priceAttribute = static fn ($price): string => rtrim(rtrim(number_format((float) $price, 2, '.', ''), '0'), '.');
    $discountAttribute = static fn ($discount): string => $discount === null
        ? '0'
        : rtrim(rtrim(number_format((float) $discount, 2, '.', ''), '0'), '.');
    $reviewsForBrowser = $reviews->map(fn ($review): array => [
        'name' => $review->user?->name ?: 'Customer',
        'text' => $review->comment,
        'rating' => (int) $review->rating,
        'date' => $review->created_at?->toISOString(),
    ]);
@endphp

@section('title', $product->name . ' | ' . (optional($appSetting)->site_name ?: 'Maanyas'))

@section('content')
    <div class="container">
        <section class="product-detail-page" aria-label="Product details">
            <nav class="product-breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                @if ($product->category)
                    <a href="{{ url('/category/' . $product->category->slug) }}">{{ $product->category->name }}</a>
                    <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                @endif
                <span aria-current="page">{{ $product->name }}</span>
            </nav>

            <div class="product-detail-layout">
                <div class="product-gallery" id="productGallery"
                    data-product-images="{{ $variations->isNotEmpty() ? '[]' : json_encode($galleryImages) }}">
                    <div class="product-thumbnails" role="group" aria-label="Choose product image">
                        @foreach ($galleryImages as $index => $image)
                            <button class="product-thumbnail {{ $index === 0 ? 'is-active' : '' }}" type="button"
                                aria-label="View product image {{ $index + 1 }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                                data-image="{{ $image['url'] }}" data-gallery-image="{{ $image['url'] }}"
                                data-alt="{{ $image['alt'] }}">
                                <img src="{{ $image['url'] }}" alt="" loading="lazy">
                            </button>
                        @endforeach
                    @foreach ($productVideos as $video)
                        <button class="gallery-thumb-video" type="button"
                            data-video-url="{{ route('product.videos.stream', $video) }}"
                            aria-label="Play product video">
                            <video class="gallery-video-preview" muted playsinline preload="metadata"
                                src="{{ route('product.videos.stream', $video) }}#t=0.1"></video>
                            <span class="material-symbols-outlined" aria-hidden="true">play_circle</span>
                            <span>Product video</span>
                            <span class="thumb-play-badge" aria-hidden="true">▶</span>
                        </button>
                    @endforeach
                    </div>

                    @foreach ($variations as $variation)
                        @foreach ($imagesForVariation($variation) as $image)
                            <span hidden data-variation-id="{{ $variation->id }}"
                                data-gallery-image="{{ $image['url'] }}"
                                data-description="{{ $variation->description ?: $product->description }}"></span>
                        @endforeach
                    @endforeach

                    <div class="product-main-image">
                        <button class="product-image-zoom-trigger" type="button" aria-label="Open product image gallery"
                            @if ($activeProductVideo) style="display:none" @endif>
                            <img id="mainProductImg"
                                class="product-feature-image {{ $activeProductVideo ? 'main-product-img-placeholder' : '' }}"
                                @if ($activeProductVideo) style="display:none" @endif
                                src="{{ $galleryImages[0]['url'] }}"
                                alt="{{ $galleryImages[0]['alt'] }}">
                            <span class="product-zoom-lens" aria-hidden="true"></span>
                            <span class="product-zoom-hint">
                                <span class="material-symbols-outlined" aria-hidden="true">zoom_in</span>
                                Click to zoom
                            </span>
                        </button>
                        <div class="product-zoom-window" aria-hidden="true">
                            <img src="{{ $galleryImages[0]['url'] }}" alt="">
                        </div>
                        @if ($productVideos->isNotEmpty())
                            <video id="mainProductVideo" class="product-main-video" controls playsinline
                                @if ($activeProductVideo) src="{{ route('product.videos.stream', $activeProductVideo) }}"
                                @else style="display:none" @endif>
                                @if ($activeProductVideo)
                                    <source src="{{ route('product.videos.stream', $activeProductVideo) }}" type="video/mp4">
                                @endif
                            </video>
                        @endif
                        @if ($product->hasDiscount())
                            <span class="product-image-badge">{{ $discountPercentage }}% OFF</span>
                        @endif
                        <button class="product-gallery-wishlist add-to-wishlist" type="button"
                            data-product-id="{{ $product->id }}"
                            data-wishlist-key="product-{{ $product->id }}"
                            data-wishlist-url="{{ route('storefront.wishlist.toggle') }}"
                            aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                            aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
                            <span class="material-symbols-outlined" aria-hidden="true">
                                {{ $inWishlist ? 'favorite' : 'favorite_border' }}
                            </span>
                        </button>
                    </div>
                </div>

                <div class="product-information">
                    <div class="product-heading-row">
                        <div class="product-heading-copy">
                            @if ($product->material_type)
                                <p class="product-eyebrow">{{ mb_strtoupper($product->material_type) }}</p>
                            @endif
                            <h1>{{ $product->name }}</h1>
                        </div>
                        <button class="product-share-button" type="button" aria-label="Share this product">
                            <span class="material-symbols-outlined" aria-hidden="true">share</span>
                        </button>
                    </div>
                    <p class="product-share-status" role="status" aria-live="polite"></p>
                    @if ($product->short_description && ! $defaultVariation?->description)
                        <p class="product-subtitle">{{ $product->short_description }}</p>
                    @endif

                    <div class="product-rating">
                        <span class="product-stars" data-product-rating-stars
                            aria-label="{{ $reviewsCount ? 'Rated ' . number_format($averageRating, 1) . ' out of 5' : 'No ratings yet' }}">
                            {{ str_repeat('★', (int) round($averageRating)) }}{{ str_repeat('☆', 5 - (int) round($averageRating)) }}
                        </span>
                        <strong data-product-rating-average>{{ $reviewsCount ? number_format($averageRating, 1) : '—' }}</strong>
                        <a class="product-review-count" href="#product-reviews">
                            ({{ $reviewsCount }} {{ $reviewsCount === 1 ? 'review' : 'reviews' }})
                        </a>
                        @if ($currentSku)
                            <span class="rating-separator">|</span>
                            <span>SKU: <span data-product-sku>{{ $currentSku }}</span></span>
                        @endif
                    </div>

                    @if ($variations->isNotEmpty())
                        <label class="product-variation-label" for="product-variation">Choose an option</label>
                        <select class="product-variation-select" id="product-variation">
                            @foreach ($variations as $variation)
                                @php
                                    $variationPrice = $variation->effectivePrice();
                                    $variationDiscount = $variation->discountPercentage();
                                    $variationAttributes = collect($variation->attributes ?? [])
                                        ->map(fn ($value, $name) => is_scalar($value) ? $name . ': ' . $value : null)
                                        ->filter()
                                        ->values()
                                        ->join(' · ');
                                    $variationGallery = $imagesForVariation($variation);
                                    if ($variationGallery === []) {
                                        $variationGallery = [['url' => $placeholderImage, 'alt' => $product->name]];
                                    }
                                @endphp
                                <option value="{{ $variation->id }}"
                                    data-variation-id="{{ $variation->id }}"
                                    data-price="{{ $priceAttribute($variationPrice) }}"
                                    data-regular-price="{{ $priceAttribute($variation->price) }}"
                                    data-discount="{{ $discountAttribute($variationDiscount) }}"
                                    data-sku="{{ $variation->sku }}"
                                    data-stock="{{ (int) $variation->stock }}"
                                    data-description="{{ $variation->description ?: $product->description }}"
                                    data-images="{{ json_encode($variationGallery) }}"
                                    @selected($defaultVariation?->id === $variation->id)>
                                    {{ $variationAttributes ?: ($variation->sku ?: 'Option ' . $loop->iteration) }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <section class="price-and-qty">
                        <div>
                            <div class="product-price" id="product-price">
                                <strong data-product-price>₹{{ number_format($currentPrice, 0) }}</strong>
                                <del id="product-price-old" data-product-price-old
                                    @if ($discountPercentage === null) style="display:none" @endif>
                                    ₹{{ number_format($regularPrice, 0) }}
                                </del>
                                <span id="product-price-discount" class="product-discount"
                                    @if ($discountPercentage === null) style="display:none" @endif>
                                    {{ $discountPercentage !== null ? number_format($discountPercentage, 0) . '% OFF' : '' }}
                                </span>
                            </div>
                            <p class="product-tax-note">Inclusive of all taxes</p>
                        </div>
                        <div class="product-quantity">
                            <span>Quantity</span>
                            <div class="quantity-control" aria-label="Quantity">
                                <button type="button" data-quantity-change="-1" aria-label="Decrease quantity">−</button>
                                <output class="quantity-value" aria-live="polite">1</output>
                                <button type="button" data-quantity-change="1" aria-label="Increase quantity">+</button>
                            </div>
                        </div>
                    </section>

                    <div class="product-highlights" aria-label="Product benefits">
                        <div class="product-highlight">
                            <span class="material-symbols-outlined" aria-hidden="true">verified</span>
                            <span>925 Sterling<br>Silver</span>
                        </div>
                        <div class="product-highlight">
                            <span class="material-symbols-outlined" aria-hidden="true">diamond</span>
                            <span>Premium<br>Finish</span>
                        </div>
                        <div class="product-highlight">
                            <span class="material-symbols-outlined" aria-hidden="true">local_shipping</span>
                            <span>Free<br>Delivery</span>
                        </div>
                        <div class="product-highlight">
                            <span class="material-symbols-outlined" aria-hidden="true">autorenew</span>
                            <span>Easy<br>Returns</span>
                        </div>
                    </div>

                    <div class="product-purchase-row">
                        <div class="product-stock" data-product-stock
                            data-in-stock="{{ $currentStock > 0 ? 'true' : 'false' }}">
                            <span aria-hidden="true"></span>
                            {{ $currentStock > 0 ? 'In stock — ready to ship' : 'Currently out of stock' }}
                        </div>
                    </div>

                    <div class="product-actions">
                        <button class="product-add-to-cart" type="button" @disabled($currentStock < 1)>
                            <span class="material-symbols-outlined" aria-hidden="true">shopping_bag</span>
                            Add to Cart
                        </button>
                        <button class="product-buy-now" type="button" @disabled($currentStock < 1)>Buy Now</button>
                    </div>
                    <img class="product-jewelry-badges" src="{{ asset('frontend/images/Jewelry-Badges.png') }}"
                        alt="Jewelry quality badges" loading="lazy">
                    <p class="product-cart-status" role="status" aria-live="polite"></p>

                    @if ($product->description || $currentDescription)
                        <div class="product-accordion">
                            <details class="product-accordion-item" open>
                                <summary>Product Details</summary>
                                <div class="product-accordion-content" id="product-description" data-product-description>
                                    {!! nl2br(e($currentDescription ?: $product->description)) !!}
                                </div>
                            </details>
                            <details class="product-accordion-item">
                                <summary>Shipping Policy</summary>
                                <div class="product-accordion-content">
                                    <p>Orders are carefully packed and dispatched promptly. Delivery timelines may vary depending on your location.</p>
                                </div>
                            </details>
                            <details class="product-accordion-item">
                                <summary>Return Policy</summary>
                                <div class="product-accordion-content">
                                    <p>If your order is damaged or incorrect, contact our support team for help with a return or replacement.</p>
                                </div>
                            </details>
                        </div>
                    @endif
                </div>
            </div>

            <dialog class="product-lightbox" aria-label="Product image gallery">
                <div class="product-lightbox-content">
                    <div class="product-lightbox-toolbar">
                        <p class="product-lightbox-counter" aria-live="polite"></p>
                        <button class="product-lightbox-close" type="button" aria-label="Close image gallery">
                            <span class="material-symbols-outlined" aria-hidden="true">close</span>
                        </button>
                    </div>
                    <div class="product-lightbox-viewer">
                        <button class="product-lightbox-nav product-lightbox-prev" type="button" aria-label="Previous image">
                            <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                        </button>
                        <img class="product-lightbox-image" src="{{ $galleryImages[0]['url'] }}"
                            alt="{{ $galleryImages[0]['alt'] }}">
                        <button class="product-lightbox-nav product-lightbox-next" type="button" aria-label="Next image">
                            <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                        </button>
                    </div>
                    <div class="product-lightbox-thumbnails" role="group" aria-label="Choose product image"></div>
                </div>
            </dialog>

            <section class="customer-reviews" id="product-reviews" data-product-id="{{ $product->id }}"
                data-server-reviews="{{ $reviewsForBrowser->toJson() }}"
                aria-labelledby="customer-reviews-title">
                <div class="reviews-heading">
                    <div>
                        <p class="product-eyebrow">YOUR EXPERIENCE MATTERS</p>
                        <h2 id="customer-reviews-title">Customer Reviews</h2>
                    </div>
                    <a class="reviews-write-link" href="#write-review">Write a Review</a>
                </div>

                <div class="reviews-overview">
                    <div class="reviews-average">
                        <strong data-reviews-average>{{ $reviewsCount ? number_format($averageRating, 1) : '—' }}</strong>
                        <span class="review-stars" data-reviews-stars
                            aria-label="{{ $reviewsCount ? 'Rated ' . number_format($averageRating, 1) . ' out of 5' : 'No ratings yet' }}">
                            {{ str_repeat('★', (int) round($averageRating)) }}{{ str_repeat('☆', 5 - (int) round($averageRating)) }}
                        </span>
                        <span data-reviews-total>
                            {{ $reviewsCount }} {{ $reviewsCount === 1 ? 'review' : 'reviews' }}
                        </span>
                    </div>
                    <div class="reviews-breakdown" aria-label="Ratings breakdown">
                        @for ($rating = 5; $rating >= 1; $rating--)
                            @php
                                $ratingCount = (int) ($ratingCounts[$rating] ?? 0);
                                $ratingWidth = $reviewsCount ? ($ratingCount / $reviewsCount) * 100 : 0;
                            @endphp
                            <div class="review-rating-row">
                                <span>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</span>
                                <span class="review-rating-track"><span data-rating-fill="{{ $rating }}"
                                    style="width: {{ $ratingWidth }}%"></span></span>
                                <span data-rating-count="{{ $rating }}">{{ $ratingCount }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="reviews-body">
                    <div class="reviews-list-section">
                        <h3>Recent Reviews <span data-review-list-count>({{ $reviewsCount }})</span></h3>
                        <div class="review-list" aria-live="polite">
                            @forelse ($reviews as $review)
                                <article class="review-card">
                                    <div class="review-card-header">
                                        <span class="review-card-name">{{ $review->user?->name ?: 'Customer' }}</span>
                                        <time class="review-card-date" datetime="{{ $review->created_at?->toDateString() }}">
                                            {{ $review->created_at?->format('M j, Y') }}
                                        </time>
                                    </div>
                                    <div class="review-card-stars" aria-label="Rated {{ $review->rating }} out of 5">
                                        {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}
                                    </div>
                                    <p class="review-card-text">{{ $review->comment }}</p>
                                    @if ($review->images->isNotEmpty())
                                        <div class="review-images">
                                            @foreach ($review->images as $image)
                                                <img src="{{ asset('storage/' . ltrim($image->path, '/')) }}"
                                                    alt="Customer review photo" loading="lazy">
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <p class="reviews-empty-state">No reviews yet. Be the first to share your experience.</p>
                            @endforelse
                        </div>
                    </div>

                    <form class="review-form" id="write-review">
                        <h3>Write a Review</h3>
                        <p class="review-form-intro">Tell others what you think about this piece.</p>
                        <p class="review-storage-note">Reviews are saved in this browser.</p>
                        <fieldset class="review-rating-field">
                            <legend>Your rating <span aria-hidden="true">*</span></legend>
                            <div class="review-rating-options">
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    <input type="radio" id="review-rating-{{ $rating }}" name="rating"
                                        value="{{ $rating }}" @required($rating === 5)>
                                    <label for="review-rating-{{ $rating }}" aria-label="{{ $rating }} stars"
                                        title="{{ $rating }} stars">★</label>
                                @endfor
                            </div>
                        </fieldset>
                        <label class="review-form-label" for="review-name">Your name</label>
                        <input class="review-form-input" id="review-name" name="name" type="text" maxlength="80"
                            autocomplete="name" placeholder="Enter your name" required>
                        <label class="review-form-label" for="review-text">Your review</label>
                        <textarea class="review-form-input" id="review-text" name="review" rows="4" maxlength="1000"
                            placeholder="Share your experience with this product" required></textarea>
                        <button class="review-submit-button" type="submit">Submit Review</button>
                        <p class="review-form-status" role="status" aria-live="polite"></p>
                    </form>
                </div>
            </section>
        </section>
    </div>

    @if ($frequentlyBoughtTogether->isNotEmpty())
        <section class="handpicked pt-6 pb-6" id="frequentlyBoughtTogether" style="background-color: #fbf5eb;">
            <div class="container">
                <div class="section-heading">
                    <h2>Frequently Bought Together</h2>
                    <p>Complete your look with these popular picks.</p>
                </div>
                <div class="swiper handpickedSwiper mt-2">
                    <div class="swiper-wrapper">
                        @foreach ($frequentlyBoughtTogether as $suggestion)
                            @include('frontend.partials.best-seller-card', [
                                'products' => [[
                                    'id' => (int) $suggestion->id,
                                    'name' => $suggestion->name,
                                    'slug' => $suggestion->slug,
                                    'image' => $suggestion->primary_image
                                        ? asset('storage/' . ltrim($suggestion->primary_image, '/'))
                                        : ($suggestion->images->firstWhere('is_primary', true)
                                            ? asset('storage/' . ltrim($suggestion->images->firstWhere('is_primary', true)->path, '/'))
                                            : ($suggestion->images->first()
                                                ? asset('storage/' . ltrim($suggestion->images->first()->path, '/'))
                                                : $placeholderImage)),
                                    'hover_image' => null,
                                    'price' => $suggestion->effectivePrice(),
                                    'regular_price' => $suggestion->regularPrice(),
                                    'rating' => $suggestion->reviews_avg_rating,
                                    'reviews_count' => (int) $suggestion->reviews_count,
                                ]],
                                'wishlistProductIds' => $wishlistProductIds,
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($relatedProducts->isNotEmpty())
        <section class="handpicked pt-6 pb-6" id="relatedProducts" style="background-color: #fbf5eb;">
            <div class="container">
                <div class="section-heading">
                    <h2>More From {{ $product->category->name }}</h2>
                    <p>Explore more pieces from this collection.</p>
                    <a href="{{ url('/products?category=' . $product->category_id) }}">View all</a>
                </div>
                <div class="swiper handpickedSwiper mt-2">
                    <div class="swiper-wrapper">
                        @foreach ($relatedProducts as $suggestion)
                            @include('frontend.partials.best-seller-card', [
                                'products' => [[
                                    'id' => (int) $suggestion->id,
                                    'name' => $suggestion->name,
                                    'slug' => $suggestion->slug,
                                    'image' => $suggestion->primary_image
                                        ? asset('storage/' . ltrim($suggestion->primary_image, '/'))
                                        : ($suggestion->images->firstWhere('is_primary', true)
                                            ? asset('storage/' . ltrim($suggestion->images->firstWhere('is_primary', true)->path, '/'))
                                            : ($suggestion->images->first()
                                                ? asset('storage/' . ltrim($suggestion->images->first()->path, '/'))
                                                : $placeholderImage)),
                                    'hover_image' => null,
                                    'price' => $suggestion->effectivePrice(),
                                    'regular_price' => $suggestion->regularPrice(),
                                    'rating' => $suggestion->reviews_avg_rating,
                                    'reviews_count' => (int) $suggestion->reviews_count,
                                ]],
                                'wishlistProductIds' => $wishlistProductIds,
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        document.querySelector(".product-thumbnails")?.addEventListener("click", (event) => {
            const thumbnail = event.target.closest(".gallery-thumb-video");
            if (thumbnail) switchToVideo(thumbnail.dataset.videoUrl);
        });

        document.querySelector(".product-variation-select")?.addEventListener("change", (event) => {
            const varId = event.currentTarget.selectedOptions[0].dataset.variationId;
            renderGalleryForVariation(varId);
        });
    </script>
@endpush
