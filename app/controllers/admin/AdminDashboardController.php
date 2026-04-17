<?php
class AdminDashboardController
{
    use Controller;
    use Database;

    public function index()
    {

        $data = [];
        $user = new UserModel;
        $verificationDoc = new VerificationDocumentModel;
        /* if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'admin'){
            $data['username'] = $_SESSION['USER']->name;
        } */
        $data['users'] = $user->findAll();
        if (!empty($_SESSION['USER'])) {
            $data['username'] = $_SESSION['USER']->name;
            $data['farmers'] = count($user->where(['role' => 'farmer'], []));
            $data['buyers'] = count($user->where(['role' => 'buyer'], []));
            $data['transporters'] = count($user->where(['role' => 'transporter'], []));
            $data['admins'] = count($user->where(['role' => 'admin'], []));
            /* $result = $user->delete(2); */
            /* show($result); */
        }

        $data['verifications'] = $verificationDoc->getAllVerificationsWithDocuments();



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

    public function addUser()
    {
        // Always set JSON header first
        header('Content-Type: application/json');

        $user = new UserModel;

        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if ($user->validate($_POST)) {
                    // Insert user and get the result
                    $insertResult = $user->insert($_POST);

                    if ($insertResult) {
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
            'userCount' => $userCount,
            'message' => 'User count retrieved successfully'
        ]);
        exit;
    }

    public function register()
    {
    public function register()
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

    public function getUser($id)
    {
        header('Content-Type: application/json');

        try {
            $userModel = new UserModel;
            $user = $userModel->first(['id' => $id]);

            if ($user) {
                echo json_encode([
                    'success' => true,
                    'data' => $user,
                    'success' => true,
                    'data' => $user
                ]);
            } else {
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
    public function updateUser()
    {
        header('Content-Type: application/json');
        if (!$this->requireAdminJson()) {
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

            // Check if user is logged in
            if (!isset($_SESSION['USER'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized. Please login again.'
                ]);
                exit;
            }

            $userModel = new UserModel();
            $updateData = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ];

            // Only update password if a new one is provided
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

    public function getVerifications()
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
        SELECT 
            u.id as user_id,
            u.name,
            u.email,
            u.role,
            u.verification_status,
            u.created_at as registered_at,
            COUNT(vd.id) as doc_count,
            SUM(CASE WHEN vd.status = 'approved' THEN 1 ELSE 0 END) as approved_docs,
            SUM(CASE WHEN vd.status = 'rejected' THEN 1 ELSE 0 END) as rejected_docs
        FROM users u
        LEFT JOIN verification_documents vd ON u.id = vd.user_id
        WHERE u.role IN ('farmer', 'transporter')
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");

        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 👉 OUTPUT HTML ROWS (same as getUsersTable)
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['user_id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['verification_status']}</td>";
            echo "<td>{$user['doc_count']}</td>";
            echo "<td>{$user['approved_docs']}</td>";
            echo "<td>{$user['rejected_docs']}</td>";
            echo "<td>
                <button onclick='viewDocs({$user['user_id']})'>View</button>
              </td>";
            echo "</tr>";
        }

        exit;
    }

    public function getUserDocuments($userId = null)
    {
        header('Content-Type: application/json');

        try {
            // Accept from URL segment (GET) OR JSON body (POST)
            if (!$userId) {
                $input = json_decode(file_get_contents('php://input'), true);
                $userId = $input['user_id'] ?? null;
            }

            // Also try from URL parameter if using router
            if (!$userId && isset($this->params['id'])) {
                $userId = $this->params['id'];
            }

            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
                exit;
            }

            // Use the query method from the Database trait
            $query = "
                SELECT vd.*, u.name, u.email, u.role, u.verification_status
                FROM verification_documents vd
                JOIN users u ON u.id = vd.user_id
                WHERE vd.user_id = ?
                ORDER BY vd.created_at DESC
            ";

            $docs = $this->query($query, [$userId]);

            if (!$docs) {
                $docs = [];
            }

            // Convert to array if needed (since query returns objects)
            $docsArray = [];
            foreach ($docs as $doc) {
                $docsArray[] = (array) $doc;
            }

            // Add document counts
            $totalDocs = count($docsArray);
            $approvedDocs = 0;
            foreach ($docsArray as $doc) {
                if ($doc['status'] === 'approved') {
                    $approvedDocs++;
                }
            }

            // Add user info to the first document
            if (!empty($docsArray)) {
                $docsArray[0]['doc_count'] = $totalDocs;
                $docsArray[0]['approved_docs'] = $approvedDocs;
            }

            echo json_encode(['success' => true, 'data' => $docsArray]);

        } catch (Exception $e) {
            error_log("Error in getUserDocuments: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function reviewDocument()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $docId = $input['doc_id'] ?? null;
            $action = $input['action'] ?? null;
            $reason = $input['reason'] ?? null;

            if (!$docId || !$action) {
                echo json_encode(['success' => false, 'message' => 'Document ID and action required']);
                exit;
            }

            $status = $action === 'approve' ? 'approved' : 'rejected';

            // Update the document status
            $updateQuery = "UPDATE verification_documents SET status = ?, rejection_reason = ? WHERE id = ?";
            $result = $this->write($updateQuery, [$status, $reason, $docId]);

            if ($result) {
                // Check if all documents for this user are approved
                $getUserQuery = "SELECT user_id FROM verification_documents WHERE id = ?";
                $doc = $this->query($getUserQuery, [$docId]);

                if ($doc && !empty($doc)) {
                    $userId = $doc[0]->user_id;

                    // Check if all documents are approved
                    $checkAllQuery = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved FROM verification_documents WHERE user_id = ?";
                    $stats = $this->query($checkAllQuery, [$userId]);

                    if ($stats && !empty($stats) && $stats[0]->total == $stats[0]->approved) {
                        // All documents approved - update user verification status
                        $updateUserQuery = "UPDATE users SET verification_status = 'approved' WHERE id = ?";
                        $this->write($updateUserQuery, [$userId]);
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Document ' . $action . 'd successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update document']);
            }
        } catch (Exception $e) {
            error_log("Error in reviewDocument: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function setUserVerification()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'] ?? null;
            $status = $input['status'] ?? null;

            if (!$userId || !$status) {
                echo json_encode(['success' => false, 'message' => 'User ID and status required']);
                exit;
            }

            $query = "UPDATE users SET verification_status = ? WHERE id = ?";
            $result = $this->write($query, [$status, $userId]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User verification status updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
            }
        } catch (Exception $e) {
            error_log("Error in setUserVerification: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    private function syncUserVerificationStatus(int $userId, $db): void
    {
        $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM verification_documents
        WHERE user_id = ?
    ");
        $stmt->execute([$userId]);
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($counts['total'] == 0)
            return;

        if ($counts['rejected'] > 0) {
            $newStatus = 'rejected';
        } elseif ($counts['approved'] == $counts['total']) {
            $newStatus = 'approved';
        } else {
            $newStatus = 'pending';
        }

        $db->prepare("UPDATE users SET verification_status = ? WHERE id = ?")
            ->execute([$newStatus, $userId]);
    }

    // Add this method to your AdminDashboardController.php

    public function getOrders()
    {
        header('Content-Type: application/json');

        try {
            $db = $this->connect();

            // Get filter parameters
            $input = json_decode(file_get_contents('php://input'), true);
            $status = $input['status'] ?? '';
            $paymentStatus = $input['payment_status'] ?? '';
            $dateRange = $input['date_range'] ?? '';
            $search = $input['search'] ?? '';

            // Build the query
            $query = "
            SELECT 
                o.id as order_id,
                o.total_amount,
                o.status as order_status,
                o.created_at as order_date,
                b.name as buyer_name,
                b.id as buyer_id,
                f.name as farmer_name,
                f.id as farmer_id,
                (
                    SELECT COUNT(*) 
                    FROM order_items oi 
                    WHERE oi.order_id = o.id
                ) as item_count
            FROM orders o
            JOIN users b ON o.buyer_id = b.id
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            JOIN users f ON p.farmer_id = f.id
            WHERE 1=1
        ";

            $params = [];

            // Apply status filter
            if (!empty($status)) {
                $query .= " AND o.status = :status";
                $params[':status'] = $status;
            }

            // Apply payment status filter
            if (!empty($paymentStatus)) {
                $query .= " AND o.payment_status = :payment_status";
                $params[':payment_status'] = $paymentStatus;
            }

            // Apply date range filter
            if (!empty($dateRange)) {
                switch ($dateRange) {
                    case 'today':
                        $query .= " AND DATE(o.created_at) = CURDATE()";
                        break;
                    case 'week':
                        $query .= " AND YEARWEEK(o.created_at) = YEARWEEK(CURDATE())";
                        break;
                    case 'month':
                        $query .= " AND MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
                        break;
                    case 'quarter':
                        $query .= " AND QUARTER(o.created_at) = QUARTER(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
                        break;
                }
            }

            // Apply search filter
            if (!empty($search)) {
                $query .= " AND (o.order_number LIKE :search OR b.name LIKE :search OR f.name LIKE :search)";
                $params[':search'] = "%{$search}%";
            }

            $query .= " GROUP BY o.id ORDER BY o.created_at DESC";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get statistics
            $statsQuery = "
            SELECT 
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                AVG(total_amount) as avg_order_value
            FROM orders
            WHERE status != 'cancelled'
        ";

            $statsStmt = $db->prepare($statsQuery);
            $statsStmt->execute();
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $orders,
                'stats' => [
                    'pending' => (int) ($stats['pending'] ?? 0),
                    'processing' => (int) ($stats['processing'] ?? 0),
                    'completed' => (int) ($stats['completed'] ?? 0),
                    'avg_order_value' => round($stats['avg_order_value'] ?? 0, 2)
                ]
            ]);

        } catch (Exception $e) {
            error_log("Error in getOrders: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function getOrderDetails($orderId = null)
    {
        header('Content-Type: application/json');

        try {
            if (!$orderId) {
                $input = json_decode(file_get_contents('php://input'), true);
                $orderId = $input['order_id'] ?? null;
            }

            if (!$orderId) {
                echo json_encode(['success' => false, 'message' => 'Order ID required']);
                exit;
            }

            $db = $this->connect();

            $query = "
            SELECT 
                o.*,
                b.name as buyer_name,
                b.email as buyer_email,
                b.phone as buyer_phone,
                b.address as buyer_address,
                f.name as farmer_name,
                f.email as farmer_email
            FROM orders o
            JOIN users b ON o.buyer_id = b.id
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            JOIN users f ON p.farmer_id = f.id
            WHERE o.id = ?
            GROUP BY o.id
        ";

            $stmt = $db->prepare($query);
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get order items
            $itemsQuery = "
            SELECT 
                oi.*,
                p.name as product_name,
                p.image as product_image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ";

            $itemsStmt = $db->prepare($itemsQuery);
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'order' => $order,
                'items' => $items
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function updateOrderStatus()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $orderId = $input['order_id'] ?? null;
            $status = $input['status'] ?? null;

            if (!$orderId || !$status) {
                echo json_encode(['success' => false, 'message' => 'Order ID and status required']);
                exit;
            }

            $db = $this->connect();

            $query = "UPDATE orders SET status = :status WHERE id = :order_id";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([':status' => $status, ':order_id' => $orderId]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Add these methods to your AdminDashboardController class

    public function getProducts()
    {
        header('Content-Type: application/json');

        try {
            $db = $this->connect();

            // Get filter parameters
            $input = json_decode(file_get_contents('php://input'), true);
            $search = $input['search'] ?? '';
            $category = $input['category'] ?? '';
            $status = $input['status'] ?? '';
            $minPrice = $input['min_price'] ?? '';
            $maxPrice = $input['max_price'] ?? '';

            // Build the query
            $query = "
            SELECT 
                p.id,
                p.name,
                p.description,
                p.price,
                p.quantity as stock,
                p.category,
                p.status,
                p.image,
                p.created_at,
                u.name as farmer_name,
                u.id as farmer_id,
                (
                    SELECT COUNT(*) 
                    FROM order_items oi 
                    WHERE oi.product_id = p.id
                ) as total_orders
            FROM products p
            JOIN users u ON p.farmer_id = u.id
            WHERE 1=1
        ";

            $params = [];

            // Apply search filter
            if (!empty($search)) {
                $query .= " AND (p.name LIKE :search OR p.description LIKE :search OR u.name LIKE :search)";
                $params[':search'] = "%{$search}%";
            }

            // Apply category filter
            if (!empty($category)) {
                $query .= " AND p.category = :category";
                $params[':category'] = $category;
            }

            // Apply status filter
            if (!empty($status)) {
                $query .= " AND p.status = :status";
                $params[':status'] = $status;
            }

            // Apply price range filter
            if (!empty($minPrice)) {
                $query .= " AND p.price >= :min_price";
                $params[':min_price'] = $minPrice;
            }

            if (!empty($maxPrice)) {
                $query .= " AND p.price <= :max_price";
                $params[':max_price'] = $maxPrice;
            }

            $query .= " ORDER BY p.created_at DESC";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get statistics
            $statsQuery = "
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_approval,
                AVG(price) as avg_price
            FROM products
        ";

            $statsStmt = $db->prepare($statsQuery);
            $statsStmt->execute();
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

            // Get category counts for filter options
            $categoryQuery = "
            SELECT 
                category,
                COUNT(*) as count
            FROM products
            WHERE category IS NOT NULL AND category != ''
            GROUP BY category
            ORDER BY count DESC
        ";

            $categoryStmt = $db->prepare($categoryQuery);
            $categoryStmt->execute();
            $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $products,
                'stats' => [
                    'total_products' => (int) ($stats['total_products'] ?? 0),
                    'active_products' => (int) ($stats['active_products'] ?? 0),
                    'out_of_stock' => (int) ($stats['out_of_stock'] ?? 0),
                    'pending_approval' => (int) ($stats['pending_approval'] ?? 0),
                    'avg_price' => round($stats['avg_price'] ?? 0, 2)
                ],
                'categories' => $categories
            ]);

        } catch (Exception $e) {
            error_log("Error in getProducts: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function getProductDetails($productId = null)
    {
        header('Content-Type: application/json');

        try {
            if (!$productId) {
                $input = json_decode(file_get_contents('php://input'), true);
                $productId = $input['product_id'] ?? null;
            }

            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                exit;
            }

            $db = $this->connect();

            // Get product details with farmer info
            $query = "
            SELECT 
                p.*,
                u.name as farmer_name,
                u.email as farmer_email,
                u.phone as farmer_phone,
                u.address as farmer_address
            FROM products p
            JOIN users u ON p.farmer_id = u.id
            WHERE p.id = ?
        ";

            $stmt = $db->prepare($query);
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }

            // Get order history for this product
            $orderQuery = "
            SELECT 
                o.id as order_id,
                o.order_number,
                o.total_amount,
                o.status as order_status,
                o.created_at as order_date,
                oi.quantity,
                oi.price as unit_price,
                u.name as buyer_name,
                u.email as buyer_email
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN users u ON o.buyer_id = u.id
            WHERE oi.product_id = ?
            ORDER BY o.created_at DESC
            LIMIT 20
        ";

            $orderStmt = $db->prepare($orderQuery);
            $orderStmt->execute([$productId]);
            $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'product' => $product,
                'orders' => $orders
            ]);

        } catch (Exception $e) {
            error_log("Error in getProductDetails: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function updateProductStatus()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $productId = $input['product_id'] ?? null;
            $status = $input['status'] ?? null;

            if (!$productId || !$status) {
                echo json_encode(['success' => false, 'message' => 'Product ID and status required']);
                exit;
            }

            $validStatuses = ['active', 'inactive', 'pending', 'rejected'];
            if (!in_array($status, $validStatuses)) {
                echo json_encode(['success' => false, 'message' => 'Invalid status. Must be: ' . implode(', ', $validStatuses)]);
                exit;
            }

            $db = $this->connect();

            $query = "UPDATE products SET status = :status WHERE id = :product_id";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([':status' => $status, ':product_id' => $productId]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Product status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update product status']);
            }

        } catch (Exception $e) {
            error_log("Error in updateProductStatus: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function deleteProduct()
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $productId = $input['product_id'] ?? null;

            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                exit;
            }

            $db = $this->connect();

            // Check if product has any orders
            $checkQuery = "SELECT COUNT(*) as order_count FROM order_items WHERE product_id = ?";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->execute([$productId]);
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($result['order_count'] > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Cannot delete product with existing orders. ' . $result['order_count'] . ' orders found.'
                ]);
                exit;
            }

            // Delete the product
            $query = "DELETE FROM products WHERE id = ?";
            $stmt = $db->prepare($query);
            $success = $stmt->execute([$productId]);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
            }

        } catch (Exception $e) {
            error_log("Error in deleteProduct: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Add these methods to your AdminDashboardController class

public function getAnalytics()
{
    header('Content-Type: application/json');
    
    try {
        $db = $this->connect();
        
        // Get date range from request
        $input = json_decode(file_get_contents('php://input'), true);
        $period = $input['period'] ?? 'month'; // week, month, year
        
        // Set date range based on period
        $dateCondition = "";
        switch($period) {
            case 'week':
                $dateCondition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $dateCondition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'year':
                $dateCondition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
                break;
            default:
                $dateCondition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        
        // User statistics
        $userStatsQuery = "
            SELECT 
                COUNT(*) as total_users,
                SUM(CASE WHEN role = 'farmer' THEN 1 ELSE 0 END) as total_farmers,
                SUM(CASE WHEN role = 'buyer' THEN 1 ELSE 0 END) as total_buyers,
                SUM(CASE WHEN role = 'transporter' THEN 1 ELSE 0 END) as total_transporters,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as new_today,
                SUM(CASE WHEN WEEK(created_at) = WEEK(CURDATE()) THEN 1 ELSE 0 END) as new_this_week,
                SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) THEN 1 ELSE 0 END) as new_this_month
            FROM users
        ";
        
        $userStatsStmt = $db->prepare($userStatsQuery);
        $userStatsStmt->execute();
        $userStats = $userStatsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Order statistics
        $orderStatsQuery = "
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_order_value
            FROM orders
            WHERE status != 'cancelled'
        ";
        
        $orderStatsStmt = $db->prepare($orderStatsQuery);
        $orderStatsStmt->execute();
        $orderStats = $orderStatsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Product statistics
        $productStatsQuery = "
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_approval,
                AVG(price) as avg_price,
                SUM(price * quantity) as inventory_value
            FROM products
        ";
        
        $productStatsStmt = $db->prepare($productStatsQuery);
        $productStatsStmt->execute();
        $productStats = $productStatsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Monthly revenue for chart
        $monthlyRevenueQuery = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE status = 'completed' OR status = 'delivered'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";
        
        $monthlyRevenueStmt = $db->prepare($monthlyRevenueQuery);
        $monthlyRevenueStmt->execute();
        $monthlyRevenue = $monthlyRevenueStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // User growth over time
        $userGrowthQuery = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as new_users,
                SUM(CASE WHEN role = 'farmer' THEN 1 ELSE 0 END) as new_farmers,
                SUM(CASE WHEN role = 'buyer' THEN 1 ELSE 0 END) as new_buyers
            FROM users
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";
        
        $userGrowthStmt = $db->prepare($userGrowthQuery);
        $userGrowthStmt->execute();
        $userGrowth = $userGrowthStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Top selling products
        $topProductsQuery = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.image,
                COUNT(oi.id) as total_orders,
                SUM(oi.quantity) as total_quantity_sold,
                SUM(oi.price * oi.quantity) as total_revenue
            FROM products p
            JOIN order_items oi ON p.id = oi.product_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status IN ('completed', 'delivered')
            GROUP BY p.id
            ORDER BY total_revenue DESC
            LIMIT 10
        ";
        
        $topProductsStmt = $db->prepare($topProductsQuery);
        $topProductsStmt->execute();
        $topProducts = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Category distribution
        $categoryDistributionQuery = "
            SELECT 
                category,
                COUNT(*) as product_count,
                SUM(price * quantity) as total_value
            FROM products
            WHERE category IS NOT NULL AND category != ''
            GROUP BY category
            ORDER BY product_count DESC
        ";
        
        $categoryStmt = $db->prepare($categoryDistributionQuery);
        $categoryStmt->execute();
        $categoryDistribution = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recent activities (last 10 orders and registrations)
        $recentOrdersQuery = "
            SELECT 
                'order' as type,
                o.id as id,
                o.order_number as reference,
                o.total_amount as amount,
                o.status,
                o.created_at as date,
                b.name as user_name
            FROM orders o
            JOIN users b ON o.buyer_id = b.id
            ORDER BY o.created_at DESC
            LIMIT 5
        ";
        
        $recentOrdersStmt = $db->prepare($recentOrdersQuery);
        $recentOrdersStmt->execute();
        $recentOrders = $recentOrdersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $recentUsersQuery = "
            SELECT 
                'user' as type,
                id,
                name as reference,
                role,
                created_at as date,
                verification_status
            FROM users
            ORDER BY created_at DESC
            LIMIT 5
        ";
        
        $recentUsersStmt = $db->prepare($recentUsersQuery);
        $recentUsersStmt->execute();
        $recentUsers = $recentUsersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combine recent activities
        $recentActivities = array_merge($recentOrders, $recentUsers);
        usort($recentActivities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        $recentActivities = array_slice($recentActivities, 0, 10);
        
        // Calculate growth percentages
        $previousMonthRevenue = 0;
        $currentMonthRevenue = 0;
        
        foreach ($monthlyRevenue as $mr) {
            if ($mr['month'] == date('Y-m', strtotime('-1 month'))) {
                $previousMonthRevenue = $mr['revenue'];
            }
            if ($mr['month'] == date('Y-m')) {
                $currentMonthRevenue = $mr['revenue'];
            }
        }
        
        $revenueGrowth = $previousMonthRevenue > 0 
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : 0;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'user_stats' => $userStats,
                'order_stats' => $orderStats,
                'product_stats' => $productStats,
                'monthly_revenue' => $monthlyRevenue,
                'user_growth' => $userGrowth,
                'top_products' => $topProducts,
                'category_distribution' => $categoryDistribution,
                'recent_activities' => $recentActivities,
                'growth_metrics' => [
                    'revenue_growth' => $revenueGrowth,
                    'user_growth' => $userGrowth ? round((($userStats['new_this_month'] ?? 0) / max(1, ($userStats['total_users'] - ($userStats['new_this_month'] ?? 0)))) * 100, 1) : 0
                ]
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error in getAnalytics: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function getRevenueChart()
{
    header('Content-Type: application/json');
    
    try {
        $db = $this->connect();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $year = $input['year'] ?? date('Y');
        
        $query = "
            SELECT 
                MONTH(created_at) as month,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE YEAR(created_at) = ? AND (status = 'completed' OR status = 'delivered')
            GROUP BY MONTH(created_at)
            ORDER BY month ASC
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$year]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fill in missing months
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $found = false;
            foreach ($data as $row) {
                if ((int)$row['month'] === $i) {
                    $monthlyData[] = $row;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $monthlyData[] = [
                    'month' => $i,
                    'order_count' => 0,
                    'revenue' => 0
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => $monthlyData,
            'year' => $year
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function getTopPerformers()
{
    header('Content-Type: application/json');
    
    try {
        $db = $this->connect();
        
        // Top farmers by revenue
        $topFarmersQuery = "
            SELECT 
                u.id,
                u.name,
                u.email,
                COUNT(DISTINCT o.id) as total_orders,
                SUM(oi.price * oi.quantity) as total_revenue,
                COUNT(DISTINCT p.id) as total_products
            FROM users u
            JOIN products p ON u.id = p.farmer_id
            JOIN order_items oi ON p.id = oi.product_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status IN ('completed', 'delivered')
            GROUP BY u.id
            ORDER BY total_revenue DESC
            LIMIT 10
        ";
        
        $farmerStmt = $db->prepare($topFarmersQuery);
        $farmerStmt->execute();
        $topFarmers = $farmerStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Top buyers by spending
        $topBuyersQuery = "
            SELECT 
                u.id,
                u.name,
                u.email,
                COUNT(o.id) as total_orders,
                SUM(o.total_amount) as total_spent,
                AVG(o.total_amount) as avg_order_value
            FROM users u
            JOIN orders o ON u.id = o.buyer_id
            WHERE o.status IN ('completed', 'delivered')
            GROUP BY u.id
            ORDER BY total_spent DESC
            LIMIT 10
        ";
        
        $buyerStmt = $db->prepare($topBuyersQuery);
        $buyerStmt->execute();
        $topBuyers = $buyerStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'top_farmers' => $topFarmers,
            'top_buyers' => $topBuyers
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Add these methods to your AdminDashboardController class

public function getPayments()
{
    header('Content-Type: application/json');
    
    try {
        $db = $this->connect();
        
        // Get filter parameters
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? '';
        $method = $input['method'] ?? '';
        $dateRange = $input['date_range'] ?? '';
        $search = $input['search'] ?? '';
        
        // Build the query
        $query = "
            SELECT 
                p.id as payment_id,
                p.order_id,
                p.amount,
                p.payment_method,
                p.payment_status,
                p.transaction_id,
                p.payment_date,
                p.created_at,
                o.order_number,
                u.name as buyer_name,
                u.email as buyer_email,
                f.name as farmer_name
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.buyer_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products pr ON oi.product_id = pr.id
            LEFT JOIN users f ON pr.farmer_id = f.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Apply status filter
        if (!empty($status)) {
            $query .= " AND p.payment_status = :status";
            $params[':status'] = $status;
        }
        
        // Apply payment method filter
        if (!empty($method)) {
            $query .= " AND p.payment_method = :method";
            $params[':method'] = $method;
        }
        
        // Apply date range filter
        if (!empty($dateRange)) {
            switch ($dateRange) {
                case 'today':
                    $query .= " AND DATE(p.payment_date) = CURDATE()";
                    break;
                case 'week':
                    $query .= " AND YEARWEEK(p.payment_date) = YEARWEEK(CURDATE())";
                    break;
                case 'month':
                    $query .= " AND MONTH(p.payment_date) = MONTH(CURDATE()) AND YEAR(p.payment_date) = YEAR(CURDATE())";
                    break;
                case 'quarter':
                    $query .= " AND QUARTER(p.payment_date) = QUARTER(CURDATE()) AND YEAR(p.payment_date) = YEAR(CURDATE())";
                    break;
                case 'year':
                    $query .= " AND YEAR(p.payment_date) = YEAR(CURDATE())";
                    break;
            }
        }
        
        // Apply search filter
        if (!empty($search)) {
            $query .= " AND (o.order_number LIKE :search OR u.name LIKE :search OR p.transaction_id LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        $query .= " GROUP BY p.id ORDER BY p.payment_date DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get payment statistics
        $statsQuery = "
            SELECT 
                COUNT(*) as total_transactions,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_completed_amount,
                SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as total_pending_amount,
                SUM(CASE WHEN payment_status = 'failed' THEN amount ELSE 0 END) as total_failed_amount,
                SUM(CASE WHEN payment_status = 'refunded' THEN amount ELSE 0 END) as total_refunded_amount,
                COUNT(CASE WHEN payment_status = 'completed' THEN 1 END) as completed_count,
                COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as failed_count,
                COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as refunded_count,
                SUM(amount) as total_revenue,
                AVG(amount) as avg_payment_amount,
                SUM(CASE WHEN payment_method = 'cash_on_delivery' THEN amount ELSE 0 END) as cod_revenue,
                SUM(CASE WHEN payment_method = 'bank_transfer' THEN amount ELSE 0 END) as bank_revenue,
                SUM(CASE WHEN payment_method = 'card' THEN amount ELSE 0 END) as card_revenue,
                SUM(CASE WHEN payment_method = 'mobile_payment' THEN amount ELSE 0 END) as mobile_revenue
            FROM payments
        ";
        
        $statsStmt = $db->prepare($statsQuery);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get payment method distribution
        $methodQuery = "
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            FROM payments
            WHERE payment_status = 'completed'
            GROUP BY payment_method
        ";
        
        $methodStmt = $db->prepare($methodQuery);
        $methodStmt->execute();
        $methodDistribution = $methodStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get monthly payment trends
        $trendsQuery = "
            SELECT 
                DATE_FORMAT(payment_date, '%Y-%m') as month,
                COUNT(*) as payment_count,
                SUM(amount) as total_amount,
                AVG(amount) as avg_amount
            FROM payments
            WHERE payment_status = 'completed'
            AND payment_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
            ORDER BY month ASC
        ";
        
        $trendsStmt = $db->prepare($trendsQuery);
        $trendsStmt->execute();
        $trends = $trendsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate platform commission (assuming 5% commission)
        $totalCommission = ($stats['total_completed_amount'] ?? 0) * 0.05;
        
        echo json_encode([
            'success' => true,
            'data' => $payments,
            'stats' => [
                'total_transactions' => (int)($stats['total_transactions'] ?? 0),
                'total_revenue' => round($stats['total_revenue'] ?? 0, 2),
                'total_completed_amount' => round($stats['total_completed_amount'] ?? 0, 2),
                'total_pending_amount' => round($stats['total_pending_amount'] ?? 0, 2),
                'total_failed_amount' => round($stats['total_failed_amount'] ?? 0, 2),
                'total_refunded_amount' => round($stats['total_refunded_amount'] ?? 0, 2),
                'completed_count' => (int)($stats['completed_count'] ?? 0),
                'pending_count' => (int)($stats['pending_count'] ?? 0),
                'failed_count' => (int)($stats['failed_count'] ?? 0),
                'refunded_count' => (int)($stats['refunded_count'] ?? 0),
                'avg_payment_amount' => round($stats['avg_payment_amount'] ?? 0, 2),
                'platform_commission' => round($totalCommission, 2),
                'cod_revenue' => round($stats['cod_revenue'] ?? 0, 2),
                'bank_revenue' => round($stats['bank_revenue'] ?? 0, 2),
                'card_revenue' => round($stats['card_revenue'] ?? 0, 2),
                'mobile_revenue' => round($stats['mobile_revenue'] ?? 0, 2)
            ],
            'method_distribution' => $methodDistribution,
            'trends' => $trends
        ]);
        
    } catch (Exception $e) {
        error_log("Error in getPayments: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function getPaymentDetails($paymentId = null)
{
    header('Content-Type: application/json');
    
    try {
        if (!$paymentId) {
            $input = json_decode(file_get_contents('php://input'), true);
            $paymentId = $input['payment_id'] ?? null;
        }
        
        if (!$paymentId) {
            echo json_encode(['success' => false, 'message' => 'Payment ID required']);
            exit;
        }
        
        $db = $this->connect();
        
        $query = "
            SELECT 
                p.*,
                o.order_number,
                o.status as order_status,
                u.name as buyer_name,
                u.email as buyer_email,
                u.phone as buyer_phone,
                u.address as buyer_address
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.buyer_id = u.id
            WHERE p.id = ?
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$paymentId]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$payment) {
            echo json_encode(['success' => false, 'message' => 'Payment not found']);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'payment' => $payment
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function updatePaymentStatus()
{
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $paymentId = $input['payment_id'] ?? null;
        $status = $input['status'] ?? null;
        
        if (!$paymentId || !$status) {
            echo json_encode(['success' => false, 'message' => 'Payment ID and status required']);
            exit;
        }
        
        $validStatuses = ['pending', 'completed', 'failed', 'refunded'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        $db = $this->connect();
        
        $query = "UPDATE payments SET payment_status = :status WHERE id = :payment_id";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([':status' => $status, ':payment_id' => $paymentId]);
        
        if ($result) {
            // If payment is completed, update order payment status
            if ($status === 'completed') {
                $getOrderQuery = "SELECT order_id FROM payments WHERE id = ?";
                $orderStmt = $db->prepare($getOrderQuery);
                $orderStmt->execute([$paymentId]);
                $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($order) {
                    $updateOrderQuery = "UPDATE orders SET payment_status = 'paid' WHERE id = ?";
                    $updateStmt = $db->prepare($updateOrderQuery);
                    $updateStmt->execute([$order['order_id']]);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Payment status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update payment status']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function refundPayment()
{
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $paymentId = $input['payment_id'] ?? null;
        $reason = $input['reason'] ?? '';
        
        if (!$paymentId) {
            echo json_encode(['success' => false, 'message' => 'Payment ID required']);
            exit;
        }
        
        $db = $this->connect();
        
        // Update payment status to refunded
        $query = "UPDATE payments SET payment_status = 'refunded', refund_reason = :reason, refund_date = NOW() WHERE id = :payment_id";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([':reason' => $reason, ':payment_id' => $paymentId]);
        
        if ($result) {
            // Update order payment status
            $getOrderQuery = "SELECT order_id FROM payments WHERE id = ?";
            $orderStmt = $db->prepare($getOrderQuery);
            $orderStmt->execute([$paymentId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                $updateOrderQuery = "UPDATE orders SET payment_status = 'refunded' WHERE id = ?";
                $updateStmt = $db->prepare($updateOrderQuery);
                $updateStmt->execute([$order['order_id']]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Payment refunded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to refund payment']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Add these methods to your AdminDashboardController class

public function getDisputes()
{
    header('Content-Type: application/json');
    
    try {
        $db = $this->connect();
        
        // Get filter parameters
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? '';
        $type = $input['type'] ?? '';
        $priority = $input['priority'] ?? '';
        $search = $input['search'] ?? '';
        
        // Build the query
        $query = "
            SELECT 
                d.id as dispute_id,
                d.order_id,
                d.complainant_id,
                d.respondent_id,
                d.type,
                d.reason,
                d.status,
                d.priority,
                d.resolution_notes,
                d.created_at,
                d.updated_at,
                d.resolved_at,
                o.order_number,
                o.total_amount as order_amount,
                c.name as complainant_name,
                c.email as complainant_email,
                c.role as complainant_role,
                r.name as respondent_name,
                r.email as respondent_email,
                r.role as respondent_role
            FROM disputes d
            JOIN orders o ON d.order_id = o.id
            JOIN users c ON d.complainant_id = c.id
            JOIN users r ON d.respondent_id = r.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Apply status filter
        if (!empty($status)) {
            $query .= " AND d.status = :status";
            $params[':status'] = $status;
        }
        
        // Apply type filter
        if (!empty($type)) {
            $query .= " AND d.type = :type";
            $params[':type'] = $type;
        }
        
        // Apply priority filter
        if (!empty($priority)) {
            $query .= " AND d.priority = :priority";
            $params[':priority'] = $priority;
        }
        
        // Apply search filter
        if (!empty($search)) {
            $query .= " AND (o.order_number LIKE :search OR c.name LIKE :search OR r.name LIKE :search OR d.reason LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        $query .= " ORDER BY 
            CASE WHEN d.priority = 'high' THEN 1 
                 WHEN d.priority = 'medium' THEN 2 
                 WHEN d.priority = 'low' THEN 3 
                 ELSE 4 END ASC,
            d.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $disputes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get dispute statistics
        $statsQuery = "
            SELECT 
                COUNT(*) as total_disputes,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_disputes,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_disputes,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_disputes,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_disputes,
                SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_priority,
                SUM(CASE WHEN priority = 'medium' THEN 1 ELSE 0 END) as medium_priority,
                SUM(CASE WHEN priority = 'low' THEN 1 ELSE 0 END) as low_priority,
                AVG(CASE WHEN resolved_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) 
                    ELSE NULL END) as avg_resolution_hours,
                SUM(CASE WHEN type = 'order_issue' THEN 1 ELSE 0 END) as order_issues,
                SUM(CASE WHEN type = 'payment_issue' THEN 1 ELSE 0 END) as payment_issues,
                SUM(CASE WHEN type = 'delivery_issue' THEN 1 ELSE 0 END) as delivery_issues,
                SUM(CASE WHEN type = 'product_quality' THEN 1 ELSE 0 END) as quality_issues
            FROM disputes
        ";
        
        $statsStmt = $db->prepare($statsQuery);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Get dispute type distribution
        $typeQuery = "
            SELECT 
                type,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count
            FROM disputes
            GROUP BY type
        ";
        
        $typeStmt = $db->prepare($typeQuery);
        $typeStmt->execute();
        $typeDistribution = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $disputes,
            'stats' => [
                'total_disputes' => (int)($stats['total_disputes'] ?? 0),
                'open_disputes' => (int)($stats['open_disputes'] ?? 0),
                'in_progress_disputes' => (int)($stats['in_progress_disputes'] ?? 0),
                'resolved_disputes' => (int)($stats['resolved_disputes'] ?? 0),
                'closed_disputes' => (int)($stats['closed_disputes'] ?? 0),
                'high_priority' => (int)($stats['high_priority'] ?? 0),
                'medium_priority' => (int)($stats['medium_priority'] ?? 0),
                'low_priority' => (int)($stats['low_priority'] ?? 0),
                'avg_resolution_hours' => round($stats['avg_resolution_hours'] ?? 0, 1),
                'order_issues' => (int)($stats['order_issues'] ?? 0),
                'payment_issues' => (int)($stats['payment_issues'] ?? 0),
                'delivery_issues' => (int)($stats['delivery_issues'] ?? 0),
                'quality_issues' => (int)($stats['quality_issues'] ?? 0)
            ],
            'type_distribution' => $typeDistribution
        ]);
        
    } catch (Exception $e) {
        error_log("Error in getDisputes: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function getDisputeDetails($disputeId = null)
{
    header('Content-Type: application/json');
    
    try {
        if (!$disputeId) {
            $input = json_decode(file_get_contents('php://input'), true);
            $disputeId = $input['dispute_id'] ?? null;
        }
        
        if (!$disputeId) {
            echo json_encode(['success' => false, 'message' => 'Dispute ID required']);
            exit;
        }
        
        $db = $this->connect();
        
        $query = "
            SELECT 
                d.*,
                o.order_number,
                o.total_amount as order_amount,
                o.status as order_status,
                c.name as complainant_name,
                c.email as complainant_email,
                c.phone as complainant_phone,
                c.role as complainant_role,
                r.name as respondent_name,
                r.email as respondent_email,
                r.phone as respondent_phone,
                r.role as respondent_role
            FROM disputes d
            JOIN orders o ON d.order_id = o.id
            JOIN users c ON d.complainant_id = c.id
            JOIN users r ON d.respondent_id = r.id
            WHERE d.id = ?
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$disputeId]);
        $dispute = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$dispute) {
            echo json_encode(['success' => false, 'message' => 'Dispute not found']);
            exit;
        }
        
        // Get messages/conversation for this dispute
        $messagesQuery = "
            SELECT 
                dm.*,
                u.name as sender_name,
                u.role as sender_role
            FROM dispute_messages dm
            JOIN users u ON dm.sender_id = u.id
            WHERE dm.dispute_id = ?
            ORDER BY dm.created_at ASC
        ";
        
        $messagesStmt = $db->prepare($messagesQuery);
        $messagesStmt->execute([$disputeId]);
        $messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'dispute' => $dispute,
            'messages' => $messages
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function updateDisputeStatus()
{
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $disputeId = $input['dispute_id'] ?? null;
        $status = $input['status'] ?? null;
        $resolutionNotes = $input['resolution_notes'] ?? null;
        
        if (!$disputeId || !$status) {
            echo json_encode(['success' => false, 'message' => 'Dispute ID and status required']);
            exit;
        }
        
        $validStatuses = ['open', 'in_progress', 'resolved', 'closed'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        $db = $this->connect();
        
        $query = "UPDATE disputes SET status = :status";
        $params = [':status' => $status, ':id' => $disputeId];
        
        if ($status === 'resolved' || $status === 'closed') {
            $query .= ", resolved_at = NOW()";
        }
        
        if ($resolutionNotes) {
            $query .= ", resolution_notes = :resolution_notes";
            $params[':resolution_notes'] = $resolutionNotes;
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute($params);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Dispute status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update dispute status']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function addDisputeMessage()
{
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $disputeId = $input['dispute_id'] ?? null;
        $message = $input['message'] ?? null;
        $senderId = $input['sender_id'] ?? ($_SESSION['USER']->id ?? null);
        
        if (!$disputeId || !$message || !$senderId) {
            echo json_encode(['success' => false, 'message' => 'Dispute ID, message, and sender ID required']);
            exit;
        }
        
        $db = $this->connect();
        
        $query = "INSERT INTO dispute_messages (dispute_id, sender_id, message, created_at) VALUES (:dispute_id, :sender_id, :message, NOW())";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':dispute_id' => $disputeId,
            ':sender_id' => $senderId,
            ':message' => $message
        ]);
        
        if ($result) {
            // Update dispute updated_at timestamp
            $updateQuery = "UPDATE disputes SET updated_at = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$disputeId]);
            
            echo json_encode(['success' => true, 'message' => 'Message added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add message']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

public function resolveDispute()
{
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $disputeId = $input['dispute_id'] ?? null;
        $resolution = $input['resolution'] ?? null;
        
        if (!$disputeId) {
            echo json_encode(['success' => false, 'message' => 'Dispute ID required']);
            exit;
        }
        
        $db = $this->connect();
        
        $query = "UPDATE disputes SET status = 'resolved', resolution_notes = :resolution, resolved_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':resolution' => $resolution,
            ':id' => $disputeId
        ]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Dispute resolved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to resolve dispute']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

}