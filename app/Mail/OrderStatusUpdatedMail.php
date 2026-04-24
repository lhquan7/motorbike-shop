<?php
namespace App\Mail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $oldStatus) {}

    public function envelope(): Envelope {
        $labels = ['confirmed'=>'Đã xác nhận','delivering'=>'Đang giao hàng','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'];
        return new Envelope(subject: '🔔 Đơn hàng '.$this->order->order_code.' — '.($labels[$this->order->status] ?? $this->order->status));
    }

    public function content(): Content {
        return new Content(view: 'emails.orders.status-updated');
    }
}