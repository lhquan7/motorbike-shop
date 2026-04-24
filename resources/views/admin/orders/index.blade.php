@extends('layouts.admin')
@section('content')

<h3 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>Quản lý đơn hàng</h3>

{{-- Filter theo trạng thái --}}
<div class="d-flex gap-2 mb-4 flex-wrap">
    @foreach([
        ''           => 'Tất cả',
        'pending'    => 'Chờ xác nhận',
        'confirmed'  => 'Đã xác nhận',
        'delivering' => 'Đang giao',
        'completed'  => 'Hoàn thành',
        'cancelled'  => 'Đã hủy'
    ] as $val => $label)
    <a href="{{ route('admin.orders.index', ['status' => $val]) }}"
        class="btn btn-sm {{ request('status') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>SĐT</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
            @php
            $colors = [
                'pending'    => 'warning',
                'confirmed'  => 'info',
                'delivering' => 'primary',
                'completed'  => 'success',
                'cancelled'  => 'danger',
            ];
            @endphp
            <tr>
                <td class="fw-bold">{{ $order->order_code }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->customer_phone }}</td>
                <td class="fw-bold text-danger">{{ number_format($order->total_amount) }}đ</td>
                <td>
                    <span class="badge {{ $order->payment_method == 'cod' ? 'bg-secondary' : 'bg-info' }}">
                        {{ $order->payment_method == 'cod' ? 'COD' : ucfirst($order->payment_method) }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }}">
                        {{ $order->status }}
                    </span>
                </td>
                <td class="small text-muted">
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-bag-x d-block fs-3 mb-2"></i>
                    Chưa có đơn hàng nào.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $orders->links() }}
    </div>
</div>

@endsection