<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; color: #333; }
  .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
  .header { background: linear-gradient(135deg, #1a1a2e, #e63946); padding: 40px 32px; text-align: center; }
  .header-icon { font-size: 3rem; margin-bottom: 12px; }
  .header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; margin-bottom: 4px; }
  .header p { color: rgba(255,255,255,0.8); font-size: 14px; }
  .body { padding: 32px; }
  .greeting { font-size: 16px; margin-bottom: 20px; color: #444; }
  .order-code { background: #f8f9fa; border-left: 4px solid #e63946; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
  .order-code strong { font-size: 1.3rem; color: #e63946; letter-spacing: 1px; }
  .section-title { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #888; margin-bottom: 12px; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
  .info-item { background: #f8f9fa; border-radius: 8px; padding: 12px 16px; }
  .info-item .label { font-size: 11px; color: #888; text-transform: uppercase; margin-bottom: 4px; }
  .info-item .value { font-weight: 600; color: #333; font-size: 14px; }
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  .items-table th { background: #f8f9fa; padding: 10px 14px; text-align: left; font-size: 12px; color: #888; text-transform: uppercase; }
  .items-table td { padding: 14px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
  .items-table tr:last-child td { border-bottom: none; }
  .total-row { background: #fff8f8; }
  .total-row td { font-weight: 700; font-size: 16px; color: #e63946; padding: 16px 14px; }
  .status-badge { display: inline-block; background: #fff3cd; color: #856404; border-radius: 20px; padding: 4px 14px; font-size: 12px; font-weight: 600; }
  .address-box { background: #f8f9fa; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; font-size: 14px; line-height: 1.7; }
  .payment-box { border: 2px solid #0d6efd; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
  .payment-box.cod { border-color: #198754; }
  .payment-title { font-weight: 700; margin-bottom: 4px; }
  .payment-desc { font-size: 13px; color: #666; }
  .cta { text-align: center; margin: 28px 0; }
  .cta a { display: inline-block; background: #e63946; color: #fff; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-weight: 700; font-size: 15px; }
  .footer { background: #1a1a2e; color: rgba(255,255,255,0.6); text-align: center; padding: 24px; font-size: 12px; line-height: 1.8; }
  .footer a { color: rgba(255,255,255,0.8); text-decoration: none; }
  .divider { border: none; border-top: 1px solid #f0f0f0; margin: 20px 0; }
</style>
</head>
<body>
<div class="wrapper">
  {{-- Header --}}
  <div class="header">
    <div class="header-icon">🏍️</div>
    <h1>Đặt hàng thành công!</h1>
    <p>Cảm ơn bạn đã tin tưởng MotoShop</p>
  </div>

  {{-- Body --}}
  <div class="body">
    <p class="greeting">Xin chào <strong>{{ $order->customer_name }}</strong>,</p>
    <p style="color:#666;font-size:14px;margin-bottom:20px;">
      Chúng tôi đã nhận được đơn đặt hàng của bạn và sẽ liên hệ xác nhận trong vòng <strong>30 phút</strong> (8:00–21:00).
    </p>

    {{-- Mã đơn --}}
    <div class="order-code">
      <div style="font-size:12px;color:#888;margin-bottom:4px;">Mã đơn hàng</div>
      <strong>{{ $order->order_code }}</strong>
      <div style="margin-top:8px;">
        <span class="status-badge">⏳ Chờ xác nhận</span>
        <span style="font-size:12px;color:#888;margin-left:10px;">{{ $order->created_at->format('H:i — d/m/Y') }}</span>
      </div>
    </div>

    {{-- Thông tin --}}
    <div class="section-title">Thông tin giao hàng</div>
    <div class="info-grid">
      <div class="info-item"><div class="label">Họ tên</div><div class="value">{{ $order->customer_name }}</div></div>
      <div class="info-item"><div class="label">Điện thoại</div><div class="value">{{ $order->customer_phone }}</div></div>
      @if($order->customer_email)
      <div class="info-item"><div class="label">Email</div><div class="value">{{ $order->customer_email }}</div></div>
      @endif
      <div class="info-item"><div class="label">Phí giao hàng</div><div class="value" style="color:#198754;">Miễn phí</div></div>
    </div>
    <div class="address-box">
      📍 <strong>Địa chỉ nhận xe:</strong><br>{{ $order->customer_address }}
    </div>

    {{-- Danh sách xe --}}
    <div class="section-title">Xe đã đặt</div>
    <table class="items-table">
      <thead><tr><th>Tên xe</th><th style="text-align:center;">SL</th><th style="text-align:right;">Thành tiền</th></tr></thead>
      <tbody>
        @foreach($order->items as $item)
        <tr>
          <td>{{ $item->product_name }}</td>
          <td style="text-align:center;">{{ $item->quantity }}</td>
          <td style="text-align:right;font-weight:600;">{{ number_format($item->price * $item->quantity) }}đ</td>
        </tr>
        @endforeach
        <tr class="total-row">
          <td colspan="2">Tổng thanh toán</td>
          <td style="text-align:right;">{{ number_format($order->total_amount) }}đ</td>
        </tr>
      </tbody>
    </table>

    {{-- Thanh toán --}}
    <div class="section-title">Phương thức thanh toán</div>
    @if($order->payment_method === 'cod')
    <div class="payment-box cod">
      <div class="payment-title">💵 Thanh toán khi nhận xe (COD)</div>
      <div class="payment-desc">Chuẩn bị <strong>{{ number_format($order->total_amount) }}đ</strong> để thanh toán khi nhân viên giao xe đến.</div>
    </div>
    @else
    <div class="payment-box">
      <div class="payment-title">🏦 Chuyển khoản ngân hàng</div>
      <div class="payment-desc">
        Ngân hàng: <strong>MB Bank</strong><br>
        Số tài khoản: <strong>1234567890</strong><br>
        Chủ tài khoản: <strong>MOTOSHOP VIETNAM</strong><br>
        Nội dung CK: <strong>{{ $order->order_code }}</strong>
      </div>
    </div>
    @endif

    {{-- CTA --}}
    <div class="cta">
      <a href="{{ url('/profile/orders/'.$order->order_code) }}">Xem chi tiết đơn hàng →</a>
    </div>

    <hr class="divider">
    <p style="font-size:13px;color:#888;text-align:center;">
      Có thắc mắc? Liên hệ hotline <strong style="color:#e63946;">1800 1234</strong> (miễn phí, 8:00–21:00 hàng ngày)
    </p>
  </div>

  {{-- Footer --}}
  <div class="footer">
    <strong style="color:#fff;">🏍️ MotoShop Vietnam</strong><br>
    123 Nguyễn Huệ, Quận 1, TP.HCM<br>
    <a href="mailto:info@motoshop.vn">info@motoshop.vn</a> · <a href="{{ url('/') }}">motoshop.vn</a><br><br>
    <span style="font-size:11px;">Email này được gửi tự động. Vui lòng không trả lời.</span>
  </div>
</div>
</body>
</html>