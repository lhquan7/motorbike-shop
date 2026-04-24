<?php
namespace App\Exports;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportPdfExport
{
    public function __construct(
        private int  $year,
        private ?int $month = null
    ) {}

    public function download(): \Symfony\Component\HttpFoundation\Response
    {
        $monthlyRevenue = Order::where('status','completed')
            ->whereYear('created_at', $this->year)
            ->when($this->month, fn($q) => $q->whereMonth('created_at', $this->month))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('month')->orderBy('month')->get();

        $topProducts = DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->where('orders.status','completed')
            ->whereYear('orders.created_at', $this->year)
            ->when($this->month, fn($q) => $q->whereMonth('orders.created_at', $this->month))
            ->select('order_items.product_name',
                     DB::raw('SUM(order_items.quantity) as qty'),
                     DB::raw('SUM(order_items.price * order_items.quantity) as revenue'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('qty')->take(10)->get();

        $summary = [
            'revenue'   => Order::where('status','completed')->whereYear('created_at',$this->year)
                              ->when($this->month, fn($q) => $q->whereMonth('created_at',$this->month))
                              ->sum('total_amount'),
            'orders'    => Order::whereYear('created_at',$this->year)
                              ->when($this->month, fn($q) => $q->whereMonth('created_at',$this->month))
                              ->count(),
            'completed' => Order::where('status','completed')->whereYear('created_at',$this->year)
                              ->when($this->month, fn($q) => $q->whereMonth('created_at',$this->month))
                              ->count(),
        ];

        $pdf = Pdf::loadView('exports.report-pdf', compact(
            'monthlyRevenue','topProducts','summary','this'
        ))->setPaper('a4','portrait');

        $filename = 'BaoCao_MotoShop_'.$this->year.($this->month ? '_T'.$this->month : '').'.pdf';
        return $pdf->download($filename);
    }
}