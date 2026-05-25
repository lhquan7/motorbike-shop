<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#333; }
  .header { background:#1a1a2e; color:#fff; padding:24px 32px; }
  .header h1 { font-size:20px; margin-bottom:4px; }
  .header p  { font-size:11px; opacity:.75; }
  .logo { color:#e63946; font-size:22px; font-weight:bold; }
  .content { padding:24px 32px; }
  .section-title { font-size:13px; font-weight:bold; color:#1a1a2e; border-bottom:2px solid #e63946; padding-bottom:6px; margin:20px 0 12px; text-transform:uppercase; letter-spacing:.5px; }
  .kpi-row { display:flex; gap:12px; margin-bottom:20px; }
  .kpi-box { flex:1; background:#f8f9fa; border-radius:8px; padding:14px; text-align:center; border-top:3px solid #e63946; }
  .kpi-value { font-size:16px; font-weight:bold; color:#e63946; }
  .kpi-label { font-size:10px; color:#888; margin-top:4px; }
  table { width:100%; border-collapse:collapse; margin-bottom:16px; }
  th { background:#1a1a2e; color:#fff; padding:8px 10px; text-align:left; font-size:11px; }
  td { padding:7px 10px; border-bottom:1px solid #f0f0f0; }
  tr:nth-child(even) td { background:#f8f9fa; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  .badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:bold; }
  .badge-success { background:#d1fae5; color:#065f46; }
  .footer { margin-top:32px; padding:16px 32px; background:#f8f9fa; text-align:center; font-size:10px; color:#888; border-top:1px solid #e9ecef; }
  .medal-1 { color:#f59e0b; }
  .medal-2 { color:#6b7280; }
  .medal-3 { color:#92400e; }
</style>
</head>
<body>

<div class="header">
  <div class="logo">🏍️ MotoShop</div>
  <h1>BÁO CÁO KINH DOANH</h1>
  <p>
    Năm {{ $year }}
    @if(isset($month) && $month) — Tháng {{ $month }} @endif
    &nbsp;|&nbsp; Xuất lúc: {{ now()->format('H:i d/m/Y') }}
  </p>
</div>

<div class="content">

  {{-- KPI --}}
  <div class="kpi-row">
    <div class="kpi-box">
      <div class="kpi-value">{{ number_format($summary['revenue']) }}đ</div>
      <div class="kpi-label">DOANH THU</div>
    </div>
    <div class="kpi-box">
      <div class="kpi-value">{{ $summary['orders'] }}</div>
      <div class="kpi-label">TỔNG ĐƠN HÀNG</div>
    </div>
    <div class="kpi-box">
      <div class="kpi-value">{{ $summary['completed'] }}</div>
      <div class="kpi-label">ĐƠN HOÀN THÀNH</div>
    </div>
    <div class="kpi-box">
      <div class="kpi-value">
        {{ $summary['orders'] ? round($summary['completed']/$summary['orders']*100) : 0 }}%
      </div>
      <div class="kpi-label">TỶ LỆ THÀNH CÔNG</div>
    </div>
  </div>

  {{-- Doanh thu theo tháng --}}
  <div class="section-title">Doanh thu theo tháng</div>
  <table>
    <thead><tr><th>Tháng</th><th class="text-center">Số đơn HT</th><th class="text-right">Doanh thu</th><th class="text-right">TB/đơn</th></tr></thead>
    <tbody>
    @forelse($monthlyRevenue as $row)
    <tr>
      <td><strong>Tháng {{ $row->month }}/{{ $year }}</strong></td>
      <td class="text-center"><span class="badge badge-success">{{ $row->orders }} đơn</span></td>
      <td class="text-right" style="color:#e63946;font-weight:bold;">{{ number_format($row->revenue) }}đ</td>
      <td class="text-right">{{ $row->orders ? number_format($row->revenue / $row->orders) : 0 }}đ</td>
    </tr>
    @empty
    <tr><td colspan="4" class="text-center" style="color:#888;padding:16px;">Chưa có dữ liệu</td></tr>
    @endforelse
    
    @if(count($monthlyRevenue))
    <tr style="background:#fff3cd;">
      <td><strong>TỔNG CỘNG</strong></td>
      <td class="text-center"><strong>{{ $monthlyRevenue->sum('orders') }}</strong></td>
      <td class="text-right" style="color:#e63946;font-weight:bold;">{{ number_format($monthlyRevenue->sum('revenue')) }}đ</td>
      <td></td>
    </tr>
    @endif
    </tbody>
  </table>

  {{-- Top xe --}}
  <div class="section-title">Top 10 xe bán chạy</div>
  <table>
    <thead><tr><th width="30">#</th><th>Tên xe</th><th class="text-center">Số lượng</th><th class="text-right">Doanh thu</th></tr></thead>
    <tbody>
    @foreach($topProducts as $i => $p)
    <tr>
      <td class="text-center">
        @if($i===0)<span class="medal-1">🥇</span>
        @elseif($i===1)<span class="medal-2">🥈</span>
        @elseif($i===2)<span class="medal-3">🥉</span>
        @else {{ $i+1 }} @endif
      </td>
      <td>{{ $p->product_name }}</td>
      <td class="text-center"><strong>{{ $p->qty }} xe</strong></td>
      <td class="text-right" style="font-weight:bold;">{{ number_format($p->revenue) }}đ</td>
    </tr>
    @endforeach
    </tbody>
  </table>

</div>

<div class="footer">
  MotoShop Vietnam · 123 Nguyễn Huệ, Quận 1, TP.HCM · Hotline: 1800 1234 · motoshop.vn
</div>
</body>
</html>