<?php

class WishlistController
{
    use Controller;

    private $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
    }

    private function requireBuyer()
    {
        if (!isset($_SESSION['USER']) || ($_SESSION['USER']->role ?? '') !== 'buyer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return false;
        }
        return true;
    }

    public function index()
    {
        header('Content-Type: application/json');

        if (!$this->requireBuyer()) {
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $items = $this->wishlistModel->getByUserId($user_id);

        echo json_encode([
            'success' => true,
            'items' => $items
        ]);
    }

    public function add()
    {
        header('Content-Type: application/json');

        if (!$this->requireBuyer()) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if ($product_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid product']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $result = $this->wishlistModel->add($user_id, $product_id);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Product added to wishlist',
                'wishlistCount' => $this->wishlistModel->countByUser($user_id)
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add to wishlist']);
        }
    }

    public function remove($product_id = null)
    {
        header('Content-Type: application/json');

        if (!$this->requireBuyer()) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!$product_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Product required']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $result = $this->wishlistModel->remove($user_id, (int)$product_id);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'wishlistCount' => $this->wishlistModel->countByUser($user_id)
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to remove product']);
        }
    }

    public function clear()
    {
        header('Content-Type: application/json');

        if (!$this->requireBuyer()) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $this->wishlistModel->clear($user_id);

        echo json_encode(['success' => true, 'message' => 'Wishlist cleared']);
    }
}

