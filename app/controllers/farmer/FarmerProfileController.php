<?php

class FarmerProfileController
{
    use Controller;

    protected $farmerModel;
    protected $userModel;

    public function __construct()
    {
        $this->farmerModel = new FarmerModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display farmer profile page
     */
    public function index()
    {
        // Check if this is an AJAX request
        if (!empty($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
            return $this->getProfileJson();
        }

        // Regular page view
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            return redirect('login');
        }

        $userId = $_SESSION['USER']->id;

        // Get farmer profile
        $profile = $this->farmerModel->getProfileByUserId($userId);

        // If no profile exists, create empty one
        if (!$profile) {
            $this->farmerModel->createProfile($userId, []);
            $profile = $this->farmerModel->getProfileByUserId($userId);
        }

        // Prepare data for view
        $data = [
            'pageTitle' => 'Profile',
            'activePage' => 'profile',
            'username' => $_SESSION['USER']->name,
            'profile' => $profile,
            'contentView' => '../app/views/farmer/farmerProfileContent.view.php'
        ];

        $this->view('components/farmerLayout', $data);
    }

    /**
     * Get profile data as JSON (for AJAX requests)
     */
    private function getProfileJson()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
            exit;
        }

        $userId = $_SESSION['USER']->id;
        $profile = $this->farmerModel->getProfileByUserId($userId);

        if (!$profile) {
            // Create default profile if doesn't exist
            $this->farmerModel->createProfile($userId, []);
            $profile = $this->farmerModel->getProfileByUserId($userId);
        }

        echo json_encode([
            'success' => true,
            'profile' => $profile
        ]);
        exit;
    }

    /**
     * Save/Update farmer profile via AJAX
     */
    public function saveProfile()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        // Check authentication
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
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
                'district' => trim($_POST['district'] ?? ''),
                'crops_selling' => trim($_POST['crops_selling'] ?? ''),
                'full_address' => trim($_POST['full_address'] ?? '')
            ];

            // Validate profile data at model level
            $validation = $this->farmerModel->validateProfile($data);

            if ($validation !== true) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation
                ]);
                exit;
            }

            // Update profile
            $result = $this->farmerModel->updateProfile($userId, $data);

            if ($result) {
                // Get updated profile
                $profile = $this->farmerModel->getProfileByUserId($userId);

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'profile' => $profile
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update profile'
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

    /**
     * Upload profile photo via AJAX
     * Strategy: Store only filename in database, construct full URL at display time
     */
    public function uploadPhoto()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        // Check authentication
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
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
            $errors = [];

            // Validate file upload
            if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'No file uploaded'
                ]);
                exit;
            }

            if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds php.ini upload_max_filesize',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds form MAX_FILE_SIZE',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'File upload stopped by PHP extension'
                ];

                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => $uploadErrors[$_FILES['photo']['error']] ?? 'Unknown upload error'
                ]);
                exit;
            }

            $filename = $_FILES['photo']['name'];
            $filesize = $_FILES['photo']['size'];
            $tmpName = $_FILES['photo']['tmp_name'];

            // Validate file extension
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid image format. Allowed: ' . implode(', ', $allowed)
                ]);
                exit;
            }

            // Validate file size (max 5MB)
            if ($filesize > 5 * 1024 * 1024) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Image size must be less than 5MB'
                ]);
                exit;
            }

            // Validate image content
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpName);
            finfo_close($finfo);

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($mimeType, $allowedMimes)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'File is not a valid image'
                ]);
                exit;
            }

            // Generate unique filename: profile_photo_{userId}_{timestamp}.{ext}
            $uniqueFilename = 'profile_photo_' . $userId . '_' . time() . '.' . $ext;
            $uploadPath = '../public/assets/images/farmer-profiles/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Move uploaded file
            if (!move_uploaded_file($tmpName, $uploadPath . $uniqueFilename)) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to save image to server'
                ]);
                exit;
            }

            // Delete old profile photo if exists
            $oldFilename = $this->farmerModel->getOldPhotoFilename($userId);
            if ($oldFilename && file_exists($uploadPath . $oldFilename)) {
                @unlink($uploadPath . $oldFilename);
            }

            // Update database with new filename (only filename, not full path)
            $result = $this->farmerModel->updateProfilePhoto($userId, $uniqueFilename);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile photo uploaded successfully',
                    'filename' => $uniqueFilename,
                    'photoUrl' => ROOT . '/assets/images/farmer-profiles/' . $uniqueFilename
                ]);
            } else {
                // Delete the uploaded file if database update fails
                @unlink($uploadPath . $uniqueFilename);

                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update profile photo in database'
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

    /**
     * Change password via AJAX
     */
    public function changePassword()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        // Check authentication
        if (!isset($_SESSION['USER'])) {
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

            $currentPassword = $_POST['currentPassword'] ?? '';
            $newPassword = $_POST['newPassword'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';

            // Validate at model level
            $validation = $this->farmerModel->validatePasswordChange(
                $userId,
                $currentPassword,
                $newPassword,
                $confirmPassword
            );

            if ($validation !== true) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation
                ]);
                exit;
            }

            // Change password
            $result = $this->farmerModel->changePassword($userId, $newPassword);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to change password'
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
