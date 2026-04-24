<?php
namespace App\Mail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable {
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope {
        return new Envelope(
            subject: '✅ Xác nhận đơn hàng '.$this->order->order_code.' — MotoShop',
        );
    }

    public function content(): Content {
        return new Content(
            markdown: 'emails.orders.confirmation',
            with: ['order' => $this->order],
        );
    }
}