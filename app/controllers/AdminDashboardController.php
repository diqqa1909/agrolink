<?php
class AdminDashboardController {
    use Controller;
    
    public function index() {

            $data=[];
            $user = new UserModel;
            /* if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'admin'){
                $data['username'] = $_SESSION['USER']->name;
            } */
           $data['users'] = $user->findAll();
            if(!empty($_SESSION['USER'])){
                $data['username'] = $_SESSION['USER']->name;
                $data['farmers'] = count($user->where(['role'=>'farmer'],[]));
                $data['buyers'] = count($user->where(['role'=>'buyer'],[]));
                $data['transporters'] = count($user->where(['role'=>'transporter'],[]));
                $data['admins'] = count($user->where(['role'=>'admin'],[]));
                /* $result = $user->delete(2); */
                /* show($result); */
            }

            
            
            /* echo $data['users']; */
            
            // Prepare data for the view
            /* $data = [
                'title' => 'Admin Dashboard',
                'user' => $_SESSION['USER'],
                'welcome_message' => 'Welcome to Admin Dashboard'
                ]; */
                
                // Load the view
                $this->view('adminDashboard', $data);


    }

    
    
    public function deleteUser() {
        // Check if it's an AJAX request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userModel = new UserModel();
            $userId = $_POST['user_id'];
            
            if ($userModel->delete($userId)) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
            }
        } else {
            // Handle JSON request
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'] ?? null;
            
            if ($userId) {
                $userModel = new UserModel();
                
                if ($userModel->delete($userId)) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            }
        }
        exit;
    }

    public function getUsersTable() {
        $userModel = new UserModel();
        $users = $userModel->findAll();
        
        // Render just the table rows
        foreach ($users as $user) {
            echo "<tr id='user-{$user['id']}'>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td><button class='delete-btn' data-userid='{$user['id']}'>Delete</button></td>";
            echo "</tr>";
        }
        exit;
    }

    public function addUser(){
    // Always set JSON header first
    header('Content-Type: application/json');
    
    $user = new UserModel;
    
    // Check if it's a POST request
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        try {
            if ($user->validate($_POST)) {
                // Insert user and get the result
                $insertResult = $user->insert($_POST);
                
                if($insertResult) {
                    // If insert returns the user ID
                    $userId = is_numeric($insertResult) ? $insertResult : null;
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'User created successfully',
                        'userId' => $userId
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create user in database'
                    ]);
                }
            } else {
                // Validation failed - return validation errors
                echo json_encode([
                    'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $user->errors // Make sure this contains the validation errors
                    ]);
                }
            } catch (Exception $e) {
                // Handle any exceptions
                echo json_encode([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
            }
        } else {
            // Not a POST request
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }
        exit;
    }

    ///////////////MY FUNCTIONS////////////////
    public function updateUserCount(){
        header('Content-Type: application/json');

        $user = new UserModel;
        $users = $user->findAll();
        $userCount = count($users);

        echo json_encode([
            'success'=>true,
            'userCount' => $userCount,
        'message' => 'User count retrieved successfully'
    ]);
    exit;
    }

    public function register(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD']!=='POST'){
            echo json_encode([
                'success'=>false,
                'message'=>'Invalid request method'
            ]);
            exit;
        }

        try {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = new UserModel();

            $userData = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'password' => $password
            ];
            
            $userId = $user->insert($userData);

            if ($userId) {
                echo json_encode([
                    'success'=>true,
                    'message'=>'Registration succesful',
                    'userId'=>$userId
                ]);
            }else{
                echo json_encode([
                    'success'=>false,
                    'message'=>'Registration failed'
                ]);
            }
        } catch (Exception $e) {
            error_log('User registration error: ' . $e->getMessage());

            echo json_encode([
                'success'=>false,
                'message'=>'An unexpected error occured!'
            ]);
        }
        exit;
    }

    //the old one works
    /* public function getAllUsers(){
        header('Content-Type: application/json');

        try {
            $user = new UserModel();
            $users = $user->findAll();

            echo json_encode([
                'success'=>true,
                'data'=>$users,
                'total'=>count($users)
            ]);
        } catch (exception $e) {
            error_log('Error getting users: ' . $e->getMessage());
            echo json_encode([
                'success'=>false,
                'message'=>'Failed!',
                'data'=>[]
            ]);
        }
        exit;
    } */

    /* public function deleteUser(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode([
                'success' => false,
                'message'=>'Invalid request method'
            ]);
            exit;
        }

        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $userId = $data['id'] ?? null;

            $userModel = new UserModel();

            $result = $userModel->delete($userId);
            if($result){
                echo json_encode([
                    'success'=>true,
                    'message'=>'User delete success'
                ]);
            }else{
                echo json_encode([
                    'success'=>false,
                    'message'=>'User delete failed'
                ]);
            }
        } catch (Exception $e) {
            error_log('Error deleting user: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while deleting the user'
            ]);    
        }
        exit;
    } */

    public function getUser($id){
        header('Content-Type: application/json');
        
        try {
            $userModel = new UserModel;
            $user = $userModel->first(['id'=>$id]);

            if ($user) {
                echo json_encode([
                    'success'=>true,
                    'data'=>$user
                ]);
            }else{
                echo json_encode([
                    'success'=>false,
                    'message'=>'User not found'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success'=>false,
                'message'=>'Failed to load user details here'
            ]);
        }
        exit;
    }

    public function updateUser(){
        header('Content-Type: application/json');

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode([
                'success'=>false,
                'message'=>'Invalid request'
            ]);
            exit;
        }

        try {
            $userId = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $password = $_POST['password'];

            $userModel = new UserModel();

            $updateData = [
                'name'=>$name,
                'email'=>$email,
                'role'=>$role,
                'password'=>$password
            ];

            $result = $userModel->update($userId, $updateData);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update user'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while updating the user'
            ]);
        }
        exit;
    }

}