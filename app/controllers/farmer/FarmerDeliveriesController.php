<?php

class FarmerDeliveriesController
{
    use Controller;

    protected $farmerModel;

    public function __construct()
    {
        $this->farmerModel = new FarmerModel();
    }

    public function index()
    {
        if (!isset($_SESSION['USER']) || ($_SESSION['USER']->role ?? '') !== 'farmer') {
            return redirect('login');
        }

        $farmerId = (int)$_SESSION['USER']->id;
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

        $data = [
            'pageTitle' => 'Deliveries',
            'activePage' => 'deliveries',
            'filter' => $filter,
            'deliverySummary' => $summary,
            'deliveries' => $deliveries,
            'contentView' => '../app/views/farmer/farmerDeliveries.view.php'
        ];

        $this->view('farmer/farmerMain', $data);
    }
}
