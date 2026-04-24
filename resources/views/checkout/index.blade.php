@extends('layouts.app')
@section('title', 'Đặt hàng — MotoShop')

@push('styles')
<style>
.checkout-step {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--bs-secondary-color);
}
.checkout-step .step {
    width: 28px; height: 28px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px;
    background: #e9ecef; color: #6c757d;
    flex-shrink: 0;
}
.checkout-step.active .step {
    background: #e63946; color: #fff;
}
.checkout-step.done .step {
    background: #198754; color: #fff;
}

.payment-option {
    transition: all .2s;
    border-color: #dee2e6 !important;
    user-select: none;
}
.payment-option:hover {
    border-color: #adb5bd !important;
    background: #fafafa;
}

/* Highlight theo từng phương thức */
.payment-option:has(input[value="cod"]:checked) {
    border-color: #198754 !important;
    background: #f0fff5;
    box-shadow: 0 0 0 3px rgba(25,135,84,0.08);
}
.payment-option:has(input[value="vnpay"]:checked) {
    border-color: #005BAA !important;
    background: #f0f5ff;
    box-shadow: 0 0 0 3px rgba(0,91,170,0.08);
}
.payment-option:has(input[value="momo"]:checked) {
    border-color: #ae2070 !important;
    background: #fdf0f7;
    box-shadow: 0 0 0 3px rgba(174,32,112,0.08);
}
.payment-option:has(input[value="bank_transfer"]:checked) {
    border-color: #0d6efd !important;
    background: #f0f7ff;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.08);
}

/* Bank info box — hiện khi chọn chuyển khoản */
.bank-info {
    display: none;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 14px 16px;
    margin-top: 10px;
    font-size: 13px;
    border-left: 3px solid #0d6efd;
}
.bank-info .bank-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
}
.bank-info .bank-row span:last-child {
    font-weight: 600;
}
.copy-btn {
    cursor: pointer;
    font-size: 11px;
    padding: 1px 8px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    background: #fff;
    color: #495057;
    transition: all .15s;
}
.copy-btn:hover { background: #e9ecef; }
.copy-btn.copied { background: #d1fae5; border-color: #6ee7b7; color: #065f46; }

.order-summary-sticky {
    position: sticky;
    top: 80px;
}

/* Hiệu ứng validate */
.was-validated .form-control:valid { border-color: #198754; }
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- ── Breadcrumb + Steps ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                <li class="breadcrumb-item active">Đặt hàng</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">
            <div class="checkout-step done">
                <div class="step"><i class="bi bi-check" style="font-size:14px;"></i></div>
                <span>Giỏ hàng</span>
            </div>
            <i class="bi bi-chevron-right text-muted" style="font-size:12px;"></i>
            <div class="checkout-step active">
                <div class="step">2</div>
                <span>Đặt hàng</span>
            </div>
            <i class="bi bi-chevron-right text-muted" style="font-size:12px;"></i>
            <div class="checkout-step">
                <div class="step">3</div>
                <span>Hoàn thành</span>
            </div>
        </div>
    </div>

    <h2 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>Xác nhận đặt hàng</h2>

    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
    @csrf

    <div class="row g-4">

        {{-- ══════════════════════════════════════════
             CỘT TRÁI: Thông tin + Thanh toán
        ══════════════════════════════════════════ --}}
        <div class="col-lg-7">

            {{-- ── Thông tin người mua ── --}}
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-person-circle me-2 text-primary"></i>Thông tin người mua
                </h5>
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Họ và tên <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="customer_name"
                            class="form-control @error('customer_name') is-invalid @enderror"
                            value="{{ old('customer_name', auth()->user()?->name) }}"
                            placeholder="Nguyễn Văn A" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Số điện thoại <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="customer_phone"
                                class="form-control @error('customer_phone') is-invalid @enderror"
                                value="{{ old('customer_phone', auth()->user()?->phone) }}"
                                placeholder="0901 234 567" required>
                        </div>
                        @error('customer_phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="customer_email"
                                class="form-control"
                                value="{{ old('customer_email', auth()->user()?->email) }}"
                                placeholder="email@example.com">
                        </div>
                        <div class="form-text">Để nhận email xác nhận đơn hàng</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Địa chỉ nhận xe <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <textarea name="customer_address" rows="3"
                                class="form-control @error('customer_address') is-invalid @enderror"
                                placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"
                                required>{{ old('customer_address', auth()->user()?->address) }}</textarea>
                        </div>
                        @error('customer_address')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="2"
                            placeholder="Yêu cầu đặc biệt, thời gian giao hàng mong muốn...">{{ old('note') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- ── Phương thức thanh toán ── --}}
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-credit-card me-2 text-primary"></i>Phương thức thanh toán
                </h5>
                <div class="d-flex flex-column gap-3" id="paymentOptions">

                    {{-- COD --}}
                    <label class="payment-option d-flex align-items-center gap-3 p-3 border rounded-3"
                        style="cursor:pointer;" data-method="cod">
                        <input type="radio" name="payment_method" value="cod"
                            checked class="form-check-input mt-0">
                        <div class="flex-fill">
                            <div class="fw-semibold">
                                <i class="bi bi-cash-coin me-2 text-success"></i>Tiền mặt khi nhận xe (COD)
                            </div>
                            <small class="text-muted">Trả tiền khi nhân viên giao xe đến tận nơi</small>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            Miễn phí
                        </span>
                    </label>

                    {{-- VNPay --}}
                    <label class="payment-option d-flex align-items-center gap-3 p-3 border rounded-3"
                        style="cursor:pointer;" data-method="vnpay">
                        <input type="radio" name="payment_method" value="vnpay"
                            class="form-check-input mt-0">
                        <div class="flex-fill">
                            <div class="fw-semibold">
                                <span style="color:#005BAA;font-weight:800;">VN</span><span style="color:#E63946;font-weight:800;">Pay</span>
                                <span class="ms-2">— Thanh toán online</span>
                            </div>
                            <small class="text-muted">Thẻ ATM nội địa, Visa, MasterCard, QR Code</small>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <span class="badge bg-light border text-dark" style="font-size:10px;">ATM</span>
                            <span class="badge bg-light border text-dark" style="font-size:10px;">Visa</span>
                            <span class="badge bg-light border text-dark" style="font-size:10px;">QR</span>
                        </div>
                    </label>

                    {{-- MoMo --}}
                    <label class="payment-option d-flex align-items-center gap-3 p-3 border rounded-3"
                        style="cursor:pointer;" data-method="momo">
                        <input type="radio" name="payment_method" value="momo"
                            class="form-check-input mt-0">
                        <div class="flex-fill">
                            <div class="fw-semibold">
                                <span style="color:#ae2070;font-weight:800;">MoMo</span>
                                <span class="ms-2">— Ví điện tử</span>
                            </div>
                            <small class="text-muted">Thanh toán nhanh qua ứng dụng MoMo</small>
                        </div>
                        <span class="badge text-white flex-shrink-0"
                            style="background:#ae2070;font-size:11px;">Ví MoMo</span>
                    </label>

                    {{-- Chuyển khoản --}}
                    <div>
                        <label class="payment-option d-flex align-items-center gap-3 p-3 border rounded-3"
                            style="cursor:pointer;" data-method="bank_transfer">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                class="form-check-input mt-0" id="radioBankTransfer">
                            <div class="flex-fill">
                                <div class="fw-semibold">
                                    <i class="bi bi-bank me-2 text-primary"></i>Chuyển khoản ngân hàng
                                </div>
                                <small class="text-muted">
                                    MB Bank: <strong>1234567890</strong> — MotoShop Vietnam
                                </small>
                            </div>
                        </label>

                        {{-- Thông tin TK ngân hàng — hiện khi chọn --}}
                        <div class="bank-info" id="bankInfo">
                            <div class="fw-semibold mb-2 text-primary">
                                <i class="bi bi-info-circle me-1"></i>Thông tin chuyển khoản
                            </div>
                            <div class="bank-row">
                                <span class="text-muted">Ngân hàng</span>
                                <span>MB Bank (Quân Đội)</span>
                            </div>
                            <div class="bank-row">
                                <span class="text-muted">Số tài khoản</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span id="bankAccNum">1234567890</span>
                                    <button type="button" class="copy-btn" onclick="copyText('1234567890', this)">
                                        <i class="bi bi-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <div class="bank-row">
                                <span class="text-muted">Chủ tài khoản</span>
                                <span>MOTOSHOP VIETNAM</span>
                            </div>
                            <div class="bank-row">
                                <span class="text-muted">Nội dung CK</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span id="bankContent">MOTOSHOP + SĐT của bạn</span>
                                    <button type="button" class="copy-btn" onclick="copyText('MOTOSHOP', this)">
                                        <i class="bi bi-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 p-2 rounded"
                                style="background:#fff3cd;font-size:12px;color:#856404;">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Đơn hàng sẽ được xác nhận sau khi chúng tôi nhận được tiền.
                                Vui lòng chuyển trong vòng <strong>24 giờ</strong>.
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════
             CỘT PHẢI: Tóm tắt đơn hàng
        ══════════════════════════════════════════ --}}
        <div class="col-lg-5">
            <div class="order-summary-sticky">

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-receipt me-2"></i>Đơn hàng của bạn
                        </h5>
                    </div>
                    <div class="card-body px-4">

                        {{-- Danh sách xe --}}
                        @php $total = 0; @endphp
                        @foreach($cart as $id => $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total   += $subtotal;
                        @endphp
                        <div class="d-flex align-items-start gap-3 py-3
                            {{ !$loop->last ? 'border-bottom' : '' }}">
                            {{-- Ảnh xe --}}
                            @if($item['image'])
                                <img src="{{ asset('storage/'.$item['image']) }}"
                                    style="width:72px;height:52px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                            @else
                                <div style="width:72px;height:52px;background:#f0f0f0;border-radius:8px;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="bi bi-bicycle text-muted"></i>
                                </div>
                            @endif
                            {{-- Tên + giá --}}
                            <div class="flex-fill min-w-0">
                                <div class="fw-semibold text-truncate small">{{ $item['name'] }}</div>
                                <div class="text-muted" style="font-size:12px;">
                                    {{ number_format($item['price']) }}đ × {{ $item['quantity'] }}
                                </div>
                            </div>
                            <div class="fw-bold text-danger flex-shrink-0" style="font-size:14px;">
                                {{ number_format($subtotal) }}đ
                            </div>
                        </div>
                        @endforeach

                        {{-- Tổng cộng --}}
                        <div class="pt-3">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Tạm tính ({{ count($cart) }} xe)</span>
                                <span>{{ number_format($total) }}đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Phí giao hàng</span>
                                <span class="text-success fw-semibold">
                                    <i class="bi bi-truck me-1"></i>Miễn phí
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Thuế VAT</span>
                                <span class="text-muted">Đã bao gồm</span>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Tổng thanh toán</span>
                                <span class="fw-bold text-danger"
                                    style="font-size:1.4rem;">{{ number_format($total) }}đ</span>
                            </div>
                        </div>

                    </div>

                    {{-- Nút đặt hàng --}}
                    <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">

                        {{-- Cam kết --}}
                        <div class="d-flex gap-3 mb-3 text-center">
                            <div class="flex-fill">
                                <i class="bi bi-shield-check text-success d-block mb-1"></i>
                                <span style="font-size:11px;color:#6c757d;">Bảo mật</span>
                            </div>
                            <div class="flex-fill">
                                <i class="bi bi-award text-warning d-block mb-1"></i>
                                <span style="font-size:11px;color:#6c757d;">Chính hãng</span>
                            </div>
                            <div class="flex-fill">
                                <i class="bi bi-arrow-repeat text-primary d-block mb-1"></i>
                                <span style="font-size:11px;color:#6c757d;">Đổi trả 7 ngày</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold"
                            style="font-size:1.05rem;" id="submitBtn">
                            <i class="bi bi-bag-check me-2"></i>Xác nhận đặt hàng
                        </button>

                        <a href="{{ route('cart.index') }}"
                            class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại giỏ hàng
                        </a>

                        <p class="text-center text-muted mt-3 mb-0" style="font-size:12px;">
                            <i class="bi bi-lock me-1"></i>
                            Thông tin của bạn được mã hóa và bảo mật an toàn
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
(function () {

    // ── Hiện/ẩn thông tin ngân hàng khi chọn chuyển khoản ────────────
    const bankInfo   = document.getElementById('bankInfo');
    const radios     = document.querySelectorAll('input[name="payment_method"]');

    function toggleBankInfo() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        if (selected && selected.value === 'bank_transfer') {
            bankInfo.style.display = 'block';
        } else {
            bankInfo.style.display = 'none';
        }
    }

    radios.forEach(r => r.addEventListener('change', toggleBankInfo));
    toggleBankInfo(); // chạy khi load (nếu old value là bank_transfer)

    // ── Copy số tài khoản ─────────────────────────────────────────────
    window.copyText = function (text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i> Đã copy';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.classList.remove('copied');
            }, 2000);
        }).catch(() => {
            // fallback cho trình duyệt cũ
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        });
    };

    // ── Disable nút submit khi đang gửi (tránh double click) ─────────
    const form      = document.getElementById('checkoutForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
    });

    // ── Cập nhật nội dung CK tự động theo SĐT ────────────────────────
    const phoneInput   = document.querySelector('input[name="customer_phone"]');
    const bankContent  = document.getElementById('bankContent');

    if (phoneInput && bankContent) {
        phoneInput.addEventListener('input', function () {
            const phone = this.value.trim().replace(/\s/g, '');
            bankContent.textContent = phone
                ? 'MOTOSHOP ' + phone
                : 'MOTOSHOP + SĐT của bạn';
        });
        // Trigger ngay khi load nếu đã có giá trị
        if (phoneInput.value.trim()) {
            bankContent.textContent = 'MOTOSHOP ' + phoneInput.value.trim().replace(/\s/g, '');
        }
    }

})();
</script>
@endpush