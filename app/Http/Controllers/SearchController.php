<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller {
    public function ajax(Request $request) {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $products = Product::with(['brand','category'])
            ->where('is_active', true)
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$q}%"))
                      ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$q}%"));
            })
            ->take(8)->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'slug'  => $p->slug,
                'price' => number_format($p->final_price).'đ',
                'brand' => $p->brand->name ?? '',
                'image' => $p->image ? asset('storage/'.$p->image) : null,
                'stock' => $p->stock,
            ]);

        return response()->json($products);
    }
}