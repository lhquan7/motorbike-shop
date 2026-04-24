@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-bicycle me-2"></i>Quản lý xe máy</h3>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Thêm xe mới</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="60">Ảnh</th>
                    <th>Tên xe</th>
                    <th>Hãng / Loại</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th width="140">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/60x45/f0f0f0/999?text=Xe' }}"
                        style="width:60px;height:45px;object-fit:cover;border-radius:6px;" alt="">
                </td>
                <td><a href="{{ route('shop.show', $product) }}" target="_blank" class="fw-semibold text-dark text-decoration-none">{{ $product->name }}</a></td>
                <td><small class="text-muted">{{ $product->brand->name ?? '-' }} / {{ $product->category->name ?? '-' }}</small></td>
                <td>
                    @if($product->sale_price)
                        <div class="text-danger fw-bold">{{ number_format($product->sale_price) }}đ</div>
                        <small class="text-decoration-line-through text-muted">{{ number_format($product->price) }}đ</small>
                    @else
                        <div class="fw-bold">{{ number_format($product->price) }}đ</div>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $product->stock > 5 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                        {{ $product->stock }} xe
                    </span>
                </td>
                <td>
                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $product->is_active ? 'Đang bán' : 'Ẩn' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
                        onsubmit="return confirm('Xóa xe {{ $product->name }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có xe nào. <a href="{{ route('admin.products.create') }}">Thêm xe đầu tiên</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $products->links() }}</div>
</div>
@endsection