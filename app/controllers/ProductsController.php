<?php

class ProductsController
{
    use Controller;

    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductsModel();
    }

    /**
     * Create a new product (Farmer only)
     */
    public function create()
    {
        // Clean output buffer
        if (ob_get_level()) ob_clean();

        header('Content-Type: application/json');

        // Check if user is logged in
        if (!isset($_SESSION['USER'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'You must be logged in to add products'
            ]);
            exit;
        }

        // Check if user is a farmer
        $userRole = $_SESSION['USER']->role ?? null;

        if ($userRole !== 'farmer') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'Only farmers can add products'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $farmer_id = $_SESSION['USER']->id;

            // Verify the farmer exists in the database
            if (!class_exists('UserModel')) {
                require_once '../app/models/UserModel.php';
            }
            $userModel = new UserModel();
            $user = $userModel->first(['id' => $farmer_id]);

            if (!$user) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'error' => 'User account not found. Please log out and log in again.'
                ]);
                exit;
            }

            // Validation
            $errors = [];

            $category = trim($_POST['category'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $quantity = trim($_POST['quantity'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $listing_date = trim($_POST['listing_date'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Validate required fields
            if (empty($category)) {
                $errors['category'] = 'Category is required';
            }

            if (empty($name)) {
                $errors['name'] = 'Product name is required';
            } elseif (strlen($name) < 3) {
                $errors['name'] = 'Product name must be at least 3 characters';
            } elseif (strlen($name) > 100) {
                $errors['name'] = 'Product name is too long (max 100 characters)';
            }

            if (empty($price)) {
                $errors['price'] = 'Price is required';
            } elseif (!is_numeric($price) || $price <= 0) {
                $errors['price'] = 'Price must be a positive number';
            }

            if (empty($quantity)) {
                $errors['quantity'] = 'Quantity is required';
            } elseif (!is_numeric($quantity) || $quantity < 10) {
                $errors['quantity'] = 'Minimum quantity is 10kg';
            }

            if (empty($location)) {
                // Use farmer's location from profile if available
                $location = $_SESSION['USER']->location ?? '';

                // If still empty, show error
                if (empty($location)) {
                    $errors['location'] = 'Location is required';
                }
            }

            if (empty($listing_date)) {
                $errors['listing_date'] = 'Listing date is required';
            } else {
                $date = DateTime::createFromFormat('Y-m-d', $listing_date);
                $today = new DateTime();
                $today->setTime(0, 0, 0);

                if (!$date || $date < $today) {
                    $errors['listing_date'] = 'Listing date cannot be in the past';
                }
            }

            // Handle image upload
            $imageName = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $errors['image'] = 'Invalid image format. Allowed: ' . implode(', ', $allowed);
                } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $errors['image'] = 'Image size must be less than 5MB';
                } else {
                    $imageName = uniqid('product_') . '.' . $ext;
                    $uploadPath = '../public/assets/images/products/';

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath . $imageName)) {
                        $errors['image'] = 'Failed to upload image';
                        $imageName = null;
                    }
                }
            } else {
                // Image is required
                $errors['image'] = 'Product image is required';
            }

            // If validation fails, return errors
            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $errors
                ]);
                exit;
            }

            // Prepare data for insertion
            $data = [
                'farmer_id' => $farmer_id,
                'category' => $category,
                'name' => $name,
                'price' => (float)$price,
                'quantity' => (int)$quantity,
                'location' => $location,
                'listing_date' => $listing_date,
                'description' => $description ?: '', // Ensure description is not null
                'image' => $imageName ?: '' // Ensure image is not null
            ];

            // Log data being inserted
            error_log("ProductsController::create - Attempting to insert: " . print_r($data, true));

            // Insert product using ProductsModel
            $result = $this->productModel->create($data);

            error_log("ProductsController::create - Insert result: " . ($result ? $result : 'FALSE'));

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Product added successfully',
                    'product_id' => $result
                ]);
            } else {
                // Get more detailed error
                $pdo_error = $this->productModel->getLastError();
                error_log("ProductsController::create - Insert failed. PDO Error: " . print_r($pdo_error, true));

                // Check if it's a foreign key constraint error
                $errorMessage = 'Failed to add product to database';
                if (is_string($pdo_error) && strpos($pdo_error, 'foreign key constraint') !== false) {
                    $errorMessage = 'Your account is invalid. Please log out and log in again.';
                }

                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => $errorMessage,
                    'debug' => $pdo_error // Remove in production
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

    // Buyer-facing list page
    public function index()
    {
        $filters = [
            'search'    => $_GET['search']    ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'location'  => $_GET['location']  ?? '',
        ];
        $data = [
            'products' => $this->productModel->getAvailable($filters),
            'filters'  => $filters,
        ];
        $this->view('products', $data);
    }

    // Return farmer's products (JSON)
    public function farmerList()
    {
        header('Content-Type: application/json');
        if (!$this->requireFarmer()) return;

        $farmerId = (int)$_SESSION['USER']->id;
        $items = $this->productModel->getByFarmer($farmerId);
        echo json_encode(['success' => true, 'products' => $items]);
    }

    // Buyer products list (JSON) - for buyer dashboard
    public function buyerList()
    {
        header('Content-Type: application/json');

        // Optional: check if user is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $conditions = [];

        // Add filters if provided
        if (!empty($_GET['category'])) {
            $conditions['category'] = $_GET['category'];
        }
        if (!empty($_GET['location'])) {
            $conditions['location'] = $_GET['location'];
        }
        if (!empty($_GET['min_price'])) {
            $conditions['min_price'] = $_GET['min_price'];
        }
        if (!empty($_GET['max_price'])) {
            $conditions['max_price'] = $_GET['max_price'];
        }

        $products = $this->productModel->getWithFarmerDetails($conditions);
        echo json_encode(['success' => true, 'products' => $products]);
    }

    /**
     * Get a single product for editing
     */
    public function edit($id = null)
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        try {
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Product ID required']);
                exit;
            }

            $farmer_id = $_SESSION['USER']->id;

            if (!class_exists('ProductsModel')) {
                require_once '../app/models/ProductsModel.php';
            }

            $productsModel = new ProductsModel();
            $product = $productsModel->getById($id);

            if (!$product || $product->farmer_id != $farmer_id) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Product not found']);
                exit;
            }

            echo json_encode(['success' => true, 'product' => $product]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update a product
     */
    public function update($id = null)
    {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $product_id = $id ?? ($_POST['id'] ?? null);
            if (!$product_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Product ID required']);
                exit;
            }

            $farmer_id = $_SESSION['USER']->id;

            if (!class_exists('ProductsModel')) {
                require_once '../app/models/ProductsModel.php';
            }

            $productsModel = new ProductsModel();
            $existing = $productsModel->getById($product_id);

            if (!$existing || $existing->farmer_id != $farmer_id) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Product not found']);
                exit;
            }

            // Validation
            $errors = [];
            $data = [];

            // Validate name
            if (isset($_POST['name'])) {
                $name = trim($_POST['name']);
                if (empty($name)) {
                    $errors['name'] = 'Product name is required';
                } elseif (strlen($name) < 3) {
                    $errors['name'] = 'Product name must be at least 3 characters';
                } else {
                    $data['name'] = $name;
                }
            }

            // Validate category
            if (isset($_POST['category'])) {
                $category = trim($_POST['category']);
                if (empty($category)) {
                    $errors['category'] = 'Category is required';
                } else {
                    $data['category'] = $category;
                }
            }

            // Validate price
            if (isset($_POST['price'])) {
                $price = trim($_POST['price']);
                if (!is_numeric($price) || $price <= 0) {
                    $errors['price'] = 'Price must be positive';
                } else {
                    $data['price'] = (float)$price;
                }
            }

            // Validate quantity
            if (isset($_POST['quantity'])) {
                $quantity = trim($_POST['quantity']);
                if (!is_numeric($quantity) || $quantity < 0) {
                    $errors['quantity'] = 'Quantity must be non-negative';
                } else {
                    $data['quantity'] = (int)$quantity;
                }
            }

            // Optional fields
            if (isset($_POST['location'])) {
                $data['location'] = trim($_POST['location']);
            }

            if (isset($_POST['listing_date'])) {
                $data['listing_date'] = trim($_POST['listing_date']);
            }

            if (isset($_POST['description'])) {
                $data['description'] = trim($_POST['description']);
            }

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $errors['image'] = 'Invalid format. Allowed: jpg, jpeg, png, gif, webp';
                } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $errors['image'] = 'File too large (max 5MB)';
                } else {
                    $imageName = uniqid('product_') . '.' . $ext;
                    $uploadPath = '../public/assets/images/products/';

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath . $imageName)) {
                        // Delete old image
                        if ($existing->image && file_exists($uploadPath . $existing->image)) {
                            unlink($uploadPath . $existing->image);
                        }
                        $data['image'] = $imageName;
                    } else {
                        $errors['image'] = 'Failed to upload image';
                    }
                }
            }

            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit;
            }

            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No data to update']);
                exit;
            }

            $result = $productsModel->updateByFarmer($product_id, $farmer_id, $data);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update product']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function delete($id = null)
    {
        header('Content-Type: application/json');
        if (!$this->requireFarmer()) return;
        $id = (int)($id ?? ($_POST['id'] ?? 0));

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid id']);
            return;
        }

        $ok = $this->productModel->deleteByFarmer($id, (int)$_SESSION['USER']->id);
        if ($ok) echo json_encode(['success' => true, 'message' => 'Product deleted']);
        else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete product']);
        }
    }

    public function show($id)
    {
        header('Content-Type: application/json');
        $item = $this->productModel->getById((int)$id);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            return;
        }
        echo json_encode(['success' => true, 'product' => $item]);
    }

    // Helpers
    private function requireFarmer()
    {
        if (!isset($_SESSION['USER'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return false;
        }
        if (($_SESSION['USER']->role ?? '') !== 'farmer') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return false;
        }
        return true;
    }

    private function validate($in)
    {
        $errors = [];
        if (empty($in['name'])) $errors['name'] = 'Name is required';
        if (!isset($in['price']) || $in['price'] === '' || $in['price'] < 0) $errors['price'] = 'Price is required';
        if (!isset($in['quantity']) || !is_numeric($in['quantity']) || (int)$in['quantity'] < 0) $errors['quantity'] = 'Quantity is required';
        return $errors;
    }
}
