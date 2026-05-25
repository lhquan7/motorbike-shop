<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, Product, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{OrdersExport, RevenueExport};

// ĐÃ FIX: Thêm thư viện xuất PDF chuẩn của Laravel thay cho class export cũ
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', null);

        // Doanh thu theo tháng
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $revenueChart = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueChart[] = [
                'month'   => 'T' . $m,
                'revenue' => $monthlyRevenue->get($m)?->revenue ?? 0,
                'orders'  => $monthlyRevenue->get($m)?->orders ?? 0,
            ];
        }

        // Top sản phẩm bán chạy
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->when($month, fn($q) =>
                $q->whereMonth('orders.created_at', $month)
                  ->whereYear('orders.created_at', $year)
            )
            ->whereYear('orders.created_at', $year)
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // Đơn hàng theo trạng thái
        $ordersByStatus = Order::whereYear('created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Doanh thu theo ngày
        $targetMonth = $month ?? now()->month;

        $dailyRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $targetMonth)
            ->selectRaw('DAY(created_at) as day, SUM(total_amount) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $daysInMonth = Carbon::create($year, $targetMonth)->daysInMonth;

        $dailyChart = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dailyChart[] = [
                'day' => $d,
                'revenue' => $dailyRevenue->get($d)?->revenue ?? 0
            ];
        }

        // Tổng quan
        $summary = [
            'total_revenue' => Order::where('status', 'completed')
                ->whereYear('created_at', $year)
                ->sum('total_amount'),

            'total_orders' => Order::whereYear('created_at', $year)->count(),

            'completed' => Order::where('status', 'completed')
                ->whereYear('created_at', $year)
                ->count(),

            'cancelled' => Order::where('status', 'cancelled')
                ->whereYear('created_at', $year)
                ->count(),

            'avg_order' => Order::where('status', 'completed')
                ->whereYear('created_at', $year)
                ->avg('total_amount') ?? 0,
        ];

        // Phương thức thanh toán
        $paymentMethods = Order::whereYear('created_at', $year)
            ->selectRaw('payment_method, COUNT(*) as count')
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method');

        return view('admin.reports.index', compact(
            'revenueChart',
            'topProducts',
            'ordersByStatus',
            'dailyChart',
            'summary',
            'paymentMethods',
            'year',
            'month',
            'targetMonth'
        ));
    }

    // Export danh sách đơn hàng Excel
    public function exportOrdersExcel(Request $request)
    {
        $year   = $request->get('year', now()->year);
        $month  = $request->get('month', null);
        $status = $request->get('status', '');

        $name = 'DonHang_' . $year . ($month ? '_T' . $month : '') . '.xlsx';

        return Excel::download(
            new OrdersExport($year, $month, $status),
            $name
        );
    }

    // Export doanh thu Excel
    public function exportRevenueExcel(Request $request)
    {
        $year = $request->get('year', now()->year);

        return Excel::download(
            new RevenueExport($year),
            "DoanhThu_{$year}.xlsx"
        );
    }

    // ĐÃ FIX: Viết lại hàm Export PDF lấy dữ liệu thực tế đẩy trực tiếp sang Blade
    public function exportPdf(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', null);

        // 1. Lấy dữ liệu Doanh thu theo tháng
        $monthlyRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 2. Lấy dữ liệu Top sản phẩm bán chạy (map khớp với biến ngoài view)
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereYear('orders.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('orders.created_at', $month))
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('order_items.product_name')
            ->orderByDesc('qty')
            ->take(10)
            ->get();

        // 3. Tính toán mảng Summary tổng quan
        $summary = [
            'revenue'   => Order::where('status', 'completed')->whereYear('created_at', $year)->when($month, fn($q) => $q->whereMonth('created_at', $month))->sum('total_amount'),
            'orders'    => Order::whereYear('created_at', $year)->when($month, fn($q) => $q->whereMonth('created_at', $month))->count(),
            'completed' => Order::where('status', 'completed')->whereYear('created_at', $year)->when($month, fn($q) => $q->whereMonth('created_at', $month))->count(),
        ];

        // 4. Khởi tạo DomPDF cấu hình hỗ trợ font chữ tiếng Việt không bị ô vuông
        $pdf = Pdf::loadView('exports.report-pdf', compact('year', 'month', 'monthlyRevenue', 'topProducts', 'summary'))
                  ->setPaper('a4', 'portrait');

        $fileName = 'BaoCaoKinhDoanh_' . $year . ($month ? '_T' . $month : '') . '.pdf';
        return $pdf->download($fileName);
    }
}