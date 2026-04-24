<?php

namespace App\Http\Controllers;

use App\Models\{Product, Category, Brand};
use Illuminate\Http\Request;

class ShopController extends Controller 
{
    public function index(Request $request) 
    {
        // 1. Khởi tạo query với các mối quan hệ cần thiết
        $query = Product::with(['category', 'brand'])->where('is_active', true);

        // 2. Áp dụng các bộ lọc
        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        
        if ($request->brand) {
            $query->whereHas('brand', fn($q) => $q->where('slug', $request->brand));
        }
        
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. Thực hiện phân trang
        $products   = $query->paginate(12)->withQueryString();
        
        // 4. Lấy dữ liệu cho Sidebar (Categories & Brands)
        $categories = Category::withCount('products')->get();
        $brands     = Brand::withCount('products')->get();

        // 5. Xử lý phản hồi AJAX (Dành cho Load More hoặc Live Filter)
        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                // Đảm bảo bạn đã có file resources/views/partials/product-card.blade.php
                $html .= view('partials.product-card', compact('product'))->render();
            }

            return response()->json([
                'html'     => $html,
                'hasMore'  => $products->hasMorePages(),
                'nextPage' => $products->currentPage() + 1,
                'total'    => $products->total(),
            ]);
        }

        // 6. Trả về view mặc định
        return view('shop.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Chi tiết sản phẩm
     */
    public function show(Product $product) 
    {
        // Lấy sản phẩm liên quan cùng danh mục, loại bỏ sản phẩm hiện tại
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}