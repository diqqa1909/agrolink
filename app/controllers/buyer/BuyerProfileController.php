<?php

class BuyerProfileController
{
    use Controller;

    protected $buyerModel;
    protected $userModel;
    protected $orderModel;
    protected $paymentMethodsModel;

    public function __construct()
    {
        $this->buyerModel = new BuyerModel();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->paymentMethodsModel = new BuyerPaymentMethodsModel();
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

        // Build photo URL if exists
        $photoUrl = null;
        if (!empty($profile->profile_photo)) {
            $photoUrl = $this->buildPhotoUrl($profile->profile_photo);
        }

        $profileStats = $this->buildProfileStats($userId, $profile);

        $data = [
            'pageTitle' => 'Profile',
            'activePage' => 'profile',
            'username' => $_SESSION['USER']->name,
            'profile' => $profile,
            'photoUrl' => $photoUrl,
            'profileStats' => $profileStats,
            'pageScript' => 'profile.js',
            'contentView' => 'buyer/buyerProfileContent.view.php'
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
        if (!empty($profile->profile_photo)) {
            $photoUrl = $this->buildPhotoUrl($profile->profile_photo);
        }

        $maxEmailChanges = 2;
        $emailChangesUsed = $this->userModel->getEmailChangeCount($userId);
        $profileStats = $this->buildProfileStats($userId, $profile);

        echo json_encode([
            'success' => true,
            'profile' => $profile,
            'photoUrl' => $photoUrl,
            'profileStats' => $profileStats,
            'emailChangesUsed' => $emailChangesUsed,
            'emailChangesRemaining' => max(0, $maxEmailChanges - $emailChangesUsed)
        ]);
        exit;
    }

    private function buildProfileStats($userId, $profile)
    {
        $user = $this->userModel->findById($userId);
        $orders = $this->orderModel->getOrdersByBuyer($userId);
        $trackingRows = $this->orderModel->getDeliveryTrackingByBuyer($userId);
        $activeDeliveries = 0;

        if (is_array($trackingRows)) {
            foreach ($trackingRows as $row) {
                $deliveryStatus = strtolower(trim((string)($row->delivery_status ?? '')));
                $orderStatus = strtolower(trim((string)($row->order_status ?? '')));

                if (in_array($deliveryStatus, ['pending', 'accepted', 'in_transit'], true)) {
                    $activeDeliveries++;
                    continue;
                }

                if ($deliveryStatus === '' && in_array($orderStatus, ['pending', 'confirmed', 'processing', 'shipped'], true)) {
                    $activeDeliveries++;
                }
            }
        }

        return [
            'member_since' => $user->created_at ?? ($profile->created_at ?? null),
            'total_orders' => is_array($orders) ? count($orders) : 0,
            'active_deliveries' => $activeDeliveries,
            'account_status' => ucfirst((string)($user->status ?? 'active')),
        ];
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
                'street_name' => trim($_POST['street_name'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'postal_code' => trim($_POST['postal_code'] ?? ''),
                'additional_address_details' => trim($_POST['additional_address_details'] ?? ''),
            ];

            // Validate profile data at model level
            $validation = $this->buyerModel->validateProfile($data);

            if ($validation !== true) {
                error_log("Buyer Profile Validation Failed: " . json_encode($validation));
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation
                ]);
                exit;
            }

            // Name is editable from profile form; email changes are handled via Account Settings flow.
            if (!empty($data['name'])) {
                $this->userModel->update($userId, ['name' => $data['name']]);
                $_SESSION['USER']->name = $data['name'];
            }

            // Prepare profile data (exclude name and email from buyer profile update)
            $profileData = [
                'phone' => $data['phone'],
                'district' => $data['district'],
                'apartment_code' => $data['apartment_code'],
                'street_name' => $data['street_name'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'additional_address_details' => $data['additional_address_details'],
            ];

            // Update profile (creates if doesn't exist)
            $result = $this->buyerModel->updateProfile($userId, $profileData);

            if ($result === false) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update profile - database error'
                ]);
                exit;
            }

            // Get updated profile
            $profile = $this->buyerModel->getProfileByUserId($userId);

            if (!$profile) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Profile updated but failed to retrieve'
                ]);
                exit;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully',
                'profile' => $profile
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

    /**
     * Remove profile photo
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

    /**
     * Change password via AJAX
     */
    public function changePassword()
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
            $result = $this->userModel->updatePassword($userId, $newPassword);

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

    /**
     * Change email via AJAX (requires password confirmation)
     */
    public function changeEmail()
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

            $updated = $this->userModel->changeEmailWithAudit($userId, $newEmail);
            if (!$updated) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update email'
                ]);
                exit;
            }
            $_SESSION['USER']->email = $newEmail;

            $emailChangesUsed = $this->userModel->getEmailChangeCount($userId);
            $emailChangesRemaining = max(0, $maxEmailChanges - $emailChangesUsed);

            echo json_encode([
                'success' => true,
                'message' => 'Email updated successfully.',
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
     * Deactivate buyer account.
     */
    public function requestDeactivation()
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $userId = (int)$_SESSION['USER']->id;
        $reason = trim((string)($_POST['reason'] ?? ''));

        $blockingStatuses = ['pending', 'confirmed', 'processing', 'shipped'];
        $activeOrderCount = $this->orderModel->countBuyerOrdersByStatuses($userId, $blockingStatuses);
        if ($activeOrderCount > 0) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'error' => 'Cannot deactivate account while active orders exist.',
                'activeOrderCount' => $activeOrderCount,
            ]);
            exit;
        }

        $deactivated = $this->userModel->deactivateAccount($userId, $reason);
        if (!$deactivated) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to deactivate account',
            ]);
            exit;
        }

        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();

        echo json_encode([
            'success' => true,
            'message' => 'Account deactivated successfully.',
            'redirect' => ROOT . '/login?deactivated=1',
        ]);
        exit;
    }

    /**
     * List saved cards for buyer profile payment methods.
     */
    public function listCards()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $buyerId = (int)$_SESSION['USER']->id;
        echo json_encode([
            'success' => true,
            'cards' => $this->paymentMethodsModel->getCards($buyerId),
        ]);
        exit;
    }

    /**
     * Add a saved card for future payment gateway integration.
     */
    public function addCard()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $buyerId = (int)$_SESSION['USER']->id;
        $payload = [
            'card_holder_name' => trim((string)($_POST['card_holder_name'] ?? '')),
            'card_brand' => trim((string)($_POST['card_brand'] ?? '')),
            'card_last_four' => trim((string)($_POST['card_last_four'] ?? $_POST['card_last4'] ?? '')),
            'expiry_month' => trim((string)($_POST['expiry_month'] ?? '')),
            'expiry_year' => trim((string)($_POST['expiry_year'] ?? '')),
            'is_default' => !empty($_POST['is_default']),
        ];

        $result = $this->paymentMethodsModel->addCard($buyerId, $payload);
        if (empty($result['success'])) {
            if (!empty($result['errors'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $result['errors'],
                ]);
                exit;
            }
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to save card',
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Card saved successfully',
            'cards' => $result['cards'] ?? [],
        ]);
        exit;
    }

    /**
     * Set default card.
     */
    public function setDefaultCard()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $buyerId = (int)$_SESSION['USER']->id;
        $cardId = (int)($_POST['card_id'] ?? 0);
        if ($cardId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Invalid card id']);
            exit;
        }

        $result = $this->paymentMethodsModel->setDefaultCard($buyerId, $cardId);
        if (empty($result['success'])) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to set default card',
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Default card updated',
            'cards' => $result['cards'] ?? [],
        ]);
        exit;
    }

    /**
     * Remove saved card.
     */
    public function removeCard()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $buyerId = (int)$_SESSION['USER']->id;
        $cardId = (int)($_POST['card_id'] ?? 0);
        if ($cardId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Invalid card id']);
            exit;
        }

        $result = $this->paymentMethodsModel->removeCard($buyerId, $cardId);
        if (empty($result['success'])) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => $result['error'] ?? 'Failed to remove card',
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Saved card removed',
            'cards' => $result['cards'] ?? [],
        ]);
        exit;
    }
}
