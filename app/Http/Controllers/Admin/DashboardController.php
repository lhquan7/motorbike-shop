<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Order, Product, User};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        $totalRevenue = Order::where('status','completed')->sum('total_amount');
        $totalOrders  = Order::count();
        $totalProducts = Product::count();
        $totalUsers   = User::where('role','user')->count();
        $recentOrders = Order::latest()->take(10)->get();
        $topProducts  = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)->get();
        $monthlyRevenue = Order::where('status','completed')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')->orderBy('month')->get();

        return view('admin.dashboard', compact(
            'totalRevenue','totalOrders','totalProducts','totalUsers',
            'recentOrders','topProducts','monthlyRevenue'
        ));
    }
}