<?php
namespace App\Exports;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\{
    WithMultipleSheets, WithTitle
};
use Maatwebsite\Excel\Concerns\{
    FromCollection, WithHeadings, WithMapping,
    WithStyles, ShouldAutoSize, WithColumnWidths
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Alignment, Border};

// ── Sheet 1: Doanh thu theo tháng ──────────────────────────────────
class MonthlySheet implements FromCollection, WithHeadings, WithMapping,
                               WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private int $year) {}
    public function title(): string { return 'Doanh thu theo tháng'; }

    public function collection() {
        $data = Order::where('status','completed')
            ->whereYear('created_at', $this->year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        return collect(range(1,12))->map(fn($m) => (object)[
            'month'   => "Tháng {$m}/{$this->year}",
            'orders'  => $data->get($m)?->orders  ?? 0,
            'revenue' => $data->get($m)?->revenue  ?? 0,
        ]);
    }

    public function headings(): array {
        return ['Tháng', 'Số đơn hoàn thành', 'Doanh thu (đ)'];
    }

    public function map($row): array {
        return [$row->month, $row->orders, number_format($row->revenue)];
    }

    public function styles(Worksheet $sheet) {
        $last = $sheet->getHighestRow();
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'E63946']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getStyle("C2:C{$last}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A1:C{$last}")->applyFromArray([
            'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'DEE2E6']]]
        ]);
        return [];
    }
}

// ── Sheet 2: Top xe bán chạy ───────────────────────────────────────
class TopProductsSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private int $year) {}
    public function title(): string { return 'Top xe bán chạy'; }

    public function collection() {
        return DB::table('order_items')
            ->join('orders','orders.id','=','order_items.order_id')
            ->where('orders.status','completed')
            ->whereYear('orders.created_at',$this->year)
            ->select('order_items.product_name',
                     DB::raw('SUM(order_items.quantity) as so_luong'),
                     DB::raw('SUM(order_items.price * order_items.quantity) as doanh_thu'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('so_luong')->take(20)->get();
    }

    public function headings(): array { return ['#', 'Tên xe', 'Số lượng bán', 'Doanh thu (đ)']; }

    public function styles(Worksheet $sheet) {
        $last = $sheet->getHighestRow();
        // Thêm số thứ tự
        for ($r = 2; $r <= $last; $r++) {
            $sheet->insertNewColumnBefore('A');
            $sheet->setCellValue("A{$r}", $r - 1);
            break; // chỉ cần thêm cột, dữ liệu đã có
        }
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1A1A2E']],
        ]);
        return [];
    }
}

// ── Main Export class ──────────────────────────────────────────────
class RevenueExport implements WithMultipleSheets
{
    public function __construct(private int $year) {}

    public function sheets(): array {
        return [
            new MonthlySheet($this->year),
            new TopProductsSheet($this->year),
        ];
    }
}