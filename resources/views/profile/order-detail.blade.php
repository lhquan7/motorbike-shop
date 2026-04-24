@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng '.$order->order_code)

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('profile.index') }}#orders" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
        <h4 class="fw-bold mb-0">Chi tiết đơn hàng</h4>
    </div>

    @php
        $statusColors = ['pending'=>'warning','confirmed'=>'info','delivering'=>'primary','completed'=>'success','cancelled'=>'danger'];
        $statusLabels = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','delivering'=>'Đang giao hàng','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'];
        $steps = ['pending','confirmed','delivering','completed'];
        $currentStep = array_search($order->status, $steps);
    @endphp

    {{-- Thanh tiến trình --}}
    @if($order->status !== 'cancelled')
    <div class="card border-0 shadow-sm p-4 mb-4">
        <div class="d-flex justify-content-between position-relative">
            <div class="position-absolute top-50 start-0 end-0" style="height:3px;background:#e9ecef;transform:translateY(-50%);z-index:0;margin:0 10%;"></div>
            @foreach(['pending'=>'Đặt hàng','confirmed'=>'Xác nhận','delivering'=>'Đang giao','completed'=>'Hoàn thành'] as $step => $label)
            @php $idx = array_search($step, $steps); $done = $currentStep !== false && $idx <= $currentStep; @endphp
            <div class="text-center position-relative" style="z-index:1;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                    style="width:40px;height:40px;background:{{ $done ? '#e63946' : '#e9ecef' }};color:{{ $done ? '#fff' : '#adb5bd' }};">
                    @if($done) <i class="bi bi-check-lg"></i> @else <i class="bi bi-circle"></i> @endif
                </div>
                <div style="font-size:12px;font-weight:{{ $done ? '600' : '400' }};color:{{ $done ? '#e63946' : '#adb5bd' }};">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- Danh sách xe đặt --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Xe đã đặt</h5>
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                        @php $product = $item->product; @endphp
                        @if($product && $product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" style="width:80px;height:60px;object-fit:cover;border-radius:8px;">
                        @else
                            <div style="width:80px;height:60px;background:#f0f0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-bicycle text-muted fs-4"></i>
                            </div>
                        @endif
                        <div class="flex-fill">
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            <small class="text-muted">Số lượng: {{ $item->quantity }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ number_format($item->price) }}đ</div>
                            <small class="text-muted">= {{ number_format($item->price * $item->quantity) }}đ</small>
                        </div>
                    </div>
                    @endforeach
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <span class="fw-bold">Tổng thanh toán</span>
                        <span class="fw-bold text-danger fs-5">{{ number_format($order->total_amount) }}đ</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thông tin đơn --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 mb-3">
                <h6 class="fw-bold mb-3">Thông tin đơn hàng</h6>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Mã đơn</span>
                    <span class="fw-semibold">{{ $order->order_code }}</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Ngày đặt</span>
                    <span>{{ $order->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Thanh toán</span>
                    <span>{{ $order->payment_method == 'cod' ? 'COD' : 'Chuyển khoản' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Trạng thái</span>
                    <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
            </div>
            <div class="card border-0 shadow-sm p-4">
                <h6 class="fw-bold mb-3">Địa chỉ nhận xe</h6>
                <div class="mb-1 fw-semibold">{{ $order->customer_name }}</div>
                <div class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i>{{ $order->customer_phone }}</div>
                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $order->customer_address }}</div>
            </div>
        </div>
    </div>
</div>
@endsection