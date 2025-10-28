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
        $userRole = trim(strtolower($_SESSION['USER']->role ?? ''));

        // Debug logging
        error_log("User Role (trimmed): " . $userRole);
        error_log("Session USER: " . print_r($_SESSION['USER'], true));

        if ($userRole !== 'farmer') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'Only farmers can add products',
                'debug' => [
                    'userRole' => $userRole,
                    'userRoleRaw' => $_SESSION['USER']->role ?? null,
                    'expectedRole' => 'farmer'
                ]
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
                'description' => $description,
                'image' => $imageName
            ];

            // Insert product using ProductsModel
            $result = $this->productModel->create($data);

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Product added successfully',
                    'product_id' => $result
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to add product to database'
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

    public function update($id = null)
    {
        header('Content-Type: application/json');
        if (!$this->requireFarmer()) return;
        $id = (int)($id ?? ($_POST['id'] ?? 0));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id <= 0) {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Ensure the product exists and belongs to this farmer
        $current = $this->productModel->getById($id);
        if (!$current || (int)$current->farmer_id !== (int)$_SESSION['USER']->id) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        // Validate required fields
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = $_POST['price'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $listing_date = trim($_POST['listing_date'] ?? '');

        $errors = [];
        if ($name === '') $errors['name'] = 'Name is required';
        if ($category === '') $errors['category'] = 'Category is required';
        if ($price === '' || !is_numeric($price) || $price < 0) $errors['price'] = 'Price is required';
        if ($quantity === '' || !is_numeric($quantity) || (int)$quantity < 0) $errors['quantity'] = 'Quantity is required';
        if ($location === '') $errors['location'] = 'Location is required';
        if ($listing_date === '') {
            $errors['listing_date'] = 'Listing date is required';
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $listing_date);
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            if (!$date || $date < $today) {
                $errors['listing_date'] = 'Listing date cannot be in the past';
            }
        }

        // Optional image upload
        $newImageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    $errors['image'] = 'Invalid image format. Allowed: ' . implode(', ', $allowed);
                } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $errors['image'] = 'Image size must be less than 5MB';
                } else {
                    $newImageName = uniqid('product_') . '.' . $ext;
                    $uploadPath = '../public/assets/images/products/';
                    if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath . $newImageName)) {
                        $errors['image'] = 'Failed to upload image';
                        $newImageName = null;
                    }
                }
            } else {
                $errors['image'] = 'File upload failed';
            }
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation failed', 'errors' => $errors]);
            return;
        }

        $payload = [
            'name'         => $name,
            'category'     => $category,
            'price'        => (float)$price,
            'quantity'     => (int)$quantity,
            'location'     => $location,
            'listing_date' => $listing_date,
        ];
        if ($newImageName) {
            $payload['image'] = $newImageName;
        }

        $ok = $this->productModel->updateByFarmer($id, (int)$_SESSION['USER']->id, $payload);

        if ($ok) {
            // Optionally remove old image when replaced
            if ($newImageName && !empty($current->image)) {
                $oldPath = '../public/assets/images/products/' . $current->image;
                if (is_file($oldPath)) @unlink($oldPath);
            }
            echo json_encode(['success' => true, 'message' => 'Product updated']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update product']);
        }
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

        // Get the product first to check ownership and get image filename
        $product = $this->productModel->getById($id);
        if (!$product || (int)$product->farmer_id !== (int)$_SESSION['USER']->id) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $ok = $this->productModel->deleteByFarmer($id, (int)$_SESSION['USER']->id);
        if ($ok) {
            // Delete the associated image file if it exists
            if (!empty($product->image)) {
                $imagePath = '../public/assets/images/products/' . $product->image;
                if (is_file($imagePath)) {
                    @unlink($imagePath);
                }
            }
            echo json_encode(['success' => true, 'message' => 'Product deleted']);
        } else {
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
