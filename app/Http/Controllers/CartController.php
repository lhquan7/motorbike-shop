<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller {
    public function index() {
        $cart = session('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product) {
        $cart = session('cart', []);
        $id = $product->id;
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name'     => $product->name,
                'price'    => $product->final_price,
                'image'    => $product->image,
                'quantity' => 1,
            ];
        }
        session(['cart' => $cart]);
        return back()->with('success','Đã thêm vào giỏ hàng!');
    }

    public function remove($id) {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);
        return back()->with('success','Đã xóa khỏi giỏ hàng!');
    }

    public function clear() {
        session()->forget('cart');
        return back();
    }
}