<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = class_exists(Order::class)
            ? Order::where('user_id', $user->id)->latest()->take(5)->get()
            : collect();

        return view('client.dashboard', [
            'user' => $user,
            'ordersCount' => $orders->count(),
            'recentOrders' => $orders,
            'newProducts' => Product::with('images')->where('is_active', true)->latest()->take(4)->get(),
        ]);
    }
}
