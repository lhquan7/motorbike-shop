@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- Header & Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-bar-chart-line me-2"></i>Báo cáo & Thống kê</h3>
            <div class="d-flex gap-2 flex-wrap mt-2">
                <a href="{{ route('admin.reports.exportOrders', ['year'=>$year,'month'=>$month]) }}"
                   class="btn btn-success btn-sm shadow-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i>Excel đơn hàng
                </a>
                <a href="{{ route('admin.reports.exportRevenue', ['year'=>$year]) }}"
                   class="btn btn-success btn-sm shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel doanh thu
                </a>
                <a href="{{ route('admin.reports.exportPdf', ['year'=>$year,'month'=>$month]) }}"
                   class="btn btn-danger btn-sm shadow-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Xuất PDF
                </a>
            </div>
        </div>

        {{-- Bộ lọc năm/tháng --}}
        <form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex gap-2 align-items-center bg-white p-2 rounded shadow-sm">
            <select name="year" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                @foreach(range(now()->year, now()->year - 3) as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                @endforeach
            </select>
            <select name="month" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                <option value="">Cả năm</option>
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </form>
    </div>

    {{-- ── KPI Cards ────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        @php
        $kpis = [
            ['label'=>'Doanh thu','value'=>number_format($summary['total_revenue']).'đ','icon'=>'currency-dollar','bg'=>'primary','sub'=>'Đơn hoàn thành'],
            ['label'=>'Tổng đơn hàng','value'=>$summary['total_orders'],'icon'=>'bag-check','bg'=>'success','sub'=>'Tất cả trạng thái'],
            ['label'=>'Đơn hoàn thành','value'=>$summary['completed'],'icon'=>'check-circle','bg'=>'info','sub'=>'Giao thành công'],
            ['label'=>'Đơn hủy','value'=>$summary['cancelled'],'icon'=>'x-circle','bg'=>'danger','sub'=>'Đã hủy'],
            ['label'=>'TB mỗi đơn','value'=>number_format($summary['avg_order']).'đ','icon'=>'graph-up','bg'=>'warning','sub'=>'Giá trị trung bình'],
        ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="col-6 col-md {{ $loop->last ? 'col-12 col-sm-6 col-md' : '' }}">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">{{ $kpi['label'] }}</div>
                        <div class="fw-bold fs-5">{{ $kpi['value'] }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $kpi['sub'] }}</div>
                    </div>
                    <div class="rounded-circle bg-{{ $kpi['bg'] }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
                        <i class="bi bi-{{ $kpi['icon'] }} text-{{ $kpi['bg'] }} fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Biểu đồ hàng trên ───────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        {{-- Doanh thu theo tháng --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Doanh thu theo tháng — {{ $year }}</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:11px;">■ Doanh thu</span>
                        <span class="badge bg-success bg-opacity-10 text-success" style="font-size:11px;">■ Đơn hàng</span>
                    </div>
                </div>
                <div style="position:relative;height:280px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Trạng thái đơn hàng --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h6 class="fw-bold mb-3">Trạng thái đơn hàng</h6>
                <div style="position:relative;height:210px;display:flex;align-items:center;justify-content:center;">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-3">
                    @php 
                        $statusColors = ['pending'=>'#ffc107','confirmed'=>'#0dcaf0','delivering'=>'#0d6efd','completed'=>'#198754','cancelled'=>'#dc3545'];
                        $statusLabels = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','delivering'=>'Đang giao','completed'=>'Hoàn thành','cancelled'=>'Đã hủy']; 
                    @endphp
                    @foreach($ordersByStatus as $status => $count)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:10px;height:10px;border-radius:50%;background:{{ $statusColors[$status] ?? '#888' }};flex-shrink:0;"></div>
                            <span style="font-size:12px;">{{ $statusLabels[$status] ?? $status }}</span>
                        </div>
                        <span class="fw-bold" style="font-size:13px;">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Biểu đồ hàng dưới ───────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        {{-- Doanh thu theo ngày --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h6 class="fw-bold mb-3">Doanh thu theo ngày — Tháng {{ $targetMonth }}/{{ $year }}</h6>
                <div style="position:relative;height:220px;">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Phương thức thanh toán --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h6 class="fw-bold mb-3">Phương thức thanh toán</h6>
                <div style="position:relative;height:180px;display:flex;align-items:center;justify-content:center;">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="row g-2 mt-2 text-center">
                    <div class="col-6">
                        <div class="p-2 rounded" style="background:#d1fae5;">
                            <div class="fw-bold text-success">{{ $paymentMethods->get('cod', 0) }}</div>
                            <div style="font-size:11px;color:#065f46;">💵 COD</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded" style="background:#dbeafe;">
                            <div class="fw-bold text-primary">{{ $paymentMethods->get('bank_transfer', 0) }}</div>
                            <div style="font-size:11px;color:#1e40af;">🏦 Chuyển khoản</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Top xe bán chạy ─────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm p-4 mb-4">
        <h6 class="fw-bold mb-4">🏆 Top {{ $topProducts->count() }} xe bán chạy nhất — {{ $year }}</h6>
        <div class="row g-4">
            {{-- Bảng --}}
            <div class="col-md-6">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr><th>#</th><th>Tên xe</th><th>Số lượng</th><th>Doanh thu</th></tr>
                        </thead>
                        <tbody>
                        @foreach($topProducts as $i => $p)
                        <tr>
                            <td>
                                @if($i == 0) 🥇 @elseif($i == 1) 🥈 @elseif($i == 2) 🥉 @else {{ $i+1 }} @endif
                            </td>
                            <td class="fw-semibold" style="max-width:180px;">{{ Str::limit($p->product_name, 30) }}</td>
                            <td><span class="badge bg-primary">{{ $p->total_qty }} xe</span></td>
                            <td class="text-danger fw-semibold">{{ number_format($p->total_revenue) }}đ</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Bar chart ngang --}}
            <div class="col-md-6">
                <div style="position:relative;height:{{ max(200, $topProducts->count() * 40) }}px;">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Cấu hình chung ───────────────────────────────────────────────────
Chart.defaults.font.family = "'Segoe UI', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#6c757d';
const fmt = v => new Intl.NumberFormat('vi-VN').format(v) + 'đ';

// ── 1. Doanh thu theo tháng (Bar + Line combo) ───────────────────────
const revenueData = @json($revenueChart);
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: revenueData.map(d => d.month),
        datasets: [
            {
                label: 'Doanh thu',
                data: revenueData.map(d => d.revenue),
                backgroundColor: 'rgba(13,110,253,0.15)',
                borderColor: '#0d6efd',
                borderWidth: 2,
                borderRadius: 6,
                yAxisID: 'y',
            },
            {
                label: 'Số đơn',
                data: revenueData.map(d => d.orders),
                type: 'line',
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.1)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#198754',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.datasetIndex === 0
                        ? ' Doanh thu: ' + fmt(ctx.raw)
                        : ' Đơn hàng: ' + ctx.raw
                }
            }
        },
        scales: {
            y:  { position: 'left', grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: v => (v/1000000).toFixed(0)+'tr' } },
            y1: { position: 'right', grid: { display: false }, ticks: { stepSize: 1 } },
            x:  { grid: { display: false } },
        }
    }
});

// ── 2. Trạng thái đơn hàng (Doughnut) ───────────────────────────────
const statusData   = @json($ordersByStatus);
const statusLabels = { pending:'Chờ xác nhận', confirmed:'Đã xác nhận', delivering:'Đang giao', completed:'Hoàn thành', cancelled:'Đã hủy' };
const statusColors = { pending:'#ffc107', confirmed:'#0dcaf0', delivering:'#0d6efd', completed:'#198754', cancelled:'#dc3545' };
const statusKeys   = Object.keys(statusData);
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusKeys.map(k => statusLabels[k] || k),
        datasets: [{ data: Object.values(statusData), backgroundColor: statusKeys.map(k => statusColors[k] || '#888'), borderWidth: 0, hoverOffset: 6 }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false } } }
});

// ── 3. Doanh thu theo ngày (Area) ────────────────────────────────────
const dailyData = @json($dailyChart);
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: dailyData.map(d => 'N'+d.day),
        datasets: [{
            label: 'Doanh thu',
            data: dailyData.map(d => d.revenue),
            borderColor: '#e63946',
            backgroundColor: 'rgba(230,57,70,0.08)',
            borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#e63946',
            tension: 0.4, fill: true,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' ' + fmt(ctx.raw) } } },
        scales: {
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: v => (v/1000000).toFixed(1)+'tr' } },
            x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } },
        }
    }
});

// ── 4. Phương thức thanh toán (Pie) ─────────────────────────────────
const payData = @json($paymentMethods);
new Chart(document.getElementById('paymentChart'), {
    type: 'pie',
    data: {
        labels: ['COD', 'Chuyển khoản'],
        datasets: [{ data: [payData.cod ?? 0, payData.bank_transfer ?? 0], backgroundColor: ['#198754','#0d6efd'], borderWidth: 0, hoverOffset: 6 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 12 } } } } }
});

// ── 5. Top xe bán chạy (Bar ngang) ───────────────────────────────────
const topData = @json($topProducts);
new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: topData.map(d => d.product_name.length > 22 ? d.product_name.substring(0,22)+'...' : d.product_name),
        datasets: [{
            label: 'Số lượng bán',
            data: topData.map(d => d.total_qty),
            backgroundColor: topData.map((_, i) => `hsla(${210 + i*12}, 80%, ${55 - i*2}%, 0.85)`),
            borderRadius: 6, borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ' ' + ctx.raw + ' xe — ' + fmt(topData[ctx.dataIndex].total_revenue) } }
        },
        scales: {
            x: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { stepSize: 1 } },
            y: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush