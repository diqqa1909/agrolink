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

        // Build photo URL
        $photoUrl = null;
        if (!empty($profile->profile_photo)) {
            $photoUrl = $this->buildPhotoUrl($profile->profile_photo);
        }

        // Load and display the profile view through farmerDashboard layout
        $data = [
            'pageTitle' => 'Profile',
            'activePage' => 'profile',
            'username' => $_SESSION['USER']->name,
            'profile' => $profile,
            'photoUrl' => $photoUrl,
            'contentView' => '../app/views/farmer/farmerProfileContent.view.php',
            'pageScript' => 'profile.js'
        ];

        $this->view('farmer/farmerMain', $data);
    }

    /**
     * Helper: Build public URL for a stored profile photo filename
     */
    private function buildPhotoUrl(?string $filename): ?string
    {
        if (!$filename) {
            return null;
        }
        return rtrim(ROOT, '/') . '/assets/images/farmer-profiles/' . rawurlencode($filename);
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

        // Add computed photo URL for the frontend
        $photoUrl = null;
        if (!empty($profile->profile_photo)) {
            $photoUrl = $this->buildPhotoUrl($profile->profile_photo);
        }

        $maxEmailChanges = 2;
        $emailChangesUsed = $this->userModel->getEmailChangeCount($userId);

        echo json_encode([
            'success' => true,
            'profile' => $profile,
            'photoUrl' => $photoUrl,
            'emailChangesUsed' => $emailChangesUsed,
            'emailChangesRemaining' => max(0, $maxEmailChanges - $emailChangesUsed)
        ]);
        exit;
    }

    /**
     * Save/Update farmer profile via AJAX
     */
    public function saveProfile()
    {
        if (ob_get_level()) ob_clean();
        ini_set('display_errors', '0');
        ini_set('html_errors', '0');
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
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => preg_replace('/\D/', '', trim($_POST['phone'] ?? '')),
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

            // Prepare profile data (exclude name and email from farmer profile update)
            $profileData = [
                'phone' => $data['phone'],
                'district' => $data['district'],
                'crops_selling' => $data['crops_selling'],
                'full_address' => $data['full_address']
            ];

            // Ensure profile exists before update
            $existingProfile = $this->farmerModel->getProfileByUserId($userId);
            if (!$existingProfile) {
                $result = $this->farmerModel->createProfile($userId, $profileData);
            } else {
                $result = $this->farmerModel->updateProfile($userId, $profileData);
            }

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
        } catch (Throwable $e) {
            error_log('Farmer profile save error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error while saving profile'
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
        ini_set('display_errors', '0');
        ini_set('html_errors', '0');
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
            $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'farmer-profiles' . DIRECTORY_SEPARATOR;

            // Create directory if it doesn't exist
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
            $oldFilename = $this->farmerModel->getOldPhotoFilename($userId);
            if ($oldFilename) {
                $oldBasename = basename($oldFilename);
                $oldPath = $uploadDir . $oldBasename;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Update database with new filename (only filename)
            $result = $this->farmerModel->updateProfilePhoto($userId, $uniqueFilename);

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
        } catch (Throwable $e) {
            error_log('Farmer photo upload error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error while uploading photo'
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

            // Get current photo filename
            $currentPhoto = $this->farmerModel->getOldPhotoFilename($userId);

            // Delete physical file if it exists
            if ($currentPhoto) {
                $publicPath = realpath(__DIR__ . '/../../../public');
                if ($publicPath !== false) {
                    $uploadDir = $publicPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'farmer-profiles' . DIRECTORY_SEPARATOR;
                    $filePath = $uploadDir . basename($currentPhoto);

                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            // Update database to remove photo reference
            $result = $this->farmerModel->removeProfilePhoto($userId);

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

    /**
     * Change email via AJAX (requires password confirmation)
     */
    public function changeEmail()
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $userId = (int)$_SESSION['USER']->id;
            $newEmail = strtolower(trim($_POST['newEmail'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $maxEmailChanges = 2;
            $errors = [];

            $currentUser = $this->userModel->first(['id' => $userId]);
            if (!$currentUser) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'User not found']);
                exit;
            }

            $emailChangesUsed = $this->userModel->getEmailChangeCount($userId);
            $emailChangesRemaining = max(0, $maxEmailChanges - $emailChangesUsed);

            if ($emailChangesRemaining <= 0) {
                $errors['limit'] = 'Email change limit reached. You can only change email 2 times after account creation.';
            }

            if ($newEmail === '') {
                $errors['new_email'] = 'New email is required';
            } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $errors['new_email'] = 'Please enter a valid email address';
            } elseif (strcasecmp($newEmail, (string)$currentUser->email) === 0) {
                $errors['new_email'] = 'New email must be different from current email';
            } else {
                $existingUser = $this->userModel->findByEmail($newEmail);
                if ($existingUser && (int)$existingUser->id !== $userId) {
                    $errors['new_email'] = 'This email is already used by another account';
                }
            }

            if ($password === '') {
                $errors['password'] = 'Password confirmation is required';
            } elseif (!password_verify($password, (string)$currentUser->password)) {
                $errors['password'] = 'Current password is incorrect';
            }

            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $errors,
                    'emailChangesUsed' => $emailChangesUsed,
                    'emailChangesRemaining' => $emailChangesRemaining
                ]);
                exit;
            }

            $updated = $this->userModel->update($userId, ['email' => $newEmail]);
            if (!$updated) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update email'
                ]);
                exit;
            }

            $this->userModel->recordEmailChange($userId, (string)$currentUser->email, $newEmail);
            $_SESSION['USER']->email = $newEmail;

            $emailChangesUsed = $this->userModel->getEmailChangeCount($userId);
            $emailChangesRemaining = max(0, $maxEmailChanges - $emailChangesUsed);

            echo json_encode([
                'success' => true,
                'message' => 'Email updated successfully. Session data has been updated. Use your new email from now on.',
                'email' => $newEmail,
                'sessionUpdated' => true,
                'emailChangesUsed' => $emailChangesUsed,
                'emailChangesRemaining' => $emailChangesRemaining
            ]);
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
