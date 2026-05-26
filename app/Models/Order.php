<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    // Các cột có thể gán hàng loạt từ input hoặc create() / update().
    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'note'
    ];

    // Quan hệ một-đến-nhiều với OrderItem: một đơn hàng có nhiều sản phẩm.
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Quan hệ với User: nếu khách hàng đã đăng nhập, đơn hàng gắn với user đó.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}