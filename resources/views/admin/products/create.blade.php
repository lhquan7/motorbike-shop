@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Thêm xe máy mới</h3>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        {{-- Cột bên trái: Thông tin chi tiết --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3">Thông tin cơ bản</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Tên xe <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Chọn loại xe --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hãng xe <span class="text-danger">*</span></label>
                        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
                            <option value="">-- Chọn hãng --</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giá gốc (đ) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giá khuyến mãi (đ)</label>
                        <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Dung tích động cơ</label>
                        <input type="text" name="engine" class="form-control" value="{{ old('engine') }}" placeholder="110cc">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Năm sản xuất</label>
                        <input type="text" name="year" class="form-control" value="{{ old('year') }}" placeholder="2024">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Màu sắc</label>
                        <input type="text" name="color" class="form-control" value="{{ old('color') }}" placeholder="Đỏ, Đen, Trắng,...">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Mô tả chi tiết về xe...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cột bên phải: Hình ảnh & Cài đặt --}}
        <div class="col-md-4">
            {{-- Sử dụng Component Image Uploader mới --}}
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3">Hình ảnh xe</h5>
                
                <x-image-uploader name="image" label="Ảnh đại diện (bắt buộc)" />
                
                <hr class="my-3">
                
                <x-image-uploader name="images" :multiple="true" label="Ảnh bổ sung (nhiều ảnh)" />
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3">Cài đặt hiển thị</h5>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Hiển thị trên cửa hàng</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label" for="isFeatured">Xe nổi bật (Trang chủ)</label>
                </div>
            </div>
        </div>

        <div class="col-12 mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                <i class="bi bi-save me-2"></i>Lưu xe máy
            </button>
        </div>
    </div>
</form>
@endsection