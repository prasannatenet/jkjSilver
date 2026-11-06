<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StorefrontCategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('frontend.storefront.categories', compact('categories'));
    }

    public function allProducts(Request $request): View
    {
        return $this->renderProductListing($request);
    }

    public function show(Request $request, string $slug): View
    {
        $category = Category::query()
            ->with('childrenRecursive')
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->renderProductListing($request, $category);
    }

    private function renderProductListing(Request $request, ?Category $category = null): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'integer', 'exists:brands,id'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc'],
        ]);

        if (isset($filters['min_price'], $filters['max_price'])
            && (float) $filters['min_price'] > (float) $filters['max_price']) {
            return back()
                ->withErrors(['max_price' => 'The maximum price must be greater than or equal to the minimum price.'])
                ->withInput();
        }

        $selectedCategory = $category
            ? null
            : (isset($filters['category']) ? Category::query()
                ->with('childrenRecursive')
                ->findOrFail($filters['category']) : null);
        $listingCategory = $category ?? $selectedCategory;
        $categoryIds = $listingCategory
            ? collect([$listingCategory->id])
                ->merge($this->flattenCategories($listingCategory->childrenRecursive)->pluck('id'))
                ->all()
            : null;
        $priceSql = $this->effectivePriceSql();
        $productQuery = Product::query()->where('is_active', true);

        if (! empty($filters['q'])) {
            $term = trim($filters['q']);
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';
            $productQuery->where('name', 'like', $like);
        }

        if ($categoryIds !== null) {
            $productQuery->whereIn('category_id', $categoryIds);
        }

        if (! empty($filters['brand'])) {
            $productQuery->where('brand_id', $filters['brand']);
        }

        $priceBounds = (clone $productQuery)
            ->selectRaw("MIN({$priceSql}) as min_price, MAX({$priceSql}) as max_price")
            ->first();

        if (isset($filters['min_price'])) {
            $productQuery->whereRaw("{$priceSql} >= CAST(? AS DECIMAL(12, 2))", [$filters['min_price']]);
        }

        if (isset($filters['max_price'])) {
            $productQuery->whereRaw("{$priceSql} <= CAST(? AS DECIMAL(12, 2))", [$filters['max_price']]);
        }

        $products = $productQuery
            ->orderByRaw("CASE WHEN (products.primary_image IS NOT NULL AND products.primary_image != '') OR EXISTS ("
                . "SELECT 1 FROM product_images AS category_images "
                . "WHERE category_images.product_id = products.id "
                . "AND category_images.path IS NOT NULL AND category_images.path != '') "
                . 'THEN 0 ELSE 1 END')
            ->when(
                ($filters['sort'] ?? 'newest') === 'price_asc',
                fn (Builder $query) => $query->orderByRaw("{$priceSql} ASC"),
            )
            ->when(
                ($filters['sort'] ?? 'newest') === 'price_desc',
                fn (Builder $query) => $query->orderByRaw("{$priceSql} DESC"),
            )
            ->when(
                ($filters['sort'] ?? 'newest') === 'newest',
                fn (Builder $query) => $query->orderByDesc('products.created_at')->orderByDesc('products.id'),
            )
            ->with(['images', 'variations'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->paginate(12)
            ->onEachSide(1)
            ->withQueryString()
            ->through(function (Product $product): array {
                $image = $product->primary_image
                    ?: $product->images->firstWhere('is_primary', true)?->path
                    ?: $product->images->first()?->path;
                $price = $product->effectivePrice();

                return [
                    'id' => (int) $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $image
                        ? asset('storage/' . ltrim($image, '/'))
                        : asset('frontend/images/product-1.jpeg'),
                    'price' => $price,
                    'regular_price' => $product->regularPrice(),
                    'reviews_count' => (int) $product->reviews_count,
                    'rating' => (float) ($product->reviews_avg_rating ?? 0),
                ];
            });

        $brands = Brand::query()
            ->whereHas('products', function (Builder $query) use ($categoryIds): void {
                $query->where('is_active', true)
                    ->when($categoryIds !== null, fn (Builder $query) => $query->whereIn('category_id', $categoryIds));
            })
            ->orderBy('name')
            ->get(['id', 'name']);
        $sidebarCategories = Category::query()
            ->whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('position')
            ->orderBy('name')
            ->get();
        $wishlistProductIds = Auth::check()
            ? Wishlist::query()
                ->where('user_id', Auth::id())
                ->pluck('product_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all()
            : array_values(array_unique(array_filter(
                array_map('intval', (array) $request->session()->get('guest_wishlist', [])),
                fn (int $id): bool => $id > 0
            )));

        return view('frontend.storefront.category', [
            'category' => $category,
            'isAllProducts' => $category === null,
            'sidebarCategoryOptions' => $this->flattenCategoryTree($sidebarCategories),
            'brands' => $brands,
            'products' => $products,
            'priceBounds' => $priceBounds,
            'filters' => $filters,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, Category>
     */
    private function flattenCategories(Collection $categories): Collection
    {
        return $categories->flatMap(fn (Category $category): Collection => collect([$category])
            ->concat($this->flattenCategories($category->childrenRecursive)));
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, array{category: Category, depth: int}>
     */
    private function flattenCategoryTree(Collection $categories, int $depth = 0): Collection
    {
        return $categories->flatMap(fn (Category $category): Collection => collect([[
            'category' => $category,
            'depth' => $depth,
        ]])->concat($this->flattenCategoryTree($category->childrenRecursive, $depth + 1)));
    }

    private function effectivePriceSql(): string
    {
        $variationPrice = 'CASE WHEN pv.sale_price IS NOT NULL AND pv.sale_price > 0 AND pv.sale_price < pv.price '
            . 'THEN pv.sale_price ELSE pv.price END';
        $eligibleVariation = '(pv.is_active = 1 OR NOT EXISTS ('
            . 'SELECT 1 FROM product_variations AS active_pv '
            . 'WHERE active_pv.product_id = products.id AND active_pv.is_active = 1))';
        $inStockPrice = "(SELECT {$variationPrice} FROM product_variations AS pv "
            . "WHERE pv.product_id = products.id AND {$eligibleVariation} AND pv.stock > 0 "
            . "ORDER BY {$variationPrice} ASC LIMIT 1)";
        $anyStockPrice = "(SELECT {$variationPrice} FROM product_variations AS pv "
            . "WHERE pv.product_id = products.id AND {$eligibleVariation} "
            . "ORDER BY {$variationPrice} ASC LIMIT 1)";

        return "COALESCE({$inStockPrice}, {$anyStockPrice}, COALESCE(products.sale_price, products.base_price, 0))";
    }
}
