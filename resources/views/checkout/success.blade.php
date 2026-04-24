@extends('layouts.app')
@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">
            <div class="mb-4" style="font-size: 5rem;">🎉</div>
            <h2 class="fw-bold text-success mb-2">Đặt hàng thành công!</h2>

            {{-- Thông báo thanh toán online thành công (nếu có) --}}
            @if(session('payment_success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4 text-start shadow-sm border-0">
                <i class="bi bi-shield-check fs-4"></i>
                <div>
                    <strong>{{ session('payment_success') }}</strong><br>
                    <small>Mã giao dịch đã được ghi nhận. Đơn hàng của bạn đang được xử lý.</small>
                </div>
            </div>
            @endif

            {{-- Badge trạng thái thanh toán --}}
            <div class="mb-3">
                @if($order->payment_status === 'paid')
                    <span class="badge bg-success fs-6 px-3 py-2 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i>Đã thanh toán
                    </span>
                @else
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 shadow-sm">
                        <i class="bi bi-clock me-1"></i>Chờ thanh toán
                    </span>
                @endif
            </div>

            <p class="text-muted mb-4">Cảm ơn bạn đã tin tưởng MotoShop. Chúng tôi sẽ liên hệ sớm để xác nhận đơn hàng.</p>

            <div class="card border-0 shadow-sm p-4 text-start mb-4">
                <h5 class="fw-bold mb-3">Chi tiết đơn hàng</h5>
                <div class="row g-2">
                    <div class="col-5 text-muted small">Mã đơn hàng</div>
                    <div class="col-7 fw-bold text-primary">{{ $order->order_code }}</div>
                    
                    <div class="col-5 text-muted small">Khách hàng</div>
                    <div class="col-7">{{ $order->customer_name }}</div>
                    
                    <div class="col-5 text-muted small">Số điện thoại</div>
                    <div class="col-7">{{ $order->customer_phone }}</div>
                    
                    <div class="col-5 text-muted small">Địa chỉ</div>
                    <div class="col-7 text-truncate" title="{{ $order->customer_address }}">{{ $order->customer_address }}</div>
                    
                    <div class="col-5 text-muted small">Phương thức thanh toán</div>
                    <div class="col-7">
                        @if($order->payment_method == 'cod')
                            <span class="text-dark">💵 Tiền mặt khi nhận xe</span>
                        @elseif($order->payment_method == 'momo')
                            <span class="text-dark">💳 Ví MoMo</span>
                        @else
                            <span class="text-dark">🏦 Chuyển khoản ngân hàng</span>
                        @endif
                    </div>
                    
                    <div class="col-5 text-muted small">Tổng tiền</div>
                    <div class="col-7 fw-bold price-tag fs-5 text-danger">{{ number_format($order->total_amount) }}đ</div>
                    
                    <div class="col-5 text-muted small">Trạng thái đơn hàng</div>
                    <div class="col-7">
                        <span class="badge bg-secondary">Chờ xác nhận</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center mt-4">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="bi bi-house me-1"></i>Trang chủ
                </a>
                <a href="{{ route('shop.index') }}" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-bicycle me-1"></i>Tiếp tục mua xe
                </a>
            </div>
        </div>
    </div>
</div>
@endsection