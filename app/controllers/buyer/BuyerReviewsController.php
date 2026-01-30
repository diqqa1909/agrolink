<?php

class BuyerReviewsController
{
    use Controller;

    private $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
    }

    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        $userId = $_SESSION['USER']->id;
        $reviews = $this->reviewModel->getReviewsByBuyer($userId);

        $data = [
            'pageTitle' => 'My Reviews',
            'activePage' => 'reviews',
            'reviews' => $reviews,
            'contentView' => 'buyer/reviews.view.php'
        ];

        $this->view('components/buyerLayout', $data);
    }

    public function submit()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $buyerId = $_SESSION['USER']->id;
        $productId = $_POST['product_id'] ?? null;
        $orderId = $_POST['order_id'] ?? null;
        $farmerId = $_POST['farmer_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $comment = trim($_POST['comment'] ?? '');

        if (!$productId || !$orderId || !$farmerId || !$rating || !$comment) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }

        // Check if already reviewed
        if ($this->reviewModel->exists($orderId, $productId, $buyerId)) {
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
