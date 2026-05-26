<div class="card product-card h-100 shadow-sm border-0">
    <div class="position-relative">
        @if($product->image)
            @if(str_starts_with($product->image, 'http'))
                {{-- Nếu là ảnh tự động từ Seeder (Đường dẫn URL) --}}
                <img src="{{ $product->image }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $product->name }}">
            @else
                {{-- Nếu là ảnh do Admin upload thủ công (Lưu ở local storage) --}}
                <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $product->name }}">
            @endif
        @else
            {{-- Fallback nếu xe hoàn toàn không có ảnh --}}
            <img src="https://placehold.co/400x300?text=No+Image" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $product->name }}">
        @endif
    </div>
    <div class="card-body d-flex flex-column">
        <h6 class="card-title fw-bold mb-2">{{ $product->name }}</h6>
        <div class="mt-auto">
            <div class="text-danger fw-bold fs-5">{{ number_format($product->price) }}đ</div>
            <div class="d-grid mt-3">
                <a href="{{ route('shop.show', $product->slug) }}" class="btn btn-primary btn-sm">Xem chi tiết</a>
            </div>
        </div>
    </div>
</div>