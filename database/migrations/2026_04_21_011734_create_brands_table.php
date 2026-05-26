<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Hàm up() được gọi khi chạy lệnh migrate. Nó định nghĩa cách tạo bảng brands.
     */
    public function up(): void
    {
        // Tạo bảng 'brands' trong cơ sở dữ liệu.
        Schema::create('brands', function (Blueprint $table) {
            // Khóa chính tự tăng.
            $table->id();

            // Tên thương hiệu.
            $table->string('name');

            // Slug dùng để định danh duy nhất, thường dùng trong URL.
            $table->string('slug')->unique();

            // Đường dẫn tới logo của thương hiệu, cho phép null nếu chưa có logo.
            $table->string('logo')->nullable();

            // Tạo hai cột created_at và updated_at để theo dõi thời điểm tạo/sửa.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Hàm down() được gọi khi rollback migration. Nó xoá bảng brands nếu tồn tại.
     */
    public function down(): void
    {
        // Xoá bảng 'brands' nếu bảng này tồn tại trong cơ sở dữ liệu.
        Schema::dropIfExists('brands');
    }
};
