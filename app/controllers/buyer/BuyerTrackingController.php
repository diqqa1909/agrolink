<?php

class BuyerTrackingController
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

        $userId = $_SESSION['USER']->id;
        $orderFilter = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        $trackingRows = $this->orderModel->getDeliveryTrackingByBuyer($userId);

        if ($orderFilter > 0) {
            $trackingRows = array_values(array_filter($trackingRows, function ($row) use ($orderFilter) {
                return (int)$row->order_id === $orderFilter;
            }));
        }

        $data = [
            'pageTitle' => 'Order Tracking',
            'activePage' => 'tracking',
            'trackingRows' => $trackingRows,
            'orderFilter' => $orderFilter,
            'contentView' => 'buyer/tracking.view.php'
        ];

        $this->view('components/buyerLayout', $data);
    }
}
