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
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Server error: ' . $e->getMessage(),
                    ]);
                }
            }
            exit;
        }

        public function updateUserCount()
        {
            // Ensure clean output - no whitespace before this
            header('Content-Type: application/json');
            header('X-Content-Type-Options: nosniff');

            try {
                // Check if user is logged in and is admin
                if (!isset($_SESSION['USER'])) {
                    echo json_encode([
                        'success' => false,
                        'userCount' => 0,
                        'message' => 'Not authenticated'
                    ]);
                    exit;
                }

                $user = new UserModel();
                $users = $user->findAll();

                if ($users === false) {
                    $users = [];
                }

                $userCount = is_array($users) ? count($users) : 0;

                echo json_encode([
                    'success' => true,
                    'userCount' => $userCount,
                    'message' => 'User count retrieved successfully'
                ]);
            } catch (Exception $e) {
                error_log("Error in updateUserCount: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'userCount' => 0,
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
            exit;
        }

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
        }

        /* public function getUser($id)
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
        }  */

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
                    SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
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
                $period = $input['period'] ?? 'month';

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
                    SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) as active_products,
                    SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                    0 as pending_approval,
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
                WHERE (status = 'completed' OR status = 'delivered')
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
                    SUM(p.price * oi.quantity) as total_revenue
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

                // FIXED: Recent activities - removed order_number reference
                $recentOrdersQuery = "
                SELECT 
                    'order' as type,
                    o.id as id,
                    CONCAT('ORD-', o.id) as reference,
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
                usort($recentActivities, function ($a, $b) {
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

                // Build the query - MATCHING YOUR ORDERS TABLE STRUCTURE
                $query = "
                SELECT 
                    o.id as payment_id,
                    o.id as order_id,
                    o.order_total as amount,
                    o.payment_method,
                    o.status as payment_status,
                    CONCAT('TXN-', o.id, '-', DATE_FORMAT(o.created_at, '%Y%m%d')) as transaction_id,
                    o.created_at as payment_date,
                    o.created_at,
                    o.order_total,
                    o.shipping_cost,
                    o.total_weight_kg,
                    u.name as buyer_name,
                    u.email as buyer_email,
                    o.delivery_address,
                    o.delivery_city,
                    o.delivery_phone,
                    o.status as order_status
                FROM orders o
                JOIN users u ON o.buyer_id = u.id
                WHERE 1=1
            ";

                $params = [];

                // Apply status filter (using order status as payment status indicator)
                if (!empty($status)) {
                    $query .= " AND o.status = :status";
                    $params[':status'] = $status;
                }

                // Apply payment method filter
                if (!empty($method)) {
                    $query .= " AND o.payment_method = :method";
                    $params[':method'] = $method;
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
                        case 'year':
                            $query .= " AND YEAR(o.created_at) = YEAR(CURDATE())";
                            break;
                    }
                }

                // Apply search filter
                if (!empty($search)) {
                    $query .= " AND (o.id LIKE :search OR u.name LIKE :search OR CONCAT('TXN-', o.id) LIKE :search OR o.delivery_city LIKE :search)";
                    $params[':search'] = "%{$search}%";
                }

                $query .= " ORDER BY o.created_at DESC";

                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Get payment statistics from orders table
                $statsQuery = "
                SELECT 
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = 'delivered' OR status = 'completed' THEN order_total ELSE 0 END) as total_completed_amount,
                    SUM(CASE WHEN status = 'pending' THEN order_total ELSE 0 END) as total_pending_amount,
                    SUM(CASE WHEN status = 'cancelled' THEN order_total ELSE 0 END) as total_cancelled_amount,
                    SUM(CASE WHEN status = 'shipped' THEN order_total ELSE 0 END) as total_shipped_amount,
                    COUNT(CASE WHEN status = 'delivered' OR status = 'completed' THEN 1 END) as completed_count,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count,
                    COUNT(CASE WHEN status = 'shipped' THEN 1 END) as shipped_count,
                    SUM(order_total) as total_revenue,
                    AVG(order_total) as avg_payment_amount,
                    SUM(CASE WHEN payment_method = 'cash_on_delivery' THEN order_total ELSE 0 END) as cod_revenue,
                    SUM(CASE WHEN payment_method = 'bank_transfer' THEN order_total ELSE 0 END) as bank_revenue,
                    SUM(CASE WHEN payment_method = 'card' THEN order_total ELSE 0 END) as card_revenue,
                    SUM(CASE WHEN payment_method = 'mobile_payment' THEN order_total ELSE 0 END) as mobile_revenue,
                    SUM(shipping_cost) as total_shipping_revenue,
                    AVG(total_weight_kg) as avg_weight
                FROM orders
            ";

                $statsStmt = $db->prepare($statsQuery);
                $statsStmt->execute();
                $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

                // Get payment method distribution from orders
                $methodQuery = "
                SELECT 
                    payment_method,
                    COUNT(*) as count,
                    SUM(order_total) as total_amount
                FROM orders
                WHERE status IN ('delivered', 'completed', 'shipped')
                GROUP BY payment_method
            ";

                $methodStmt = $db->prepare($methodQuery);
                $methodStmt->execute();
                $methodDistribution = $methodStmt->fetchAll(PDO::FETCH_ASSOC);

                // Get monthly payment trends from orders
                $trendsQuery = "
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as payment_count,
                    SUM(order_total) as total_amount,
                    AVG(order_total) as avg_amount
                FROM orders
                WHERE status IN ('delivered', 'completed')
                AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
            ";

                $trendsStmt = $db->prepare($trendsQuery);
                $trendsStmt->execute();
                $trends = $trendsStmt->fetchAll(PDO::FETCH_ASSOC);

                // Calculate platform commission (assuming 5% commission on completed orders)
                $totalCommission = ($stats['total_completed_amount'] ?? 0) * 0.05;

                echo json_encode([
                    'success' => true,
                    'data' => $payments,
                    'stats' => [
                        'total_transactions' => (int)($stats['total_transactions'] ?? 0),
                        'total_revenue' => round($stats['total_revenue'] ?? 0, 2),
                        'total_completed_amount' => round($stats['total_completed_amount'] ?? 0, 2),
                        'total_pending_amount' => round($stats['total_pending_amount'] ?? 0, 2),
                        'total_cancelled_amount' => round($stats['total_cancelled_amount'] ?? 0, 2),
                        'total_shipped_amount' => round($stats['total_shipped_amount'] ?? 0, 2),
                        'completed_count' => (int)($stats['completed_count'] ?? 0),
                        'pending_count' => (int)($stats['pending_count'] ?? 0),
                        'cancelled_count' => (int)($stats['cancelled_count'] ?? 0),
                        'shipped_count' => (int)($stats['shipped_count'] ?? 0),
                        'avg_payment_amount' => round($stats['avg_payment_amount'] ?? 0, 2),
                        'platform_commission' => round($totalCommission, 2),
                        'cod_revenue' => round($stats['cod_revenue'] ?? 0, 2),
                        'bank_revenue' => round($stats['bank_revenue'] ?? 0, 2),
                        'card_revenue' => round($stats['card_revenue'] ?? 0, 2),
                        'mobile_revenue' => round($stats['mobile_revenue'] ?? 0, 2),
                        'total_shipping_revenue' => round($stats['total_shipping_revenue'] ?? 0, 2),
                        'avg_weight' => round($stats['avg_weight'] ?? 0, 2)
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

                // Get payment details from orders table
                $query = "
                SELECT 
                    o.id as payment_id,
                    o.id as order_id,
                    o.order_total as amount,
                    o.shipping_cost,
                    o.total_weight_kg,
                    o.payment_method,
                    o.status as payment_status,
                    o.created_at as payment_date,
                    CONCAT('TXN-', o.id, '-', DATE_FORMAT(o.created_at, '%Y%m%d')) as transaction_id,
                    u.name as buyer_name,
                    u.email as buyer_email,
                    u.phone as buyer_phone,
                    o.delivery_address,
                    o.delivery_city,
                    o.delivery_district_id,
                    o.delivery_town_id,
                    o.delivery_phone,
                    o.status as order_status,
                    NULL as refund_reason,
                    NULL as refund_date
                FROM orders o
                JOIN users u ON o.buyer_id = u.id
                WHERE o.id = ?
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

                // Valid order statuses based on your table
                $validStatuses = ['pending', 'shipped', 'delivered', 'cancelled'];

                if (!in_array($status, $validStatuses)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid status. Must be: pending, shipped, delivered, cancelled']);
                    exit;
                }

                $db = $this->connect();

                // Update status in orders table
                $query = "UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :order_id";
                $stmt = $db->prepare($query);
                $result = $stmt->execute([':status' => $status, ':order_id' => $paymentId]);

                if ($result) {
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

                // Update order status to cancelled (as a form of refund)
                $query = "UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = :order_id";
                $stmt = $db->prepare($query);
                $result = $stmt->execute([':order_id' => $paymentId]);

                if ($result) {
                    // You might want to log the refund reason in a separate table
                    echo json_encode(['success' => true, 'message' => 'Payment refunded successfully (order cancelled)']);
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
                    o.id as dispute_id,
                    o.id as order_id,
                    o.buyer_id as complainant_id,
                    NULL as respondent_id,
                    'cancelled_order' as type,
                    'Order cancelled - payment revision needed' as reason,
                    'open' as status,
                    'medium' as priority,
                    NULL as resolution_notes,
                    o.created_at,
                    o.updated_at,
                    NULL as resolved_at,
                    CONCAT('ORD-', o.id) as order_number,
                    o.total_amount as order_amount,
                    b.name as complainant_name,
                    b.email as complainant_email,
                    b.role as complainant_role,
                    'System' as respondent_name,
                    '' as respondent_email,
                    'admin' as respondent_role
                FROM orders o
                JOIN users b ON o.buyer_id = b.id
                WHERE o.status = 'cancelled'
            ";

                $params = [];

                // Apply status filter (for cancelled orders, status is always 'open' for disputes)
                if (!empty($status) && $status !== 'open') {
                    // If filtering for other statuses, no results since all are 'open'
                    $query .= " AND 1=0";
                }

                // Apply type filter
                if (!empty($type) && $type !== 'cancelled_order') {
                    $query .= " AND 1=0"; // No other types
                }

                // Apply priority filter
                if (!empty($priority) && $priority !== 'medium') {
                    $query .= " AND 1=0"; // All are medium
                }

                // Apply search filter
                if (!empty($search)) {
                    $query .= " AND (CONCAT('ORD-', o.id) LIKE :search OR b.name LIKE :search OR 'System' LIKE :search OR 'Order cancelled - payment revision needed' LIKE :search)";
                    $params[':search'] = "%{$search}%";
                }

                $query .= " ORDER BY o.created_at DESC";

                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $disputes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Get dispute statistics
                $statsQuery = "
                SELECT 
                    COUNT(*) as total_disputes,
                    COUNT(*) as open_disputes,
                    0 as in_progress_disputes,
                    0 as resolved_disputes,
                    0 as closed_disputes,
                    0 as high_priority,
                    COUNT(*) as medium_priority,
                    0 as low_priority,
                    NULL as avg_resolution_hours,
                    0 as order_issues,
                    COUNT(*) as payment_issues,
                    0 as delivery_issues,
                    0 as quality_issues
                FROM orders
                WHERE status = 'cancelled'
            ";

                $statsStmt = $db->prepare($statsQuery);
                $statsStmt->execute();
                $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

                // Get dispute type distribution
                $typeQuery = "
                SELECT 
                    'cancelled_order' as type,
                    COUNT(*) as count,
                    COUNT(*) as open_count,
                    0 as resolved_count
                FROM orders
                WHERE status = 'cancelled'
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
                    o.id as id,
                    o.id as order_id,
                    'cancelled_order' as type,
                    'Order cancelled - payment revision needed' as reason,
                    'open' as status,
                    'medium' as priority,
                    NULL as resolution_notes,
                    o.created_at,
                    o.updated_at,
                    NULL as resolved_at,
                    CONCAT('ORD-', o.id) as order_number,
                    o.total_amount as order_amount,
                    o.status as order_status,
                    b.name as complainant_name,
                    b.email as complainant_email,
                    b.phone as complainant_phone,
                    b.role as complainant_role,
                    'System' as respondent_name,
                    '' as respondent_email,
                    '' as respondent_phone,
                    'admin' as respondent_role
                FROM orders o
                JOIN users b ON o.buyer_id = b.id
                WHERE o.id = ? AND o.status = 'cancelled'
            ";

                $stmt = $db->prepare($query);
                $stmt->execute([$disputeId]);
                $dispute = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$dispute) {
                    echo json_encode(['success' => false, 'message' => 'Dispute not found']);
                    exit;
                }

                // For cancelled orders, no dispute messages yet
                $messages = [];

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

                // For cancelled orders as disputes, if resolving, perform refund
                if ($status === 'resolved') {
                    // Call refund payment
                    $refundInput = ['payment_id' => $disputeId, 'reason' => $resolutionNotes ?? 'Dispute resolved'];
                    // Simulate the refund process
                    $query = "UPDATE orders SET status = 'refunded', updated_at = NOW() WHERE id = :order_id";
                    $stmt = $db->prepare($query);
                    $result = $stmt->execute([':order_id' => $disputeId]);

                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Dispute resolved and payment refunded successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to refund payment']);
                    }
                } else {
                    // For other statuses, just acknowledge (since no table to update)
                    echo json_encode(['success' => true, 'message' => 'Dispute status updated successfully']);
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

                // For cancelled orders, messages are not stored yet
                echo json_encode(['success' => true, 'message' => 'Message noted (not stored)']);
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

                // For cancelled orders, resolving means refunding
                $query = "UPDATE orders SET status = 'refunded', updated_at = NOW() WHERE id = :id AND status = 'cancelled'";
                $stmt = $db->prepare($query);
                $result = $stmt->execute([
                    ':id' => $disputeId
                ]);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Dispute resolved and payment refunded successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to resolve dispute']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        public function sendNotification()
        {
            if (ob_get_level()) {
                ob_clean();
            }

            header('Content-Type: application/json');

            if (!requireRole('admin', ['json' => true, 'message' => 'Admin access required'])) {
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $title = trim((string)($_POST['title'] ?? ''));
            $message = trim((string)($_POST['message'] ?? ''));
            $recipient = strtolower(trim((string)($_POST['recipient'] ?? '')));
            $type = strtolower(trim((string)($_POST['type'] ?? 'system')));
            $selectedUserIds = $_POST['user_ids'] ?? [];

            if ($title === '' || $message === '' || $recipient === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Title, message and recipient are required']);
                exit;
            }

            $allowedTypes = ['system', 'maintenance', 'promotion', 'alert'];
            if (!in_array($type, $allowedTypes, true)) {
                $type = 'system';
            }

            if ($type !== 'system') {
                $title = strtoupper(substr($type, 0, 1)) . substr($type, 1) . ': ' . $title;
            }

            $userIds = $this->resolveNotificationRecipientUserIds($recipient, $selectedUserIds);
            if (empty($userIds)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'No matching recipients found']);
                exit;
            }

            $notifications = new NotificationsModel();
            $eventKey = 'admin_' . date('YmdHis') . '_' . bin2hex(random_bytes(6));
            $result = $notifications->broadcast($userIds, [
                'event_key' => $eventKey,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'link' => null,
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Notification sent',
                'sent' => (int)($result['sent'] ?? 0),
                'failed' => (int)($result['failed'] ?? 0),
            ]);
            exit;
        }

        public function getNotificationStats()
        {
            if (ob_get_level()) {
                ob_clean();
            }

            header('Content-Type: application/json');

            if (!requireRole('admin', ['json' => true, 'message' => 'Admin access required'])) {
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            try {
                $notifications = new NotificationsModel();
                $stats = $notifications->get_row(
                    'SELECT COUNT(*) AS total_sent, SUM(is_read) AS read_count FROM notifications',
                    []
                );

                $total = (int)($stats->total_sent ?? 0);
                $read = (int)($stats->read_count ?? 0);
                $openRate = $total > 0 ? (int)round(($read / $total) * 100) : 0;

                echo json_encode([
                    'success' => true,
                    'total_sent' => $total,
                    'delivered' => $total,
                    'open_rate' => $openRate,
                    'click_rate' => 0,
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }

        private function resolveNotificationRecipientUserIds(string $recipient, $selectedUserIds = []): array
        {
            $userModel = new UserModel();

            $recipient = strtolower(trim($recipient));
            $roleMap = [
                'farmers' => 'farmer',
                'buyers' => 'buyer',
                'transporters' => 'transporter',
                'admins' => 'admin',
            ];

            if ($recipient === 'selected') {
                $ids = is_array($selectedUserIds) ? $selectedUserIds : [$selectedUserIds];
                $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($id) => $id > 0)));
                if (empty($ids)) {
                    return [];
                }

                $placeholders = [];
                $params = [];
                foreach ($ids as $index => $id) {
                    $key = 'id_' . $index;
                    $placeholders[] = ':' . $key;
                    $params[$key] = $id;
                }

                $rows = $userModel->query(
                    "SELECT id FROM users WHERE id IN (" . implode(',', $placeholders) . ")",
                    $params
                );

                if (!is_array($rows)) {
                    return [];
                }

                return array_values(array_unique(array_map(fn($row) => (int)($row->id ?? 0), $rows)));
            }

            if ($recipient === 'all') {
                $rows = $userModel->query("SELECT id FROM users", []);
                if (!is_array($rows)) {
                    return [];
                }
                return array_values(array_unique(array_map(fn($row) => (int)($row->id ?? 0), $rows)));
            }

            if (isset($roleMap[$recipient])) {
                $role = $roleMap[$recipient];
                $rows = $userModel->query("SELECT id FROM users WHERE role = :role", ['role' => $role]);
                if (!is_array($rows)) {
                    return [];
                }
                return array_values(array_unique(array_map(fn($row) => (int)($row->id ?? 0), $rows)));
            }

            return [];
        }

        public function getCancelledOrdersDisputes()
        {
            if (ob_get_level()) {
                ob_clean();
            }

            header('Content-Type: application/json');

            if (!requireRole('admin', ['json' => true, 'message' => 'Admin access required'])) {
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $search = trim((string)($input['search'] ?? ''));
            $revisionStatus = strtolower(trim((string)($input['revision_status'] ?? ''))); // revised|unrevised|all
            $paymentMethod = strtolower(trim((string)($input['payment_method'] ?? '')));

            try {
                $db = $this->connect();

                $where = ["o.status = 'cancelled'"];
                $params = [];

                if ($search !== '') {
                    $where[] = "(CAST(o.id AS CHAR) LIKE :search OR b.name LIKE :search OR b.email LIKE :search)";
                    $params[':search'] = '%' . $search . '%';
                }

                if ($paymentMethod !== '') {
                    $where[] = "LOWER(COALESCE(o.payment_method, '')) = :payment_method";
                    $params[':payment_method'] = $paymentMethod;
                }

                if ($revisionStatus === 'revised') {
                    $where[] = "o.revised_at IS NOT NULL";
                } elseif ($revisionStatus === 'unrevised') {
                    $where[] = "o.revised_at IS NULL";
                }

                $whereSql = implode(' AND ', $where);

                $sql = "
                SELECT
                    o.id AS order_id,
                    o.buyer_id,
                    b.name AS buyer_name,
                    b.email AS buyer_email,
                    o.total_amount,
                    o.shipping_cost,
                    o.order_total,
                    o.payment_method,
                    o.delivery_city,
                    o.created_at,
                    o.updated_at,
                    o.id AS revision_id,
                    o.total_amount AS revised_total_amount,
                    o.shipping_cost AS revised_shipping_cost,
                    o.order_total AS revised_order_total,
                    o.revision_reason,
                    o.revised_at,
                    admin_u.name AS revised_by_name
                FROM orders o
                JOIN users b ON b.id = o.buyer_id
                LEFT JOIN users admin_u ON admin_u.id = o.revised_by_admin_id
                WHERE {$whereSql}
                ORDER BY o.updated_at DESC, o.id DESC
                LIMIT 200
            ";

                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

                $total = count($rows);
                $revised = 0;
                foreach ($rows as $r) {
                    if (!empty($r['revised_at'])) {
                        $revised++;
                    }
                }
                $unrevised = $total - $revised;

                echo json_encode([
                    'success' => true,
                    'data' => $rows,
                    'stats' => [
                        'total_cancelled' => $total,
                        'revised' => $revised,
                        'unrevised' => $unrevised,
                    ],
                ]);
                exit;
            } catch (Exception $e) {
                error_log("Error in getCancelledOrdersDisputes: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        public function getCancelledOrderDisputeDetails()
        {
            if (ob_get_level()) {
                ob_clean();
            }

            header('Content-Type: application/json');

            if (!requireRole('admin', ['json' => true, 'message' => 'Admin access required'])) {
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $orderId = (int)($input['order_id'] ?? 0);

            if ($orderId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Order ID required']);
                exit;
            }

            try {
                $db = $this->connect();

                $orderStmt = $db->prepare("
                SELECT
                    o.*,
                    b.name AS buyer_name,
                    b.email AS buyer_email
                FROM orders o
                JOIN users b ON b.id = o.buyer_id
                WHERE o.id = ?
                LIMIT 1
            ");
                $orderStmt->execute([$orderId]);
                $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

                if (!$order || ($order['status'] ?? '') !== 'cancelled') {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Cancelled order not found']);
                    exit;
                }

                $itemsStmt = $db->prepare("
                SELECT
                    oi.*,
                    u.name AS farmer_name
                FROM order_items oi
                LEFT JOIN users u ON u.id = oi.farmer_id
                WHERE oi.order_id = ?
                ORDER BY oi.id ASC
            ");
                $itemsStmt->execute([$orderId]);
                $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

                // Build revision data from order columns
                $revision = null;
                if (!empty($order['revised_at'])) {
                    $revision = [
                        'original_total_amount' => $order['original_total_amount'],
                        'original_shipping_cost' => $order['original_shipping_cost'],
                        'original_order_total' => $order['original_order_total'],
                        'revised_total_amount' => $order['total_amount'],
                        'revised_shipping_cost' => $order['shipping_cost'],
                        'revised_order_total' => $order['order_total'],
                        'reason' => $order['revision_reason'],
                        'revised_at' => $order['revised_at'],
                        'revised_by_admin_id' => $order['revised_by_admin_id']
                    ];
                }

                echo json_encode([
                    'success' => true,
                    'order' => $order,
                    'items' => $items,
                    'revision' => $revision,
                ]);
                exit;
            } catch (Exception $e) {
                error_log("Error in getCancelledOrderDisputeDetails: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        public function reviseCancelledOrderPayment()
        {
            if (ob_get_level()) {
                ob_clean();
            }

            header('Content-Type: application/json');

            if (!requireRole('admin', ['json' => true, 'message' => 'Admin access required'])) {
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $orderId = (int)($input['order_id'] ?? 0);
            $revisedTotal = (float)($input['revised_total_amount'] ?? 0);
            $revisedShipping = (float)($input['revised_shipping_cost'] ?? 0);
            $reason = trim((string)($input['reason'] ?? ''));

            if ($orderId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Order ID required']);
                exit;
            }

            if ($reason === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Reason is required']);
                exit;
            }

            if ($revisedTotal < 0 || $revisedShipping < 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Amounts cannot be negative']);
                exit;
            }

            try {
                $db = $this->connect();

                $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
                $stmt->execute([$orderId]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$order || ($order['status'] ?? '') !== 'cancelled') {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Cancelled order not found']);
                    exit;
                }

                // Store original amounts if not already stored
                $originalTotal = (float)($order['original_total_amount'] ?? $order['total_amount'] ?? 0);
                $originalShipping = (float)($order['original_shipping_cost'] ?? $order['shipping_cost'] ?? 0);
                $originalOrderTotal = (float)($order['original_order_total'] ?? $order['order_total'] ?? ($originalTotal + $originalShipping));

                $revisedOrderTotal = round($revisedTotal + $revisedShipping, 2);

                $adminId = (int)(authUserId() ?? 0);
                if ($adminId <= 0 && !empty($_SESSION['USER']->id)) {
                    $adminId = (int)$_SESSION['USER']->id;
                }

                $db->beginTransaction();

                $update = $db->prepare("
                UPDATE orders
                SET total_amount = :total_amount,
                    shipping_cost = :shipping_cost,
                    order_total = :order_total,
                    original_total_amount = :original_total,
                    original_shipping_cost = :original_shipping,
                    original_order_total = :original_order_total,
                    revision_reason = :reason,
                    revised_at = NOW(),
                    revised_by_admin_id = :admin_id,
                    updated_at = NOW()
                WHERE id = :order_id
                LIMIT 1
            ");

                $update->execute([
                    ':total_amount' => $revisedTotal,
                    ':shipping_cost' => $revisedShipping,
                    ':order_total' => $revisedOrderTotal,
                    ':original_total' => $originalTotal,
                    ':original_shipping' => $originalShipping,
                    ':original_order_total' => $originalOrderTotal,
                    ':reason' => substr($reason, 0, 500),
                    ':admin_id' => $adminId > 0 ? $adminId : null,
                    ':order_id' => $orderId,
                ]);

                $db->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Payment revised and recorded',
                    'order_id' => $orderId,
                    'revised_order_total' => $revisedOrderTotal,
                ]);
                exit;
            } catch (Exception $e) {
                try {
                    if (!empty($db) && $db instanceof PDO && $db->inTransaction()) {
                        $db->rollBack();
                    }
                } catch (Exception $ignored) {
                }

                error_log("Error in reviseCancelledOrderPayment: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }
    }
