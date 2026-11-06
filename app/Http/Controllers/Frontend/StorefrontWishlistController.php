<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use App\Support\StorefrontFallbackProducts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StorefrontWishlistController extends Controller
{
    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'required_without:fallback_key', 'integer', 'exists:products,id'],
            'fallback_key' => ['nullable', 'required_without:product_id', 'string', Rule::in(StorefrontFallbackProducts::keys())],
        ]);

        $fallbackKeys = collect((array) $request->session()->get('guest_wishlist_fallbacks', []))
            ->filter(fn ($key): bool => is_string($key) && in_array($key, StorefrontFallbackProducts::keys(), true))
            ->unique()
            ->values();

        if (! empty($data['fallback_key'])) {
            $fallbackProduct = StorefrontFallbackProducts::find($data['fallback_key']);
            $inWishlist = ! $fallbackKeys->contains($data['fallback_key']);
            $fallbackKeys = $inWishlist
                ? $fallbackKeys->push($data['fallback_key'])->unique()->values()
                : $fallbackKeys->reject(fn (string $key): bool => $key === $data['fallback_key'])->values();
            $request->session()->put('guest_wishlist_fallbacks', $fallbackKeys->all());

            $productCount = Auth::check()
                ? Wishlist::query()->where('user_id', Auth::id())->distinct('product_id')->count('product_id')
                : collect(array_map('intval', (array) $request->session()->get('guest_wishlist', [])))
                    ->filter(fn (int $id): bool => $id > 0)
                    ->unique()
                    ->count();

            return response()->json([
                'success' => true,
                'product_id' => null,
                'wishlist_key' => $data['fallback_key'],
                'in_wishlist' => $inWishlist,
                'count' => (int) ($productCount + $fallbackKeys->count()),
                'product' => [
                    'id' => null,
                    'name' => $fallbackProduct['name'],
                    'slug' => $fallbackProduct['slug'],
                    'href' => url('/product/' . $fallbackProduct['slug']),
                    'image' => $fallbackProduct['image'],
                    'price' => $fallbackProduct['price'],
                ],
            ]);
        }

        $product = Product::query()
            ->where('is_active', true)
            ->with('images')
            ->findOrFail((int) $data['product_id']);

        if (Auth::check()) {
            $wishlistQuery = Wishlist::query()
                ->where('user_id', Auth::id())
                ->where('product_id', $product->id);
            $inWishlist = ! $wishlistQuery->exists();

            if ($inWishlist) {
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'product_variation_id' => null,
                ]);
            } else {
                $wishlistQuery->delete();
            }

            $count = Wishlist::query()
                ->where('user_id', Auth::id())
                ->distinct('product_id')
                ->count('product_id') + $fallbackKeys->count();
            $wishlistProductIds = Wishlist::query()
                ->where('user_id', Auth::id())
                ->pluck('product_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all();
        } else {
            $wishlistProductIds = collect(array_map(
                'intval',
                (array) $request->session()->get('guest_wishlist', [])
            ))->filter(fn (int $id): bool => $id > 0)->unique()->values();
            $inWishlist = ! $wishlistProductIds->contains($product->id);

            $wishlistProductIds = $inWishlist
                ? $wishlistProductIds->push((int) $product->id)->unique()->values()
                : $wishlistProductIds->reject(fn (int $id): bool => $id === (int) $product->id)->values();

            $request->session()->put('guest_wishlist', $wishlistProductIds->all());
            $count = $wishlistProductIds->count() + $fallbackKeys->count();
            $wishlistProductIds = $wishlistProductIds->all();
        }

        $image = $product->primary_image
            ?: $product->images->firstWhere('is_primary', true)?->path
            ?: $product->images->first()?->path;

        return response()->json([
            'success' => true,
            'product_id' => (int) $product->id,
            'wishlist_key' => 'product-' . $product->id,
            'in_wishlist' => $inWishlist,
            'count' => (int) $count,
            'wishlist_product_ids' => $wishlistProductIds,
            'product' => [
                'id' => (int) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'href' => url('/product/' . $product->slug),
                'image' => $image
                    ? asset('storage/' . ltrim($image, '/'))
                    : asset('frontend/images/product-1.jpeg'),
                'price' => $product->effectivePrice(),
            ],
        ]);
    }
}
