<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ReportController;

// Các tuyến chính của hệ thống MotoShop.
// Đây là nơi liên kết URL với controller tương ứng, phân chia rõ giữa frontend, payment, profile và admin.

// ── FRONTEND ──────────────────────────────────────────────────────────
// Trang chủ và cửa hàng.
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

// Tìm kiếm AJAX
// Dùng để tìm sản phẩm ngay trong trang shop mà không cần tải lại toàn bộ trang.
Route::get('/search/ajax', [SearchController::class, 'ajax'])->name('search.ajax');

// Giỏ hàng
// Các route này quản lý hiển thị, thêm, xoá và xóa toàn bộ giỏ hàng.
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Đặt hàng
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderCode}', [CheckoutController::class, 'success'])->name('checkout.success');

// ── THANH TOÁN ONLINE ─────────────────────────────────────────────────
// Các route chuyển hướng người dùng sang cổng thanh toán và nhận callback trả về.
Route::middleware('auth')->group(function () {
    Route::get('/payment/vnpay/redirect', [PaymentController::class, 'redirectVNPay'])->name('payment.vnpay.redirect');
    Route::get('/payment/momo/redirect',  [PaymentController::class, 'redirectMoMo'])->name('payment.momo.redirect');
});

// Callbacks không cần auth
// Cổng thanh toán sẽ gọi lại URL này sau khi xử lý giao dịch.
Route::get('/payment/vnpay/return',  [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::get('/payment/momo/return',   [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
Route::post('/payment/momo/notify',  [PaymentController::class, 'momoNotify'])->name('payment.momo.notify');

// ── PROFILE KHÁCH HÀNG ────────────────────────────────────────────────
// Quản lý thông tin khách hàng và lịch sử đơn hàng cá nhân.
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',                        [ProfileController::class, 'index'])->name('index');
    Route::put('/update',                  [ProfileController::class, 'update'])->name('update');
    Route::put('/change-password',         [ProfileController::class, 'changePassword'])->name('changePassword');
    Route::get('/orders/{orderCode}',      [ProfileController::class, 'orderDetail'])->name('orderDetail');
});

// ── AUTH ──────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── ADMIN ─────────────────────────────────────────────────────────────
// Các route quản lý dành cho admin, chỉ truy cập được khi đã đăng nhập và có quyền admin.
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý xe máy
    Route::resource('products', ProductController::class);

    // Quản lý danh mục
    Route::resource('categories', CategoryController::class);

    // Quản lý hãng xe
    Route::resource('brands', BrandController::class);

    // Quản lý đơn hàng
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Quản lý bài viết
    Route::resource('posts', PostController::class);

    // Báo cáo — phải đặt trước resource để tránh conflict
    Route::get('reports',                [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/orders',  [ReportController::class, 'exportOrdersExcel'])->name('reports.exportOrders');
    Route::get('reports/export/revenue', [ReportController::class, 'exportRevenueExcel'])->name('reports.exportRevenue');
    Route::get('reports/export/pdf',     [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
});