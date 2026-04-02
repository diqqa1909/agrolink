<?php
class TransporterDashboardController
{
    use Controller;

    public function index()
    {
        $data = [];
        // Require login
        if (!isset($_SESSION['USER'])) {
            redirect('login');
            return;
        }

        // Only transporters can access
        if ($_SESSION['USER']->role !== 'transporter') {
            redirect('home');
            return;
        }

        $data['pageTitle'] = 'Dashboard';
        $data['activePage'] = 'dashboard';
        $data['username'] = $_SESSION['USER']->name;
        $data['pageScript'] = 'transporterDashboard.js';
        $data['contentView'] = '../app/views/transporter/transporterDashboard.view.php';

        $vehicleModel = new VehicleModel();
        $data['vehicles'] = $vehicleModel->getByUserId($_SESSION['USER']->id);
        
        // Get vehicle types from database
        $vehicleTypeModel = new VehicleTypeModel();
        $data['vehicleTypes'] = $vehicleTypeModel->getActiveTypes();

        // Get delivery request statistics
        $transporterModel = new TransporterModel();
        $earningsSummary = $transporterModel->getEarningsSummary($_SESSION['USER']->id);
        $data['earningsSummary'] = $earningsSummary;

        // Get available delivery requests count
        $availableRequests = $transporterModel->getAvailableDeliveryRequests($_SESSION['USER']->id);
        $data['availableRequestsCount'] = is_array($availableRequests) ? count($availableRequests) : 0;

        $this->view('transporter/transporterMain', $data);
    }

    public function addVehicle()
    {
        $response = ['success' => false, 'message' => 'Failed to add vehicle'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
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
                'transporter_id' => $_SESSION['USER']->id,
                'type' => $_POST['type'] ?? '',
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
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicleModel = new VehicleModel();
            $vehicleTypeModel = new VehicleTypeModel();

            $vehicle = $vehicleModel->getById($id);
            if (!$vehicle || $vehicle->transporter_id != $_SESSION['USER']->id) {
                $response['message'] = 'Vehicle not found or unauthorized';
                echo json_encode($response);
                exit;
            }
            
            // Determine capacity from vehicle type if type is being updated
            $capacity = $vehicle->capacity;
            $newType = $_POST['type'] ?? $vehicle->type;
            if ($newType !== $vehicle->type || empty($capacity)) {
                $types = $vehicleTypeModel->getActiveTypes();
                foreach ($types as $vType) {
                    $slug = strtolower(str_replace(' ', '', $vType->vehicle_name));
                    if ($slug === strtolower($newType)) {
                        $capacity = $vType->max_weight_kg;
                        break;
                    }
                }
            }

            // Get vehicle type details if vehicle_type_id provided
            $vehicleTypeId = $_POST['vehicle_type_id'] ?? null;
            $type = $_POST['type'] ?? $vehicle->type; // Fallback to existing
            $capacity = $_POST['capacity'] ?? $vehicle->capacity; // Fallback to existing
            
            if ($vehicleTypeId) {
                try {
                    $vehicleType = $this->get_row(
                        "SELECT vehicle_name, max_weight_kg FROM vehicle_types WHERE id = ? AND is_active = 1",
                        [$vehicleTypeId]
                    );
                    
                    if ($vehicleType) {
                        $type = $vehicleType->vehicle_name;
                        $capacity = $vehicleType->max_weight_kg;
                    } else {
                        $response['message'] = 'Invalid vehicle type selected';
                        echo json_encode($response);
                        exit;
                    }
                } catch (Exception $e) {
                    error_log("Error fetching vehicle type: " . $e->getMessage());
                    $response['message'] = 'Database error';
                    echo json_encode($response);
                    exit;
                }
            }

            $data = [
<<<<<<< HEAD
                'type' => $type,
=======
                'type' => $newType,
>>>>>>> Transporters'_2
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
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicleModel = new VehicleModel();

            $vehicle = $vehicleModel->getById($id);
            if (!$vehicle || $vehicle->transporter_id != $_SESSION['USER']->id) {
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
            $vehicleModel = new VehicleModel();
            $vehicles = $vehicleModel->getByUserId($_SESSION['USER']->id);
            $response['success'] = true;
            $response['vehicles'] = $vehicles ?: [];
        }

        echo json_encode($response);
        exit;
    }

    public function getVehicleTypes()
    {
<<<<<<< HEAD
        $response = ['success' => false, 'types' => []];

        try {
            $types = $this->query(
                "SELECT id, vehicle_name, min_weight_kg, max_weight_kg 
                 FROM vehicle_types 
                 WHERE is_active = 1 
                 ORDER BY min_weight_kg"
            );
            
            if ($types) {
                // Convert objects to arrays
                $typesArray = array_map(function($obj) {
                    return [
                        'id' => $obj->id,
                        'vehicle_name' => $obj->vehicle_name,
                        'min_weight_kg' => $obj->min_weight_kg,
                        'max_weight_kg' => $obj->max_weight_kg
                    ];
                }, $types);
                $response['success'] = true;
                $response['types'] = $typesArray;
            } else {
                $response['success'] = true;
                $response['types'] = [];
            }
        } catch (Exception $e) {
            error_log("Error fetching vehicle types: " . $e->getMessage());
            $response['error'] = 'Failed to load vehicle types';
=======
        $response = ['success' => false, 'vehicleTypes' => []];

        if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
            $vehicleTypeModel = new VehicleTypeModel();
            $types = $vehicleTypeModel->getActiveTypes();
            $response['success'] = true;
            $response['vehicleTypes'] = $types ?: [];
>>>>>>> Transporters'_2
        }

        echo json_encode($response);
        exit;
    }

    public function setActiveVehicle($id = null)
    {
        $response = ['success' => false, 'message' => 'Failed to set active vehicle'];

        if ($id) {
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $vehicleModel = new VehicleModel();

            $vehicle = $vehicleModel->getById($id);
            if (!$vehicle || $vehicle->user_id != $_SESSION['USER']->id) {
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
        // Clean output buffer and ensure JSON output
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        $response = ['success' => false, 'requests' => [], 'debug' => []];

        try {
            if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
                $transporterModel = new TransporterModel();
                $vehicleModel = new VehicleModel();
                
                // Check if transporter has vehicles
                $vehicles = $vehicleModel->getByUserId($_SESSION['USER']->id);
                $vehicles = is_array($vehicles) ? $vehicles : [];
                
                $activeVehicles = array_filter($vehicles, function($v) {
                    return isset($v->status) && $v->status === 'active';
                });
                
                $response['debug']['total_vehicles'] = count($vehicles);
                $response['debug']['active_vehicles'] = count($activeVehicles);
                
                // Check total pending delivery requests
                $allPending = $transporterModel->query("SELECT COUNT(*) as total FROM delivery_requests WHERE status = 'pending'", []);
                $response['debug']['total_pending_requests'] = (is_array($allPending) && !empty($allPending)) ? $allPending[0]->total : 0;
                
                $requests = $transporterModel->getAvailableDeliveryRequests($_SESSION['USER']->id);
                $response['success'] = true;
                $response['requests'] = is_array($requests) ? $requests : [];
                $response['debug']['matched_requests'] = count($response['requests']);
            }
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            $response['debug']['exception'] = true;
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
            $transporterModel = new TransporterModel();
            $requests = $transporterModel->getMyDeliveryRequests($_SESSION['USER']->id, $status);
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
        // Enable error logging
        error_log("=== acceptRequest called ===");
        error_log("Raw parameter received: " . var_export($id, true));
        error_log("All function arguments: " . var_export(func_get_args(), true));
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Request URI: " . $_SERVER['REQUEST_URI']);
        error_log("Session USER: " . json_encode($_SESSION['USER'] ?? 'not set'));
        
        $response = ['success' => false, 'message' => 'Failed to accept delivery request'];

        // Convert string "0" to null
        if ($id === '0' || $id === 0 || empty($id)) {
            error_log("Invalid ID provided: " . var_export($id, true));
            $response['message'] = 'Invalid delivery request ID';
            echo json_encode($response);
            exit;
        }
        
        if ($id) {
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
                $response['message'] = 'Unauthorized access';
                error_log("Authorization failed");
                echo json_encode($response);
                exit;
            }

            // Check if transporter has active vehicles
            $vehicleModel = new VehicleModel();
            $vehicles = $vehicleModel->getByUserId($_SESSION['USER']->id);
            error_log("Vehicles found: " . count($vehicles));
            
            $hasActiveVehicle = false;
            if (is_array($vehicles)) {
                foreach ($vehicles as $vehicle) {
                    error_log("Vehicle status: " . ($vehicle->status ?? 'no status'));
                    if ($vehicle->status === 'active') {
                        $hasActiveVehicle = true;
                        break;
                    }
                }
            }

            if (!$hasActiveVehicle) {
                $response['message'] = 'You must have at least one active vehicle to accept deliveries';
                error_log("No active vehicles");
                echo json_encode($response);
                exit;
            }

            $transporterModel = new TransporterModel();
            $result = $transporterModel->acceptDeliveryRequest($id, $_SESSION['USER']->id);
            error_log("Accept result: " . ($result ? 'true' : 'false'));

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Delivery request accepted successfully!';
            } else {
                $response['message'] = 'This request is no longer available or has already been accepted';
            }
        } else {
            error_log("No ID provided after validation");
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
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }

            $status = $_POST['status'] ?? '';
            $transporterModel = new TransporterModel();
            
            $result = $transporterModel->updateDeliveryStatus($id, $_SESSION['USER']->id, $status);

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
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
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

        if (isset($_SESSION['USER']) && $_SESSION['USER']->role === 'transporter') {
            $transporterModel = new TransporterModel();
            $earnings = $transporterModel->getEarningsSummary($_SESSION['USER']->id);
            $response['success'] = true;
            $response['earnings'] = $earnings;
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
        // Only allow admin or transporter to run this
        if (!isset($_SESSION['USER']) || !in_array($_SESSION['USER']->role, ['transporter', 'admin'])) {
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
        if (!isset($_SESSION['USER'])) {
            die('Unauthorized');
        }

        $transporterId = $_SESSION['USER']->id;
        $transporterModel = new TransporterModel();
        $vehicleModel = new VehicleModel();

        echo "<h2>Debug Information</h2>";
        echo "<h3>Transporter ID: {$transporterId}</h3>";
        
        // Check vehicles
        $vehicles = $vehicleModel->getByUserId($transporterId);
        echo "<h3>Vehicles (".count($vehicles)." found):</h3>";
        echo "<pre>" . print_r($vehicles, true) . "</pre>";
        
        // Check delivery requests in database
        $allRequests = $transporterModel->query("SELECT COUNT(*) as total FROM delivery_requests WHERE status = 'pending'", []);
        echo "<h3>Total Pending Delivery Requests: " . ($allRequests[0]->total ?? 0) . "</h3>";
        
        // Check available requests for this transporter
        $availableRequests = $transporterModel->getAvailableDeliveryRequests($transporterId);
        echo "<h3>Available for This Transporter (".count($availableRequests)." found):</h3>";
        echo "<pre>" . print_r($availableRequests, true) . "</pre>";
        
        echo "<br><a href='" . ROOT . "/transporter/transporterdashboard'>Go to Dashboard</a>";
    }
}
