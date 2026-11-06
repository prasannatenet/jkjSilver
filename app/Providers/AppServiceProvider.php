<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Wishlist;
use App\Support\StorefrontFallbackProducts;
use App\Services\CouponService;
use App\Services\MetalPriceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $setting = Schema::hasTable('settings') ? Setting::query()->first() : null;

        View::share('appSetting', $setting);

        // The admin "SMTP Configuration" section is the single source of truth
        // for outgoing mail. Setting::applyMailConfig() holds the one and only
        // settings => mail config mapping; nothing else may duplicate it.
        if ($setting && $setting->smtp_host) {
            $setting->applyMailConfig();
        }

        View::composer(['layouts.frontend', 'layouts.navbar'], function ($view): void {
            $wishlistFallbackKeys = collect((array) session('guest_wishlist_fallbacks', []))
                ->filter(fn ($key): bool => is_string($key) && in_array($key, StorefrontFallbackProducts::keys(), true))
                ->unique()
                ->values();

            if (Auth::check()) {
                $cartRows = Cart::where('user_id', Auth::id())
                    ->with(['product.images', 'variation.images', 'variation.product.images', 'combo'])
                    ->get();
                $wishlistProductIds = Wishlist::where('user_id', Auth::id())
                    ->orderByDesc('id')
                    ->pluck('product_id')
                    ->unique()
                    ->values();
                $wishlistCount = $wishlistProductIds->count() + $wishlistFallbackKeys->count();
            } else {
                // Guests keep their cart & wishlist in the session.
                $cartRows = collect(array_values((array) session('guest_cart', [])));
                $wishlistProductIds = collect(array_map(
                    'intval',
                    (array) session('guest_wishlist', [])
                ))->filter(fn (int $id): bool => $id > 0)->unique()->values();
                $wishlistCount = $wishlistProductIds->count() + $wishlistFallbackKeys->count();
            }

            $guestProductIds = $cartRows
                ->map(fn ($row) => (int) (is_array($row) ? ($row['product_id'] ?? 0) : $row->product_id))
                ->filter(fn (int $id): bool => $id > 0)
                ->unique()
                ->values();
            $guestVariationIds = $cartRows
                ->map(fn ($row) => (int) (is_array($row) ? ($row['product_variation_id'] ?? 0) : $row->product_variation_id))
                ->filter(fn (int $id): bool => $id > 0)
                ->unique()
                ->values();
            $guestProductsById = $guestProductIds->isNotEmpty()
                ? Product::query()->whereIn('id', $guestProductIds)->with('images')->get()->keyBy('id')
                : collect();
            $guestVariationsById = $guestVariationIds->isNotEmpty()
                ? \App\Models\ProductVariation::query()
                    ->whereIn('id', $guestVariationIds)
                    ->with(['images', 'product.images'])
                    ->get()
                    ->keyBy('id')
                : collect();

            $headerCartItems = $cartRows->map(function ($row) use ($guestProductsById, $guestVariationsById): ?array {
                $productId = (int) (is_array($row) ? ($row['product_id'] ?? 0) : $row->product_id);
                $variationId = (int) (is_array($row) ? ($row['product_variation_id'] ?? 0) : $row->product_variation_id);
                $product = is_array($row)
                    ? $guestProductsById->get($productId)
                    : ($row->product ?? $row->variation?->product);
                $variation = is_array($row) ? $guestVariationsById->get($variationId) : $row->variation;
                $combo = is_array($row) ? null : $row->combo;

                if (! $product && ! $combo) {
                    return null;
                }

                $quantity = max(1, (int) (is_array($row) ? ($row['quantity'] ?? 1) : $row->quantity));
                $price = (float) (is_array($row)
                    ? ($row['price'] ?? $variation?->effectivePrice() ?? $product?->effectivePrice() ?? 0)
                    : ($row->price ?? $variation?->effectivePrice() ?? $product?->effectivePrice() ?? 0));
                $image = $variation?->images->firstWhere('is_primary', true)?->path
                    ?: $variation?->images->first()?->path
                    ?: $product?->primary_image
                    ?: $product?->images->firstWhere('is_primary', true)?->path
                    ?: $product?->images->first()?->path;

                return [
                    'name' => $combo?->name ?? $product?->name ?? 'Shopping bag item',
                    'slug' => $product?->slug,
                    'image' => $image
                        ? asset('storage/' . ltrim($image, '/'))
                        : asset('frontend/images/product-1.jpeg'),
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $price * $quantity,
                    'cart_id' => is_array($row) ? null : (int) $row->id,
                    'product_id' => $productId,
                    'product_variation_id' => $variationId > 0 ? $variationId : null,
                ];
            })->filter()->values();

            $wishlistProductsById = $wishlistProductIds->isNotEmpty()
                ? Product::query()
                    ->where('is_active', true)
                    ->whereIn('id', $wishlistProductIds)
                    ->with(['images', 'variations'])
                    ->get()
                    ->keyBy('id')
                : collect();
            $wishlistProducts = $wishlistProductIds
                ->map(function (int $productId) use ($wishlistProductsById): ?array {
                    $product = $wishlistProductsById->get($productId);

                    if (! $product) {
                        return null;
                    }

                    $image = $product->primary_image
                        ?: $product->images->firstWhere('is_primary', true)?->path
                        ?: $product->images->first()?->path;

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'image' => $image
                            ? asset('storage/' . ltrim($image, '/'))
                            : asset('frontend/images/product-1.jpeg'),
                        'price' => $product->effectivePrice(),
                    ];
                })
                ->filter()
                ->values();
            $wishlistFallbackProducts = $wishlistFallbackKeys
                ->map(fn (string $key): ?array => StorefrontFallbackProducts::find($key))
                ->filter()
                ->values();
            $wishlistProducts = $wishlistProducts->concat($wishlistFallbackProducts)->values();

            $cartCount = (int) $cartRows->sum(fn ($row) => (int) (
                is_array($row) ? ($row['quantity'] ?? 1) : $row->quantity
            ));
            $cartSubtotal = (float) $headerCartItems->sum('subtotal');

            // Quantities of simple (variation-free) products keyed by product id.
            // Exposed to the layout so product-card counters render instantly on
            // page load instead of waiting for the /cart/quantities AJAX call.
            $simpleCartQuantities = [];
            foreach ($cartRows as $row) {
                $variationId = is_array($row)
                    ? ($row['product_variation_id'] ?? null)
                    : $row->product_variation_id;
                if (! empty($variationId)) {
                    continue;
                }

                $productId = (int) (is_array($row) ? ($row['product_id'] ?? 0) : $row->product_id);
                $quantity = (int) (is_array($row) ? ($row['quantity'] ?? 0) : $row->quantity);
                if ($productId > 0 && $quantity > 0) {
                    $key = (string) $productId;
                    $simpleCartQuantities[$key] = ($simpleCartQuantities[$key] ?? 0) + $quantity;
                }
            }

            $view->with('headerCartCount', $cartCount)
                ->with('headerCartItems', $headerCartItems)
                ->with('headerCartSubtotal', $cartSubtotal)
                ->with('headerWishlistCount', $wishlistCount)
                ->with('headerWishlistProducts', $wishlistProducts)
                ->with('simpleCartQuantities', $simpleCartQuantities);
        });

        View::composer('layouts.navbar', function ($view): void {
            $categories = Schema::hasTable('categories')
                ? Category::query()
                    ->whereNull('parent_id')
                    ->with('childrenRecursive')
                    ->orderBy('position')
                    ->orderBy('name')
                    ->get()
                : collect();
            $searchCategoryNames = Schema::hasTable('categories')
                ? Category::query()
                    ->orderBy('position')
                    ->orderBy('name')
                    ->pluck('name')
                    ->filter()
                    ->values()
                : collect();

            $view->with('navigationCategories', $categories)
                ->with('searchCategoryNames', $searchCategoryNames);
        });

        View::composer('frontend.home', function ($view): void {
            $productIds = Auth::check()
                ? Wishlist::query()
                    ->where('user_id', Auth::id())
                    ->pluck('product_id')
                    ->map(fn ($id): int => (int) $id)
                    ->unique()
                    ->values()
                    ->all()
                : array_values(array_unique(array_filter(
                    array_map('intval', (array) session('guest_wishlist', [])),
                    fn (int $id): bool => $id > 0
                )));

            $view->with('wishlistProductIds', $productIds);
            $view->with('wishlistFallbackKeys', collect((array) session('guest_wishlist_fallbacks', []))
                ->filter(fn ($key): bool => is_string($key) && in_array($key, StorefrontFallbackProducts::keys(), true))
                ->unique()
                ->values()
                ->all());
        });

        View::composer('frontend.partials.floating-offers', function ($view): void {
            // The offer tab is part of the global chrome, so it needs its own
            // tiny query rather than borrowing a controller's cart-aware list.
            $view->with('offerCoupons', Schema::hasTable('coupons')
                ? app(CouponService::class)->offerSummaries()
                : collect());
        });

        View::composer(['layouts.footer'], function ($view): void {
            $aboutUsPage = null;

            if (Schema::hasTable('pages')) {
                $aboutUsPage = Page::query()
                    ->where('is_active', true)
                    ->where(function ($query): void {
                        $query->whereIn('slug', ['about-us', 'aboutus', 'about_us', 'about'])
                            ->orWhere('title', 'like', '%about%');
                    })
                    ->latest('id')
                    ->first();
            }

            $view->with('aboutUsPage', $aboutUsPage);
        });

        View::composer(['layouts.topbar'], function ($view): void {
            $contactPage = null;

            if (Schema::hasTable('pages')) {
                $contactPage = Page::query()
                    ->where('is_active', true)
                    ->where(function ($query): void {
                        $query->whereIn('slug', ['contact', 'contact-us', 'contactus', 'contact_us'])
                            ->orWhere('title', 'like', '%contact%');
                    })
                    ->latest('id')
                    ->first();
            }

            $view->with('contactPage', $contactPage);

            // Live gold & silver spot rates for the top bar's LIVE button. The
            // service is cached and never throws, so a provider outage cannot
            // take the page down with it.
            $view->with('liveRates', app(MetalPriceService::class)->displayPayload());
        });
    }
}
