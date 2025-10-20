<?php

class Products
{
    use Controller;

    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductsModel();
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

    // Create product (JSON)
    public function create()
    {
        header('Content-Type: application/json');
        if (!$this->requireFarmer()) return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $data = [
            'farmer_id'  => (int)$_SESSION['USER']->id,                 // DB column
            'name'       => trim($_POST['name'] ?? ''),
            'price'      => (float)($_POST['price'] ?? 0),
            'quantity'   => (int)($_POST['quantity'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'location'   => trim($_POST['location'] ?? ''),
            'image'      => null,
        ];

        // Optional image upload
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $data['image'] = $this->uploadImage($_FILES['image']);
            } catch (Exception $e) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                return;
            }
        }

        // Basic validation
        if ($data['name'] === '' || $data['price'] <= 0 || $data['quantity'] <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Invalid input']);
            return;
        }

        $ok = $this->productModel->create($data);
        if ($ok) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'DB insert failed']);
        }
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

        $errors = $this->validate($_POST);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation failed', 'errors' => $errors]);
            return;
        }

        $payload = [
            'name'        => trim($_POST['name']),
            'price'       => (float)$_POST['price'],
            'quantity'    => (int)$_POST['quantity'],
            'description' => trim($_POST['description'] ?? ''),
            'location'    => trim($_POST['location'] ?? '')
        ];

        $ok = $this->productModel->updateByFarmer($id, (int)$_SESSION['USER']->id, $payload);

        if ($ok) echo json_encode(['success' => true, 'message' => 'Product updated']);
        else {
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

    private function uploadImage($file)
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!in_array($file['type'], $allowed)) throw new Exception('Invalid image type');
        if ($file['size'] > 5 * 1024 * 1024) throw new Exception('Image too large');

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/agrolink/public/assets/uploads/products/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = uniqid('prod_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $path = $dir . $name;
        if (!move_uploaded_file($file['tmp_name'], $path)) throw new Exception('Upload failed');

        return 'assets/uploads/products/' . $name; // relative path
    }
}
