@extends('layouts.admin')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Chỉnh sửa bài viết</h3>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm p-4">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" name="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $post->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nội dung <span class="text-danger">*</span></label>
                <textarea name="content" rows="15"
                    class="form-control @error('content') is-invalid @enderror"
                    required>{{ old('content', $post->content) }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Cài đặt</h5>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox"
                    name="is_published" value="1" id="isPublished"
                    {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                <label class="form-check-label" for="isPublished">
                    <span class="fw-semibold">Đăng công khai</span>
                </label>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">Ảnh đại diện</h5>
            @if($post->thumbnail)
                <img src="{{ asset('storage/'.$post->thumbnail) }}"
                    class="img-fluid rounded mb-2" style="max-height:160px;object-fit:cover;">
            @endif
            <input type="file" name="thumbnail" class="form-control" accept="image/*"
                onchange="previewThumb(this)">
            <img id="thumbPreview" src="" class="mt-2 img-fluid rounded d-none"
                style="max-height:160px;object-fit:cover;">
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg">
            <i class="bi bi-save me-2"></i>Lưu thay đổi
        </button>
    </div>
</div>
</form>

@push('scripts')
<script>
function previewThumb(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('thumbPreview');
            img.src = e.target.result;
            img.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection