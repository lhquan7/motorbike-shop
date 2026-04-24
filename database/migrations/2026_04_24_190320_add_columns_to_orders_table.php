<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->enum('payment_method', ['cod','bank_transfer','vnpay','momo'])
                      ->default('cod')->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['unpaid','paid'])
                      ->default('unpaid')->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['pending','confirmed','delivering','completed','cancelled'])
                      ->default('pending')->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'order_code')) {
                $table->string('order_code')->unique()->after('id');
            }
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('orders', 'customer_address')) {
                $table->text('customer_address')->after('customer_email');
            }
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 15, 0)->after('customer_address');
            }
            if (!Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('status');
            }
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_code','customer_name','customer_phone',
                'customer_email','customer_address','total_amount',
                'payment_method','payment_status','status','note'
            ]);
        });
    }
};