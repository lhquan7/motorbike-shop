<?php
namespace App\Exports;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\{
    FromQuery, WithHeadings, WithMapping,
    WithStyles, WithColumnWidths, WithTitle,
    ShouldAutoSize
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Alignment, Border};

class OrdersExport implements FromQuery, WithHeadings, WithMapping,
                               WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    public function __construct(
        private int    $year,
        private ?int   $month = null,
        private string $status = ''
    ) {}

    public function title(): string { return 'Đơn hàng'; }

    public function query() {
        return Order::with('items')
            ->whereYear('created_at', $this->year)
            ->when($this->month,  fn($q) => $q->whereMonth('created_at', $this->month))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest();
    }

    public function headings(): array {
        return ['Mã đơn', 'Khách hàng', 'Điện thoại', 'Địa chỉ',
                'Số xe', 'Tổng tiền (đ)', 'Thanh toán', 'Trạng thái', 'Ngày đặt'];
    }

    public function map($order): array {
        $paymentMap = ['cod' => 'Tiền mặt (COD)', 'bank_transfer' => 'Chuyển khoản'];
        $statusMap  = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận',
                       'delivering'=>'Đang giao','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'];
        return [
            $order->order_code,
            $order->customer_name,
            $order->customer_phone,
            $order->customer_address,
            $order->items->sum('quantity'),
            number_format($order->total_amount),
            $paymentMap[$order->payment_method] ?? $order->payment_method,
            $statusMap[$order->status] ?? $order->status,
            $order->created_at->format('H:i d/m/Y'),
        ];
    }

    public function columnWidths(): array {
        return ['A'=>18,'B'=>22,'C'=>15,'D'=>36,'E'=>10,'F'=>18,'G'=>20,'H'=>18,'I'=>20];
    }

    public function styles(Worksheet $sheet) {
        $lastRow = $sheet->getHighestRow();

        // Header row
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF'],'size'=>12],
            'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1A1A2E']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Data rows — zebra stripe
        for ($r = 2; $r <= $lastRow; $r++) {
            $color = ($r % 2 === 0) ? 'F8F9FA' : 'FFFFFF';
            $sheet->getStyle("A{$r}:I{$r}")->applyFromArray([
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$color]],
                'alignment' => ['vertical'=>Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(22);
        }

        // Tổng tiền cột F — căn phải
        $sheet->getStyle("F2:F{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Border toàn bảng
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'DEE2E6']]]
        ]);

        // Freeze header
        $sheet->freezePane('A2');

        return [];
    }
}