<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->get();

        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items');

        return view('client.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate(['note' => 'nullable|string|max:1000']);

        $items = CartItem::with('product')->where('user_id', auth()->id())->get();

        if ($items->isEmpty()) {
            return back()->with('success', 'Votre liste est vide.');
        }

        $order = DB::transaction(function () use ($items, $request) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $items->sum(fn ($item) => $item->product->price * $item->quantity),
                'note' => $request->note,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);
            }

            CartItem::where('user_id', auth()->id())->delete();

            return $order;
        });

        return redirect()->route('client.orders.show', $order)
            ->with('success', 'Votre commande a été envoyée. Nous vous contacterons bientôt.');
    }
}
