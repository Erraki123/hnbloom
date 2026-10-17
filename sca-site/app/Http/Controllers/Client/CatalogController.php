<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->when($request->q, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($request->category, fn ($query, $category) => $query->where('category_id', $category))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('client.catalog.index', [
            'products' => $products,
            'categories' => Category::all(),
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'images']);

        return view('client.catalog.show', compact('product'));
    }
}
