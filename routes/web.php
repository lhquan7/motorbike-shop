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

// ── FRONTEND ──────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

// Tìm kiếm AJAX
Route::get('/search/ajax', [SearchController::class, 'ajax'])->name('search.ajax');

// Giỏ hàng
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Đặt hàng
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderCode}', [CheckoutController::class, 'success'])->name('checkout.success');

// ── THANH TOÁN ONLINE ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/payment/vnpay/redirect', [PaymentController::class, 'redirectVNPay'])->name('payment.vnpay.redirect');
    Route::get('/payment/momo/redirect',  [PaymentController::class, 'redirectMoMo'])->name('payment.momo.redirect');
});

// Callbacks không cần auth
Route::get('/payment/vnpay/return',  [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::get('/payment/momo/return',   [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
Route::post('/payment/momo/notify',  [PaymentController::class, 'momoNotify'])->name('payment.momo.notify');

// ── PROFILE KHÁCH HÀNG ────────────────────────────────────────────────
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',                        [ProfileController::class, 'index'])->name('index');
    Route::put('/update',                  [ProfileController::class, 'update'])->name('update');
    Route::put('/change-password',         [ProfileController::class, 'changePassword'])->name('changePassword');
    Route::get('/orders/{orderCode}',      [ProfileController::class, 'orderDetail'])->name('orderDetail');
});

// ── AUTH ──────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── ADMIN ─────────────────────────────────────────────────────────────
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