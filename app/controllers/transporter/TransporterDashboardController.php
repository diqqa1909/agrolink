<?php
class TransporterDashboardController
{
    use Controller;

    private $transporterModel;
    private $vehicleModel;
    private $vehicleTypeModel;
    private $feedbackModel;

    public function __construct()
    {
        $this->transporterModel = new TransporterModel();
        $this->vehicleModel = new VehicleModel();
        $this->vehicleTypeModel = new VehicleTypeModel();
        $this->feedbackModel = new TransporterFeedbackModel();
    }

    private function getSelectedOrFallbackVehicle($userId)
    {
        $selectedVehicle = $this->vehicleModel->getSelectedVehicleByUserId($userId);
        if ($selectedVehicle) {
            return $selectedVehicle;
        }

        $vehicles = $this->vehicleModel->getByUserId($userId);
        $activeVehicles = array_values(array_filter($vehicles, function ($vehicle) {
            return ($vehicle->status ?? '') === 'active';
        }));

        if (count($activeVehicles) === 1) {
            $this->vehicleModel->setSelectedVehicle($activeVehicles[0]->id, $userId);
            return $activeVehicles[0];
        }

        return false;
    }

    private function getProfileRestrictionPayload($userId)
    {
        $status = $this->transporterModel->getProfileCompletionStatus($userId);
        if ($status['complete']) {
            return null;
        }

        $missingLabels = [
            'phone' => 'phone number',
            'street_name' => 'street address',
            'district' => 'district',
            'city' => 'town/city',
            'license_number' => 'license number',
        ];

        $missing = array_map(function ($field) use ($missingLabels) {
            return $missingLabels[$field] ?? $field;
        }, $status['missing']);

        return [
            'success' => false,
            'message' => 'Complete your transporter profile before handling deliveries. Missing: ' . implode(', ', $missing),
            'missingFields' => $status['missing'],
        ];
    }

    private function enforceDeliveryReadiness()
    {
        $userId = $_SESSION['USER']->id ?? 0;
        $restriction = $this->getProfileRestrictionPayload($userId);
        if ($restriction !== null) {
            echo json_encode($restriction);
            exit;
        }
    }

    public function index()
    {
        if (!requireRole('transporter')) {
            return;
        }

        $data = [];
        $userId = authUserId();

        $data['pageTitle'] = 'Dashboard';
        $data['activePage'] = 'dashboard';
        $data['username'] = authUserName();
        $data['pageScript'] = 'transporterDashboard.js';
        $data['contentView'] = '../app/views/transporter/transporterDashboard.view.php';

        $data['vehicles'] = $this->vehicleModel->getByUserId($userId);
        
        // Get vehicle types from database
        $data['vehicleTypes'] = $this->vehicleTypeModel->getActiveTypes();

        // Get delivery request statistics
        $earningsSummary = $this->transporterModel->getEarningsSummary($userId);
        $data['earningsSummary'] = $earningsSummary;
        $data['performanceMetrics'] = $this->transporterModel->getPerformanceMetrics($userId);
        $data['profileRestriction'] = $this->getProfileRestrictionPayload($userId);
        $data['selectedVehicle'] = $this->getSelectedOrFallbackVehicle($userId);

        // Get available delivery requests count
        $availableRequests = $this->transporterModel->getAvailableDeliveryRequests($userId);
        $data['availableRequestsCount'] = is_array($availableRequests) ? count($availableRequests) : 0;

        $this->view('transporter/transporterSidebar', $data);
    }

    public function addVehicle()
    {
        $response = ['success' => false, 'message' => 'Failed to add vehicle'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hasRole('transporter')) {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicleModel = $this->vehicleModel;
            $vehicleTypeModel = $this->vehicleTypeModel;
            
            // Get vehicle type to determine capacity
            $vehicleType = null;
            if (!empty($_POST['type'])) {
                $types = $vehicleTypeModel->getActiveTypes();
                foreach ($types as $vType) {
                    $slug = strtolower(str_replace(' ', '', $vType->vehicle_name));
                    if ($slug === strtolower($_POST['type'])) {
                        $vehicleType = $vType;
                        break;
                    }
                }
            }

            // Use max_weight_kg from vehicle type as capacity
            $capacity = $vehicleType ? $vehicleType->max_weight_kg : 0;

            $data = [
                'transporter_id' => authUserId(),
                'type' => $_POST['type'] ?? '',
                'vehicle_type_id' => $vehicleType ? $vehicleType->id : null,
                'registration' => $_POST['registration'] ?? '',
                'license_number' => trim($_POST['license_number'] ?? ''),
                'capacity' => $capacity,
                'fuel_type' => $_POST['fuel_type'] ?? 'petrol',
                'model' => $_POST['model'] ?? '',
                'status' => 'active'
            ];

            error_log("Data to validate: " . json_encode($data));

            if ($vehicleModel->validate($data)) {
                error_log("Validation passed, calling create()");
                $result = $vehicleModel->create($data);
                if ($result) {
                    $existingSelected = $vehicleModel->getSelectedVehicleByUserId($_SESSION['USER']->id);
                    if (!$existingSelected) {
                        $vehicleModel->setSelectedVehicle((int)$result, $_SESSION['USER']->id);
                    }
                }
                error_log("Create result: " . json_encode($result));
                $response['success'] = true;
                $response['message'] = 'Vehicle added successfully!';
            } else {
                error_log("Validation failed: " . json_encode($vehicleModel->errors));
                $response['errors'] = $vehicleModel->errors;
                $response['message'] = 'Validation failed';
            }
        }

        echo json_encode($response);
        exit;
    }

    public function editVehicle($id = null)
    {
        $response = ['success' => false, 'message' => 'Failed to update vehicle'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            if (!hasRole('transporter')) {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicleModel = $this->vehicleModel;
            $vehicleTypeModel = $this->vehicleTypeModel;

            $vehicle = $vehicleModel->getById($id);
            if (!$vehicle || $vehicle->transporter_id != authUserId()) {
                $response['message'] = 'Vehicle not found or unauthorized';
                echo json_encode($response);
                exit;
            }

            // Resolve vehicle type and capacity consistently.
            $newType = $_POST['type'] ?? $vehicle->type;
            $capacity = (float)($vehicle->capacity ?? 0);
            $resolvedVehicleTypeId = isset($vehicle->vehicle_type_id) ? (int)$vehicle->vehicle_type_id : null;
            $vehicleTypeId = isset($_POST['vehicle_type_id']) ? (int)$_POST['vehicle_type_id'] : null;

            if ($vehicleTypeId > 0) {
                $selectedType = $vehicleTypeModel->getById($vehicleTypeId);
                if (!$selectedType || (int)$selectedType->is_active !== 1) {
                    $response['message'] = 'Invalid vehicle type selected';
                    echo json_encode($response);
                    exit;
                }

                $resolvedVehicleTypeId = (int)$selectedType->id;
                $newType = strtolower(str_replace(' ', '', $selectedType->vehicle_name));
                $capacity = (float)$selectedType->max_weight_kg;
            } else {
                $types = $vehicleTypeModel->getActiveTypes();
                foreach ($types as $vType) {
                    $slug = strtolower(str_replace(' ', '', $vType->vehicle_name));
                    if ($slug === strtolower($newType)) {
                        $resolvedVehicleTypeId = (int)$vType->id;
                        $capacity = (float)$vType->max_weight_kg;
                        break;
                    }
                }
            }

            $data = [
                'type' => $newType,
                'vehicle_type_id' => $resolvedVehicleTypeId,
                'registration' => $_POST['registration'] ?? $vehicle->registration,
                'license_number' => trim($_POST['license_number'] ?? $vehicle->license_number ?? ''),
                'capacity' => $capacity,
                'fuel_type' => $_POST['fuel_type'] ?? $vehicle->fuel_type,
                'model' => $_POST['model'] ?? $vehicle->model,
                'status' => $_POST['status'] ?? $vehicle->status
            ];
            $data['id'] = $id;

            if ($vehicleModel->validate($data)) {
                $vehicleModel->updateVehicle($id, $data);
                $response['success'] = true;
                $response['message'] = 'Vehicle updated successfully!';
            } else {
                $response['errors'] = $vehicleModel->errors;
                $response['message'] = 'Validation failed';
            }
        }

        echo json_encode($response);
        exit;
    }

    public function deleteVehicle($id = null)
    {
        $response = ['success' => false, 'message' => 'Failed to delete vehicle'];

        if ($id) {
            if (!hasRole('transporter')) {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicleModel = $this->vehicleModel;

            $vehicle = $vehicleModel->getById($id);
            if (!$vehicle || $vehicle->transporter_id != authUserId()) {
                $response['message'] = 'Vehicle not found or unauthorized';
                echo json_encode($response);
                exit;
            }

            $vehicleModel->deleteVehicle($id);
            $response['success'] = true;
            $response['message'] = 'Vehicle deleted successfully!';
        }

        echo json_encode($response);
        exit;
    }

    public function getVehicles()
    {
        $response = ['success' => false, 'vehicles' => []];

        if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
            $vehicleModel = $this->vehicleModel;
            $vehicles = $vehicleModel->getByUserId($_SESSION['USER']->id);
            $response['success'] = true;
            $response['vehicles'] = $vehicles ?: [];
        }

        echo json_encode($response);
        exit;
    }

    public function getVehicleTypes()
    {
        $response = ['success' => false, 'vehicleTypes' => []];

        if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
            $types = $this->vehicleTypeModel->getActiveTypes();
            $response['success'] = true;
            $response['vehicleTypes'] = $types ?: [];
        }

        echo json_encode($response);
        exit;
    }

    public function setActiveVehicle($id = null)
    {
        $response = ['success' => false, 'message' => 'Failed to set active vehicle'];

        if ($id) {
            if (!hasRole('transporter')) {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicleModel = $this->vehicleModel;

            $vehicle = $vehicleModel->getById($id);
            if (!$vehicle || $vehicle->transporter_id != authUserId()) {
                $response['message'] = 'Vehicle not found or unauthorized';
                echo json_encode($response);
                exit;
            }

            // Toggle vehicle status between active and inactive
            $newStatus = ($vehicle->status === 'active') ? 'inactive' : 'active';
            $vehicleModel->updateVehicle($id, ['status' => $newStatus]);
            $response['success'] = true;
            $response['message'] = 'Vehicle status updated successfully!';
        }

        echo json_encode($response);
        exit;
    }

    public function selectVehicle($id = null)
    {
        $response = ['success' => false, 'message' => 'Failed to select vehicle'];

        if ($id) {
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicle = $this->vehicleModel->getById($id);
            if (!$vehicle || (int)$vehicle->transporter_id !== (int)$_SESSION['USER']->id) {
                $response['message'] = 'Vehicle not found or unauthorized';
                echo json_encode($response);
                exit;
            }

            if (($vehicle->status ?? '') !== 'active') {
                $response['message'] = 'Only active vehicles can be selected for deliveries';
                echo json_encode($response);
                exit;
            }

            $this->vehicleModel->setSelectedVehicle($id, $_SESSION['USER']->id);
            $response['success'] = true;
            $response['message'] = 'Vehicle selected successfully';
        }

        echo json_encode($response);
        exit;
    }

    public function updateCurrentLocation()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $this->enforceDeliveryReadiness();

        $districtId = (int)($_POST['district_id'] ?? 0);
        $townId = (int)($_POST['town_id'] ?? 0);

        if ($districtId <= 0 || $townId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'District and town are required']);
            exit;
        }

        if ($this->transporterModel->updateCurrentLocation($_SESSION['USER']->id, $districtId, $townId)) {
            echo json_encode(['success' => true, 'message' => 'Current location updated successfully']);
        } else {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid district or town selection']);
        }
        exit;
    }

    /**
     * Get available delivery requests for the transporter
     */
    public function getAvailableRequests()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        $response = ['success' => false, 'requests' => []];

        try {
            if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
                $restriction = $this->getProfileRestrictionPayload($_SESSION['USER']->id);
                if ($restriction !== null) {
                    echo json_encode($restriction + ['requests' => [], 'debug' => []]);
                    exit;
                }
                $vehicleModel = $this->vehicleModel;
                
                // Check if transporter has vehicles
                $vehicles = $vehicleModel->getByUserId($_SESSION['USER']->id);
                $vehicles = is_array($vehicles) ? $vehicles : [];
                
                $activeVehicles = array_filter($vehicles, function($v) {
                    return isset($v->status) && $v->status === 'active';
                });
                
                $response['debug']['total_vehicles'] = count($vehicles);
                $response['debug']['active_vehicles'] = count($activeVehicles);
                
                // Check total pending delivery requests
                $allPending = $this->transporterModel->query("SELECT COUNT(*) as total FROM delivery_requests WHERE status = 'pending'", []);
                $response['debug']['total_pending_requests'] = (is_array($allPending) && !empty($allPending)) ? $allPending[0]->total : 0;
                
                $requests = $this->transporterModel->getAvailableDeliveryRequests($_SESSION['USER']->id);
                $response['success'] = true;
                $response['requests'] = is_array($requests) ? $requests : [];
            }
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Get transporter's accepted/in-progress delivery requests
     */
    public function getMyRequests()
    {
        $response = ['success' => false, 'requests' => []];

        if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
            $status = $_GET['status'] ?? null;
            $vehicleId = isset($_GET['vehicle_id']) && (int)$_GET['vehicle_id'] > 0 ? (int)$_GET['vehicle_id'] : null;
            $requests = $this->transporterModel->getMyDeliveryRequests($_SESSION['USER']->id, $status, $vehicleId);
            $response['success'] = true;
            $response['requests'] = $requests ?: [];
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Accept a delivery request
     */
    public function acceptRequest($id = null)
    {
        $response = ['success' => false, 'message' => 'Failed to accept delivery request'];

        if ($id === '0' || $id === 0 || empty($id)) {
            $response['message'] = 'Invalid delivery request ID';
            echo json_encode($response);
            exit;
        }

        if ($id) {
            if (!hasRole('transporter')) {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $restriction = $this->getProfileRestrictionPayload($_SESSION['USER']->id);
            if ($restriction !== null) {
                $response = $restriction;
                echo json_encode($response);
                exit;
            }

            // Accept vehicle_id from POST if provided, otherwise fall back to selected vehicle
            $postedVehicleId = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : 0;
            if ($postedVehicleId > 0) {
                $vehicleCheck = $this->vehicleModel->getByUserIdAndVehicleId($_SESSION['USER']->id, $postedVehicleId);
                $selectedVehicle = ($vehicleCheck && ($vehicleCheck->status ?? '') === 'active') ? $vehicleCheck : false;
            } else {
                $selectedVehicle = $this->getSelectedOrFallbackVehicle($_SESSION['USER']->id);
            }

            if (!$selectedVehicle) {
                $response['message'] = 'Select an active vehicle before accepting deliveries';
                error_log("No active vehicles");
                echo json_encode($response);
                exit;
            }

            $result = $this->transporterModel->acceptDeliveryRequest($id, $_SESSION['USER']->id, $selectedVehicle->id);

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Delivery request accepted successfully!';
            } else {
                $response['message'] = 'This request is no longer available or has already been accepted';
            }
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus($id = null)
    {
        $response = ['success' => false, 'message' => 'Failed to update delivery status'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            if (!hasRole('transporter')) {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $restriction = $this->getProfileRestrictionPayload($_SESSION['USER']->id);
            if ($restriction !== null) {
                echo json_encode($restriction);
                exit;
            }

            $status = $_POST['status'] ?? '';
            
            $result = $this->transporterModel->updateDeliveryStatus($id, $_SESSION['USER']->id, $status);

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Delivery status updated successfully!';
            } else {
                $response['message'] = 'Invalid status or unauthorized access';
            }
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Get delivery request details
     */
    public function getRequestDetails($id = null)
    {
        $response = ['success' => false, 'request' => null];

        if ($id) {
            if (!hasRole('transporter')) {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $request = $this->transporterModel->getDeliveryRequestById($id);

            if ($request) {
                $response['success'] = true;
                $response['request'] = $request;
            } else {
                $response['message'] = 'Request not found';
            }
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Get earnings summary
     */
    public function getEarnings()
    {
        $response = ['success' => false, 'earnings' => null];

        if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
            $earnings = $this->transporterModel->getEarningsSummary($_SESSION['USER']->id);
            $performance = $this->transporterModel->getPerformanceMetrics($_SESSION['USER']->id);
            $response['success'] = true;
            $response['earnings'] = $earnings;
            $response['performance'] = $performance;
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Get reviews/complaints addressed to the logged-in transporter.
     */
    public function getFeedbackReviews()
    {
        $response = ['success' => false, 'reviews' => []];

        if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
            $reviews = $this->feedbackModel->getFeedbackByTransporter($_SESSION['USER']->id);
            $response['success'] = true;
            $response['reviews'] = is_array($reviews) ? $reviews : [];
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Migrate existing orders to delivery requests (run once)
     * Access via: /transporter/transporterdashboard/migrateOrders
     */
    public function migrateOrders()
    {
        // Restrict migration endpoint to admins only.
        if (!hasRole('admin')) {
            die('Unauthorized access');
        }

        $transporterModel = new TransporterModel();
        $result = $transporterModel->migrateExistingOrders();

        echo "<h2>Migration Results</h2>";
        echo "<p>Success: {$result['success']}</p>";
        echo "<p>Errors: {$result['errors']}</p>";
        echo "<h3>Details:</h3><pre>" . implode("\n", $result['details']) . "</pre>";
        echo "<br><a href='" . ROOT . "/transporter/transporterdashboard'>Go to Dashboard</a>";
    }

    /**
     * Debug endpoint to check system status
     * Access via: /transporter/transporterdashboard/debugStatus
     */
    public function debugStatus()
    {
        // Restrict debug internals to admins only.
        if (!hasRole('admin')) {
            die('Unauthorized');
        }

        $transporterId = $_SESSION['USER']->id;
        $transporterModel = $this->transporterModel;
        $vehicleModel = $this->vehicleModel;

        echo "<h2>Debug Information</h2>";
        echo "<h3>Transporter ID: {$transporterId}</h3>";

        // Check vehicles
        $vehicles = $vehicleModel->getByUserId($transporterId);
        echo "<h3>Vehicles (" . count($vehicles) . " found):</h3>";
        echo "<pre>" . print_r($vehicles, true) . "</pre>";

        // Check delivery requests in database
        $allRequests = $transporterModel->query("SELECT COUNT(*) as total FROM delivery_requests WHERE status = 'pending'", []);
        echo "<h3>Total Pending Delivery Requests: " . ($allRequests[0]->total ?? 0) . "</h3>";

        // Check available requests for this transporter
        $availableRequests = $transporterModel->getAvailableDeliveryRequests($transporterId);
        echo "<h3>Available for This Transporter (" . count($availableRequests) . " found):</h3>";
        echo "<pre>" . print_r($availableRequests, true) . "</pre>";

        echo "<br><a href='" . ROOT . "/transporter/transporterdashboard'>Go to Dashboard</a>";
    }

    /**
     * Upload vehicle image
     * POST /transporterdashboard/uploadVehicleImage/{id}
     */
    public function uploadVehicleImage($id = null)
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!hasRole('transporter')) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Vehicle ID is required']);
            exit;
        }

        $vehicle = $this->vehicleModel->getById($id);
        if (!$vehicle || (int)$vehicle->transporter_id !== (int)authUserId()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Vehicle not found or unauthorized']);
            exit;
        }

        if (empty($_FILES['vehicle_image']) || $_FILES['vehicle_image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No image file uploaded']);
            exit;
        }

        $file = $_FILES['vehicle_image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG and WebP images are allowed']);
            exit;
        }

        if ($file['size'] > $maxSize) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Image must be smaller than 5MB']);
            exit;
        }

        $uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/') . '/agrolink/public/assets/images/vehicles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'vehicle_' . (int)$id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save image']);
            exit;
        }

        // Delete old image if exists
        if (!empty($vehicle->vehicle_image)) {
            $oldPath = $uploadDir . $vehicle->vehicle_image;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $this->vehicleModel->updateVehicleImage((int)$id, $filename);

        echo json_encode([
            'success' => true,
            'message' => 'Vehicle image uploaded successfully',
            'filename' => $filename,
            'url' => (defined('ROOT') ? ROOT : '') . '/public/assets/images/vehicles/' . $filename,
        ]);
        exit;
    }
}
