<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Services\{VNPayService, MoMoService};
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct(
        private VNPayService $vnpay,
        private MoMoService  $momo
    ) {}

    // ── Redirect sang VNPay ──────────────────────────────────────────
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

        if (!$this->vnpay->verifyReturn($data)) {
            return redirect()->route('home')->with('error','Chữ ký không hợp lệ!');
        }

        // Lấy order_code từ vnp_TxnRef (format: ORDERCODE_timestamp)
        $orderCode = explode('_', $data['vnp_TxnRef'])[0];
        $order     = Order::where('order_code', $orderCode)->firstOrFail();

        if ($this->vnpay->isSuccess($data)) {
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

        if (isset($response['payUrl'])) {
            return redirect($response['payUrl']);
        }

        return back()->with('error', 'Không thể kết nối MoMo: '.($response['message'] ?? 'Lỗi không xác định'));
    }

    // ── MoMo callback (redirect) ─────────────────────────────────────
    public function momoReturn(Request $request)
    {
        $data = $request->all();

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