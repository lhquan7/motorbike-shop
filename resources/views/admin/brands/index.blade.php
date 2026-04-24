@extends('layouts.admin')
@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-award me-2"></i>Quản lý hãng xe</h3>

<div class="row g-4">
    {{-- Form thêm --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3">Thêm hãng xe mới</h5>
            <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data">@csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên hãng <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="Honda, Yamaha, SYM..." required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Logo hãng</label>
                    <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewLogo(this)">
                    <img id="logoPreview" src="" class="mt-2 rounded d-none" style="height:60px;object-fit:contain;">
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Thêm hãng xe</button>
            </form>
        </div>
    </div>

    {{-- Danh sách hãng --}}
    <div class="col-md-8">
        <div class="row g-3">
            @forelse($brands as $brand)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        @if($brand->logo)
                            <img src="{{ asset('storage/'.$brand->logo) }}" style="width:64px;height:64px;object-fit:contain;border-radius:8px;background:#f8f8f8;padding:4px;">
                        @else
                            <div style="width:64px;height:64px;background:#f0f0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-award fs-3 text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-fill">
                            <div class="fw-bold fs-5">{{ $brand->name }}</div>
                            <span class="badge bg-primary">{{ $brand->products_count }} xe</span>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="openBrandEdit({{ $brand->id }}, '{{ addslashes($brand->name) }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}"
                                onsubmit="return confirm('Xóa hãng {{ $brand->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">Chưa có hãng xe nào.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal edit hãng --}}
<div class="modal fade" id="brandEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form method="POST" id="brandEditForm" enctype="multipart/form-data">@csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Chỉnh sửa hãng xe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên hãng</label>
                        <input type="text" name="name" id="brandEditName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Logo mới (tùy chọn)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { const img = document.getElementById('logoPreview'); img.src = e.target.result; img.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
function openBrandEdit(id, name) {
    document.getElementById('brandEditName').value = name;
    document.getElementById('brandEditForm').action = '/admin/brands/' + id;
    new bootstrap.Modal(document.getElementById('brandEditModal')).show();
}
</script>
@endpush
@endsection