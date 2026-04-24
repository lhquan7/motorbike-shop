@props(['name' => 'image', 'multiple' => false, 'preview' => null, 'label' => 'Ảnh đại diện'])

<div class="image-uploader-wrap" data-name="{{ $name }}" data-multiple="{{ $multiple ? 'true' : 'false' }}">
    <label class="form-label fw-semibold">{{ $label }}</label>

    {{-- Drop Zone --}}
    <div class="drop-zone" id="dropZone_{{ $name }}">
        <div class="drop-zone__inner">
            <i class="bi bi-cloud-upload drop-zone__icon"></i>
            <p class="drop-zone__text">Kéo & thả ảnh vào đây</p>
            <p class="drop-zone__sub">hoặc</p>
            <button type="button" class="btn btn-outline-primary btn-sm drop-zone__btn"
                onclick="document.getElementById('fileInput_{{ $name }}').click()">
                <i class="bi bi-folder2-open me-1"></i>Chọn từ máy tính
            </button>
            <p class="drop-zone__hint mt-2">PNG, JPG, WEBP — Tối đa 2MB{{ $multiple ? ' — Có thể chọn nhiều' : '' }}</p>
        </div>
    </div>

    {{-- Input file ẩn --}}
    <input type="file" id="fileInput_{{ $name }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        accept="image/*" {{ $multiple ? 'multiple' : '' }} class="d-none">

    {{-- Preview grid --}}
    <div class="preview-grid mt-3" id="previewGrid_{{ $name }}">
        @if($preview)
        <div class="preview-item existing">
            <img src="{{ asset('storage/'.$preview) }}" alt="Ảnh hiện tại">
            <span class="preview-label">Ảnh hiện tại</span>
        </div>
        @endif
    </div>
</div>

@once
@push('styles')
<style>
.drop-zone {
    border: 2px dashed var(--bs-border-color);
    border-radius: 12px;
    padding: 32px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .25s;
    background: var(--bs-light);
    position: relative;
}
.drop-zone.drag-over {
    border-color: #e63946;
    background: rgba(230,57,70,0.05);
    transform: scale(1.01);
}
.drop-zone__icon { font-size: 2.5rem; color: #adb5bd; display: block; margin-bottom: 8px; transition: color .2s; }
.drop-zone.drag-over .drop-zone__icon { color: #e63946; }
.drop-zone__text { font-weight: 600; color: #495057; margin: 0; }
.drop-zone__sub { color: #adb5bd; font-size: 13px; margin: 4px 0; }
.drop-zone__hint { color: #adb5bd; font-size: 12px; margin: 0; }
.preview-grid { display: flex; flex-wrap: wrap; gap: 10px; }
.preview-item {
    position: relative; width: 110px; height: 90px; border-radius: 8px;
    overflow: hidden; border: 2px solid #e9ecef; flex-shrink: 0;
    transition: border-color .2s;
}
.preview-item:hover { border-color: #e63946; }
.preview-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
.preview-item .preview-remove {
    position: absolute; top: 4px; right: 4px;
    width: 22px; height: 22px; border-radius: 50%;
    background: rgba(230,57,70,0.9); color: #fff; border: none;
    font-size: 12px; display: flex; align-items: center; justify-content: center;
    cursor: pointer; opacity: 0; transition: opacity .2s; line-height: 1;
}
.preview-item:hover .preview-remove { opacity: 1; }
.preview-item .preview-label {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: rgba(0,0,0,0.45); color: #fff;
    font-size: 10px; text-align: center; padding: 2px;
}
.preview-item.existing { border-style: solid; border-color: #0d6efd; }
.upload-progress {
    position: absolute; inset: 0; background: rgba(255,255,255,0.85);
    display: flex; align-items: center; justify-content: center; border-radius: 6px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.image-uploader-wrap').forEach(wrap => {
        const name      = wrap.dataset.name;
        const multiple  = wrap.dataset.multiple === 'true';
        const dropZone  = wrap.querySelector('#dropZone_' + name);
        const fileInput = wrap.querySelector('#fileInput_' + name);
        const grid      = wrap.querySelector('#previewGrid_' + name);
        let dataTransfer = new DataTransfer();

        // Click vào drop zone
        dropZone.addEventListener('click', e => {
            if (!e.target.classList.contains('drop-zone__btn')) fileInput.click();
        });

        // Drag events
        ['dragenter','dragover'].forEach(ev => {
            dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        });
        ['dragleave','dragend','drop'].forEach(ev => {
            dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.remove('drag-over'); });
        });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            handleFiles(e.dataTransfer.files);
        });

        // Change input
        fileInput.addEventListener('change', () => handleFiles(fileInput.files));

        function handleFiles(files) {
            const arr = Array.from(files);
            if (!multiple) {
                dataTransfer = new DataTransfer();
                // Xóa preview cũ (giữ lại "existing")
                grid.querySelectorAll('.preview-item:not(.existing)').forEach(el => el.remove());
            }
            arr.forEach(file => {
                if (!file.type.startsWith('image/')) { alert('Chỉ chấp nhận file ảnh: ' + file.name); return; }
                if (file.size > 2 * 1024 * 1024)     { alert('File quá 2MB: ' + file.name); return; }
                dataTransfer.items.add(file);
                addPreview(file);
            });
            // Gán lại file list vào input
            fileInput.files = dataTransfer.files;
        }

        function addPreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <button type="button" class="preview-remove" title="Xóa ảnh">×</button>
                    <span class="preview-label">${(file.size/1024).toFixed(0)}KB</span>`;
                item.querySelector('.preview-remove').addEventListener('click', () => {
                    // Xóa khỏi DataTransfer
                    const newDT = new DataTransfer();
                    Array.from(dataTransfer.files).forEach(f => { if (f !== file) newDT.items.add(f); });
                    dataTransfer = newDT;
                    fileInput.files = dataTransfer.files;
                    item.remove();
                });
                grid.appendChild(item);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endonce