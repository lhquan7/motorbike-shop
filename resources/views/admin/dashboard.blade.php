@extends('layouts.admin')
@section('content')
<h2 class="mb-4">Dashboard</h2>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Doanh thu</p>
                    <h4>{{ number_format($totalRevenue) }}đ</h4>
                </div>
                <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Đơn hàng</p>
                    <h4>{{ $totalOrders }}</h4>
                </div>
                <i class="bi bi-bag-check fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Xe máy</p>
                    <h4>{{ $totalProducts }}</h4>
                </div>
                <i class="bi bi-bicycle fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Khách hàng</p>
                    <h4>{{ $totalUsers }}</h4>
                </div>
                <i class="bi bi-people fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Đơn hàng gần đây --}}
    <div class="col-md-8">
        <div class="card p-3">
            <h5 class="mb-3">Đơn hàng gần đây</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}">
                            {{ $order->order_code }}
                        </a>
                    </td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ number_format($order->total_amount) }}đ</td>
                    <td>
                        @php
                        $colors = [
                            'pending'   => 'warning',
                            'confirmed' => 'info',
                            'delivering'=> 'primary',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                        ];
                        @endphp
                        <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }}">
                            {{ $order->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        Chưa có đơn hàng nào
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top xe bán chạy --}}
    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="mb-3">Top xe bán chạy</h5>
            @forelse($topProducts as $p)
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                <span class="small">{{ $p->product_name }}</span>
                <span class="badge bg-primary">{{ $p->total_sold }} xe</span>
            </div>
            @empty
            <p class="text-muted text-center">Chưa có dữ liệu</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Doanh thu theo tháng --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card p-3">
            <h5 class="mb-3">Doanh thu theo tháng</h5>
            @if($monthlyRevenue->count())
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tháng</th>
                        <th class="text-end">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($monthlyRevenue as $row)
                <tr>
                    <td>Tháng {{ $row->month }}</td>
                    <td class="text-end fw-bold text-danger">
                        {{ number_format($row->revenue) }}đ
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted text-center py-3">Chưa có dữ liệu doanh thu</p>
            @endif
        </div>
    </div>
</div>

@endsection