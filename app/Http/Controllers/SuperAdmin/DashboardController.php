<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Statistics
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalVendors = User::where('role', 'vendor')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // Recent Orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Top Products
        $topProducts = Product::withCount('orderItems')
            ->with(['vendor', 'images'])
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get();

        // Monthly Revenue
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subYear())
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Order Status Distribution
        $orderStatusDistribution = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Top Vendors - Fixed Query
        // Calculate total revenue from vendor's order items
        $topVendors = User::where('role', 'vendor')
            ->withCount('products')
            ->addSelect([
                'total_revenue' => OrderItem::selectRaw('COALESCE(SUM(total), 0)')
                    ->whereColumn('vendor_id', 'users.id')
                    ->whereHas('order', function($query) {
                        $query->where('payment_status', 'paid');
                    })
            ])
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();

        return view('super-admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalVendors',
            'totalCustomers',
            'pendingOrders',
            'recentOrders',
            'topProducts',
            'monthlyRevenue',
            'orderStatusDistribution',
            'topVendors'
        ));
    }
    
    /**
     * Get notifications for the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notifications()
    {
        /** @var User $user */
        $user = auth()->user();
        
        // Get unread notifications for the current user
        $notifications = Notification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Count unread notifications
        $unreadCount = Notification::forUser($user->id)->unread()->count();
        
        // Transform notifications for the response
        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $this->getNotificationTitle($notification),
                'message' => $this->getNotificationMessage($notification),
                'created_at' => $notification->created_at->toIso8601String(),
                'read' => $notification->isRead(),
            ];
        });
        
        return response()->json([
            'notifications' => $formattedNotifications,
            'unread_count' => $unreadCount,
        ]);
    }
    
    /**
     * Get notification title based on type
     *
     * @param Notification $notification
     * @return string
     */
    private function getNotificationTitle($notification)
    {
        switch ($notification->type) {
            case 'App\Notifications\NewOrderPlaced':
                return 'New Order Placed';
            case 'App\Notifications\NewVendorRegistered':
                return 'New Vendor Registration';
            case 'App\Notifications\SupportTicketCreated':
                return 'New Support Ticket';
            case 'App\Notifications\RefundRequest':
                return 'New Refund Request';
            default:
                return 'Notification';
        }
    }
    
    /**
     * Get notification message based on type
     *
     * @param Notification $notification
     * @return string
     */
    private function getNotificationMessage($notification)
    {
        switch ($notification->type) {
            case 'App\Notifications\NewOrderPlaced':
                $orderId = $notification->data['order_id'] ?? 'N/A';
                $customerName = $notification->data['customer_name'] ?? 'A customer';
                return "{$customerName} placed a new order (#{$orderId})";
            case 'App\Notifications\NewVendorRegistered':
                $vendorName = $notification->data['vendor_name'] ?? 'A vendor';
                return "{$vendorName} has registered as a new vendor";
            case 'App\Notifications\SupportTicketCreated':
                $ticketSubject = $notification->data['subject'] ?? 'A support ticket';
                return "New support ticket: {$ticketSubject}";
            case 'App\Notifications\RefundRequest':
                $orderId = $notification->data['order_id'] ?? 'N/A';
                return "New refund request for order #{$orderId}";
            default:
                return 'You have a new notification';
        }
    }
}