<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cửa Hàng Xe Máy')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #e63946;
            --dark: #1a1a2e;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background: var(--dark) !important;
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: 700;
            font-size: 1.4rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.85) !important;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff !important;
            font-weight: 500;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: #c1121f;
            border-color: #c1121f;
        }

        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.7);
        }

        footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
        }

        footer a:hover {
            color: #fff;
        }

        /* Đảm bảo search result nổi lên trên các thành phần khác */
        #searchResults {
            z-index: 1060;
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-bicycle me-2"></i>MotoShop
        </a>

        {{-- Nút hamburger cho mobile --}}
        <button class="navbar-toggler text-white border-white" type="button" 
            data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            {{-- Menu trái --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" 
                        href="{{ route('home') }}">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}" 
                        href="{{ route('shop.index') }}">Cửa hàng</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Xe theo hãng
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        @foreach(\App\Models\Brand::all() as $brand)
                        <li>
                            <a class="dropdown-item" href="{{ route('shop.index', ['brand' => $brand->slug]) }}">
                                {{ $brand->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
            </ul>

            {{-- Menu phải: Search + Cart + Auth --}}
            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3">
                
                {{-- Search AJAX --}}
                <div class="position-relative" style="min-width:280px;" id="searchBox">
                    <div class="d-flex">
                        <input type="text" id="searchInput" class="form-control form-control-sm" 
                            placeholder="Tìm xe máy..." autocomplete="off">
                        <a id="searchBtn" href="{{ route('shop.index') }}" class="btn btn-sm btn-primary ms-1">
                            <i class="bi bi-search"></i>
                        </a>
                    </div>
                    <div id="searchResults" 
                        class="position-absolute top-100 start-0 end-0 mt-1 bg-white border rounded-3 shadow-lg d-none" 
                        style="max-height:420px; overflow-y:auto;">
                    </div>
                </div>

                {{-- Nhóm icon Cart & Auth --}}
                <div class="d-flex align-items-center gap-3">
                    {{-- Giỏ hàng --}}
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-sm position-relative">
                        <i class="bi bi-cart3"></i>
                        @if(count(session('cart', [])) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ count(session('cart', [])) }}
                        </span>
                        @endif
                    </a>

                    {{-- Auth --}}
                    @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Str::limit(auth()->user()->name, 12) }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Tài khoản</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}#orders"><i class="bi bi-bag me-2"></i>Đơn hàng</a></li>
                            
                            @if(auth()->user()->isAdmin())
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Trang Admin</a></li>
                            @endif
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger d-flex align-items-center">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Đăng ký</a>
                    </div>
                    @endauth
                </div>
            </div>
        </div> {{-- Kết thúc collapse --}}
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="mt-5 py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <h5 class="text-white fw-bold">MotoShop</h5>
                <p class="small mb-0">Hệ thống bán lẻ xe máy uy tín hàng đầu Việt Nam.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="small mb-0 text-white-50">
                    © {{ date('Y') }} MotoShop. Bảo lưu mọi quyền.
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
// Logic Search AJAX
(function() {
    const input = document.getElementById('searchInput');
    const results = document.getElementById('searchResults');
    const btn = document.getElementById('searchBtn');
    if (!input || !results || !btn) return;

    let timer;
    input.addEventListener('input', function() {
        const q = this.value.trim();
        btn.href = q ? '{{ route("shop.index") }}?search=' + encodeURIComponent(q) : '#';

        clearTimeout(timer);
        if (q.length < 2) {
            results.classList.add('d-none');
            return;
        }

        results.innerHTML = `<div class="p-3 text-muted small"><i class="bi bi-hourglass-split me-2"></i>Đang tìm...</div>`;
        results.classList.remove('d-none');

        timer = setTimeout(async () => {
            try {
                const res = await fetch('{{ route("search.ajax") }}?q=' + encodeURIComponent(q));
                const data = await res.json();
                renderResults(data, q);
            } catch (e) {
                results.innerHTML = `<div class="p-3 text-danger small">Lỗi kết nối</div>`;
            }
        }, 300);
    });

    function renderResults(data, q) {
        if (!data.length) {
            results.innerHTML = `<div class="p-3 text-center text-muted small">Không tìm thấy xe "<strong>${q}</strong>"</div>`;
            return;
        }
        let html = '';
        data.forEach(p => {
            html += `
                <a href="/shop/${p.slug}" class="d-flex align-items-center gap-3 px-3 py-2 text-decoration-none border-bottom bg-white">
                    <img src="${p.image || '/images/default-bike.png'}" style="width:50px;height:40px;object-fit:cover;border-radius:4px;">
                    <div class="flex-fill overflow-hidden">
                        <div class="fw-semibold text-dark small text-truncate">${p.name}</div>
                        <div class="text-danger fw-bold" style="font-size:12px;">${p.price}</div>
                    </div>
                </a>`;
        });
        html += `<a href="/shop?search=${encodeURIComponent(q)}" class="d-block text-center p-2 small text-primary fw-bold bg-light text-decoration-none">Xem tất cả</a>`;
        results.innerHTML = html;
    }

    // Đóng search khi click ra ngoài
    document.addEventListener('click', e => {
        if (!document.getElementById('searchBox').contains(e.target)) results.classList.add('d-none');
    });
})();
</script>

@stack('scripts')
</body>
</html>