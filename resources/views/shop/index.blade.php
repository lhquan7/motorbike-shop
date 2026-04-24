@extends('layouts.app')
@section('title','Cửa hàng xe máy')

@push('styles')
<style>
.filter-tag { display:inline-flex; align-items:center; gap:6px; background:#e9ecef; border-radius:20px; padding:4px 12px; font-size:13px; }
.filter-tag .remove { cursor:pointer; color:#868e96; font-size:16px; line-height:1; }
.filter-tag .remove:hover { color:#e63946; }
#productGrid { min-height:400px; }
.skeleton-card { background:#f8f9fa; border-radius:12px; overflow:hidden; }
.skeleton-img  { height:200px; background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; }
.skeleton-body { padding:16px; }
.skeleton-line { height:14px; background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; border-radius:4px; margin-bottom:10px; }
.skeleton-line.short { width:60%; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
#loadMoreBtn { transition:all .2s; }
#scrollSentinel { height:1px; margin-top:32px; }
</style>
@endpush

@section('content')
<div class="container py-5">
<div class="row g-4">

  {{-- ── Sidebar ────────────────────────────────────────────────── --}}
  <div class="col-md-3">
    <div class="card border-0 shadow-sm p-4 sticky-top" style="top:76px;">
      <h5 class="fw-bold mb-4"><i class="bi bi-funnel me-2"></i>Bộ lọc</h5>
      <form id="filterForm" method="GET" action="{{ route('shop.index') }}">

        <div class="mb-4">
          <label class="form-label fw-semibold small text-uppercase text-muted">Tìm kiếm</label>
          <input type="text" name="search" id="filterSearch" class="form-control"
            value="{{ request('search') }}" placeholder="Tên xe...">
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold small text-uppercase text-muted">Loại xe</label>
          @foreach($categories as $cat)
          <div class="form-check">
            <input class="form-check-input filter-input" type="radio" name="category"
              value="{{ $cat->slug }}" id="cat{{ $cat->id }}"
              {{ request('category') == $cat->slug ? 'checked' : '' }}>
            <label class="form-check-label small" for="cat{{ $cat->id }}">
              {{ $cat->name }} <span class="text-muted">({{ $cat->products_count }})</span>
            </label>
          </div>
          @endforeach
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold small text-uppercase text-muted">Hãng xe</label>
          @foreach($brands as $brand)
          <div class="form-check">
            <input class="form-check-input filter-input" type="radio" name="brand"
              value="{{ $brand->slug }}" id="brand{{ $brand->id }}"
              {{ request('brand') == $brand->slug ? 'checked' : '' }}>
            <label class="form-check-label small" for="brand{{ $brand->id }}">
              {{ $brand->name }} <span class="text-muted">({{ $brand->products_count }})</span>
            </label>
          </div>
          @endforeach
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold small text-uppercase text-muted">
            Khoảng giá: <span id="priceLabel" class="text-primary fw-bold"></span>
          </label>
          <div class="d-flex gap-2">
            <input type="number" name="min_price" id="minPrice" class="form-control form-control-sm"
              placeholder="Từ (đ)" value="{{ request('min_price') }}">
            <input type="number" name="max_price" id="maxPrice" class="form-control form-control-sm"
              placeholder="Đến (đ)" value="{{ request('max_price') }}">
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">
          <i class="bi bi-search me-1"></i>Lọc xe
        </button>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
          <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
        </a>
      </form>
    </div>
  </div>

  {{-- ── Kết quả ─────────────────────────────────────────────────── --}}
  <div class="col-md-9">

    {{-- Thanh trạng thái --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <div>
        <span id="resultCount" class="fw-semibold">{{ $products->total() }}</span>
        <span class="text-muted"> xe được tìm thấy</span>

        {{-- Filter tags --}}
        <div class="d-flex gap-2 flex-wrap mt-2" id="filterTags">
          @if(request('search'))
          <span class="filter-tag">🔍 {{ request('search') }} <span class="remove" onclick="clearFilter('search')">×</span></span>
          @endif
          @if(request('category'))
          <span class="filter-tag">📂 {{ request('category') }} <span class="remove" onclick="clearFilter('category')">×</span></span>
          @endif
          @if(request('brand'))
          <span class="filter-tag">🏷️ {{ request('brand') }} <span class="remove" onclick="clearFilter('brand')">×</span></span>
          @endif
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="text-muted small" id="loadedCount">Hiển thị {{ $products->count() }}/{{ $products->total() }}</span>
      </div>
    </div>

    {{-- Grid sản phẩm --}}
    <div class="row g-4" id="productGrid">
      @forelse($products as $product)
      <div class="col-6 col-lg-4 product-col">
        @include('partials.product-card', ['product' => $product])
      </div>
      @empty
      <div class="col-12" id="emptyState">
        <div class="text-center py-5">
          <i class="bi bi-bicycle display-1 text-muted"></i>
          <h5 class="mt-3 text-muted">Không tìm thấy xe phù hợp</h5>
          <a href="{{ route('shop.index') }}" class="btn btn-primary mt-2">Xem tất cả xe</a>
        </div>
      </div>
      @endforelse
    </div>

    {{-- Skeleton placeholders (ẩn, dùng khi load) --}}
    <div class="row g-4 d-none" id="skeletonGrid">
      @for($i = 0; $i < 3; $i++)
      <div class="col-6 col-lg-4">
        <div class="skeleton-card">
          <div class="skeleton-img"></div>
          <div class="skeleton-body">
            <div class="skeleton-line"></div>
            <div class="skeleton-line short"></div>
            <div class="skeleton-line" style="width:40%;height:20px;"></div>
          </div>
        </div>
      </div>
      @endfor
    </div>

    {{-- Sentinel (trigger infinite scroll) --}}
    <div id="scrollSentinel"></div>

    {{-- Load more button (fallback) --}}
    @if($products->hasMorePages())
    <div class="text-center mt-4" id="loadMoreWrap">
      <button id="loadMoreBtn" class="btn btn-outline-primary px-5"
        data-page="{{ $products->currentPage() + 1 }}"
        data-has-more="{{ $products->hasMorePages() ? 'true' : 'false' }}">
        <i class="bi bi-arrow-down-circle me-2"></i>Xem thêm xe
      </button>
    </div>
    @endif

    {{-- Đã hết --}}
    <div class="text-center text-muted small mt-4 d-none" id="endMessage">
      <i class="bi bi-check-all me-1"></i>Đã hiển thị tất cả {{ $products->total() }} xe
    </div>

  </div>
</div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    // ── State ─────────────────────────────────────────────────────────
    let page      = {{ $products->currentPage() + 1 }};
    let hasMore   = {{ $products->hasMorePages() ? 'true' : 'false' }};
    let loading   = false;
    let total     = {{ $products->total() }};
    let loaded    = {{ $products->count() }};

    const grid       = document.getElementById('productGrid');
    const skeleton   = document.getElementById('skeletonGrid');
    const loadMoreBtn= document.getElementById('loadMoreBtn');
    const loadMoreWrap=document.getElementById('loadMoreWrap');
    const endMsg     = document.getElementById('endMessage');
    const loadedCount= document.getElementById('loadedCount');
    const sentinel   = document.getElementById('scrollSentinel');

    // ── Build query string từ form hiện tại ──────────────────────────
    function getFilterParams(p) {
        const form   = document.getElementById('filterForm');
        const fd     = new FormData(form);
        const params = new URLSearchParams();
        params.set('page', p);
        for (const [key, val] of fd.entries()) {
            if (val) params.set(key, val);
        }
        return params.toString();
    }

    // ── Load trang tiếp theo ─────────────────────────────────────────
    async function loadMore() {
        if (loading || !hasMore) return;
        loading = true;

        // Hiện skeleton
        skeleton.classList.remove('d-none');
        if (loadMoreBtn) loadMoreBtn.disabled = true;

        try {
            const res  = await fetch(`{{ route('shop.index') }}?${getFilterParams(page)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            // Append cards
            const tmp = document.createElement('div');
            tmp.innerHTML = data.html;

            // Wrap mỗi card vào col
            const cards = tmp.querySelectorAll('.card');
            cards.forEach(card => {
                const col = document.createElement('div');
                col.className = 'col-6 col-lg-4 product-col';
                col.appendChild(card);
                grid.appendChild(col);

                // Fade-in animation
                col.style.opacity = '0';
                col.style.transform = 'translateY(16px)';
                col.style.transition = 'opacity .3s ease, transform .3s ease';
                requestAnimationFrame(() => {
                    col.style.opacity = '1';
                    col.style.transform = 'translateY(0)';
                });
            });

            loaded  += cards.length;
            hasMore  = data.hasMore;
            page     = data.nextPage;

            // Cập nhật UI
            loadedCount.textContent = `Hiển thị ${loaded}/${total}`;

            if (!hasMore) {
                if (loadMoreWrap) loadMoreWrap.classList.add('d-none');
                endMsg.classList.remove('d-none');
                observer.disconnect();
            } else {
                if (loadMoreBtn) loadMoreBtn.disabled = false;
            }
        } catch(e) {
            console.error('Load more error:', e);
            if (loadMoreBtn) loadMoreBtn.disabled = false;
        } finally {
            loading = false;
            skeleton.classList.add('d-none');
        }
    }

    // ── Intersection Observer (tự động load khi scroll tới cuối) ─────
    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting && hasMore && !loading) loadMore();
    }, { rootMargin: '200px' });

    if (sentinel && hasMore) observer.observe(sentinel);

    // ── Nút Load More (fallback) ──────────────────────────────────────
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMore);
    }

    // ── Xóa filter tag ───────────────────────────────────────────────
    window.clearFilter = function(name) {
        const input = document.querySelector(`[name="${name}"]`);
        if (!input) return;
        if (input.type === 'radio') {
            document.querySelectorAll(`[name="${name}"]`).forEach(r => r.checked = false);
        } else { input.value = ''; }
        document.getElementById('filterForm').submit();
    };

    // ── Debounce cho ô tìm kiếm ──────────────────────────────────────
    let searchTimer;
    const searchInput = document.getElementById('filterSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
        });
    }
})();
</script>
@endpush