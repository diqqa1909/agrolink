<?php

class BuyerReviewsController
{
    use Controller;

    private $reviewModel;
    private $orderModel;
    private $transporterFeedbackModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
        $this->orderModel = new OrderModel();
        $this->transporterFeedbackModel = new TransporterFeedbackModel();
    }

    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!hasRole('buyer')) {
            redirect('login');
            return;
        }

        $userId = authUserId();
        $reviews = $this->reviewModel->getReviewsByBuyer($userId);
        $reviewableItems = $this->orderModel->getReviewableItemsByBuyer($userId);
        $reviewableTransporters = $this->orderModel->getReviewableTransportersByBuyer($userId);

        $data = [
            'pageTitle' => 'My Reviews',
            'activePage' => 'reviews',
            'reviews' => $reviews,
            'reviewableItems' => $reviewableItems,
            'reviewableTransporters' => $reviewableTransporters,
            'pageStyles' => 'reviews.css',
            'contentView' => 'buyer/reviews.view.php'
        ];

        $this->view('buyer/buyerSidebar', $data);
    }

    public function submit()
    {
        header('Content-Type: application/json');

        if (!hasRole('buyer')) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $buyerId = authUserId();
        $reviewType = strtolower(trim($_POST['review_type'] ?? 'farmer'));
        $productId = $_POST['product_id'] ?? null;
        $orderId = $_POST['order_id'] ?? null;
        $farmerId = $_POST['farmer_id'] ?? null;
        $transporterId = $_POST['transporter_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $comment = trim($_POST['comment'] ?? '');
        $complaintText = trim($_POST['complaint_text'] ?? '');
        $satisfactionStatus = trim($_POST['satisfaction_status'] ?? 'neutral');
        $validSatisfactionStatuses = ['very_satisfied', 'satisfied', 'neutral', 'dissatisfied', 'very_dissatisfied'];

        if (!$orderId || !$rating || !$comment) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }

        $rating = (int)$rating;
        if ($rating < 1 || $rating > 5) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
            exit;
        }

        if (!in_array($satisfactionStatus, $validSatisfactionStatuses, true)) {
            $satisfactionStatus = 'neutral';
        }

        if ($reviewType === 'transporter') {
            if (!$transporterId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Transporter is required']);
                exit;
            }

            $orderContext = $this->orderModel->getOrderWithTransporterForBuyer($orderId, $buyerId);
            if (!$orderContext) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'This order is not available for transporter review']);
                exit;
            }

            if ((int)$orderContext->transporter_id !== (int)$transporterId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid transporter for selected order']);
                exit;
            }

            if ($orderContext->order_status !== 'delivered') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Transporter reviews are available after delivery is completed']);
                exit;
            }

            $canonicalProductId = (int)$orderContext->product_id;
            if ($this->transporterFeedbackModel->hasFeedback('buyer', $buyerId, $transporterId, $orderId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'You have already reviewed this transporter for this order']);
                exit;
            }

            $data = [
                'reviewer_type' => 'buyer',
                'reviewer_id' => $buyerId,
                'transporter_id' => $transporterId,
                'order_id' => $orderId,
                'rating' => $rating,
                'review_text' => $comment,
                'on_time_flag' => !empty($_POST['on_time_flag']),
                'satisfaction_status' => $satisfactionStatus,
                'complaint_text' => $complaintText,
                'complaint_status' => $complaintText !== '' ? 'open' : 'none',
            ];

            if ($this->transporterFeedbackModel->createFeedback($data)) {
                echo json_encode(['success' => true, 'message' => 'Transporter review submitted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to submit transporter review']);
            }
            exit;
        }

        if (!$productId || !$farmerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Product and farmer are required']);
            exit;
        }

        // Validate ownership and allowed order state before creating farmer review/complaint
        $orderItem = $this->orderModel->getOrderItemForBuyer($orderId, $productId, $buyerId);
        if (!$orderItem) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'This order item is not available for review']);
            exit;
        }

        if ((int)$orderItem->farmer_id !== (int)$farmerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid farmer for selected product']);
            exit;
        }

        $allowedStatuses = ['confirmed', 'processing', 'shipped', 'delivered'];
        if (!in_array($orderItem->order_status, $allowedStatuses, true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Reviews can be written once the order is confirmed']);
            exit;
        }

        if ($this->reviewModel->existsForTarget($orderId, $productId, $buyerId, $farmerId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this product']);
            exit;
        }

        $data = [
            'order_id' => $orderId,
            'product_id' => $productId,
            'buyer_id' => $buyerId,
            'farmer_id' => $farmerId,
            'rating' => $rating,
            'comment' => $comment
        ];

        if ($this->reviewModel->createReview($data)) {
            echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
        }
        exit;
    }
}
