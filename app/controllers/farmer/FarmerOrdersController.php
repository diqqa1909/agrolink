<?php

class FarmerOrdersController
{
    use Controller;

    protected $farmerModel;
    protected $orderModel;

    public function __construct()
    {
        $this->farmerModel = new FarmerModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Display farmer orders page
     */
    public function index()
    {
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            return redirect('login');
        }

        $userId = $_SESSION['USER']->id;

        // Get all orders that contain this farmer's products
        $orders = $this->farmerModel->getFarmerOrders($userId);

        $data = [
            'pageTitle' => 'Orders',
            'activePage' => 'orders',
            'orders' => $orders,
            'contentView' => '../app/views/farmer/farmerOrders.view.php',
            'pageScript' => 'farmerOrders.js'
        ];

        $this->view('farmer/farmerMain', $data);
    }

    /**
     * Get order details (AJAX)
     */
    public function getOrderDetails()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $orderId = $_GET['order_id'] ?? null;
        if (!$orderId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Order ID required']);
            exit;
        }

        $userId = $_SESSION['USER']->id;

        // Get order details
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Order not found']);
            exit;
        }

        // Get order items for this farmer only
        $orderItems = $this->farmerModel->getFarmerOrderItems($orderId, $userId);

        echo json_encode([
            'success' => true,
            'order' => $order,
            'items' => $orderItems
        ]);
        exit;
    }

    /**
     * Update order item status (AJAX)
     */
    public function updateItemStatus()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $itemId = $input['item_id'] ?? null;
        $status = $input['status'] ?? null;

        if (!$itemId || !$status) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Item ID and status required']);
            exit;
        }

        $userId = $_SESSION['USER']->id;

        // Verify the item belongs to this farmer
        if (!$this->farmerModel->verifyOrderItemOwnership($itemId, $userId)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized to update this item']);
            exit;
        }

        // Update the item status
        $result = $this->farmerModel->updateOrderItemStatus($itemId, $status);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Order item status updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update status']);
        }
        exit;
    }
}
