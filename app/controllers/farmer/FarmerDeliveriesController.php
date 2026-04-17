<?php

class FarmerDeliveriesController
{
    use Controller;

    protected $farmerModel;
    protected $feedbackModel;

    public function __construct()
    {
        $this->farmerModel = new FarmerModel();
        $this->feedbackModel = new TransporterFeedbackModel();
    }

    public function index()
    {
        if (!hasRole('farmer')) {
            return redirect('login');
        }

        $farmerId = (int)authUserId();
        $filter = strtolower(trim($_GET['status'] ?? 'running'));

        $deliveries = $this->farmerModel->getFarmerDeliveryRequests($farmerId);

        if ($filter === 'running') {
            $deliveries = array_values(array_filter($deliveries, function ($delivery) {
                return in_array(($delivery->status ?? ''), ['accepted', 'in_transit'], true);
            }));
        } elseif ($filter !== 'all') {
            $deliveries = array_values(array_filter($deliveries, function ($delivery) use ($filter) {
                return ($delivery->status ?? '') === $filter;
            }));
        }

        $summary = $this->farmerModel->getFarmerDeliverySummary($farmerId);
        $submittedFeedbackOrderIds = $this->feedbackModel->getSubmittedOrderIdsForReviewer('farmer', $farmerId);

        $data = [
            'pageTitle' => 'Deliveries',
            'activePage' => 'deliveries',
            'pageStyles' => ['deliveries.css'],
            'filter' => $filter,
            'deliverySummary' => $summary,
            'deliveries' => $deliveries,
            'submittedFeedbackOrderIds' => $submittedFeedbackOrderIds,
            'contentView' => '../app/views/farmer/farmerDeliveries.view.php'
        ];

        $this->view('farmer/farmerSidebar', $data);
    }

    public function submitFeedback()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || ($_SESSION['USER']->role ?? '') !== 'farmer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $farmerId = (int)$_SESSION['USER']->id;
        $orderId = (int)($_POST['order_id'] ?? 0);
        $transporterId = (int)($_POST['transporter_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $reviewText = trim((string)($_POST['comment'] ?? ''));
        $complaintText = trim((string)($_POST['complaint_text'] ?? ''));
        $satisfactionStatus = trim((string)($_POST['satisfaction_status'] ?? 'neutral'));
        $validSatisfactionStatuses = ['very_satisfied', 'satisfied', 'neutral', 'dissatisfied', 'very_dissatisfied'];

        if ($orderId <= 0 || $transporterId <= 0 || $rating <= 0 || $reviewText === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Order, transporter, rating, and feedback are required']);
            exit;
        }

        if ($rating < 1 || $rating > 5) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
            exit;
        }

        if (!in_array($satisfactionStatus, $validSatisfactionStatuses, true)) {
            $satisfactionStatus = 'neutral';
        }

        $deliveries = $this->farmerModel->getFarmerDeliveryRequests($farmerId, 'delivered');
        $delivery = null;
        foreach ($deliveries as $row) {
            if ((int)$row->order_id === $orderId && (int)($row->transporter_id ?? 0) === $transporterId) {
                $delivery = $row;
                break;
            }
        }

        if (!$delivery) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'This delivered order is not available for feedback']);
            exit;
        }

        if ($this->feedbackModel->hasFeedback('farmer', $farmerId, $transporterId, $orderId)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'You have already submitted feedback for this delivery']);
            exit;
        }

        $result = $this->feedbackModel->createFeedback([
            'reviewer_type' => 'farmer',
            'reviewer_id' => $farmerId,
            'transporter_id' => $transporterId,
            'order_id' => $orderId,
            'delivery_request_id' => $delivery->id ?? null,
            'rating' => $rating,
            'review_text' => $reviewText,
            'on_time_flag' => !empty($_POST['on_time_flag']),
            'satisfaction_status' => $satisfactionStatus,
            'complaint_text' => $complaintText,
            'complaint_status' => $complaintText !== '' ? 'open' : 'none',
        ]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Transporter feedback submitted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to submit feedback']);
        }
        exit;
    }
}
