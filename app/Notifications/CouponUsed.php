<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouponUsed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $coupon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, Coupon $coupon)
    {
        $this->order = $order;
        $this->coupon = $coupon;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Coupon Used: ' . $this->coupon->code)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A coupon has been used in an order.')
            ->line('Coupon Code: ' . $this->coupon->code)
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Discount Amount: $' . number_format($this->order->discount, 2))
            ->action('View Order', url('/super-admin/orders/' . $this->order->id));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'coupon_id' => $this->coupon->id,
            'coupon_code' => $this->coupon->code,
            'discount_amount' => $this->order->discount,
        ];
    }
}
