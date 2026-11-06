<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontendSearchController extends Controller
{
    public function search(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:120'],
        ]);

        return redirect()->route('storefront.products', [
            'q' => trim($validated['q']),
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->query('q');
        $term = is_string($query) ? trim($query) : '';
        $validated = Validator::make(['q' => $term], [
            'q' => ['required', 'string', 'min:4', 'max:120'],
        ])->validate();

        $term = $validated['q'];
        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        $categories = Category::query()
            ->where('name', 'like', $like)
            ->orderBy('position')
            ->orderBy('name')
            ->limit(5)
            ->get(['name', 'slug'])
            ->map(fn (Category $category): array => [
                'name' => $category->name,
                'url' => route('storefront.category.show', $category->slug),
            ])
            ->values();

        $products = Product::query()
            ->where('is_active', true)
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(5)
            ->get(['name', 'slug'])
            ->map(fn (Product $product): array => [
                'name' => $product->name,
                'url' => route('product.show', $product->slug),
            ])
            ->values();

        return response()->json([
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
