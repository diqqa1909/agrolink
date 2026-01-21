<?php

class CheckoutController
{
    use Controller;

    protected $cartModel;
    protected $buyerProfileModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->buyerProfileModel = new BuyerProfileModel();
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        $user_id = $_SESSION['USER']->id;

        // Check if this is a "Buy Now" checkout (single product)
        $isBuyNow = isset($_GET['buy_now']) && $_GET['buy_now'] == '1';
        if ($isBuyNow && isset($_GET['product_id'])) {
            $_SESSION['buy_now_product_id'] = (int)$_GET['product_id'];
        }
        $isBuyNow = isset($_SESSION['buy_now_product_id']);
        
        // Get cart items
        $cartItems = $this->cartModel->getCartByUserId($user_id);
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);
        $cartTotal = $this->cartModel->getCartTotal($user_id);

        // If Buy Now mode, filter to show only that product
        if ($isBuyNow && isset($_SESSION['buy_now_product_id'])) {
            $buyNowProductId = $_SESSION['buy_now_product_id'];
            $cartItems = array_filter($cartItems, function($item) use ($buyNowProductId) {
                return $item->product_id == $buyNowProductId;
            });
            $cartItems = array_values($cartItems); // Re-index array
            
            // Recalculate totals for Buy Now item only
            $cartTotal = 0;
            $cartItemCount = 0;
            foreach ($cartItems as $item) {
                $cartTotal += $item->product_price * $item->quantity;
                $cartItemCount += $item->quantity;
            }
        }

        // Check if cart is empty
        if (empty($cartItems) || $cartItemCount === 0) {
            $_SESSION['error'] = 'Your cart is empty. Please add items before checkout.';
            redirect('Cart');
            return;
        }

        // Get buyer profile to check if delivery details exist
        $buyerProfile = $this->buyerProfileModel->getProfileByUserId($user_id);
        $hasDeliveryDetails = $this->buyerProfileModel->hasDeliveryDetails($user_id);

        // Calculate delivery fee (you can customize this logic)
        $deliveryFee = 150.00; // Default delivery fee
        $orderTotal = $cartTotal + $deliveryFee;

        $data = [
            'cartItems' => $cartItems ?: [],
            'cartItemCount' => $cartItemCount,
            'cartTotal' => $cartTotal,
            'deliveryFee' => $deliveryFee,
            'orderTotal' => $orderTotal,
            'buyerProfile' => $buyerProfile,
            'hasDeliveryDetails' => $hasDeliveryDetails,
            'isBuyNow' => $isBuyNow,
            'pageTitle' => 'Checkout',
            'activePage' => 'checkout',
            'contentView' => 'buyer/checkout.view.php'
        ];

        $this->view('components/buyerLayout', $data);
    }

    /**
     * Save delivery details (AJAX)
     */
    public function saveDeliveryDetails()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $user_id = $_SESSION['USER']->id;

        $data = [
            'phone' => trim($_POST['phone'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'delivery_address' => trim($_POST['delivery_address'] ?? '')
        ];

        // Validate required fields
        if (empty($data['phone']) || empty($data['city']) || empty($data['delivery_address'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }

        try {
            // Check if profile exists
            $existingProfile = $this->buyerProfileModel->getProfileByUserId($user_id);
            
            if ($existingProfile && is_object($existingProfile)) {
                // Update existing profile
                $result = $this->buyerProfileModel->updateProfile($user_id, $data);
            } else {
                // Create new profile
                $result = $this->buyerProfileModel->createProfile($user_id, $data);
            }

            // Check if result is valid (not 1 which means error in Database trait)
            // write() returns insert ID (int > 0) or true on success, 1 on failure
            if ($result && $result !== 1) {
                // Verify the save by fetching the profile
                $savedProfile = $this->buyerProfileModel->getProfileByUserId($user_id);
                if ($savedProfile && is_object($savedProfile)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Delivery details saved successfully'
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Details saved but could not verify. Please refresh the page.']);
                }
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save delivery details. Please check your database connection and table structure.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Clear buy now session flag (called after order completion)
     */
    public function clearBuyNow()
    {
        if (isset($_SESSION['buy_now_product_id'])) {
            unset($_SESSION['buy_now_product_id']);
        }
    }
}

