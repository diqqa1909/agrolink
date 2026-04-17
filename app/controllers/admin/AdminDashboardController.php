<?php
class AdminDashboardController
{
    use Controller;

    private function requireAdminJson(): bool
    {
        return requireRole('admin', [
            'json' => true,
            'message' => 'Unauthorized. Admin access required.',
        ]);
    }

    public function index()
    {
        if (!requireRole('admin')) {
            return;
        }

        $user = new UserModel();
        $data = [
            'users' => $user->findAll(),
            'username' => authUserName(),
            'farmers' => count($user->where(['role' => 'farmer'], [])),
            'buyers' => count($user->where(['role' => 'buyer'], [])),
            'transporters' => count($user->where(['role' => 'transporter'], [])),
            'admins' => count($user->where(['role' => 'admin'], [])),
        ];

        $this->view('adminDashboard', $data);
    }

    public function deleteUser()
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        $userModel = new UserModel();
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $_POST['user_id'] ?? ($input['user_id'] ?? null);

        if (empty($userId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            exit;
        }

        $userToDelete = $userModel->first(['id' => $userId]);
        if ($userToDelete && ($userToDelete->role ?? '') === 'admin') {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot delete admin users. Admin accounts are protected for security.'
            ]);
            exit;
        }

        if ($userModel->delete($userId)) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
        exit;
    }

    public function getUsersTable()
    {
        if (!requireRole('admin')) {
            return;
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

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

    public function addUser()
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            exit;
        }

        $user = new UserModel();

        try {
            if (!$user->validate($_POST)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $user->errors,
                ]);
                exit;
            }

            $insertResult = $user->insert($_POST);
            if ($insertResult) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User created successfully',
                    'userId' => is_numeric($insertResult) ? (int)$insertResult : null,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create user in database',
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ]);
        }
        exit;
    }

    public function updateUserCount()
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        $user = new UserModel();
        $users = $user->findAll();
        echo json_encode([
            'success' => true,
            'userCount' => count($users),
            'message' => 'User count retrieved successfully',
        ]);
        exit;
    }

    public function register()
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method',
            ]);
            exit;
        }

        try {
            $userModel = new UserModel();
            $userData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? '',
                'password' => $_POST['password'] ?? '',
            ];

            $userId = $userModel->insert($userData);
            if ($userId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful',
                    'userId' => $userId,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Registration failed',
                ]);
            }
        } catch (Exception $e) {
            error_log('User registration error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An unexpected error occurred!',
            ]);
        }
        exit;
    }

    public function getUser($id)
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        try {
            $userModel = new UserModel();
            $user = $userModel->first(['id' => $id]);

            if ($user) {
                echo json_encode([
                    'success' => true,
                    'data' => $user,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found',
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load user details here',
            ]);
        }
        exit;
    }

    public function updateUser()
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request',
            ]);
            exit;
        }

        try {
            $userId = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new UserModel();
            $updateData = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ];

            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            $result = $userId ? $userModel->update($userId, $updateData) : false;
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User updated successfully',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update user',
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while updating the user: ' . $e->getMessage(),
            ]);
        }
        exit;
    }
}