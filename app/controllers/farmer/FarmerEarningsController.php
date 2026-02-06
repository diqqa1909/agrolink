<?php

class FarmerEarningsController
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
     * Display farmer earnings page
     */
    public function index()
    {
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            return redirect('login');
        }

        $userId = $_SESSION['USER']->id;

        // Get earnings data
        $totalEarnings = $this->farmerModel->getTotalEarnings($userId);
        $monthlyEarnings = $this->farmerModel->getMonthlyEarnings($userId);
        $earningsByProduct = $this->farmerModel->getEarningsByProduct($userId);
        $recentEarnings = $this->farmerModel->getRecentEarnings($userId, 10);
        $earningsStats = $this->farmerModel->getEarningsStats($userId);

        $data = [
            'pageTitle' => 'Earnings',
            'activePage' => 'earnings',
            'totalEarnings' => $totalEarnings,
            'monthlyEarnings' => $monthlyEarnings,
            'earningsByProduct' => $earningsByProduct,
            'recentEarnings' => $recentEarnings,
            'earningsStats' => $earningsStats,
            'contentView' => '../app/views/farmer/farmerEarnings.view.php',
            'pageScript' => 'farmerEarnings.js'
        ];

        $this->view('farmer/farmerMain', $data);
    }

    /**
     * Get earnings data for a specific period (AJAX)
     */
    public function getEarningsByPeriod()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['USER']->id;
        $period = $_GET['period'] ?? 'month'; // month, year, all

        switch ($period) {
            case 'week':
                $earnings = $this->farmerModel->getWeeklyEarnings($userId);
                break;
            case 'year':
                $earnings = $this->farmerModel->getYearlyEarnings($userId);
                break;
            case 'all':
                $earnings = $this->farmerModel->getTotalEarnings($userId);
                break;
            default:
                $earnings = $this->farmerModel->getMonthlyEarnings($userId);
        }

        echo json_encode([
            'success' => true,
            'earnings' => $earnings,
            'period' => $period
        ]);
        exit;
    }
}
