<?php

class BuyerProfileController
{
    use Controller;

    protected $buyerModel;
    protected $userModel;

    public function __construct()
    {
        $this->buyerModel = new BuyerModel();
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
        $profile = $this->buyerModel->getProfileByUserId($userId);

        // If no profile exists, create empty one
        if (!$profile) {
            $this->buyerModel->createProfile($userId, []);
            $profile = $this->buyerModel->getProfileByUserId($userId);
        }

        // Load and display the profile view through buyerLayout
        $data = [
            'pageTitle' => 'Profile',
            'activePage' => 'profile',
            'username' => $_SESSION['USER']->name,
            'profile' => $profile,
            'contentView' => '../app/views/buyer/buyerProfile.view.php'
        ];

        $this->view('components/buyerLayout', $data);
    }

    /**
     * Helper: Build public URL for a stored profile photo filename
     */
    private function buildPhotoUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }
        return rtrim(ROOT, '/') . '/assets/images/buyer-profiles/' . rawurlencode($filename);
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
        $profile = $this->buyerModel->getProfileByUserId($userId);

        if (!$profile) {
            // Create default profile if doesn't exist
            $this->buyerModel->createProfile($userId, []);
            $profile = $this->buyerModel->getProfileByUserId($userId);
        }

        // Add computed photo URL for the frontend
        $photoUrl = null;
        if (!empty($profile->photo)) {
            $photoUrl = $this->buildPhotoUrl($profile->photo);
        }

        echo json_encode([
            'success' => true,
            'profile' => $profile,
            'photoUrl' => $photoUrl
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
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'district' => trim($_POST['district'] ?? ''),
                'apartment_code' => trim($_POST['apartment_code'] ?? ''),
                'street_number' => trim($_POST['street_number'] ?? ''),
                'street_name' => trim($_POST['street_name'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'postal_code' => trim($_POST['postal_code'] ?? '')
            ];

            // Validate profile data at model level
            $validation = $this->buyerModel->validateProfile($data);

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

            // Prepare profile data (exclude name and email from buyer profile update)
            $profileData = [
                'phone' => $data['phone'],
                'district' => $data['district'],
                'apartment_code' => $data['apartment_code'],
                'street_number' => $data['street_number'],
                'street_name' => $data['street_name'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code']
            ];

            // Update profile
            $result = $this->buyerModel->updateProfile($userId, $profileData);

            if ($result) {
                // Get updated profile
                $profile = $this->buyerModel->getProfileByUserId($userId);

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

            // Validate extension
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid image format. Allowed: ' . implode(', ', $allowed)
                ]);
                exit;
            }

            // Validate size (max 5MB)
            if ($filesize > 5 * 1024 * 1024) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Image size must be less than 5MB'
                ]);
                exit;
            }

            // Validate image content
            if (!is_uploaded_file($tmpName)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid upload'
                ]);
                exit;
            }

            $imgInfo = @getimagesize($tmpName);
            if ($imgInfo === false) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'File is not a valid image'
                ]);
                exit;
            }

            // Generate unique filename
            $uniqueFilename = 'profile_photo_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

            // FIX: correct path to /agrolink/public
            $publicPath = realpath(__DIR__ . '/../../../public');
            if ($publicPath === false) {
                throw new RuntimeException('Public directory not found');
            }
            $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'buyer-profiles' . DIRECTORY_SEPARATOR;

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                    throw new RuntimeException('Failed to create upload directory');
                }
            }

            // Move uploaded file
            if (!move_uploaded_file($tmpName, $uploadDir . $uniqueFilename)) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to save image to server'
                ]);
                exit;
            }

            // Delete old profile photo if exists (handle full URL vs filename safely)
            $oldFilename = $this->buyerModel->getOldPhotoFilename($userId);
            if ($oldFilename) {
                $oldBasename = basename($oldFilename);
                $oldPath = $uploadDir . $oldBasename;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Update database with new filename (only filename)
            $result = $this->buyerModel->updateProfilePhoto($userId, $uniqueFilename);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile photo uploaded successfully',
                    'filename' => $uniqueFilename,
                    'photoUrl' => $this->buildPhotoUrl($uniqueFilename)
                ]);
            } else {
                // Delete the uploaded file if database update fails
                @unlink($uploadDir . $uniqueFilename);

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

    /**     * Remove profile photo
     */
    public function removePhoto()
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

            // Get current photo filename
            $currentPhoto = $this->buyerModel->getOldPhotoFilename($userId);

            // Delete physical file if it exists
            if ($currentPhoto) {
                $publicPath = realpath(__DIR__ . '/../../../public');
                if ($publicPath !== false) {
                    $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'buyer-profiles' . DIRECTORY_SEPARATOR;
                    $filePath = $uploadDir . basename($currentPhoto);

                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            // Update database to remove photo reference
            $result = $this->buyerModel->removeProfilePhoto($userId);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile photo removed successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to remove profile photo from database'
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

    /**     * Change password via AJAX
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
            $validation = $this->buyerModel->validatePasswordChange(
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
            $result = $this->buyerModel->changePassword($userId, $newPassword);

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