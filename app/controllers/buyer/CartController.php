<?php

class CartController
{
    use Controller;

    protected $cartModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
    }

    /**
     * Display cart page
     */
    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        $user_id = $_SESSION['USER']->id;

        // Get cart items
        $cartItems = $this->cartModel->getCartByUserId($user_id);
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);
        $cartTotal = $this->cartModel->getCartTotal($user_id);

        $data = [
            'cartItems' => $cartItems ?: [],
            'cartItemCount' => $cartItemCount,
            'cartTotal' => $cartTotal
        ];

        $this->view('cart', $data);
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add()
    {
        header('Content-Type: application/json');
        if (!$this->requireBuyer()) exit;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $user_id = $_SESSION['USER']->id;
        $data = [
            'user_id' => $user_id,
            'product_id' => $_POST['product_id'] ?? null,
            'product_name' => trim($_POST['product_name'] ?? ''),
            'product_price' => (float)($_POST['product_price'] ?? 0),
            'quantity' => (int)($_POST['quantity'] ?? 1),
            'product_image' => trim($_POST['product_image'] ?? '🌱')
        ];

        if (!$data['product_id']) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit;
        }

        try {
            // Check if product already exists in cart
            $existingItem = $this->cartModel->getCartItem($user_id, $data['product_id']);

            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem->quantity + $data['quantity'];
                $updated = $this->cartModel->updateQuantity($user_id, $data['product_id'], $newQuantity);

                if ($updated) {
                    $cartItemCount = $this->cartModel->getCartItemCount($user_id);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Product quantity updated in cart',
                        'cartItemCount' => $cartItemCount
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
                }
            } else {
                // Add new item
                $added = $this->cartModel->addToCart($data);

                if ($added) {
                    $cartItemCount = $this->cartModel->getCartItemCount($user_id);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Product added to cart successfully',
                        'cartItemCount' => $cartItemCount
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update quantity (AJAX)
     */
    public function update($id = null)
    {
        header('Content-Type: application/json');
        if (!$this->requireBuyer()) exit;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $user_id = $_SESSION['USER']->id;
        $product_id = $id ?? ($_POST['product_id'] ?? null);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (!$product_id || $quantity < 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        try {
            $updated = $this->cartModel->updateQuantity($user_id, $product_id, $quantity);

            if ($updated) {
                $cartItemCount = $this->cartModel->getCartItemCount($user_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Cart updated successfully',
                    'cartItemCount' => $cartItemCount
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Remove item (AJAX)
     */
    public function remove($id = null)
    {
        header('Content-Type: application/json');
        if (!$this->requireBuyer()) exit;

        $user_id = $_SESSION['USER']->id;
        $product_id = $id ?? ($_POST['product_id'] ?? null);

        if (!$product_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
            exit;
        }

        try {
            $removed = $this->cartModel->removeFromCart($user_id, $product_id);

            if ($removed) {
                $cartItemCount = $this->cartModel->getCartItemCount($user_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cartItemCount' => $cartItemCount
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to remove']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Clear cart (AJAX)
     */
    public function clear()
    {
        header('Content-Type: application/json');
        if (!$this->requireBuyer()) exit;

        try {
            $user_id = $_SESSION['USER']->id;
            $cleared = $this->cartModel->clearCart($user_id);

            if ($cleared) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cart cleared successfully',
                    'cartItemCount' => 0
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get cart data (AJAX)
     */
    public function getData()
    {
        header('Content-Type: application/json');
        if (!$this->requireBuyer()) exit;

        try {
            $user_id = $_SESSION['USER']->id;
            $cartItemCount = $this->cartModel->getCartItemCount($user_id);
            $cartTotal = $this->cartModel->getCartTotal($user_id);

            echo json_encode([
                'success' => true,
                'cartItemCount' => $cartItemCount,
                'cartTotal' => $cartTotal
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    // Helper
    private function requireBuyer()
    {
        if (!isset($_SESSION['USER'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return false;
        }
        if (($_SESSION['USER']->role ?? '') !== 'buyer') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            return false;
        }
        return true;
    }
}
