<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isAdmin = $notifiable->role === 'super_admin';
        $isVendor = $notifiable->role === 'vendor';

        if ($isAdmin) {
            return $this->toMailForAdmin($notifiable);
        } elseif ($isVendor) {
            return $this->toMailForVendor($notifiable);
        }

        return (new MailMessage)
            ->subject('New Order Placed')
            ->line('A new order has been placed.')
            ->action('View Order', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the mail representation for admin.
     */
    protected function toMailForAdmin(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Order Placed - Admin Notification')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new order has been placed on your marketplace.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total Amount: $' . number_format($this->order->total, 2))
            ->line('Customer: ' . $this->order->customer_name)
            ->action('View Order Details', url('/admin/orders/' . $this->order->id))
            ->line('Please review the order and take necessary actions.');
    }

    /**
     * Get the mail representation for vendor.
     */
    protected function toMailForVendor(object $notifiable): MailMessage
    {
        $vendorItems = $this->order->items->filter(function ($item) use ($notifiable) {
            return $item->vendor_id == $notifiable->id;
        });

        $vendorTotal = $vendorItems->sum('total');

        return (new MailMessage)
            ->subject('New Order for Your Products')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new order has been placed that includes your products.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Your Items Total: $' . number_format($vendorTotal, 2))
            ->action('View Order Details', url('/vendor/orders/' . $this->order->id))
            ->line('Please prepare the items for shipping.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'customer_name' => $this->order->customer_name,
        ];
    }
}