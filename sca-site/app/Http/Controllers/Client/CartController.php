<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = CartItem::with('product.images')
            ->where('user_id', auth()->id())
            ->get();

        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        return view('client.cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));

        $item = CartItem::firstOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $product->id],
            ['quantity' => 0]
        );

        $item->increment('quantity', $quantity);

        return back()->with('success', 'Produit ajouté à votre liste.');
    }

    public function update(Request $request, CartItem $item)
    {
        abort_unless($item->user_id === auth()->id(), 403);

        $request->validate(['quantity' => 'required|integer|min:1']);
        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Quantité mise à jour.');
    }

    public function remove(CartItem $item)
    {
        abort_unless($item->user_id === auth()->id(), 403);

        $item->delete();

        return back()->with('success', 'Produit retiré.');
    }
}
