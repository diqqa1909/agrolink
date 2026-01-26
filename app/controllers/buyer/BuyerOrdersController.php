<?php

class BuyerOrdersController
{
    use Controller;

    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        // Redirect to dashboard with orders section hash - orders section is in buyerDashboard.view.php
        redirect('buyerDashboard#orders');
    }

    public function cancel()
    {
        // Set JSON header since this is an API endpoint
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $orderId = $_POST['order_id'] ?? null;

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID is required']);
            return;
        }

        // Get order details to verify ownership and status
        $order = $this->orderModel->getOrderById($orderId);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        // Verify ownership
        if ($order->buyer_id != $_SESSION['USER']->id) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to cancel this order']);
            return;
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            echo json_encode(['success' => false, 'message' => 'This order cannot be cancelled in its current status']);
            return;
        }

        // Attempt to cancel
        if ($this->orderModel->updateOrderStatus($orderId, 'cancelled')) {
            // Restore stock
            $this->orderModel->restoreOrderStock($orderId);
            echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order']);
        }
    }

    public function details()
    {
        // Set JSON header
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            return;
        }

        $orderId = $_GET['id'] ?? null;

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID is required']);
            return;
        }

        // Get order details
        $order = $this->orderModel->getOrderById($orderId);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }

        // Verify ownership
        if ($order->buyer_id != $_SESSION['USER']->id) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to view this order']);
            return;
        }

        // Get order items
        $items = $this->orderModel->getOrderItems($orderId);

        echo json_encode([
            'success' => true,
            'order' => $order,
            'items' => $items
        ]);
    }
}
