<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewPosted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
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
        $isVendor = $notifiable->role === 'vendor';
        $isAdmin = $notifiable->role === 'super_admin';

        $subject = 'New Review Posted';
        $greeting = 'Hello ' . $notifiable->name . ',';

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line('A new review has been posted on a product.')
            ->line('Product: ' . $this->review->product->name)
            ->line('Rating: ' . str_repeat('★', $this->review->rating) . str_repeat('☆', 5 - $this->review->rating))
            ->line('Review: ' . substr($this->review->comment, 0, 100) . '...');

        if ($isVendor) {
            $message->action('View Review', url('/vendor/products/' . $this->review->product_id));
        } elseif ($isAdmin) {
            $message->action('Moderate Review', url('/super-admin/reviews/' . $this->review->id));
        }

        return $message->line('Please take appropriate action if needed.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'product_id' => $this->review->product_id,
            'product_name' => $this->review->product->name,
            'rating' => $this->review->rating,
            'user_name' => $this->review->user->name,
        ];
    }
}
