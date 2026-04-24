<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, Brand};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller {
    public function index() {
        $products = Product::with(['category','brand'])->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create() {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories','brands'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048', // Ảnh đại diện
            'images.*'    => 'nullable|image|max:2048', // Album ảnh chi tiết
        ]);

        $data = $request->except(['image','images','_token']);
        $data['slug'] = Str::slug($request->name).'-'.time();

        // Xử lý 1 ảnh đại diện
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products','public');
        }

        // Xử lý nhiều ảnh chi tiết (Album)
        if ($request->hasFile('images')) {
            $imgs = [];
            foreach ($request->file('images') as $img) {
                $imgs[] = $img->store('products','public');
            }
            $data['images'] = $imgs; // Lưu mảng đường dẫn vào DB (đảm bảo Model đã cast sang array/json)
        }

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success','Thêm xe máy thành công!');
    }

    public function edit(Product $product) {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product','categories','brands'));
    }

    public function update(Request $request, Product $product) {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
            'images.*'    => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['image','images','_token','_method']);

        // Cập nhật ảnh đại diện mới (nếu có)
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products','public');
        }

        // Cập nhật album ảnh chi tiết mới (nếu có)
        if ($request->hasFile('images')) {
            $imgs = [];
            foreach ($request->file('images') as $img) {
                $imgs[] = $img->store('products', 'public');
            }
            $data['images'] = $imgs;
        }

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success','Cập nhật thành công!');
    }

    public function destroy(Product $product) {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success','Đã xóa xe máy!');
    }
}