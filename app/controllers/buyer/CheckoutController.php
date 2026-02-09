<?php

require_once __DIR__ . '/../../simple_shipping_calculator.php';

class CheckoutController
{
    use Controller;

    protected $cartModel;
    protected $buyerProfileModel;
    protected $orderModel;
    protected $productModel;
    protected $farmerModel;
    protected $shippingCalculator;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->buyerProfileModel = new BuyerProfileModel();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductsModel();
        $this->farmerModel = new FarmerModel();
        
        // Initialize shipping calculator
        try {
            $pdo = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->shippingCalculator = new SimpleShippingCalculator($pdo);
        } catch (PDOException $e) {
            error_log("Shipping calculator initialization error: " . $e->getMessage());
            $this->shippingCalculator = null;
        }
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
        
        // Get cart items with product details including available quantity
        $cartItems = $this->cartModel->getCartByUserId($user_id);
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);
        $cartTotal = $this->cartModel->getCartTotal($user_id);

        // Enrich cart items with product quantity information
        foreach ($cartItems as $item) {
            $product = $this->productModel->getById($item->product_id);
            if ($product) {
                $item->available_quantity = $product->quantity ?? 0;
                // Ensure cart quantity doesn't exceed available quantity
                if ($item->quantity > $item->available_quantity) {
                    // Update cart quantity to max available
                    $this->cartModel->updateQuantity($user_id, $item->product_id, $item->available_quantity);
                    $item->quantity = $item->available_quantity;
                }
            } else {
                $item->available_quantity = 0;
            }
        }

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

        // Calculate shipping cost using shipping calculator
        $deliveryFee = 150.00; // Default fallback
        $shippingCalculation = null;
        
        if ($hasDeliveryDetails && $this->shippingCalculator && !empty($cartItems)) {
            // Try to calculate shipping cost
            $shippingCalculation = $this->calculateShippingForCart($cartItems, $buyerProfile);
            if ($shippingCalculation && $shippingCalculation['success']) {
                $deliveryFee = $shippingCalculation['calculation']['total_shipping_cost_lkr'];
            } else {
                // Log error for debugging
                error_log("Shipping calculation failed: " . ($shippingCalculation['error'] ?? 'Unknown error'));
            }
        }
        
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
            'shippingCalculation' => $shippingCalculation,
            'pageTitle' => 'Checkout',
            'activePage' => 'checkout',
            'contentView' => 'buyer/checkout.view.php'
        ];

        $this->view('components/buyerLayout', $data);
    }

    /**
     * Calculate shipping cost for cart items
     * Handles multiple farmers by grouping items by farmer and calculating shipping for each
     */
    private function calculateShippingForCart($cartItems, $buyerProfile)
    {
        if (!$this->shippingCalculator || empty($cartItems)) {
            return null;
        }

        // Get buyer's district and town IDs
        $buyerDistrictId = $this->getDistrictIdByName($buyerProfile->district ?? 'Colombo');
        $buyerTownId = $this->getTownIdByName($buyerProfile->city ?? '', $buyerDistrictId);
        
        if (!$buyerDistrictId) {
            return null;
        }

        // Group cart items by farmer
        $itemsByFarmer = [];
        foreach ($cartItems as $item) {
            $product = $this->productModel->getById($item->product_id);
            if (!$product) {
                continue;
            }
            
            $farmerId = $product->farmer_id;
            if (!isset($itemsByFarmer[$farmerId])) {
                $itemsByFarmer[$farmerId] = [
                    'items' => [],
                    'total_weight' => 0,
                    'product' => $product
                ];
            }
            
            $itemsByFarmer[$farmerId]['items'][] = $item;
            $itemsByFarmer[$farmerId]['total_weight'] += $item->quantity; // Assuming quantity is in kg
        }

        if (empty($itemsByFarmer)) {
            return null;
        }

        // Calculate shipping for each farmer and sum them up
        $totalShippingCost = 0;
        $allCalculations = [];
        $selectedVehicles = [];

        foreach ($itemsByFarmer as $farmerId => $farmerData) {
            $product = $farmerData['product'];
            $totalWeight = $farmerData['total_weight'];
            $firstItem = $farmerData['items'][0];
            $cropName = $firstItem->product_name;

            // Get farmer's district/town (Try new ID fields first)
            $farmerDistrictId = $product->district_id ?? null;
            $farmerTownId = $product->town_id ?? null;

            if (!$farmerDistrictId) {
                // Get farmer's district from farmer profile
                $farmerProfile = $this->farmerModel->getProfileByUserId($farmerId);
                
                // Get farmer's district and town (legacy fallback)
                $farmerDistrictName = $farmerProfile ? ($farmerProfile->district ?? $product->location ?? 'Colombo') : ($product->location ?? 'Colombo');
                $farmerDistrictId = $this->getDistrictIdByName($farmerDistrictName);
                $farmerTownId = $this->getTownIdByName($product->location ?? '', $farmerDistrictId);
            }

            if ($totalWeight <= 0) {
                continue;
            }

            $params = [
                'pickup_district_id' => $farmerDistrictId,
                'pickup_town_id' => $farmerTownId ?: $farmerDistrictId, // Fallback to district center
                'delivery_district_id' => $buyerDistrictId,
                'delivery_town_id' => $buyerTownId ?: $buyerDistrictId, // Fallback to district center
                'crop_name' => $cropName,
                'weight_kg' => $totalWeight
            ];

            $calculation = $this->shippingCalculator->calculateShippingCost($params);
            
            if ($calculation && $calculation['success']) {
                $totalShippingCost += $calculation['calculation']['total_shipping_cost_lkr'];
                $allCalculations[] = $calculation['calculation'];
                $selectedVehicles[] = $calculation['calculation']['selected_vehicle'];
            }
        }

        if ($totalShippingCost <= 0) {
            return null;
        }

        // Return combined result
        return [
            'success' => true,
            'calculation' => [
                'total_shipping_cost_lkr' => round($totalShippingCost, 2),
                'multiple_farmers' => count($itemsByFarmer) > 1,
                'farmer_count' => count($itemsByFarmer),
                'calculations' => $allCalculations,
                'selected_vehicles' => $selectedVehicles
            ]
        ];
    }

    /**
     * Get district ID by name
     */
    private function getDistrictIdByName($districtName)
    {
        $dbModel = new CartModel(); // Use existing model to access Database trait
        $sql = "SELECT id FROM districts WHERE district_name = :name LIMIT 1";
        $result = $dbModel->query($sql, ['name' => $districtName]);
        if ($result && is_array($result) && !empty($result)) {
            return $result[0]->id;
        }
        // Default to Colombo if not found
        $result = $dbModel->query($sql, ['name' => 'Colombo']);
        return ($result && is_array($result) && !empty($result)) ? $result[0]->id : 1;
    }

    /**
     * Get town ID by name and district
     */
    private function getTownIdByName($townName, $districtId)
    {
        if (empty($townName)) {
            return null;
        }
        $dbModel = new CartModel(); // Use existing model to access Database trait
        $sql = "SELECT id FROM towns WHERE town_name LIKE :name AND district_id = :district_id LIMIT 1";
        $result = $dbModel->query($sql, ['name' => '%' . $townName . '%', 'district_id' => $districtId]);
        if ($result && is_array($result) && !empty($result)) {
            return $result[0]->id;
        }
        return null;
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
            'street_name' => trim($_POST['delivery_address'] ?? ''),
            'apartment_code' => trim($_POST['address2'] ?? ''),
            'postal_code' => trim($_POST['zipCode'] ?? ''),
            'district' => trim($_POST['state'] ?? '')
        ];

        // Validate required fields
        if (empty($data['phone']) || empty($data['city']) || empty($data['street_name'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Phone, city, and street address are required']);
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
     * Place order (AJAX)
     */
    public function placeOrder()
    {
        header('Content-Type: application/json');

        // DEBUG: Log debugging info
        $logFile = 'debug_log.txt';
        $logData = date('Y-m-d H:i:s') . " - placeOrder Called\n";
        $logData .= "Session ID: " . session_id() . "\n";
        $logData .= "Session Data: " . print_r($_SESSION, true) . "\n";
        $logData .= "Headers: " . print_r(getallheaders(), true) . "\n";
        file_put_contents($logFile, $logData, FILE_APPEND);
        
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            file_put_contents($logFile, "Auth Failed: USER not set or role not buyer\n", FILE_APPEND); 
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

        // Get cart items
        $cartItems = $this->cartModel->getCartByUserId($user_id);
        if (empty($cartItems)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
            exit;
        }

        // Get buyer profile
        $buyerProfile = $this->buyerProfileModel->getProfileByUserId($user_id);
        if (!$buyerProfile || !$this->buyerProfileModel->hasDeliveryDetails($user_id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Please complete your delivery details first']);
            exit;
        }

        // --- STEP 1: VALIDATE STOCK FOR ALL ITEMS ---
        $errors = [];
        $itemsByFarmer = []; // Group items by farmer for later
        
        foreach ($cartItems as $item) {
            $product = $this->productModel->getById($item->product_id);
            if (!$product) {
                $errors[] = "Product not found: {$item->product_name}";
                continue;
            }
            
            $availableQuantity = $product->quantity ?? 0;
            
            // Check for valid quantity
            if ($item->quantity <= 0) {
                 $errors[] = "Item '{$item->product_name}' is out of stock. Please remove it from cart.";
                 continue;
            }

            if ($item->quantity > $availableQuantity) {
                $errors[] = "Insufficient stock for {$item->product_name}. Only {$availableQuantity} kg available.";
            }

            // Group by farmer
            $farmerId = $this->getFarmerIdByProductId($item->product_id);
            if (!isset($itemsByFarmer[$farmerId])) {
                $itemsByFarmer[$farmerId] = [];
            }
            $itemsByFarmer[$farmerId][] = $item;
        }

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Stock validation failed: ' . implode(', ', $errors)
            ]);
            exit;
        }

        // Common details
        $paymentMethod = $_POST['payment_method'] ?? 'cash_on_delivery';
        $deliveryDistrictId = $this->getDistrictIdByName($buyerProfile->district ?? 'Colombo');
        $deliveryTownId = $this->getTownIdByName($buyerProfile->city ?? '', $deliveryDistrictId);
        $orderIds = [];
        $overallTotal = 0;

        // --- STEP 2: LOOP AND CREATE SPLIT ORDERS ---
        foreach ($itemsByFarmer as $farmerId => $farmerItems) {
            // Calculate shipping for this specific group of items
            $shippingCalculation = $this->calculateShippingForCart($farmerItems, $buyerProfile);
            $shippingCost = 150.00; // Default fallback for this sub-order
            
            if ($shippingCalculation && $shippingCalculation['success']) {
                $shippingCost = $shippingCalculation['calculation']['total_shipping_cost_lkr'];
            } else {
                error_log("Shipping calculation failed for farmer {$farmerId}: " . ($shippingCalculation['error'] ?? 'Unknown error'));
            }

            // Calculate subtotal for this group
            $cartTotal = 0;
            foreach ($farmerItems as $item) {
                $cartTotal += $item->product_price * $item->quantity;
            }
            
            $orderTotal = $cartTotal + $shippingCost;
            $overallTotal += $orderTotal;

            $orderData = [
                'buyer_id' => $user_id,
                'total_amount' => $cartTotal,
                'shipping_cost' => $shippingCost,
                'order_total' => $orderTotal,
                'payment_method' => $paymentMethod,
                'delivery_address' => ($buyerProfile->apartment_code ?? '') . ', ' . ($buyerProfile->street_name ?? ''),
                'delivery_city' => $buyerProfile->city ?? '',
                'delivery_district_id' => $deliveryDistrictId,
                'delivery_town_id' => $deliveryTownId,
                'delivery_phone' => $buyerProfile->phone ?? '',
                'status' => 'pending'
            ];

            $orderId = $this->orderModel->createOrder($orderData);

            if (!$orderId || $orderId === false || $orderId === 1) {
                error_log("Failed to create sub-order for farmer {$farmerId}");
                // Continue to try other orders? Or fail all? 
                // For now, we continue but this is messy partial failure state.
                continue;
            }
            
            $orderIds[] = $orderId;

            // --- STEP 3: ADD ITEMS & UPDATE STOCK FOR THIS SUB-ORDER ---
            foreach ($farmerItems as $item) {
                $itemData = [
                    'order_id' => $orderId,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'farmer_id' => $farmerId 
                ];

                if (!$this->orderModel->addOrderItem($itemData)) {
                    error_log("Failed to add item {$item->product_name} to order {$orderId}");
                }

                if (!$this->orderModel->updateProductQuantity($item->product_id, $item->quantity)) {
                    error_log("Failed to update quantity for {$item->product_name}");
                }
            }
        }

        if (empty($orderIds)) {
             http_response_code(500);
             echo json_encode(['success' => false, 'message' => 'Failed to process valid orders']);
             exit;
        }

        // Clear cart (only if at least one order succeeded)
        $this->cartModel->clearCart($user_id);
        $this->clearBuyNow();

        echo json_encode([
            'success' => true,
            'message' => 'Orders placed successfully!',
            'order_ids' => $orderIds,
            'order_total' => $overallTotal
        ]);
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

    private function getFarmerIdByProductId($productId)
    {
        $product = $this->productModel->getById($productId);
        return $product ? $product->farmer_id : null;
    }
}
