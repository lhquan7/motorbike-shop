<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Services\{VNPayService, MoMoService};
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    // Inject các service thanh toán để tách logic cổng thanh toán ra ngoài controller.
    public function __construct(
        private VNPayService $vnpay,
        private MoMoService  $momo
    ) {}

    // ── Redirect sang VNPay ──────────────────────────────────────────
    // Tạo URL thanh toán VNPay và chuyển hướng người dùng sang cổng VNPay.

    public function redirectVNPay(Request $request)
    {
        $order = Order::where('order_code', $request->order_code)->firstOrFail();
        $url   = $this->vnpay->createPaymentUrl(
            $order->order_code,
            (int) $order->total_amount,
            "Thanh toan don hang {$order->order_code} - MotoShop"
        );
        return redirect($url);
    }

    // ── VNPay callback ────────────────────────────────────────────────
    public function vnpayReturn(Request $request)
    {
        $data = $request->all();

        // Xác thực chữ ký trả về của VNPay để đảm bảo dữ liệu không bị giả mạo.
        if (!$this->vnpay->verifyReturn($data)) {
            return redirect()->route('home')->with('error','Chữ ký không hợp lệ!');
        }

        // Lấy order_code từ vnp_TxnRef (format: ORDERCODE_timestamp)
        $orderCode = explode('_', $data['vnp_TxnRef'])[0];
        $order     = Order::where('order_code', $orderCode)->firstOrFail();

        if ($this->vnpay->isSuccess($data)) {
            // Thanh toán thành công, cập nhật trạng thái đơn hàng.
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
            ]);
            if ($order->customer_email) {
                try { Mail::to($order->customer_email)->queue(new OrderConfirmationMail($order->fresh())); }
                catch (\Exception $e) {}
            }
            return redirect()->route('checkout.success', $order->order_code)
                ->with('payment_success', 'Thanh toán VNPay thành công!');
        }

        // Nếu VNPay báo lỗi thì huỷ đơn và thông báo lỗi cho người dùng.
        $order->update(['status' => 'cancelled']);
        return redirect()->route('checkout.index')
            ->with('error', 'Thanh toán VNPay thất bại. Mã lỗi: '.$data['vnp_ResponseCode']);
    }

    // ── Redirect sang MoMo ───────────────────────────────────────────
    public function redirectMoMo(Request $request)
    {
        $order    = Order::where('order_code', $request->order_code)->firstOrFail();
        $response = $this->momo->createPayment(
            $order->order_code,
            (int) $order->total_amount,
            "Thanh toan {$order->order_code} tai MotoShop"
        );

        // Nếu MoMo trả về URL thanh toán, redirect người dùng sang cổng MoMo.
        if (isset($response['payUrl'])) {
            return redirect($response['payUrl']);
        }

        return back()->with('error', 'Không thể kết nối MoMo: '.($response['message'] ?? 'Lỗi không xác định'));
    }

    // ── MoMo callback (redirect) ─────────────────────────────────────
    public function momoReturn(Request $request)
    {
        $data = $request->all();

        // Xác thực chữ ký MoMo trả về để đảm bảo callback hợp lệ.
        if (!$this->momo->verifyReturn($data)) {
            return redirect()->route('home')->with('error','Chữ ký MoMo không hợp lệ!');
        }

        $orderCode = explode('_', $data['orderId'])[0];
        $order     = Order::where('order_code', $orderCode)->firstOrFail();

        if ($this->momo->isSuccess($data)) {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
            ]);
            if ($order->customer_email) {
                try { Mail::to($order->customer_email)->queue(new OrderConfirmationMail($order->fresh())); }
                catch (\Exception $e) {}
            }
            return redirect()->route('checkout.success', $order->order_code)
                ->with('payment_success', 'Thanh toán MoMo thành công!');
        }

        $order->update(['status' => 'cancelled']);
        return redirect()->route('checkout.index')
            ->with('error', 'Thanh toán MoMo thất bại: '.($data['message'] ?? ''));
    }

    // ── MoMo IPN (server-to-server notify) ──────────────────────────
    public function momoNotify(Request $request)
    {
        $data = $request->all();

        // Callback server-to-server từ MoMo để cập nhật thanh toán ngay cả khi người dùng không quay lại.
        if (!$this->momo->verifyReturn($data)) {
            return response()->json(['message' => 'invalid signature'], 400);
        }

        $orderCode = explode('_', $data['orderId'])[0];
        $order     = Order::where('order_code', $orderCode)->first();
        if ($order && $this->momo->isSuccess($data)) {
            $order->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        }
        return response()->json(['message' => 'ok']);
    }
}