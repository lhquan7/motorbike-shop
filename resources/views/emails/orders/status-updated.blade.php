<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><style>
  body { font-family: 'Segoe UI', Arial, sans-serif; background:#f4f4f4; }
  .wrapper { max-width:560px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
  .header { padding:32px; text-align:center; }
  .body { padding:28px 32px; }
  .status-box { border-radius:10px; padding:20px 24px; text-align:center; margin:20px 0; }
  .footer { background:#1a1a2e; color:rgba(255,255,255,.6); text-align:center; padding:20px; font-size:12px; }
</style></head>
<body>
<div class="wrapper">
  @php
    $configs = [
        'confirmed'  => ['bg'=>'#d1fae5','color'=>'#065f46','icon'=>'✅','msg'=>'Đơn hàng của bạn đã được xác nhận! Chúng tôi đang chuẩn bị xe.'],
        'delivering' => ['bg'=>'#dbeafe','color'=>'#1e40af','icon'=>'🚚','msg'=>'Xe đang trên đường giao đến bạn. Vui lòng giữ điện thoại.'],
        'completed'  => ['bg'=>'#d1fae5','color'=>'#065f46','icon'=>'🎉','msg'=>'Giao xe thành công! Cảm ơn bạn đã mua xe tại MotoShop.'],
        'cancelled'  => ['bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'❌','msg'=>'Đơn hàng đã bị hủy. Vui lòng liên hệ hotline nếu có thắc mắc.'],
    ];
    $cfg = $configs[$order->status] ?? ['bg'=>'#f3f4f6','color'=>'#374151','icon'=>'ℹ️','msg'=>'Trạng thái đơn hàng đã được cập nhật.'];
  @endphp
  <div class="header" style="background:linear-gradient(135deg,#1a1a2e,#e63946);">
    <div style="font-size:2.5rem;">{{ $cfg['icon'] }}</div>
    <h1 style="color:#fff;font-size:1.4rem;margin-top:8px;">Cập nhật đơn hàng</h1>
  </div>
  <div class="body">
    <p>Xin chào <strong>{{ $order->customer_name }}</strong>,</p>
    <div class="status-box" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">
      <div style="font-size:1.8rem;margin-bottom:8px;">{{ $cfg['icon'] }}</div>
      <div style="font-weight:700;font-size:1.1rem;margin-bottom:4px;">{{ strtoupper($order->status) }}</div>
      <div>{{ $cfg['msg'] }}</div>
    </div>
    <table style="width:100%;font-size:14px;border-collapse:collapse;">
      <tr><td style="padding:8px;color:#888;">Mã đơn:</td><td style="padding:8px;font-weight:700;color:#e63946;">{{ $order->order_code }}</td></tr>
      <tr><td style="padding:8px;color:#888;">Tổng tiền:</td><td style="padding:8px;font-weight:700;">{{ number_format($order->total_amount) }}đ</td></tr>
      <tr><td style="padding:8px;color:#888;">Địa chỉ:</td><td style="padding:8px;">{{ $order->customer_address }}</td></tr>
    </table>
    <div style="text-align:center;margin:24px 0;">
      <a href="{{ url('/profile/orders/'.$order->order_code) }}"
         style="background:#e63946;color:#fff;text-decoration:none;padding:12px 30px;border-radius:8px;font-weight:700;">
        Xem đơn hàng →
      </a>
    </div>
  </div>
  <div class="footer">🏍️ MotoShop · Hotline: 1800 1234</div>
</div>
</body>
</html>