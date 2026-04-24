@extends('layouts.admin')
@section('content')
<h3 class="fw-bold mb-4"><i class="bi bi-grid me-2"></i>Quản lý danh mục</h3>

<div class="row g-4">
    {{-- Form thêm mới --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3">Thêm danh mục mới</h5>
            <form method="POST" action="{{ route('admin.categories.store') }}">@csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="VD: Xe số, Xe tay ga..." required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Mô tả ngắn...">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Thêm danh mục</button>
            </form>
        </div>
    </div>

    {{-- Danh sách --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Tên danh mục</th><th>Mô tả</th><th>Số xe</th><th width="130">Thao tác</th></tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $i => $cat)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $cat->name }}</td>
                        <td class="text-muted small">{{ Str::limit($cat->description, 50) ?? '-' }}</td>
                        <td><span class="badge bg-primary">{{ $cat->products_count }} xe</span></td>
                        <td>
                            {{-- Nút edit mở modal --}}
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description) }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="d-inline"
                                onsubmit="return confirm('Xóa danh mục {{ $cat->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có danh mục nào.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal chỉnh sửa --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form method="POST" id="editForm">@csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Chỉnh sửa danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên danh mục</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
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
function openEditModal(id, name, desc) {
    document.getElementById('editName').value = name;
    document.getElementById('editDesc').value = desc;
    document.getElementById('editForm').action = '/admin/categories/' + id;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endpush
@endsection