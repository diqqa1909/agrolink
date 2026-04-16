<?php

class TransporterProfileController
{
    use Controller;

    protected $transporterModel;
    protected $userModel;

    public function __construct()
    {
        $this->transporterModel = new TransporterModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display transporter profile page
     */
    public function index()
    {
        // Check if this is an AJAX request
        if (!empty($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
            return $this->getProfileJson();
        }

        // Regular page view
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
            return redirect('login');
        }

        $userId = $_SESSION['USER']->id;

        // Get transporter profile
        $profile = $this->transporterModel->getProfileByUserId($userId);

        // If no profile exists, create empty one
        if (!$profile) {
            $this->transporterModel->createProfile($userId, []);
            $profile = $this->transporterModel->getProfileByUserId($userId);
        }

        // Build photo URL
        $photoUrl = null;
        if (!empty($profile->profile_photo)) {
            $photoUrl = $this->buildPhotoUrl($profile->profile_photo);
        }

        // Get vehicle types from database
        $vehicleTypeModel = new VehicleTypeModel();
        $vehicleTypes = $vehicleTypeModel->getActiveTypes();

        // Load and display the profile view through transporterMain layout
        $data = [
            'pageTitle' => 'Profile',
            'activePage' => 'profile',
            'username' => $_SESSION['USER']->name,
            'profile' => $profile,
            'photoUrl' => $photoUrl,
            'vehicleTypes' => $vehicleTypes,
            'contentView' => '../app/views/transporter/transporterProfileContent.view.php',
            'pageScript' => 'profile.js'
        ];

        $this->view('transporter/transporterMain', $data);
    }

    /**
     * Helper: Build public URL for a stored profile photo filename
     */
    private function buildPhotoUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }
        return rtrim(ROOT, '/') . '/assets/images/transporter-profiles/' . rawurlencode($filename);
    }

    /**
     * Absolute path to transporter profile photo directory under public assets.
     */
    private function getPhotoUploadDirectory(): string
    {
        $publicPath = realpath(__DIR__ . '/../../../public');
        if ($publicPath === false) {
            throw new RuntimeException('Public directory not found');
        }

        return $publicPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'transporter-profiles';
    }

    /**
     * Get profile data as JSON (for AJAX requests)
     */
    private function getProfileJson()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Unauthorized'
            ]);
            exit;
        }

        $userId = $_SESSION['USER']->id;
        $profile = $this->transporterModel->getProfileByUserId($userId);

        if (!$profile) {
            // Create default profile if doesn't exist
            $this->transporterModel->createProfile($userId, []);
            $profile = $this->transporterModel->getProfileByUserId($userId);
        }

        // Add computed photo URL for the frontend
        $photoUrl = null;
        if (!empty($profile->profile_photo)) {
            $photoUrl = $this->buildPhotoUrl($profile->profile_photo);
        }

        echo json_encode([
            'success' => true,
            'profile' => $profile,
            'photoUrl' => $photoUrl
        ]);
        exit;
    }

    /**
     * Save/Update transporter profile via AJAX
     */
    public function saveProfile()
    {
        if (ob_get_level()) ob_clean();
        ini_set('display_errors', '0');
        ini_set('html_errors', '0');
        header('Content-Type: application/json');

        // Check authentication
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
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
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'apartment_code' => trim($_POST['apartment_code'] ?? ''),
                'street_name' => trim($_POST['street_name'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'district' => trim($_POST['district'] ?? ''),
                'postal_code' => trim($_POST['postal_code'] ?? ''),
                'full_address' => trim($_POST['full_address'] ?? ''),
                'company_name' => trim($_POST['company_name'] ?? ''),
                'license_number' => trim($_POST['license_number'] ?? ''),
                'vehicle_type' => trim($_POST['vehicle_type'] ?? ''),
                'availability' => trim($_POST['availability'] ?? '')
            ];

            // Validate profile data at model level
            $validation = $this->transporterModel->validateProfile($data);

            if ($validation !== true) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation
                ]);
                exit;
            }

            // Update name and email in users table if provided
            if (!empty($data['name']) || !empty($data['email'])) {
                $userUpdateData = [];

                if (!empty($data['name'])) {
                    $userUpdateData['name'] = $data['name'];
                }

                if (!empty($data['email'])) {
                    // Check if email is already taken by another user
                    $existingUser = $this->userModel->findByEmail($data['email']);
                    if ($existingUser && $existingUser->id !== $userId) {
                        http_response_code(422);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Validation failed',
                            'errors' => ['email' => 'This email is already in use']
                        ]);
                        exit;
                    }
                    $userUpdateData['email'] = $data['email'];
                }

                if (!empty($userUpdateData)) {
                    $this->userModel->update($userId, $userUpdateData);

                    // Update session with new name/email
                    if (!empty($userUpdateData['name'])) {
                        $_SESSION['USER']->name = $userUpdateData['name'];
                    }
                    if (!empty($userUpdateData['email'])) {
                        $_SESSION['USER']->email = $userUpdateData['email'];
                    }
                }
            }

            // Prepare profile data (exclude name and email from transporter profile update)
            $profileData = [
                'phone' => $data['phone'],
                'apartment_code' => $data['apartment_code'],
                'street_name' => $data['street_name'],
                'city' => $data['city'],
                'district' => $data['district'],
                'postal_code' => $data['postal_code'],
                'full_address' => $data['full_address'],
                'company_name' => $data['company_name'],
                'license_number' => $data['license_number'],
                'vehicle_type' => $data['vehicle_type'],
                'availability' => $data['availability']
            ];

            // DEBUG: Log profile data
            error_log("=== TRANSPORTER PROFILE SAVE DEBUG ===");
            error_log("User ID: " . $userId);
            error_log("Profile Data: " . json_encode($profileData));

            // Check if profile exists
            $existingProfile = $this->transporterModel->getProfileByUserId($userId);
            error_log("Existing Profile: " . ($existingProfile ? "Found (ID: {$existingProfile->id})" : "Not Found"));

            if (!$existingProfile) {
                // Create profile
                $result = $this->transporterModel->createProfile($userId, $profileData);
                error_log("Create Profile Result: " . ($result ? "SUCCESS" : "FAILED"));
            } else {
                // Update profile
                $result = $this->transporterModel->updateProfile($userId, $profileData);
                error_log("Update Profile Result: " . ($result ? "SUCCESS" : "FAILED"));
            }

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update profile'
                ]);
            }

        } catch (Throwable $e) {
            error_log('Transporter profile save error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error while saving profile'
            ]);
        }

        exit;
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto()
    {
        if (ob_get_level()) ob_clean();
        ini_set('display_errors', '0');
        ini_set('html_errors', '0');
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $userId = $_SESSION['USER']->id;

            // Check if file was uploaded
            if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'error' => 'No photo uploaded']);
                exit;
            }

            $file = $_FILES['photo'];

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($file['tmp_name']);

            if (!in_array($fileType, $allowedTypes, true)) {
                echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and WebP are allowed.']);
                exit;
            }

            // Validate file size (5MB max)
            if ($file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'error' => 'File too large. Maximum size is 5MB.']);
                exit;
            }

            // Create upload directory if it doesn't exist
            $uploadDir = $this->getPhotoUploadDirectory();
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                    echo json_encode(['success' => false, 'error' => 'Failed to create upload directory']);
                    exit;
                }
            }
            if (!is_writable($uploadDir)) {
                @chmod($uploadDir, 0777);
            }
            if (!is_writable($uploadDir)) {
                echo json_encode(['success' => false, 'error' => 'Upload directory is not writable']);
                exit;
            }

            // Generate unique filename
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'transporter_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

            // Delete old photo if exists
            $oldPhoto = $this->transporterModel->getOldPhotoFilename($userId);
            if ($oldPhoto) {
                $oldPath = $uploadDir . DIRECTORY_SEPARATOR . $oldPhoto;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Move uploaded file with debug logging
            error_log("Attempting move_uploaded_file from {$file['tmp_name']} to {$uploadPath}");
            error_log("Upload dir exists: " . (is_dir($uploadDir) ? "yes" : "no"));
            error_log("Upload dir writable: " . (is_writable($uploadDir) ? "yes" : "no"));
            error_log("Temp file exists: " . (file_exists($file['tmp_name']) ? "yes" : "no"));

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $error = "Failed to move uploaded file from {$file['tmp_name']} to {$uploadPath}";
                error_log("MOVE_UPLOAD_FILE ERROR: " . $error);
                echo json_encode(['success' => false, 'error' => 'Failed to save photo']);
                exit;
            }

            error_log("File moved successfully to {$uploadPath}");

            // Update database
            $this->transporterModel->updateProfilePhoto($userId, $filename);

            // Return new photo URL
            $photoUrl = $this->buildPhotoUrl($filename);

            echo json_encode([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'photoUrl' => $photoUrl
            ]);
        } catch (Throwable $e) {
            error_log('Transporter photo upload error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error while uploading photo'
            ]);
        }
        exit;
    }

    /**
     * Remove profile photo
     */
    public function removePhoto()
    {
        if (ob_get_level()) ob_clean();
        ini_set('display_errors', '0');
        ini_set('html_errors', '0');
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $userId = $_SESSION['USER']->id;

            // Get old photo filename
            $oldPhoto = $this->transporterModel->getOldPhotoFilename($userId);

            if ($oldPhoto) {
                $uploadDir = $this->getPhotoUploadDirectory();
                $oldPath = $uploadDir . DIRECTORY_SEPARATOR . $oldPhoto;

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Update database
            $this->transporterModel->removeProfilePhoto($userId);

            echo json_encode([
                'success' => true,
                'message' => 'Photo removed successfully'
            ]);
        } catch (Throwable $e) {
            error_log('Transporter photo remove error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error while removing photo'
            ]);
        }
        exit;
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $userId = $_SESSION['USER']->id;
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate passwords
        $validation = $this->transporterModel->validatePasswordChange($userId, $currentPassword, $newPassword, $confirmPassword);

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
        $this->transporterModel->changePassword($userId, $newPassword);

        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
        exit;
    }
}
