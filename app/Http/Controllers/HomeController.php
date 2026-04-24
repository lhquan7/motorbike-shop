<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::where('is_featured', true)
            ->where('is_active', true)
            ->take(8)
            ->get();

        $latest = Product::where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::withCount('products')->get();
        $brands = Brand::all();

        return view('home', compact(
            'featured',
            'latest',
            'categories',
            'brands'
        ));
    }
}