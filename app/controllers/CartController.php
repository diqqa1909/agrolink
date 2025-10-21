<?php
class CartController {
    use Controller;
    
    private $cartModel;
    
    public function __construct() {
        $this->cartModel = new CartModel();
    }

    /**
     * Display cart page
     */
    public function index() {
        // Check if user is logged in and is a buyer
        if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $cartItems = $this->cartModel->getUserCart($user_id);
        $cartTotal = $this->cartModel->getCartTotal($user_id);
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);

        $data = [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartItemCount' => $cartItemCount,
            'username' => $_SESSION['USER']->name
        ];

        $this->view('cart', $data);
    }

    /**
     * Add item to cart (AJAX endpoint)
     */
    public function add() {
        // Check if user is logged in and is a buyer
        if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            $this->jsonResponse(['success' => false, 'message' => 'Please login as a buyer']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        
        // Get POST data
        $product_id = $_POST['product_id'] ?? '';
        $product_name = $_POST['product_name'] ?? '';
        $product_price = $_POST['product_price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        $farmer_name = $_POST['farmer_name'] ?? '';
        $farmer_location = $_POST['farmer_location'] ?? '';
        $product_image = $_POST['product_image'] ?? '';

        $data = [
            'user_id' => $user_id,
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'quantity' => $quantity,
            'farmer_name' => $farmer_name,
            'farmer_location' => $farmer_location,
            'product_image' => $product_image
        ];

        // Validate data
        if(!$this->cartModel->validate($data)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid data', 'errors' => $this->cartModel->errors]);
            return;
        }

        // Add to cart
        $result = $this->cartModel->addToCart($data);
        
        if($result !== false) {
            $cartItemCount = $this->cartModel->getCartItemCount($user_id);
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Item added to cart successfully',
                'cartItemCount' => $cartItemCount
            ]);
        } else {
            $errorData = ['success' => false, 'message' => 'Failed to add item to cart'];
            // If model has errors, include them
            if (!empty($this->cartModel->errors)) {
                $errorData['errors'] = $this->cartModel->errors;
            }
            // If DEBUG mode, try to include last DB error from PHP error log (best-effort)
            if (defined('DEBUG') && DEBUG === true) {
                $errorData['debug'] = 'Check PHP error log for database error details.';
            }
            $this->jsonResponse($errorData);
        }
    }

    /**
     * Update item quantity in cart (AJAX endpoint)
     */
    public function update() {
        // Check if user is logged in and is a buyer
        if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            $this->jsonResponse(['success' => false, 'message' => 'Please login as a buyer']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $product_id = $_POST['product_id'] ?? '';
        $quantity = $_POST['quantity'] ?? 0;

        if(empty($product_id) || !is_numeric($quantity)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid product ID or quantity']);
            return;
        }

        $result = $this->cartModel->updateQuantity($user_id, $product_id, $quantity);
        
        if($result !== false) {
            $cartTotal = $this->cartModel->getCartTotal($user_id);
            $cartItemCount = $this->cartModel->getCartItemCount($user_id);
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Cart updated successfully',
                'cartTotal' => $cartTotal,
                'cartItemCount' => $cartItemCount
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update cart']);
        }
    }

    /**
     * Remove item from cart (AJAX endpoint)
     */
    public function remove() {
        // Check if user is logged in and is a buyer
        if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            $this->jsonResponse(['success' => false, 'message' => 'Please login as a buyer']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $product_id = $_POST['product_id'] ?? '';

        if(empty($product_id)) {
            $this->jsonResponse(['success' => false, 'message' => 'Product ID is required']);
            return;
        }

        $result = $this->cartModel->removeFromCart($user_id, $product_id);
        
        if($result !== false) {
            $cartTotal = $this->cartModel->getCartTotal($user_id);
            $cartItemCount = $this->cartModel->getCartItemCount($user_id);
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Item removed from cart successfully',
                'cartTotal' => $cartTotal,
                'cartItemCount' => $cartItemCount
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to remove item from cart']);
        }
    }

    /**
     * Clear entire cart (AJAX endpoint)
     */
    public function clear() {
        // Check if user is logged in and is a buyer
        if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            $this->jsonResponse(['success' => false, 'message' => 'Please login as a buyer']);
            return;
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $result = $this->cartModel->clearCart($user_id);
        
        if($result !== false) {
            $this->jsonResponse(['success' => true, 'message' => 'Cart cleared successfully']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to clear cart']);
        }
    }

    /**
     * Get cart data (AJAX endpoint)
     */
    public function getCartData() {
        // Check if user is logged in and is a buyer
        if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            $this->jsonResponse(['success' => false, 'message' => 'Please login as a buyer']);
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $cartItems = $this->cartModel->getUserCart($user_id);
        $cartTotal = $this->cartModel->getCartTotal($user_id);
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);

        $this->jsonResponse([
            'success' => true,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartItemCount' => $cartItemCount
        ]);
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}