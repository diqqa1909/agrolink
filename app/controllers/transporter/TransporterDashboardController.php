<?php
class TransporterDashboardController {
    use Controller;
    
    public function index() {
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

        $data['username'] = $_SESSION['USER']->name;

        $vehicleModel = new VehicleModel();
        $data['vehicles'] = $vehicleModel->getByUserId($_SESSION['USER']->id);
        
        $this->view('transporterDashboard', $data);
    }

    public function addVehicle() {
        $response = ['success' => false, 'message' => 'Failed to add vehicle'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter') {
                $response['message'] = 'Unauthorized access';
                echo json_encode($response);
                exit;
            }
            
            $vehicleModel = new VehicleModel();
            
            $data = [
                'user_id' => $_SESSION['USER']->id,
                'type' => $_POST['type'] ?? '',
                'registration' => $_POST['registration'] ?? '',
                'capacity' => $_POST['capacity'] ?? '',
                'fuel_type' => $_POST['fuel_type'] ?? 'petrol',
                'model' => $_POST['model'] ?? '',
                'status' => 'active'
            ];
            
            if ($vehicleModel->validate($data)) {
                $vehicleModel->create($data);
                $response['success'] = true;
                $response['message'] = 'Vehicle added successfully!';
            } else {
                $response['errors'] = $vehicleModel->errors;
                $response['message'] = 'Validation failed';
            }
        }
        
        echo json_encode($response);
        exit;
    }

    public function editVehicle($id = null) {
        $response = ['success' => false, 'message' => 'Failed to update vehicle'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
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
            
            $data = [
                'type' => $_POST['type'] ?? $vehicle->type,
                'registration' => $_POST['registration'] ?? $vehicle->registration,
                'capacity' => $_POST['capacity'] ?? $vehicle->capacity,
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

    public function deleteVehicle($id = null) {
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

    public function getVehicles() {
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

    public function setActiveVehicle($id = null) {
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