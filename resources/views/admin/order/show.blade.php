@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Đơn hàng: {{ $order->order_code }}</h3>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="row g-4">
    <div class="col-md-8">
        {{-- Chi tiết sản phẩm --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Xe đặt mua</h5>
                <table class="table">
                    <thead class="table-light"><tr><th>Tên xe</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ number_format($item->price) }}đ</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="fw-bold">{{ number_format($item->price * $item->quantity) }}đ</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end fw-bold">Tổng cộng:</td><td class="fw-bold text-danger fs-5">{{ number_format($order->total_amount) }}đ</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Ghi chú --}}
        @if($order->note)
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold">Ghi chú từ khách:</h6>
            <p class="mb-0 text-muted">{{ $order->note }}</p>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        {{-- Thông tin khách --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Thông tin khách hàng</h5>
            <div class="mb-2"><i class="bi bi-person me-2 text-muted"></i>{{ $order->customer_name }}</div>
            <div class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $order->customer_phone }}</div>
            @if($order->customer_email)
            <div class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $order->customer_email }}</div>
            @endif
            <div><i class="bi bi-geo-alt me-2 text-muted"></i>{{ $order->customer_address }}</div>
        </div>

        {{-- Cập nhật trạng thái --}}
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3">Cập nhật trạng thái</h5>
            @php $colors = ['pending'=>'warning','confirmed'=>'info','delivering'=>'primary','completed'=>'success','cancelled'=>'danger']; @endphp
            <div class="mb-3">
                <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }} fs-6 px-3 py-2">{{ strtoupper($order->status) }}</span>
            </div>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                @csrf @method('PATCH')
                <select name="status" class="form-select mb-3">
                    @foreach(['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','delivering'=>'Đang giao hàng','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'] as $val => $label)
                    <option value="{{ $val }}" {{ $order->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1"></i>Cập nhật</button>
            </form>
            <hr>
            <div class="small text-muted">
                <div>Đặt lúc: {{ $order->created_at->format('H:i d/m/Y') }}</div>
                <div>Thanh toán: {{ $order->payment_method == 'cod' ? 'COD - Tiền mặt' : 'Chuyển khoản' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection