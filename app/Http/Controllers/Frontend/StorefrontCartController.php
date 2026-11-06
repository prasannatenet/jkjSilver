<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StorefrontCartController extends Controller
{
    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()
            ->where('is_active', true)
            ->with('variations')
            ->findOrFail($data['product_id']);
        $variation = $product->defaultVariation();

        if (($variation && (int) $variation->stock < 1)
            || (! $variation && $product->manage_stock && (int) $product->total_stock < 1)) {
            throw ValidationException::withMessages([
                'product_id' => 'This item is out of stock.',
            ]);
        }

        $quantity = (int) ($data['quantity'] ?? 1);
        $price = $variation ? $variation->effectivePrice() : $product->effectivePrice();

        if (Auth::check()) {
            $line = Cart::query()->firstOrNew([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'product_variation_id' => $variation?->id,
                'combo_id' => null,
            ]);
            $line->quantity = (int) $line->quantity + $quantity;
            $line->price = $price;
            $line->save();
        } else {
            $entries = collect((array) $request->session()->get('guest_cart', []))->values();
            $lineIndex = $entries->search(fn ($entry): bool => is_array($entry)
                && (int) ($entry['product_id'] ?? 0) === (int) $product->id
                && (int) ($entry['product_variation_id'] ?? 0) === (int) ($variation?->id ?? 0));

            if ($lineIndex === false) {
                $entries->push([
                    'product_id' => (int) $product->id,
                    'product_variation_id' => $variation?->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            } else {
                $entry = $entries->get($lineIndex);
                $entry['quantity'] = (int) ($entry['quantity'] ?? 0) + $quantity;
                $entry['price'] = $price;
                $entries->put($lineIndex, $entry);
            }

            $request->session()->put('guest_cart', $entries->all());
        }

        $cart = $this->cartDrawerData($request);

        return response()->json([
            'success' => true,
            'message' => 'Added to cart.',
            'count' => $cart['count'],
            'drawer_html' => view('layouts.partials.cart-drawer-content', [
                'headerCartItems' => $cart['items'],
                'headerCartSubtotal' => $cart['subtotal'],
            ])->render(),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        if (Auth::check()) {
            $data = $request->validate([
                'cart_id' => ['required', 'integer', 'min:1'],
            ]);

            $deleted = Cart::query()
                ->where('user_id', $request->user()->id)
                ->whereKey($data['cart_id'])
                ->delete();

            abort_unless($deleted, 404);
        } else {
            $data = $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'product_variation_id' => ['nullable', 'integer', 'min:1'],
            ]);

            $entries = collect((array) $request->session()->get('guest_cart', []))
                ->filter(fn ($entry): bool => is_array($entry))
                ->values();
            $matchingIndex = $entries->search(fn (array $entry): bool =>
                (int) ($entry['product_id'] ?? 0) === (int) $data['product_id']
                && (int) ($entry['product_variation_id'] ?? 0) === (int) ($data['product_variation_id'] ?? 0)
            );

            abort_if($matchingIndex === false, 404);

            $entries->forget($matchingIndex);
            $request->session()->put('guest_cart', $entries->values()->all());
        }

        $cart = $this->cartDrawerData($request);

        return response()->json([
            'success' => true,
            'count' => $cart['count'],
            'drawer_html' => view('layouts.partials.cart-drawer-content', [
                'headerCartItems' => $cart['items'],
                'headerCartSubtotal' => $cart['subtotal'],
            ])->render(),
        ]);
    }

    /**
     * @return array{count: int, subtotal: float, items: Collection<int, array{name: string, slug: ?string, image: string, quantity: int, subtotal: float, cart_id: ?int, product_id: int, product_variation_id: ?int}>}
     */
    private function cartDrawerData(Request $request): array
    {
        if (Auth::check()) {
            $rows = Cart::query()
                ->where('user_id', $request->user()->id)
                ->with(['product.images', 'variation.images', 'variation.product.images', 'combo'])
                ->get()
                ->map(fn (Cart $row): array => [
                    'product' => $row->product ?? $row->variation?->product,
                    'variation' => $row->variation,
                    'combo' => $row->combo,
                    'quantity' => (int) $row->quantity,
                    'price' => (float) $row->price,
                    'cart_id' => (int) $row->id,
                ]);
        } else {
            $entries = collect((array) $request->session()->get('guest_cart', []))
                ->filter(fn ($entry): bool => is_array($entry));
            $productIds = $entries->pluck('product_id')->filter()->unique()->values();
            $variationIds = $entries->pluck('product_variation_id')->filter()->unique()->values();
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->with('images')
                ->get()
                ->keyBy('id');
            $variations = ProductVariation::query()
                ->whereIn('id', $variationIds)
                ->with(['images', 'product.images'])
                ->get()
                ->keyBy('id');

            $rows = $entries->map(fn (array $entry): array => [
                'product' => $products->get((int) ($entry['product_id'] ?? 0)),
                'variation' => $variations->get((int) ($entry['product_variation_id'] ?? 0)),
                'combo' => null,
                'quantity' => max(1, (int) ($entry['quantity'] ?? 1)),
                'price' => (float) ($entry['price'] ?? 0),
                'cart_id' => null,
            ]);
        }

        $items = $rows->map(function (array $row): ?array {
            $product = $row['product'];
            $variation = $row['variation'];
            $combo = $row['combo'];

            if (! $product && ! $combo) {
                return null;
            }

            $image = $variation?->images->firstWhere('is_primary', true)?->path
                ?: $variation?->images->first()?->path
                ?: $product?->primary_image
                ?: $product?->images->firstWhere('is_primary', true)?->path
                ?: $product?->images->first()?->path;
            $quantity = $row['quantity'];
            $subtotal = $row['price'] * $quantity;

            return [
                'name' => $combo?->name ?? $product?->name ?? 'Shopping bag item',
                'slug' => $product?->slug,
                'image' => $image
                    ? asset('storage/' . ltrim($image, '/'))
                    : asset('frontend/images/product-1.jpeg'),
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'cart_id' => $row['cart_id'],
                'product_id' => (int) ($product?->id ?? 0),
                'product_variation_id' => $variation?->id,
            ];
        })->filter()->values();

        return [
            'count' => (int) $rows->sum('quantity'),
            'subtotal' => (float) $items->sum('subtotal'),
            'items' => $items,
        ];
    }
}
