<?php
namespace App\Http\Controllers;

use App\Models\{Order, OrderItem, Product};
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    // ── Trang checkout ────────────────────────────────────────────────
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        return view('checkout.index', compact('cart'));
    }

    // ── Xử lý đặt hàng ───────────────────────────────────────────────
    public function store(Request $request)
    {
        // ── 1. Validate ───────────────────────────────────────────────
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'customer_email'   => 'nullable|email|max:255',
            'payment_method'   => 'required|in:cod,vnpay,momo,bank_transfer',
            'note'             => 'nullable|string|max:500',
        ]);

        // ── 2. Kiểm tra giỏ hàng ─────────────────────────────────────
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng trống, vui lòng thêm xe trước khi đặt hàng!');
        }

        // ── 3. Tính tổng tiền ─────────────────────────────────────────
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // ── 4. Tạo đơn hàng ───────────────────────────────────────────
        $order = Order::create([
            'order_code'       => 'MBK-' . strtoupper(Str::random(8)),
            'user_id'          => auth()->id(),
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'customer_email'   => $request->customer_email,
            'customer_address' => $request->customer_address,
            'total_amount'     => $total,
            'payment_method'   => $request->payment_method,
            'payment_status'   => 'unpaid',
            'status'           => 'pending',
            'note'             => $request->note,
        ]);

        // ── 5. Tạo order items + trừ tồn kho ─────────────────────────
        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $productId,
                'product_name' => $item['name'],
                'price'        => $item['price'],
                'quantity'     => $item['quantity'],
            ]);

            // Trừ tồn kho (không cho âm)
            Product::where('id', $productId)
                ->where('stock', '>', 0)
                ->decrement('stock', $item['quantity']);
        }

        // ── 6. Xóa giỏ hàng ──────────────────────────────────────────
        session()->forget('cart');

        // ── 7. Redirect theo phương thức thanh toán ───────────────────
        $paymentMethod = $request->payment_method;

        // VNPay → redirect sang cổng VNPay
        if ($paymentMethod === 'vnpay') {
            return redirect()->route('payment.vnpay.redirect', [
                'order_code' => $order->order_code,
            ]);
        }

        // MoMo → redirect sang cổng MoMo
        if ($paymentMethod === 'momo') {
            return redirect()->route('payment.momo.redirect', [
                'order_code' => $order->order_code,
            ]);
        }

        // COD / Chuyển khoản → gửi mail + trang success
        if ($order->customer_email) {
            try {
                Mail::to($order->customer_email)
                    ->queue(new OrderConfirmationMail($order->load('items')));
            } catch (\Exception $e) {
                \Log::error('Checkout mail error: ' . $e->getMessage());
            }
        }

        return redirect()->route('checkout.success', $order->order_code);
    }

    // ── Trang đặt hàng thành công ─────────────────────────────────────
    public function success($orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->with('items.product')
            ->firstOrFail();

        // Chỉ cho xem nếu là chủ đơn hàng (hoặc chưa đăng nhập thì cho xem)
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}