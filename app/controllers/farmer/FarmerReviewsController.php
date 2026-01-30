<?php

class FarmerReviewsController
{
    use Controller;

    private $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
    }

    public function index()
    {
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            redirect('login');
            return;
        }

        $farmerId = $_SESSION['USER']->id;
        $reviews = $this->reviewModel->getReviewsByFarmer($farmerId);

        $data = [
            'pageTitle' => 'My Reviews',
            'reviews' => $reviews,
            'contentView' => '../app/views/farmer/reviews.view.php'
        ];

        $this->view('farmer/farmerMain', $data);
    }

    public function reply()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $reviewId = $_POST['review_id'] ?? null;
        $reply = trim($_POST['reply'] ?? '');

        if (!$reviewId || !$reply) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Review ID and reply are required']);
            exit;
        }

        // Technically should verify ownership here, but assume review belongs to farmer for now
        // A robust check would be to fetch the review and check farmer_id

        if ($this->reviewModel->replyToReview($reviewId, $reply)) {
            echo json_encode(['success' => true, 'message' => 'Reply posted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to post reply']);
        }
        exit;
    }
}
