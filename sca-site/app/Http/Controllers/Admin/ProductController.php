<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create', ['categories' => Category::all()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $product = Product::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->saveImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Produit ajouté.');
    }

    public function edit(Product $product)
    {
        $product->load('images');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

        $product->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->saveImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Produit modifié.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $product->delete();

        return back()->with('success', 'Produit supprimé.');
    }

    public function destroyImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Photo supprimée.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|max:4096',
        ]);
    }

    private function saveImages(Request $request, Product $product): void
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $product->images()->create([
                    'path' => $file->store('products', 'public'),
                ]);
            }
        }
    }
}
