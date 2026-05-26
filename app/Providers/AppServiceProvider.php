<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Bắt buộc phải thêm dòng use này

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ép các thanh phân trang (.links()) sử dụng giao diện chuẩn của Bootstrap 5
        Paginator::useBootstrapFive();
    }
}