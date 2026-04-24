@extends('layouts.app')
@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>Giỏ hàng của bạn</h2>

    @if(empty($cart))
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Giỏ hàng trống</h4>
            <a href="{{ route('shop.index') }}" class="btn btn-primary mt-3">Tiếp tục mua xe</a>
        </div>
    @else
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @foreach($cart as $id => $item)
                    <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                        <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : 'https://placehold.co/100x70/f0f0f0/999?text=Xe' }}"
                            style="width:100px;height:70px;object-fit:cover;border-radius:8px;" alt="{{ $item['name'] }}">
                        <div class="flex-fill">
                            <div class="fw-bold">{{ $item['name'] }}</div>
                            <div class="price-tag">{{ number_format($item['price']) }}đ</div>
                            <small class="text-muted">Số lượng: {{ $item['quantity'] }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ number_format($item['price'] * $item['quantity']) }}đ</div>
                            <form method="POST" action="{{ route('cart.remove', $id) }}" class="mt-1">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Tiếp tục mua</a>
                    <form method="POST" action="{{ route('cart.clear') }}">@csrf
                        <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Xóa tất cả</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>
                @php $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']); @endphp
                @foreach($cart as $item)
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">{{ Str::limit($item['name'], 25) }}</span>
                    <span>{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                </div>
                @endforeach
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Tổng cộng</span>
                    <span class="price-tag">{{ number_format($total) }}đ</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 mt-3 py-2">
                    <i class="bi bi-bag-check me-2"></i>Đặt hàng ngay
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection