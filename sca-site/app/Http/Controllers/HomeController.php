<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'categories' => Category::withCount(['products' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('name')->take(6)->get(),
            'latest' => Product::with(['category', 'images'])
                ->where('is_active', true)->latest()->take(8)->get(),
            'canSeePrices' => auth()->check() && auth()->user()->canSeePrices(),
        ]);
    }
}
