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
            
            // Get vehicle type details if vehicle_type_id provided
            $vehicleTypeId = $_POST['vehicle_type_id'] ?? null;
            $type = $_POST['type'] ?? ''; // Fallback for compatibility
            $capacity = $_POST['capacity'] ?? ''; // Fallback for compatibility
            
            // DEBUG LOG
            error_log("=== ADD VEHICLE DEBUG ===");
            error_log("vehicle_type_id: " . ($vehicleTypeId ?? 'NULL'));
            
            if ($vehicleTypeId) {
                try {
                    $vehicleType = $this->get_row(
                        "SELECT vehicle_name, max_weight_kg FROM vehicle_types WHERE id = ? AND is_active = 1",
                        [$vehicleTypeId]
                    );
                    
                    if ($vehicleType) {
                        $type = $vehicleType->vehicle_name;
                        $capacity = $vehicleType->max_weight_kg;
                        error_log("Fetched type: $type, capacity: $capacity");
                    } else {
                        error_log("Invalid vehicle type ID: $vehicleTypeId");
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
                'transporter_id' => $_SESSION['USER']->id,
                'type' => $type,
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

            $vehicle = $vehicleModel->getById($id);
            if (!$vehicle || $vehicle->transporter_id != $_SESSION['USER']->id) {
                $response['message'] = 'Vehicle not found or unauthorized';
                echo json_encode($response);
                exit;
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
                'type' => $type,
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
            if (!$vehicle || $vehicle->user_id != $_SESSION['USER']->id) {
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
}
