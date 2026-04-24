@extends('layouts.admin')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-newspaper me-2"></i>Quản lý bài viết</h3>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm bài viết
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="80">Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Tác giả</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th width="120">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($posts as $post)
            <tr>
                <td>
                    @if($post->thumbnail)
                        <img src="{{ asset('storage/'.$post->thumbnail) }}"
                            style="width:64px;height:48px;object-fit:cover;border-radius:6px;">
                    @else
                        <div style="width:64px;height:48px;background:#f0f0f0;border-radius:6px;
                            display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-image text-muted"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <div class="fw-semibold">{{ $post->title }}</div>
                    <small class="text-muted">{{ Str::limit($post->slug, 40) }}</small>
                </td>
                <td>{{ $post->user->name ?? '-' }}</td>
                <td>
                    <span class="badge {{ $post->is_published ? 'bg-success' : 'bg-secondary' }}">
                        {{ $post->is_published ? 'Đã đăng' : 'Nháp' }}
                    </span>
                </td>
                <td class="small text-muted">
                    {{ $post->created_at->format('d/m/Y') }}
                </td>
                <td>
                    <a href="{{ route('admin.posts.edit', $post) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                        class="d-inline"
                        onsubmit="return confirm('Xóa bài viết này?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-newspaper d-block fs-2 mb-2"></i>
                    Chưa có bài viết nào.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $posts->links() }}
    </div>
</div>

@endsection