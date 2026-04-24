<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Mail\OrderStatusUpdatedMail; // Thêm Mailable
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Thêm Facade Mail

class OrderController extends Controller {
    public function index(Request $request) {
        $query = Order::with('items')->latest();
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order) {
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order) {
        $request->validate([
            'status' => 'required|in:pending,confirmed,delivering,completed,cancelled'
        ]);

        $oldStatus = $order->status;
        
        // Cập nhật trạng thái mới
        $order->update(['status' => $request->status]);

        // Gửi mail thông báo cho khách nếu có email và trạng thái thực sự thay đổi
        if ($order->customer_email && $oldStatus !== $request->status) {
            try {
                Mail::to($order->customer_email)
                    ->queue(new OrderStatusUpdatedMail($order, $oldStatus));
            } catch (\Exception $e) {
                // Ghi log nếu lỗi gửi mail để không làm gián đoạn luồng xử lý của Admin
                \Log::error('Mail status error: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}