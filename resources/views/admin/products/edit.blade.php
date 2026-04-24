@extends('layouts.admin')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Chỉnh sửa xe: {{ $product->name }}</h3>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="row g-4">

    {{-- ── Cột trái: Thông tin ── --}}
    <div class="col-md-8">

        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Thông tin cơ bản</h5>
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Tên xe <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                    <select name="category_id"
                        class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">-- Chọn loại xe --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hãng xe <span class="text-danger">*</span></label>
                    <select name="brand_id"
                        class="form-select @error('brand_id') is-invalid @enderror" required>
                        <option value="">-- Chọn hãng --</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}"
                            {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Giá gốc (đ) <span class="text-danger">*</span></label>
                    <input type="number" name="price"
                        class="form-control @error('price') is-invalid @enderror"
                        value="{{ old('price', $product->price) }}"
                        min="0" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Giá khuyến mãi (đ)</label>
                    <input type="number" name="sale_price"
                        class="form-control"
                        value="{{ old('sale_price', $product->sale_price) }}"
                        min="0">
                    <div class="form-text">Để trống nếu không giảm giá</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tồn kho <span class="text-danger">*</span></label>
                    <input type="number" name="stock"
                        class="form-control @error('stock') is-invalid @enderror"
                        value="{{ old('stock', $product->stock) }}"
                        min="0" required>
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Dung tích động cơ</label>
                    <input type="text" name="engine"
                        class="form-control"
                        value="{{ old('engine', $product->engine) }}"
                        placeholder="VD: 110cc, 155cc">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Năm sản xuất</label>
                    <input type="text" name="year"
                        class="form-control"
                        value="{{ old('year', $product->year) }}"
                        placeholder="VD: 2024">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Màu sắc</label>
                    <input type="text" name="color"
                        class="form-control"
                        value="{{ old('color', $product->color) }}"
                        placeholder="VD: Đỏ, Đen, Trắng, Xanh">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Mô tả chi tiết</label>
                    <textarea name="description" class="form-control" rows="6"
                        placeholder="Mô tả chi tiết về xe máy...">{{ old('description', $product->description) }}</textarea>
                </div>

            </div>
        </div>

        {{-- ── Hình ảnh (dùng component drag & drop) ── --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Hình ảnh xe</h5>

            <x-image-uploader
                name="image"
                label="Ảnh đại diện"
                :preview="$product->image" />

            <hr class="my-3">

            <x-image-uploader
                name="images"
                :multiple="true"
                label="Ảnh bổ sung (nhiều ảnh)" />

            {{-- Hiển thị ảnh bổ sung hiện có --}}
            @if($product->images && count($product->images))
            <div class="mt-3">
                <label class="form-label fw-semibold small text-muted text-uppercase">
                    Ảnh bổ sung hiện tại
                </label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($product->images as $img)
                    <div class="position-relative">
                        <img src="{{ asset('storage/'.$img) }}"
                            style="width:100px;height:75px;object-fit:cover;border-radius:8px;border:2px solid #dee2e6;">
                        <span class="position-absolute top-0 end-0 badge bg-info"
                            style="font-size:9px;border-radius:0 8px 0 4px;">Cũ</span>
                    </div>
                    @endforeach
                </div>
                <div class="form-text text-warning">
                    <i class="bi bi-info-circle me-1"></i>
                    Upload ảnh mới ở trên sẽ thay thế toàn bộ ảnh bổ sung cũ.
                </div>
            </div>
            @endif
        </div>

    </div>

    {{-- ── Cột phải: Cài đặt ── --}}
    <div class="col-md-4">

        {{-- Trạng thái --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Cài đặt hiển thị</h5>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox"
                    name="is_active" value="1" id="isActive"
                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive">
                    <span class="fw-semibold">Hiển thị trên cửa hàng</span><br>
                    <small class="text-muted">Bỏ tick để ẩn xe khỏi shop</small>
                </label>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                    name="is_featured" value="1" id="isFeatured"
                    {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="isFeatured">
                    <span class="fw-semibold">Xe nổi bật</span><br>
                    <small class="text-muted">Hiển thị ở trang chủ</small>
                </label>
            </div>
        </div>

        {{-- Thông tin nhanh --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Thông tin hiện tại</h5>
            <div class="d-flex flex-column gap-2 small">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Mã xe</span>
                    <span class="fw-semibold">#{{ $product->id }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Slug</span>
                    <span class="fw-semibold text-truncate ms-2" style="max-width:160px;"
                        title="{{ $product->slug }}">{{ $product->slug }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Ngày thêm</span>
                    <span>{{ $product->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Cập nhật</span>
                    <span>{{ $product->updated_at->format('d/m/Y') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Đã bán</span>
                    <span class="fw-semibold text-success">
                        {{ $product->orderItems()->sum('quantity') }} xe
                    </span>
                </div>
            </div>
        </div>

        {{-- Xem trước --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Xem trước</h5>
            @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}"
                class="img-fluid rounded mb-3"
                style="max-height:180px;width:100%;object-fit:cover;"
                alt="{{ $product->name }}" id="currentMainImg">
            @else
            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3"
                style="height:140px;">
                <div class="text-center text-muted">
                    <i class="bi bi-image fs-2 d-block mb-1"></i>
                    <small>Chưa có ảnh</small>
                </div>
            </div>
            @endif
            <div class="fw-semibold text-truncate">{{ $product->name }}</div>
            <div class="text-danger fw-bold mt-1">
                {{ number_format($product->final_price) }}đ
            </div>
            <a href="{{ route('shop.show', $product) }}"
                target="_blank"
                class="btn btn-outline-secondary btn-sm w-100 mt-2">
                <i class="bi bi-eye me-1"></i>Xem trên shop
            </a>
        </div>

        {{-- Nút lưu --}}
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save me-2"></i>Lưu thay đổi
            </button>
            <a href="{{ route('admin.products.index') }}"
                class="btn btn-outline-secondary">
                Hủy bỏ
            </a>
        </div>

    </div>
</div>

</form>

@endsection