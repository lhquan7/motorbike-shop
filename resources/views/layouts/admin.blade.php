<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Cửa hàng xe máy</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .sidebar { 
            width: 250px; 
            min-height: 100vh; 
            background: #1a1a2e; 
            position: sticky;
            top: 0;
        }
        .sidebar .nav-link { 
            color: #adb5bd; 
            padding: 10px 20px; 
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            color: #fff; 
            background: rgba(255,255,255,0.1); 
            border-radius: 8px; 
        }
        .sidebar .brand { 
            color: #fff; 
            font-size: 1.2rem; 
            font-weight: bold; 
            padding: 20px; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        .main-content { 
            flex: 1; 
            background: #f8f9fa; 
        }
        .stat-card { 
            border-radius: 12px; 
            border: none; 
        }

        /* ===== BẢO HIỂM FIX LỖI ICON PHÂN TRANG TO KHỔNG LỒ ===== */
        nav[role="navigation"] svg,
        .pagination svg {
            width: 16px !important;
            height: 16px !important;
            display: inline-block;
            vertical-align: middle;
        }
        nav[role="navigation"] .flex,
        .pagination .flex {
            display: inline-flex;
            align-items: center;
        }
        nav[role="navigation"] p,
        .pagination p {
            margin-bottom: 0 !important;
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    <nav class="sidebar d-flex flex-column p-2 shadow">
        <div class="brand">
            <i class="bi bi-bicycle me-2"></i>Xe Máy Admin
        </div>
        
        <ul class="nav flex-column mt-3 gap-1">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-bar-chart-line me-2"></i>Báo cáo
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                    <i class="bi bi-bicycle me-2"></i>Xe máy
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-grid me-2"></i>Danh mục
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                    <i class="bi bi-award me-2"></i>Hãng xe
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <i class="bi bi-bag-check me-2"></i>Đơn hàng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">
                    <i class="bi bi-newspaper me-2"></i>Bài viết
                </a>
            </li>
            
            <li class="nav-item mt-5">
                <hr class="text-white-50">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="bi bi-house me-2"></i>Xem website
                </a>
            </li>
            
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link border-0 bg-transparent w-100 text-start text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <div class="main-content p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>