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
    // Hiển thị giao diện checkout cho người dùng với dữ liệu giỏ hàng.
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
        // Kiểm tra dữ liệu nhập từ khách hàng trước khi tạo đơn hàng.
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'customer_email'   => 'nullable|email|max:255',
            'payment_method'   => 'required|in:cod,vnpay,momo,bank_transfer',
            'note'             => 'nullable|string|max:500',
        ]);

        // ── 2. Kiểm tra giỏ hàng ─────────────────────────────────────
        // Lấy dữ liệu giỏ hàng từ session và xác thực rằng giỏ không rỗng.
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng trống, vui lòng thêm xe trước khi đặt hàng!');
        }

        // ── 3. Tính tổng tiền ─────────────────────────────────────────
        // Tổng tiền tạm tính từ các sản phẩm trong giỏ.
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // ── 4. Tạo đơn hàng ───────────────────────────────────────────
        // Lưu thông tin đơn hàng vào bảng orders, trạng thái ban đầu là pending/unpaid.
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
        // Ghi nhận chi tiết từng sản phẩm trong đơn và cập nhật tồn kho.
        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $productId,
                'product_name' => $item['name'],
                'price'        => $item['price'],
                'quantity'     => $item['quantity'],
            ]);

            // Giảm số lượng tồn kho sản phẩm, nhưng chỉ khi còn hàng.
            Product::where('id', $productId)
                ->where('stock', '>', 0)
                ->decrement('stock', $item['quantity']);
        }

        // ── 6. Xóa giỏ hàng ──────────────────────────────────────────
        // Giỏ hàng đã được chuyển thành đơn nên loại bỏ session cart.
        session()->forget('cart');

        // ── 7. Redirect theo phương thức thanh toán ───────────────────
        // Chuyển hướng sang xử lý thanh toán online hoặc trang hoàn tất.
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
    // Hiển thị thông tin đơn hàng đã đặt, bao gồm tất cả sản phẩm.
    public function success($orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->with('items.product')
            ->firstOrFail();

        // Chỉ cho xem nếu là chủ đơn hàng (hoặc chưa đăng nhập thì cho xem)
        // Nếu đơn hàng thuộc người khác thì trả về lỗi 403 để bảo mật.
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}