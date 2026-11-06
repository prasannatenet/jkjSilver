<section class="hero-slider swiper" aria-label="Featured banners">
    <div class="swiper-wrapper">
        @forelse ($sliders ?? collect() as $slider)
            <div class="swiper-slide">
                @if (filled($slider->button_link))
                    <a href="{{ $slider->button_link }}" aria-label="{{ $slider->title }}">
                @endif
                <picture>
                    <img src="{{ asset('storage/' . ltrim($slider->image_path, '/')) }}" alt="{{ $slider->title }}">
                </picture>
                @if (filled($slider->button_link))
                    </a>
                @endif
            </div>
        @empty
            <div class="swiper-slide">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('frontend/images/banner-mobile-1.jpg') }}">
                    <img src="{{ asset('frontend/images/slide_01.jpg') }}" alt="Banner 1">
                </picture>
            </div>

            <div class="swiper-slide">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ asset('frontend/images/banner-mobile-1.jpg') }}">
                    <img src="{{ asset('frontend/images/slide_02.jpg') }}" alt="Banner 2">
                </picture>
            </div>
        @endforelse
    </div>

    <div class="hero-controls" aria-label="Banner controls">
        <button class="hero-arrow hero-prev" type="button" aria-label="Previous banner">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="m15 18-6-6 6-6" />
            </svg>
        </button>
        <div class="hero-pagination" aria-label="Choose a banner"></div>
        <button class="hero-arrow hero-next" type="button" aria-label="Next banner">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="m9 18 6-6-6-6" />
            </svg>
        </button>
    </div>
</section>
