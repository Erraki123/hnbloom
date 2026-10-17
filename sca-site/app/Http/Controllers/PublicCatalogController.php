<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
            'category' => 'nullable|integer',
            'min' => 'nullable|numeric|min:0',
            'max' => 'nullable|numeric|min:0',
            'sort' => 'nullable|in:new,name,price_asc,price_desc',
        ]);

        $canSeePrices = auth()->check() && auth()->user()->canSeePrices();

        $products = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->when($request->q, fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($request->category, fn ($query, $category) => $query->where('category_id', $category))
            ->when($canSeePrices && $request->filled('min'), fn ($query) => $query->where('price', '>=', $request->min))
            ->when($canSeePrices && $request->filled('max'), fn ($query) => $query->where('price', '<=', $request->max));

        match ($request->sort) {
            'name' => $products->orderBy('name'),
            'price_asc' => $canSeePrices ? $products->orderBy('price') : $products->latest(),
            'price_desc' => $canSeePrices ? $products->orderByDesc('price') : $products->latest(),
            default => $products->latest(),
        };

        return view('catalog.index', [
            'products' => $products->paginate(12)->withQueryString(),
            'categories' => Category::withCount(['products' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('name')->get(),
            'canSeePrices' => $canSeePrices,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load(['category', 'images']);

        return view('catalog.show', [
            'product' => $product,
            'related' => Product::with(['category', 'images'])
                ->where('is_active', true)
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->latest()->take(4)->get(),
            'canSeePrices' => auth()->check() && auth()->user()->canSeePrices(),
        ]);
    }
}
