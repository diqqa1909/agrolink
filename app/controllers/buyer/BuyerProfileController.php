<?php

class BuyerProfileController
{
    use Controller;

    protected $buyerProfileModel;
    protected $userModel;

    public function __construct()
    {
        $this->buyerProfileModel = new BuyerProfileModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display buyer profile page
     */
    public function index()
    {
        // Check if this is an AJAX request
        if (!empty($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
            return $this->getProfileJson();
        }

        // Regular page view
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            return redirect('login');
        }

        $userId = $_SESSION['USER']->id;

        // Get buyer profile
        $profile = $this->buyerProfileModel->getProfileByUserId($userId);

        // Prepare data for view
        $data = [
            'pageTitle' => 'Profile',
            'activePage' => 'profile',
            'username' => $_SESSION['USER']->name,
            'profile' => $profile,
            'contentView' => '../app/views/buyer/buyerProfileContent.view.php'
        ];

        $this->view('components/buyerLayout', $data);
    }

    /**
     * Get profile data as JSON (for AJAX requests)
     */
    private function getProfileJson()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
            exit;
        }

        $userId = $_SESSION['USER']->id;
        $profile = $this->buyerProfileModel->getProfileByUserId($userId);

        echo json_encode([
            'success' => true,
            'profile' => $profile ?: null
        ]);
        exit;
    }

    /**
     * Save/Update buyer profile via AJAX
     */
    public function saveProfile()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        // Check authentication
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $userId = $_SESSION['USER']->id;

            // Get POST data
            $data = [
                'phone' => trim($_POST['phone'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'delivery_address' => trim($_POST['delivery_address'] ?? '')
            ];

            // Basic validation
            if (empty($data['phone']) || empty($data['city']) || empty($data['delivery_address'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'All fields are required'
                ]);
                exit;
            }

            // Check if profile exists
            $existingProfile = $this->buyerProfileModel->getProfileByUserId($userId);
            
            if ($existingProfile && is_object($existingProfile)) {
                // Update existing profile
                $result = $this->buyerProfileModel->updateProfile($userId, $data);
            } else {
                // Create new profile
                $result = $this->buyerProfileModel->createProfile($userId, $data);
            }

            // write() returns:
            // - Insert ID (int > 0) for successful INSERT
            // - true for successful UPDATE/DELETE
            // - 1 for failure
            // So: result !== 1 means success
            if ($result !== false && $result !== 1) {
                // Get updated profile to verify
                $profile = $this->buyerProfileModel->getProfileByUserId($userId);
                
                if ($profile && is_object($profile)) {
                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Profile saved successfully',
                        'profile' => $profile
                    ]);
                } else {
                    // If profile doesn't exist after save, try creating it
                    $createResult = $this->buyerProfileModel->createProfile($userId, $data);
                    if ($createResult !== false && $createResult !== 1) {
                        $profile = $this->buyerProfileModel->getProfileByUserId($userId);
                        http_response_code(200);
                        echo json_encode([
                            'success' => true,
                            'message' => 'Profile created successfully',
                            'profile' => $profile
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Failed to save profile. Please check your database connection.'
                        ]);
                    }
                }
            } else {
                // Result was 1 (failure) or false
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to save profile. Please check your database connection and ensure the buyer_profiles table exists.'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}

