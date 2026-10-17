<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $days  = 30;
        $today = now()->startOfDay();
        $from  = $today->copy()->subDays($days - 1);
        $prevFrom = $from->copy()->subDays($days);

        // ---------- Commandes par jour (hors annulées) : 30 derniers jours ----------
        $rows = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as n, COALESCE(SUM(total), 0) as t')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()->keyBy('d');

        $labels = $orders = $revenue = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $from->copy()->addDays($i);
            $key = $day->toDateString();
            $labels[]  = $day->format('d/m');
            $orders[]  = (int) ($rows->get($key)?->n ?? 0);
            $revenue[] = (float) ($rows->get($key)?->t ?? 0);
        }

        // Période précédente (pour les tendances)
        $prev = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$prevFrom, $from->copy()->subSecond()])
            ->selectRaw('COUNT(*) as n, COALESCE(SUM(total), 0) as t')
            ->first();

        $curN = array_sum($orders);
        $curT = array_sum($revenue);
        $trend = fn ($cur, $before) => $before > 0 ? (int) round((($cur - $before) / $before) * 100) : null;

        // ---------- Mini-courbes (7 derniers jours) ----------
        $daily = function (string $model, ?callable $extra = null) use ($today) {
            $start = $today->copy()->subDays(6);
            $q = $model::where('created_at', '>=', $start);
            if ($extra) {
                $extra($q);
            }
            $r = $q->selectRaw('DATE(created_at) as d, COUNT(*) as n')
                ->groupBy(DB::raw('DATE(created_at)'))->get()->keyBy('d');

            $out = [];
            for ($i = 0; $i < 7; $i++) {
                $out[] = (int) ($r->get($start->copy()->addDays($i)->toDateString())?->n ?? 0);
            }

            return $out;
        };

        // ---------- Compteurs ----------
        $pendingClients  = User::where('role', 'client')->where('is_approved', false)->count();
        $approvedClients = User::where('role', 'client')->where('is_approved', true)->count();
        $pendingOrders   = Order::where('status', 'pending')->count();
        $newMessages     = ContactMessage::where('status', 'new')->count();
        $productsActive  = Product::where('is_active', true)->count();
        $productsTotal   = Product::count();

        $kpis = [
            [
                'label' => 'Chiffre d’affaires · 30 j', 'value' => $curT, 'suffix' => ' MAD', 'icon' => 'banknote',
                'trend' => $trend($curT, (float) ($prev->t ?? 0)), 'spark' => array_slice($revenue, -7),
                'note' => 'Commandes non annulées', 'href' => route('admin.orders.index'),
            ],
            [
                'label' => 'Commandes · 30 j', 'value' => $curN, 'icon' => 'shopping-cart',
                'trend' => $trend($curN, (int) ($prev->n ?? 0)), 'spark' => array_slice($orders, -7),
                'note' => Order::count().' au total', 'href' => route('admin.orders.index'),
            ],
            [
                'label' => 'Commandes à traiter', 'value' => $pendingOrders, 'icon' => 'clock',
                'trend' => null, 'spark' => $daily(Order::class, fn ($q) => $q->where('status', 'pending')),
                'note' => 'Statut « En attente »', 'href' => route('admin.orders.index', ['status' => 'pending']),
            ],
            [
                'label' => 'Revendeurs en attente', 'value' => $pendingClients, 'icon' => 'user-check',
                'trend' => null, 'spark' => $daily(User::class, fn ($q) => $q->where('role', 'client')),
                'note' => $approvedClients.' approuvé(s)', 'href' => route('admin.clients.index'),
            ],
            [
                'label' => 'Nouveaux messages', 'value' => $newMessages, 'icon' => 'mail',
                'trend' => null, 'spark' => $daily(ContactMessage::class),
                'note' => 'Formulaire de contact', 'href' => route('admin.messages.index', ['status' => 'new']),
            ],
            [
                'label' => 'Produits actifs', 'value' => $productsActive, 'icon' => 'package',
                'trend' => null, 'spark' => $daily(Product::class),
                'note' => 'sur '.$productsTotal.' produit(s)', 'href' => route('admin.products.index'),
            ],
        ];

        // ---------- Répartition des commandes par statut ----------
        $counts = Order::select('status', DB::raw('COUNT(*) as n'))->groupBy('status')->pluck('n', 'status');
        $palette = ['pending' => '#e0a93b', 'confirmed' => '#4f8fdc', 'delivered' => '#4cae7a', 'cancelled' => '#d9605b'];
        $statusData = [];
        foreach (Order::STATUSES as $key => $label) {
            $statusData[] = ['key' => $key, 'label' => $label, 'n' => (int) ($counts[$key] ?? 0), 'color' => $palette[$key] ?? '#999'];
        }

        // ---------- Listes ----------
        $topProducts = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('order_items.product_name as name, SUM(order_items.quantity) as qty, SUM(order_items.price * order_items.quantity) as amount')
            ->groupBy('order_items.product_name')
            ->orderByDesc('qty')->limit(5)->get();

        return view('admin.dashboard', [
            'kpis'            => $kpis,
            'chart'           => ['labels' => $labels, 'orders' => $orders, 'revenue' => $revenue],
            'statusData'      => $statusData,
            'statusTotal'     => array_sum(array_column($statusData, 'n')),
            'recentOrders'    => Order::with('user')->latest()->take(6)->get(),
            'pendingClients'  => User::where('role', 'client')->where('is_approved', false)->latest()->take(5)->get(),
            'recentMessages'  => ContactMessage::latest()->take(5)->get(),
            'topProducts'     => $topProducts,
            'categories'      => Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
                                    ->orderByDesc('products_count')->take(6)->get(),
        ]);
    }
}
