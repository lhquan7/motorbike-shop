@extends('layouts.app')
@section('title', $product->name)

@push('styles')
<style>
.main-img { height: 400px; object-fit: cover; width: 100%; border-radius: 12px; }
.thumb-img { height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid transparent; transition: all .2s; }
.thumb-img:hover, .thumb-img.active { border-color: var(--primary); }
.spec-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
.spec-row:last-child { border-bottom: none; }
</style>
@endpush

@section('content')
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Cửa hàng</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- Ảnh sản phẩm --}}
        <div class="col-md-6">
            <img id="mainImg" src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/600x400/f0f0f0/999?text='.$product->name }}"
                class="main-img mb-3" alt="{{ $product->name }}">
            @if($product->images)
            <div class="d-flex gap-2 flex-wrap">
                <img src="{{ asset('storage/'.$product->image) }}" class="thumb-img active" onclick="changeImg(this, '{{ asset('storage/'.$product->image) }}')" alt="">
                @foreach($product->images as $img)
                <img src="{{ asset('storage/'.$img) }}" class="thumb-img" onclick="changeImg(this, '{{ asset('storage/'.$img) }}')" alt="">
                @endforeach
            </div>
            @endif
        </div>

        {{-- Thông tin xe --}}
        <div class="col-md-6">
            <small class="text-muted">{{ $product->brand->name ?? '' }} / {{ $product->category->name ?? '' }}</small>
            <h1 class="fw-bold mt-1 mb-3">{{ $product->name }}</h1>

            {{-- Giá --}}
            <div class="mb-4">
                @if($product->sale_price)
                    <div class="price-tag" style="font-size:2rem;">{{ number_format($product->sale_price) }}đ</div>
                    <div class="price-old" style="font-size:1.1rem;">{{ number_format($product->price) }}đ</div>
                    @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                    <span class="badge bg-danger">Tiết kiệm {{ $discount }}%</span>
                @else
                    <div class="price-tag" style="font-size:2rem;">{{ number_format($product->price) }}đ</div>
                @endif
            </div>

            {{-- Thông số kỹ thuật --}}
            <div class="card border-0 bg-light p-3 mb-4">
                <h6 class="fw-bold mb-3">Thông số kỹ thuật</h6>
                @if($product->engine)
                <div class="spec-row"><span class="text-muted">Động cơ</span><strong>{{ $product->engine }}</strong></div>
                @endif
                @if($product->color)
                <div class="spec-row"><span class="text-muted">Màu sắc</span><strong>{{ $product->color }}</strong></div>
                @endif
                @if($product->year)
                <div class="spec-row"><span class="text-muted">Năm sản xuất</span><strong>{{ $product->year }}</strong></div>
                @endif
                <div class="spec-row"><span class="text-muted">Tình trạng</span>
                    <strong class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $product->stock > 0 ? "Còn {$product->stock} xe" : 'Hết hàng' }}
                    </strong>
                </div>
            </div>

            {{-- Nút hành động --}}
            @if($product->stock > 0)
            <div class="d-flex gap-3 mb-4">
                <form method="POST" action="{{ route('cart.add', $product) }}" class="flex-fill">@csrf
                    <button class="btn btn-outline-primary btn-lg w-100"><i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ</button>
                </form>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg flex-fill">Mua ngay</a>
            </div>
            @else
                <button class="btn btn-secondary btn-lg w-100 mb-4" disabled>Xe đã hết hàng</button>
            @endif

            {{-- Cam kết --}}
            <div class="row g-2 text-center">
                <div class="col-4"><div class="small text-muted"><i class="bi bi-shield-check text-success d-block fs-5 mb-1"></i>Chính hãng</div></div>
                <div class="col-4"><div class="small text-muted"><i class="bi bi-tools text-primary d-block fs-5 mb-1"></i>Bảo hành 3 năm</div></div>
                <div class="col-4"><div class="small text-muted"><i class="bi bi-truck text-warning d-block fs-5 mb-1"></i>Giao tận nhà</div></div>
            </div>
        </div>
    </div>

    {{-- Mô tả --}}
    @if($product->description)
    <div class="card border-0 shadow-sm mt-5 p-4">
        <h4 class="fw-bold mb-3">Mô tả sản phẩm</h4>
        <div>{!! nl2br(e($product->description)) !!}</div>
    </div>
    @endif

    {{-- Xe liên quan --}}
    @if($related->count())
    <div class="mt-5">
        <h4 class="fw-bold mb-4">Xe tương tự</h4>
        <div class="row g-4">
            @foreach($related as $item)
            <div class="col-6 col-md-3">
                @include('partials.product-card', ['product' => $item])
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function changeImg(el, src) {
    document.getElementById('mainImg').src = src;
    document.querySelectorAll('.thumb-img').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}
</script>
@endpush