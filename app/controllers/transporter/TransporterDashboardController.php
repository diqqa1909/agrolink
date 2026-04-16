<?php
class TransporterDashboardController
{
    use Controller;

    public function index()
    {
        if (!requireRole('transporter')) {
            return;
        }

        $data = [];

        $data['pageTitle'] = 'Dashboard';
        $data['activePage'] = 'dashboard';
        $data['username'] = authUserName();
        $data['pageScript'] = 'transporterDashboard.js';
        $data['contentView'] = '../app/views/transporter/transporterDashboard.view.php';

        $vehicleModel = new VehicleModel();
        $data['vehicles'] = $vehicleModel->getByUserId(authUserId());

        // Get vehicle types from database
        $vehicleTypeModel = new VehicleTypeModel();
        $data['vehicleTypes'] = $vehicleTypeModel->getActiveTypes();

        // Get delivery request statistics
        $transporterModel = new TransporterModel();
        $earningsSummary = $transporterModel->getEarningsSummary(authUserId());
        $data['earningsSummary'] = $earningsSummary;

        // Get available delivery requests count
        $availableRequests = $transporterModel->getAvailableDeliveryRequests(authUserId());
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

            $vehicleModel = new VehicleModel();
            $vehicleTypeModel = new VehicleTypeModel();

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
                'capacity' => $capacity,
                'fuel_type' => $_POST['fuel_type'] ?? 'petrol',
                'model' => $_POST['model'] ?? '',
                'status' => 'active'
            ];

            error_log("Data to validate: " . json_encode($data));

            if ($vehicleModel->validate($data)) {
                error_log("Validation passed, calling create()");
                $result = $vehicleModel->create($data);
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

            $vehicleModel = new VehicleModel();
            $vehicleTypeModel = new VehicleTypeModel();

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

            $vehicleModel = new VehicleModel();

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

        if (hasRole('transporter')) {
            $vehicleModel = new VehicleModel();
            $vehicles = $vehicleModel->getByUserId(authUserId());
            $response['success'] = true;
            $response['vehicles'] = $vehicles ?: [];
        }

        echo json_encode($response);
        exit;
    }

    public function getVehicleTypes()
    {
        $response = ['success' => false, 'vehicleTypes' => []];

        if (hasRole('transporter')) {
            $vehicleTypeModel = new VehicleTypeModel();
            $types = $vehicleTypeModel->getActiveTypes();
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

            $vehicleModel = new VehicleModel();

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

    /**
     * Get available delivery requests for the transporter
     */
    public function getAvailableRequests()
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        $response = ['success' => false, 'requests' => []];

        try {
            if (hasRole('transporter')) {
                $transporterModel = new TransporterModel();

                $filters = [
                    'location' => trim((string)($_GET['location'] ?? '')),
                    'max_distance' => isset($_GET['max_distance']) ? (float)$_GET['max_distance'] : 0,
                    'max_weight' => isset($_GET['max_weight']) ? (float)$_GET['max_weight'] : 0,
                    'min_payment' => isset($_GET['min_payment']) ? (float)$_GET['min_payment'] : 0,
                ];

                $requests = $transporterModel->getAvailableDeliveryRequests(authUserId(), $filters);
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

        if (hasRole('transporter')) {
            $rawStatus = strtolower(trim((string)($_GET['status'] ?? '')));
            $statusMap = [
                'all' => null,
                'pending' => 'pending',
                'accepted' => 'accepted',
                'running' => 'in_transit',
                'in_transit' => 'in_transit',
                'in-transit' => 'in_transit',
                'in-progress' => 'in_transit',
                'delivered' => 'delivered',
                'completed' => 'delivered',
                'cancelled' => 'cancelled',
            ];

            $status = array_key_exists($rawStatus, $statusMap) ? $statusMap[$rawStatus] : null;
            $transporterModel = new TransporterModel();
            $requests = $transporterModel->getMyDeliveryRequests(authUserId(), $status);
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

            // Check if transporter has active vehicles
            $vehicleModel = new VehicleModel();
            $vehicles = $vehicleModel->getByUserId(authUserId());

            $hasActiveVehicle = false;
            if (is_array($vehicles)) {
                foreach ($vehicles as $vehicle) {
                    if ($vehicle->status === 'active') {
                        $hasActiveVehicle = true;
                        break;
                    }
                }
            }

            if (!$hasActiveVehicle) {
                $response['message'] = 'You must have at least one active vehicle to accept deliveries';
                echo json_encode($response);
                exit;
            }

            $transporterModel = new TransporterModel();
            $result = $transporterModel->acceptDeliveryRequest($id, authUserId());

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

            $status = $_POST['status'] ?? '';
            $transporterModel = new TransporterModel();

            $result = $transporterModel->updateDeliveryStatus($id, authUserId(), $status);

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

            $transporterModel = new TransporterModel();
            $request = $transporterModel->getDeliveryRequestById($id);

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

        if (hasRole('transporter')) {
            $transporterModel = new TransporterModel();
            $earnings = $transporterModel->getEarningsSummary(authUserId());
            $response['success'] = true;
            $response['earnings'] = $earnings;
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

        if (hasRole('transporter')) {
            $reviewModel = new ReviewModel();
            $reviews = $reviewModel->getReviewsByTransporter(authUserId());
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

        $transporterId = authUserId();
        $transporterModel = new TransporterModel();
        $vehicleModel = new VehicleModel();

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
}
