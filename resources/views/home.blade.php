@extends('layouts.app')
@section('title', 'MotoShop - Cửa Hàng Xe Máy')

@push('styles')
<style>
.hero-swiper { height: 520px; }
.hero-slide { height: 520px; display: flex; align-items: center; position: relative; }

/* Thay thế các lớp màu tĩnh bằng ảnh nền động thực tế kết hợp hiệu ứng phủ tối */
.hero-slide.slide-1 { 
    background: url('https://i.pinimg.com/736x/6b/71/d6/6b71d660b8fd2d0034a7d0d3a22beb67.jpg') center/cover no-repeat; 
}
.hero-slide.slide-2 { 
    background: url('https://i.pinimg.com/736x/7a/c0/53/7ac0534c150a51201a761a99030b6f51.jpg') center/cover no-repeat; 
}
.hero-slide.slide-3 { 
    background: url('https://i.pinimg.com/1200x/25/a6/6a/25a66a451a9a26fcbe9c3b7cff9308fd.jpg') center/cover no-repeat; 
}

/* Lớp phủ Gradient bảo vệ chữ khỏi lóa màu ảnh nền */
.hero-slide::before {
    content: '';
    position: absolute;
    top: 0;
    start: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 100%);
    z-index: 1;
}

/* Đảm bảo toàn bộ nội dung container nổi lên trên lớp phủ */
.hero-slide .container {
    position: relative;
    z-index: 2;
}

.hero-badge { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 6px 16px; border-radius: 50px; font-size: 0.85rem; display: inline-block; margin-bottom: 16px; }
.hero-title { font-size: 3rem; font-weight: 800; color: #fff; line-height: 1.2; }
.hero-subtitle { color: rgba(255,255,255,0.85); font-size: 1.1rem; }
.hero-img { max-height: 380px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.6)); object-fit: contain; }
.section-title { font-size: 1.8rem; font-weight: 700; }
.section-title span { color: var(--primary); }
.brand-card { border-radius: 12px; border: 2px solid transparent; transition: all .2s; cursor: pointer; }
.brand-card:hover { border-color: var(--primary); transform: translateY(-3px); box-shadow: 0 8px 24px rgba(230,57,70,0.15); }
.brand-logo { width: 80px; height: 80px; object-fit: contain; }
.feature-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 12px; }
.swiper-button-next, .swiper-button-prev { color: #fff !important; } /* Đổi màu nút điều hướng sang trắng để nổi bật trên nền ảnh */
.thumb-swiper .swiper-slide { opacity: 0.5; cursor: pointer; }
.thumb-swiper .swiper-slide-thumb-active { opacity: 1; }
</style>
@endpush

@section('content')

{{-- ===== HERO SLIDER ===== --}}
<div class="swiper hero-swiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide hero-slide slide-1">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6" data-aos="fade-right">
                        <span class="hero-badge"><i class="bi bi-star-fill me-1"></i>Đại lý Honda chính hãng</span>
                        <h1 class="hero-title">Honda Wave Alpha<br><span style="color:#ffd166">Vua xe số</span> 2024</h1>
                        <p class="hero-subtitle mt-3 mb-4">Tiết kiệm nhiên liệu, bền bỉ, giá tốt nhất thị trường</p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('shop.index', ['brand'=>'honda']) }}" class="btn btn-primary btn-lg px-4">Xem ngay <i class="bi bi-arrow-right ms-1"></i></a>
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-light btn-lg px-4">Tất cả xe</a>
                        </div>
                    </div>
                    <div class="col-md-6 text-center d-none d-md-block">
                        <img src="https://hondadoanhthu.com.vn/wp-content/uploads/2022/01/eOrVrOLAOD0Fvshzhu3G.jpg" class="hero-img img-fluid" alt="Honda Wave">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="swiper-slide hero-slide slide-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="hero-badge"><i class="bi bi-lightning-fill me-1"></i>Yamaha chính hãng</span>
                        <h1 class="hero-title">Yamaha Exciter 155<br><span style="color:#a8edea">Sức mạnh vượt trội</span></h1>
                        <p class="hero-subtitle mt-3 mb-4">Động cơ VVA mạnh mẽ, thiết kế thể thao cực đỉnh</p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('shop.index', ['brand'=>'yamaha']) }}" class="btn btn-light btn-lg px-4">Xem ngay <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6 text-center d-none d-md-block">
                        <img src="https://i.pinimg.com/736x/cd/c9/99/cdc9990e1dbddb79b4afc9d022919d94.jpg" class="hero-img img-fluid" alt="Yamaha Exciter">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="swiper-slide hero-slide slide-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="hero-badge"><i class="bi bi-fire me-1"></i>Ưu đãi tháng này</span>
                        <h1 class="hero-title">Honda Vision 110<br><span style="color:#ffd166">Giảm đến 5 triệu</span></h1>
                        <p class="hero-subtitle mt-3 mb-4">Xe tay ga thời trang, tiết kiệm xăng vượt trội</p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('shop.index') }}" class="btn btn-warning btn-lg px-4 text-dark fw-bold">Mua ngay</a>
                        </div>
                    </div>
                    <div class="col-md-6 text-center d-none d-md-block">
                        <img src="https://i.pinimg.com/736x/64/91/af/6491af7d5b0f8559c1ad0299b47dd3cf.jpg" class="hero-img img-fluid" alt="Honda Vision">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

{{-- ===== FEATURES BAR ===== --}}
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <div class="feature-icon bg-primary bg-opacity-10 text-primary mx-auto"><i class="bi bi-shield-check"></i></div>
                <div class="fw-600 small">Chính hãng 100%</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-icon bg-success bg-opacity-10 text-success mx-auto"><i class="bi bi-truck"></i></div>
                <div class="fw-600 small">Giao xe tận nhà</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-icon bg-warning bg-opacity-10 text-warning mx-auto"><i class="bi bi-tools"></i></div>
                <div class="fw-600 small">Bảo hành 3 năm</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="feature-icon bg-info bg-opacity-10 text-info mx-auto"><i class="bi bi-headset"></i></div>
                <div class="fw-600 small">Hỗ trợ 24/7</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== BRANDS ===== --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Thương hiệu <span>nổi tiếng</span></h2>
            <p class="text-muted">Đại lý chính hãng các thương hiệu hàng đầu thế giới</p>
        </div>
        <div class="row g-3 justify-content-center">
            @foreach($brands as $brand)
            <div class="col-4 col-md-2">
                <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}" class="text-decoration-none">
                    <div class="brand-card card text-center p-3">
                        <div class="fw-bold text-dark">{{ $brand->name }}</div>
                        <small class="text-muted">Chính hãng</small>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== XE NỔI BẬT ===== --}}
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title mb-1">Xe <span>nổi bật</span></h2>
                <p class="text-muted mb-0">Được nhiều khách hàng lựa chọn nhất</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="swiper featured-swiper pb-4">
            <div class="swiper-wrapper">
                @forelse($featured as $product)
                <div class="swiper-slide">
                    @include('partials.product-card', ['product' => $product])
                </div>
                @empty
                @foreach($latest->take(4) as $product)
                <div class="swiper-slide">
                    @include('partials.product-card', ['product' => $product])
                </div>
                @endforeach
                @endforelse
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

{{-- ===== XE MỚI NHẤT ===== --}}
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title mb-1">Xe <span>mới nhất</span></h2>
                <p class="text-muted mb-0">Cập nhật mẫu xe mới liên tục</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            @foreach($latest as $product)
            <div class="col-6 col-md-3">
                @include('partials.product-card', ['product' => $product])
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== BANNER GIỮA TRANG ===== --}}
<section class="py-5" style="background: linear-gradient(135deg, #1a1a2e, #e63946); border-radius: 16px; margin-bottom: 30px;">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-2">🔥 Ưu đãi đặc biệt tháng này</h2>
        <p class="lead mb-4 opacity-75">Mua xe trả góp 0% lãi suất — Tặng phụ kiện trị giá 2 triệu đồng</p>
        <a href="{{ route('shop.index') }}" class="btn btn-warning btn-lg px-5 text-dark fw-bold">Mua ngay</a>
    </div>
</section>

@endsection

@push('scripts')
<script>
new Swiper('.hero-swiper', {
    loop: true, autoplay: { delay: 5000 }, speed: 800,
    pagination: { el: '.hero-swiper .swiper-pagination', clickable: true },
    navigation: { nextEl: '.hero-swiper .swiper-button-next', prevEl: '.hero-swiper .swiper-button-prev' }
});
new Swiper('.featured-swiper', {
    loop: true, slidesPerView: 1, spaceBetween: 20,
    pagination: { el: '.featured-swiper .swiper-pagination', clickable: true },
    breakpoints: { 576: { slidesPerView: 2 }, 768: { slidesPerView: 3 }, 1024: { slidesPerView: 4 } }
});
</script>
@endpush