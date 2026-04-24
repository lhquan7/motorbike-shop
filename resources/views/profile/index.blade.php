@extends('layouts.app')
@section('title', 'Tài khoản của tôi')

@push('styles')
<style>
.profile-sidebar .nav-link { color: var(--bs-body-color); border-radius: 8px; padding: 10px 16px; transition: all .2s; }
.profile-sidebar .nav-link:hover { background: #f8f9fa; color: var(--primary); }
.profile-sidebar .nav-link.active { background: var(--primary); color: #fff !important; }
.profile-sidebar .nav-link i { width: 20px; }
.order-status { font-size: 12px; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
.avatar-circle { width: 80px; height: 80px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row g-4">

        {{-- ===== SIDEBAR ===== --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 mb-3 text-center">
                <div class="avatar-circle mx-auto mb-3">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div class="fw-bold">{{ $user->name }}</div>
                <small class="text-muted">{{ $user->email }}</small>
                <div class="mt-2">
                    <span class="badge bg-{{ $user->isAdmin() ? 'danger' : 'primary' }}">
                        {{ $user->isAdmin() ? 'Admin' : 'Khách hàng' }}
                    </span>
                </div>
            </div>
            <div class="card border-0 shadow-sm p-2 profile-sidebar">
                <nav class="nav flex-column gap-1">
                    <a class="nav-link active" href="#info" data-bs-toggle="tab">
                        <i class="bi bi-person me-2"></i>Thông tin cá nhân
                    </a>
                    <a class="nav-link" href="#orders" data-bs-toggle="tab">
                        <i class="bi bi-bag me-2"></i>Đơn hàng của tôi
                        @if($orders->total())
                        <span class="badge bg-primary float-end">{{ $orders->total() }}</span>
                        @endif
                    </a>
                    <a class="nav-link" href="#password" data-bs-toggle="tab">
                        <i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu
                    </a>
                    <hr class="my-1">
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="nav-link text-danger border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        {{-- ===== NỘI DUNG ===== --}}
        <div class="col-md-9">
            <div class="tab-content">

                {{-- Tab: Thông tin cá nhân --}}
                <div class="tab-pane fade show active" id="info">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-person-circle me-2"></i>Thông tin cá nhân</h5>
                        <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $user->phone) }}" placeholder="0901234567">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tham gia từ</label>
                                    <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y') }}" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Địa chỉ</label>
                                    <textarea name="address" class="form-control" rows="3"
                                        placeholder="Địa chỉ của bạn...">{{ old('address', $user->address) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-2"></i>Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tab: Đơn hàng --}}
                <div class="tab-pane fade" id="orders">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4"><i class="bi bi-bag me-2"></i>Lịch sử đặt hàng</h5>
                            @if($orders->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-bag-x display-3 text-muted"></i>
                                    <p class="text-muted mt-3">Bạn chưa có đơn hàng nào.</p>
                                    <a href="{{ route('shop.index') }}" class="btn btn-primary">Mua xe ngay</a>
                                </div>
                            @else
                                @php $statusColors = ['pending'=>'warning','confirmed'=>'info','delivering'=>'primary','completed'=>'success','cancelled'=>'danger'];
                                     $statusLabels = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','delivering'=>'Đang giao','completed'=>'Hoàn thành','cancelled'=>'Đã hủy']; @endphp
                                @foreach($orders as $order)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold">{{ $order->order_code }}</div>
                                            <small class="text-muted">{{ $order->created_at->format('H:i — d/m/Y') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} order-status">
                                                {{ $statusLabels[$order->status] ?? $order->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">{{ $order->items->count() }} sản phẩm</small>
                                            <div class="fw-bold text-danger">{{ number_format($order->total_amount) }}đ</div>
                                        </div>
                                        <a href="{{ route('profile.orderDetail', $order->order_code) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                                <div class="mt-3">{{ $orders->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tab: Đổi mật khẩu --}}
                <div class="tab-pane fade" id="password">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu</h5>
                        <form method="POST" action="{{ route('profile.changePassword') }}" style="max-width:480px;">
                            @csrf @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                                <div class="input-group">
                                    <input type="password" name="current_password" id="cp"
                                        class="form-control @error('current_password') is-invalid @enderror" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('cp','eyecp')">
                                        <i class="bi bi-eye" id="eyecp"></i>
                                    </button>
                                </div>
                                @error('current_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mật khẩu mới</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="np"
                                        class="form-control @error('password') is-invalid @enderror" required minlength="8">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('np','eyenp')">
                                        <i class="bi bi-eye" id="eyenp"></i>
                                    </button>
                                </div>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                            </div>
                            <div class="alert alert-info py-2 small">
                                <i class="bi bi-info-circle me-2"></i>Mật khẩu tối thiểu 8 ký tự.
                            </div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-shield-check me-2"></i>Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>

            </div>{{-- end tab-content --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
    else { input.type = 'password'; icon.className = 'bi bi-eye'; }
}

// Giữ tab active khi có flash message
@if(session('success') || session('error'))
    const hash = window.location.hash || '#info';
    const activeTab = document.querySelector(`.profile-sidebar .nav-link[href="${hash}"]`);
    if (activeTab) { activeTab.click(); }
@endif

// Lưu tab khi click
document.querySelectorAll('.profile-sidebar .nav-link[data-bs-toggle="tab"]').forEach(link => {
    link.addEventListener('shown.bs.tab', e => {
        history.replaceState(null, null, e.target.getAttribute('href'));
        document.querySelectorAll('.profile-sidebar .nav-link').forEach(l => l.classList.remove('active'));
        e.target.classList.add('active');
    });
});

// Restore tab từ URL hash
const initHash = window.location.hash;
if (initHash) {
    const tabLink = document.querySelector(`.profile-sidebar .nav-link[href="${initHash}"]`);
    if (tabLink) tabLink.click();
}
</script>
@endpush