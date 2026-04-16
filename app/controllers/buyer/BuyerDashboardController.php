<?php
class BuyerDashboardController
{
    use Controller;

    private $cartModel;
    private $wishlistModel;
    private $orderModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->wishlistModel = new WishlistModel();
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        $data = [];

        // Check if user is logged in and is a buyer
        if (!hasRole('buyer')) {
            redirect('login');
            return;
        }

        $user_id = authUserId();
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);

        // Load Products model
        $productModel = new ProductsModel();

        // Fetch all available products with farmer details
        $products = $productModel->getWithFarmerDetails();
        $wishlistItems = $this->wishlistModel->getByUserId($user_id);

        // Fetch orders for the buyer
        $orders = $this->orderModel->getOrdersByBuyer($user_id);
        
        // Calculate statistics
        $totalOrders = count($orders);
        $pendingOrders = 0;
        $totalSpent = 0;
        
        foreach ($orders as $order) {
            if ($order->status === 'pending' || $order->status === 'processing' || $order->status === 'confirmed' || $order->status === 'shipped') {
                $pendingOrders++;
            }
            // Include all active orders in total spent
            if ($order->status !== 'cancelled' && $order->status !== 'rejected') {
                $totalSpent += floatval($order->order_total);
            }
        }
        
        // Get order items for each order
        $ordersWithItems = [];
        foreach ($orders as $order) {
            $orderItems = $this->orderModel->getOrderItems($order->id);
            $ordersWithItems[] = [
                'order' => $order,
                'items' => $orderItems
            ];
        }

        $trackingRows = $this->orderModel->getDeliveryTrackingByBuyer($user_id);

        $data = [
            'pageTitle' => 'Dashboard',
            'activePage' => 'dashboard',
            'username' => authUserName(),
            'cartItemCount' => $cartItemCount,
            'products' => $products ?: [],
            'wishlistItems' => $wishlistItems ?: [],
            'orders' => $ordersWithItems,
            'trackingRows' => $trackingRows,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'totalSpent' => $totalSpent,
            'wishlistCount' => count($wishlistItems),
            'pageStyles' => 'dashboard.css',
            'pageScript' => 'buyerDashboard.js?v=' . time(),
            'contentView' => 'buyer/buyerDashboard.view.php'
        ];

        // Load the view through main layout
        $this->view('buyer/buyerSidebar', $data);
    }
}
