<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\Admin\MessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\CatalogController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\PageController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalogue', [PublicCatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalogue/{product}', [PublicCatalogController::class, 'show'])->name('catalog.show');

Route::get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('client.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Page d'attente
Route::get('/pending', function () {
    if (auth()->user()->is_approved || auth()->user()->isAdmin()) {
        return redirect('/');
    }
    return view('pending');
})->middleware('auth')->name('pending');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/solutions/{solution}/{category}', [PageController::class, 'solutionCategory'])->name('solutions.category');
Route::get('/produit/{slug}', [PageController::class, 'product'])->name('solutions.product');

// Espace ADMIN
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/users', [ClientController::class, 'index'])->name('users.index');
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::patch('/clients/{user}/approve', [ClientController::class, 'approve'])->name('clients.approve');
    Route::patch('/clients/{user}/block', [ClientController::class, 'block'])->name('clients.block');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
    Route::delete('/product-images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::get('/commandes', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/commandes/{order}/statut', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::patch('/messages/{message}/statut', [MessageController::class, 'updateStatus'])->name('messages.status');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
});

// Espace CLIENT (approuvé seulement)
Route::middleware(['auth', 'approved'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboard::class, 'index'])->name('dashboard');
    Route::get('/catalogue', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/catalogue/{product}', [CatalogController::class, 'show'])->name('catalog.show');

    Route::get('/ma-liste', [CartController::class, 'index'])->name('cart.index');
    Route::post('/ma-liste/ajouter/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/ma-liste/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/ma-liste/{item}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/commander', fn () => redirect()->route('client.cart.index'))->name('orders.redirect');
    Route::post('/commander', [ClientOrderController::class, 'store'])->name('orders.store');
    Route::get('/mes-commandes', [ClientOrderController::class, 'index'])->name('orders.index');
    Route::get('/mes-commandes/{order}', [ClientOrderController::class, 'show'])->name('orders.show');
});

Route::get('/_debug-catalog', function () {
    return [
        'data_path'      => database_path('data/solutions.json'),
        'data_exists'    => is_file(database_path('data/solutions.json')),
        'storage_exists' => is_file(storage_path('app/solutions.json')),
        'json_error'     => json_last_error_msg(),
        'slugs'          => array_column(\App\Support\SolutionCatalog::solutions(), 'slug'),
    ];
});
// Pages publiques (en dehors de tout groupe auth) :
Route::get('/qui-sommes-nous', [PageController::class, 'about'])->name('about');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions.index');
Route::get('/solutions/{slug}', [PageController::class, 'solution'])->name('solutions.show');
Route::get('/domotique', [PageController::class, 'domotique'])->name('domotique');
Route::get('/domotique/{slug}', [PageController::class, 'domotiqueMarket'])->name('domotique.show');
Route::get('/projets', [PageController::class, 'projects'])->name('projects');
Route::get('/projets/{slug}', [PageController::class, 'project'])->name('projects.show');
Route::get('/espace-revendeur', function () {
    return auth()->check()
        ? redirect()->route('dashboard')   // déjà connecté : va directement à son espace (admin ou client)
        : redirect()->route('login');      // sinon : page de connexion
})->name('reseller');

// Deja existantes (ne pas dupliquer) : home, contact, contact.store, catalog.index, login, register, dashboard


require __DIR__.'/auth.php';
