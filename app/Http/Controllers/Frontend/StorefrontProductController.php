<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\ProductRecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontProductController extends Controller
{
    private const PAGE_SIZE = 12;

    public function show(
        string $slug,
        ProductRecommendationService $recommendations
    ): View {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category',
                'brand',
                'images',
                'videos',
                'variations.images',
                'attributes.values',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        $variations = $product->pricingVariations();
        $defaultVariation = $product->defaultVariation();
        $reviews = $product->reviews()
            ->with(['user:id,name', 'images'])
            ->latest()
            ->get();
        $relatedProducts = $product->category_id
            ? Product::query()
                ->where('is_active', true)
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->whereNotNull('primary_image')
                            ->where('primary_image', '!=', '');
                    })->orWhereHas('images', function ($query) {
                        $query->whereNotNull('path')
                            ->where('path', '!=', '');
                    });
                })
                ->with(['images', 'variations'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->latest()
                ->limit(8)
                ->get()
            : collect();
        $frequentlyBoughtTogether = $recommendations->forProduct($product, 6);

        $wishlistProductIds = Auth::check()
            ? Wishlist::query()
                ->where('user_id', Auth::id())
                ->pluck('product_id')
                ->map(fn ($id): int => (int) $id)
                ->all()
            : array_map('intval', (array) session('guest_wishlist', []));

        $ratingCounts = $reviews->countBy(fn ($review): int => (int) $review->rating);

        return view('frontend.product-detail', [
            'product' => $product,
            'variations' => $variations,
            'defaultVariation' => $defaultVariation,
            'reviews' => $reviews,
            'reviewsCount' => $reviews->count(),
            'averageRating' => (float) ($product->reviews_avg_rating ?? 0),
            'ratingCounts' => $ratingCounts,
            'relatedProducts' => $relatedProducts,
            'frequentlyBoughtTogether' => $frequentlyBoughtTogether,
            'wishlistProductIds' => $wishlistProductIds,
            'inWishlist' => in_array((int) $product->id, $wishlistProductIds, true),
        ]);
    }

    public function moreBestSellers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page' => ['required', 'integer', 'min:2'],
        ]);
        $page = (int) $validated['page'];

        $products = Product::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNotNull('primary_image')
                        ->where('primary_image', '!=', '');
                })->orWhereHas('images', function ($query) {
                    $query->whereNotNull('path')
                        ->where('path', '!=', '');
                });
            })
            ->with(['images', 'variations'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->offset(($page - 1) * self::PAGE_SIZE)
            ->take(self::PAGE_SIZE + 1)
            ->get();

        $hasMore = $products->count() > self::PAGE_SIZE;
        $cards = $products
            ->take(self::PAGE_SIZE)
            ->map(function (Product $product): array {
                $primaryImage = $product->primary_image
                    ?: $product->images->firstWhere('is_primary', true)?->path
                    ?: $product->images->first()?->path;
                $hoverImage = $product->images
                    ->first(fn ($image) => $image->path !== $primaryImage)?->path;

                return [
                    'id' => (int) $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => asset('storage/' . ltrim($primaryImage, '/')),
                    'hover_image' => $hoverImage
                        ? asset('storage/' . ltrim($hoverImage, '/'))
                        : null,
                    'price' => $product->effectivePrice(),
                    'regular_price' => $product->regularPrice(),
                    'rating' => $product->reviews_avg_rating,
                    'reviews_count' => (int) $product->reviews_count,
                ];
            });

        $wishlistProductIds = Auth::check()
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
        $wishlistFallbackKeys = collect((array) session('guest_wishlist_fallbacks', []))
            ->filter(fn ($key): bool => is_string($key))
            ->values()
            ->all();

        return response()->json([
            'html' => view('frontend.partials.best-seller-card', [
                'products' => $cards,
                'wishlistProductIds' => $wishlistProductIds,
                'wishlistFallbackKeys' => $wishlistFallbackKeys,
            ])->render(),
            'has_more' => $hasMore,
        ]);
    }
}
