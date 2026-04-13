<?php

class TransporterModel
{
    use Database;

    protected $table = 'transporter_profiles';
    protected $userTable = 'users';

    /**
     * Get transporter profile by user ID
     */
    public function getProfileByUserId($userId)
    {
        $sql = "SELECT tp.*, u.name, u.email, u.status, u.deactivated_at
                FROM {$this->table} tp
                LEFT JOIN {$this->userTable} u ON u.id = tp.user_id
                WHERE tp.user_id = :user_id";

        return $this->get_row($sql, ['user_id' => $userId]);
    }

    /**
     * Create transporter profile
     */
    public function createProfile($userId, $data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, phone, apartment_code, street_name, city, district, postal_code, full_address, company_name, license_number, vehicle_type, availability, profile_photo, created_at, updated_at)
                VALUES (:user_id, :phone, :apartment_code, :street_name, :city, :district, :postal_code, :full_address, :company_name, :license_number, :vehicle_type, :availability, :profile_photo, NOW(), NOW())";

        $params = [
            'user_id' => $userId,
            'phone' => $data['phone'] ?? null,
            'apartment_code' => $data['apartment_code'] ?? null,
            'street_name' => $data['street_name'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'full_address' => $data['full_address'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'availability' => $data['availability'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null
        ];

        return $this->write($sql, $params);
    }

    /**
     * Update transporter profile
     */
    public function updateProfile($userId, $data)
    {
        $allowed = ['phone', 'apartment_code', 'street_name', 'city', 'district', 'postal_code', 'full_address', 'company_name', 'license_number', 'vehicle_type', 'availability', 'profile_photo'];
        $set = [];
        $params = ['user_id' => $userId];

        error_log("=== TransporterModel::updateProfile DEBUG ===");
        error_log("Incoming data: " . json_encode($data));

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $set[] = "$field = :$field";
                $params[$field] = $data[$field];
                error_log("Added field: $field = " . $data[$field]);
            }
        }

        if (empty($set)) {
            error_log("ERROR: No fields to update!");
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . ", updated_at = NOW() WHERE user_id = :user_id";
        error_log("SQL: " . $sql);
        error_log("Params: " . json_encode($params));

        $result = $this->write($sql, $params);
        error_log("Write result: " . ($result ? "SUCCESS" : "FAILED"));

        return $result;
    }

    /**
     * Update profile photo only
     */
    public function updateProfilePhoto($userId, $filename)
    {
        $sql = "UPDATE {$this->table} SET profile_photo = :profile_photo, updated_at = NOW() WHERE user_id = :user_id";
        return $this->write($sql, ['user_id' => $userId, 'profile_photo' => $filename]);
    }

    /**
     * Remove profile photo (set to NULL)
     */
    public function removeProfilePhoto($userId)
    {
        $sql = "UPDATE {$this->table} SET profile_photo = NULL, updated_at = NOW() WHERE user_id = :user_id";
        return $this->write($sql, ['user_id' => $userId]);
    }

    /**
     * Get old profile photo filename for deletion
     */
    public function getOldPhotoFilename($userId)
    {
        $sql = "SELECT profile_photo FROM {$this->table} WHERE user_id = :user_id";
        $result = $this->get_row($sql, ['user_id' => $userId]);
        return $result ? $result->profile_photo : null;
    }

    /**
     * Validate profile data
     */
    public function validateProfile($data)
    {
        $errors = [];

        // Validate name (if provided)
        if (!empty($data['name'])) {
            if (strlen($data['name']) < 2) {
                $errors['name'] = 'Full name must be at least 2 characters';
            } elseif (strlen($data['name']) > 100) {
                $errors['name'] = 'Full name is too long (max 100 characters)';
            } elseif (!preg_match('/^[a-zA-Z\s\-\.]+$/', $data['name'])) {
                $errors['name'] = 'Full name can only contain letters, spaces, hyphens, and dots';
            }
        }

        // Validate email (if provided)
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address';
            } elseif (strlen($data['email']) > 100) {
                $errors['email'] = 'Email is too long (max 100 characters)';
            }
        }

        // Validate phone (optional but if provided, must be valid)
        if (!empty($data['phone'])) {
            // Remove all non-digit characters for validation
            $cleanPhone = preg_replace('/[^0-9+]/', '', $data['phone']);

            if (!preg_match('/^(\+?94|0)[0-9]{9}$/', $cleanPhone)) {
                $errors['phone'] = 'Phone number must be a valid Sri Lankan number (e.g., +94XXXXXXXXX or 0XXXXXXXXX)';
            }
        }

        // Validate district (optional)
        if (!empty($data['district'])) {
            if (strlen($data['district']) > 100) {
                $errors['district'] = 'District name is too long (max 100 characters)';
            }
        }

        // Validate street_name (optional)
        if (!empty($data['street_name'])) {
            if (strlen($data['street_name']) > 150) {
                $errors['street_name'] = 'Street name is too long (max 150 characters)';
            }
        }

        // Validate city (optional)
        if (!empty($data['city'])) {
            if (strlen($data['city']) > 100) {
                $errors['city'] = 'City name is too long (max 100 characters)';
            }
        }

        // Validate postal_code (optional)
        if (!empty($data['postal_code'])) {
            if (!preg_match('/^[0-9]{5}$/', $data['postal_code'])) {
                $errors['postal_code'] = 'Postal code must be exactly 5 digits';
            }
        }

        // Validate apartment_code (optional)
        if (!empty($data['apartment_code'])) {
            if (strlen($data['apartment_code']) > 50) {
                $errors['apartment_code'] = 'Apartment code is too long (max 50 characters)';
            }
        }

        // Validate full_address (optional)
        if (!empty($data['full_address'])) {
            if (strlen($data['full_address']) > 500) {
                $errors['full_address'] = 'Additional address details are too long (max 500 characters)';
            }
        }

        // Validate company_name (if provided)
        if (!empty($data['company_name'])) {
            if (strlen($data['company_name']) > 255) {
                $errors['company_name'] = 'Company name is too long (max 255 characters)';
            }
        }

        // Validate license_number (optional)
        if (!empty($data['license_number'])) {
            if (strlen($data['license_number']) > 100) {
                $errors['license_number'] = 'License number is too long (max 100 characters)';
            }
        }

        // Validate vehicle_type (if provided)
        if (!empty($data['vehicle_type'])) {
            // Get valid vehicle types from database
            $vehicleTypeModel = new VehicleTypeModel();
            $activeTypes = $vehicleTypeModel->getActiveTypes();
            $validTypes = [];
            
            foreach ($activeTypes as $vType) {
                // Convert vehicle_name to slug (e.g., "Small Van" -> "smallvan")
                $validTypes[] = strtolower(str_replace(' ', '', $vType->vehicle_name));
            }
            
            if (!in_array(strtolower($data['vehicle_type']), $validTypes)) {
                $errors['vehicle_type'] = 'Please select a valid vehicle type';
            }
        }

        // Validate availability (optional)
        if (!empty($data['availability'])) {
            $validAvailabilities = ['available', 'not available', 'busy'];
            if (!in_array(strtolower($data['availability']), $validAvailabilities)) {
                $errors['availability'] = 'Availability must be one of: available, not available, busy';
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Validate password change request
     */
    public function validatePasswordChange($userId, $currentPassword, $newPassword, $confirmPassword)
    {
        $errors = [];

        // Get current password from database
        $sql = "SELECT password FROM {$this->userTable} WHERE id = :id";
        $user = $this->get_row($sql, ['id' => $userId]);

        if (!$user) {
            $errors['current'] = 'User not found';
            return $errors;
        }

        // Verify current password
        if (!password_verify($currentPassword, $user->password)) {
            $errors['current'] = 'Current password is incorrect';
        }

        // Validate new password
        if (empty($newPassword)) {
            $errors['new'] = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $errors['new'] = 'New password must be at least 8 characters long';
        } elseif (!preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            $errors['new'] = 'New password must include at least one letter and one number';
        }

        // Validate password confirmation
        if (empty($confirmPassword)) {
            $errors['confirm'] = 'Password confirmation is required';
        } elseif ($newPassword !== $confirmPassword) {
            $errors['confirm'] = 'Passwords do not match';
        }

        // Check if new password is same as old
        if (!empty($newPassword) && password_verify($newPassword, $user->password)) {
            $errors['new'] = 'New password must be different from current password';
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Change user password
     */
    public function changePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE {$this->userTable}
                SET password = :password,
                    password_updated_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id";

        return $this->write($sql, [
            'id' => $userId,
            'password' => $hashedPassword
        ]);
    }

    /**
     * Get available delivery requests for transporter based on their vehicle capacity
     * Filters by vehicle weight capacity range from vehicle_types table
     */
    public function getAvailableDeliveryRequests($transporterId, array $filters = [])
    {
        $transporterId = (int)$transporterId;
        if ($transporterId <= 0) {
            return [];
        }

        $location = strtolower(trim((string)($filters['location'] ?? '')));
        $location = preg_replace('/\s+/', ' ', $location);
        $maxDistance = isset($filters['max_distance']) ? (float)$filters['max_distance'] : 0;
        $maxWeight = isset($filters['max_weight']) ? (float)$filters['max_weight'] : 0;
        $minPayment = isset($filters['min_payment']) ? (float)$filters['min_payment'] : 0;

        // First, get transporter's active vehicles and their capacities
        $vehicleSql = "SELECT v.*, vt.min_weight_kg, vt.max_weight_kg, vt.vehicle_name
                      FROM vehicles v
                      INNER JOIN vehicle_types vt ON v.vehicle_type_id = vt.id
                      WHERE v.transporter_id = :transporter_id 
                      AND v.status = 'active'";
        
        $vehicles = $this->query($vehicleSql, ['transporter_id' => $transporterId]);
        
        // Ensure vehicles is an array
        if (!is_array($vehicles) || empty($vehicles)) {
            return []; // No active vehicles
        }

        // Get min and max weight capacity across all active vehicles
        $minCapacity = PHP_INT_MAX;
        $maxCapacity = 0;
        foreach ($vehicles as $vehicle) {
            if ($vehicle->min_weight_kg < $minCapacity) {
                $minCapacity = $vehicle->min_weight_kg;
            }
            if ($vehicle->max_weight_kg > $maxCapacity) {
                $maxCapacity = $vehicle->max_weight_kg;
            }
        }

        // Get pending delivery requests that match the transporter's vehicle capacity range
        $sql = "SELECT 
                    dr.*,
                    o.status as order_status,
                    vt.vehicle_name as required_vehicle_type,
                    bd.district_name as buyer_district_name,
                    fd.district_name as farmer_district_name
                FROM delivery_requests dr
                INNER JOIN orders o ON dr.order_id = o.id
                LEFT JOIN vehicle_types vt ON dr.required_vehicle_type_id = vt.id
                LEFT JOIN districts bd ON dr.buyer_district_id = bd.id
                LEFT JOIN districts fd ON dr.farmer_district_id = fd.id
                WHERE dr.status = 'pending'
                AND dr.total_weight_kg >= :min_weight
                AND dr.total_weight_kg <= :max_weight
                AND o.status IN ('pending', 'confirmed')";

        $params = [
            'min_weight' => $minCapacity,
            'max_weight' => $maxCapacity,
        ];

        if ($location !== '') {
            $sql .= " AND (
                        LOWER(TRIM(COALESCE(dr.farmer_city, ''))) LIKE :location_like
                        OR LOWER(TRIM(COALESCE(fd.district_name, ''))) LIKE :location_like
                    )";
            $params['location_like'] = '%' . $location . '%';
        }

        if ($maxDistance > 0) {
            $sql .= " AND dr.distance_km IS NOT NULL AND dr.distance_km <= :max_distance";
            $params['max_distance'] = $maxDistance;
        }

        if ($maxWeight > 0) {
            $sql .= " AND dr.total_weight_kg <= :max_weight_filter";
            $params['max_weight_filter'] = $maxWeight;
        }

        if ($minPayment > 0) {
            $sql .= " AND dr.shipping_fee >= :min_payment";
            $params['min_payment'] = $minPayment;
        }

        $sql .= " ORDER BY dr.created_at DESC";
        
        $result = $this->query($sql, $params);
        
        return is_array($result) ? $result : [];
    }

    /**
     * Get delivery requests by transporter ID
     */
    public function getMyDeliveryRequests($transporterId, $status = null)
    {
        $sql = "SELECT 
                    dr.*,
                    o.status as order_status,
                    vt.vehicle_name as required_vehicle_type,
                    bd.district_name as buyer_district_name,
                    fd.district_name as farmer_district_name
                FROM delivery_requests dr
                INNER JOIN orders o ON dr.order_id = o.id
                LEFT JOIN vehicle_types vt ON dr.required_vehicle_type_id = vt.id
                LEFT JOIN districts bd ON dr.buyer_district_id = bd.id
                LEFT JOIN districts fd ON dr.farmer_district_id = fd.id
                WHERE dr.transporter_id = :transporter_id";
        
        $params = ['transporter_id' => $transporterId];
        
        if ($status !== null) {
            $sql .= " AND dr.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY dr.created_at DESC";
        
        $result = $this->query($sql, $params);
        return is_array($result) ? $result : [];
    }

    /**
     * Accept a delivery request
     */
    public function acceptDeliveryRequest($requestId, $transporterId)
    {
        // First check if request is still available
        $checkSql = "SELECT * FROM delivery_requests WHERE id = :id AND status = 'pending'";
        $request = $this->get_row($checkSql, ['id' => $requestId]);
        
        if (!$request) {
            return false;
        }

        // Update the request
        $sql = "UPDATE delivery_requests 
                SET transporter_id = :transporter_id, 
                    status = 'accepted',
                    accepted_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id AND status = 'pending'";
        
        $result = $this->write($sql, [
            'id' => $requestId,
            'transporter_id' => $transporterId
        ]);

        // Also update the order status to 'processing'
        if ($result) {
            $updateOrderSql = "UPDATE orders SET status = 'processing' WHERE id = :order_id";
            $this->write($updateOrderSql, ['order_id' => $request->order_id]);
        }

        return $result;
    }

    /**
     * Update delivery request status
     */
    public function updateDeliveryStatus($requestId, $transporterId, $status)
    {
        $validStatuses = ['accepted', 'in_transit', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $sql = "UPDATE delivery_requests 
                SET status = :status,
                    updated_at = NOW()
                WHERE id = :id AND transporter_id = :transporter_id";
        
        $result = $this->write($sql, [
            'id' => $requestId,
            'transporter_id' => $transporterId,
            'status' => $status
        ]);

        // Update order status accordingly
        if ($result) {
            $orderStatus = 'processing';
            if ($status === 'in_transit') {
                $orderStatus = 'shipped';
            } elseif ($status === 'delivered') {
                $orderStatus = 'delivered';
            } elseif ($status === 'cancelled') {
                $orderStatus = 'cancelled';
            }

            $request = $this->get_row("SELECT order_id FROM delivery_requests WHERE id = :id", ['id' => $requestId]);
            if ($request) {
                $updateOrderSql = "UPDATE orders SET status = :status WHERE id = :order_id";
                $this->write($updateOrderSql, [
                    'status' => $orderStatus,
                    'order_id' => $request->order_id
                ]);
            }
        }

        return $result;
    }

    /**
     * Get delivery request details by ID
     */
    public function getDeliveryRequestById($requestId)
    {
        $sql = "SELECT 
                    dr.*,
                    o.status as order_status,
                    o.payment_method,
                    vt.vehicle_name as required_vehicle_type,
                    bd.district_name as buyer_district_name,
                    fd.district_name as farmer_district_name
                FROM delivery_requests dr
                INNER JOIN orders o ON dr.order_id = o.id
                LEFT JOIN vehicle_types vt ON dr.required_vehicle_type_id = vt.id
                LEFT JOIN districts bd ON dr.buyer_district_id = bd.id
                LEFT JOIN districts fd ON dr.farmer_district_id = fd.id
                WHERE dr.id = :id";
        
        return $this->get_row($sql, ['id' => $requestId]);
    }

    /**
     * Get transporter's earnings summary
     */
    public function getEarningsSummary($transporterId)
    {
        $sql = "SELECT 
                    COUNT(*) as total_deliveries,
                    SUM(CASE WHEN dr.status = 'delivered' THEN dr.shipping_fee ELSE 0 END) as total_earnings,
                    SUM(CASE WHEN dr.status = 'delivered' AND DATE(dr.updated_at) = CURDATE() THEN dr.shipping_fee ELSE 0 END) as today_earnings,
                    SUM(CASE WHEN dr.status = 'delivered' AND YEARWEEK(dr.updated_at, 1) = YEARWEEK(CURDATE(), 1) THEN dr.shipping_fee ELSE 0 END) as week_earnings,
                    SUM(CASE WHEN dr.status = 'delivered' AND YEAR(dr.updated_at) = YEAR(CURDATE()) AND MONTH(dr.updated_at) = MONTH(CURDATE()) THEN dr.shipping_fee ELSE 0 END) as month_earnings,
                    COUNT(CASE WHEN dr.status = 'accepted' THEN 1 END) as active_deliveries,
                    COUNT(CASE WHEN dr.status = 'delivered' THEN 1 END) as completed_deliveries
                FROM delivery_requests dr
                WHERE dr.transporter_id = :transporter_id";
        
        $result = $this->get_row($sql, ['transporter_id' => $transporterId]);
        
        // Return default values if no data found
        if (!$result) {
            return (object)[
                'total_deliveries' => 0,
                'total_earnings' => 0,
                'today_earnings' => 0,
                'week_earnings' => 0,
                'month_earnings' => 0,
                'active_deliveries' => 0,
                'completed_deliveries' => 0
            ];
        }
        
        return $result;
    }

    /**
     * Count transporter deliveries that are not completed.
     *
     * Business rule: transporter account deactivation is allowed only when
     * every assigned delivery is completed (delivered/completed).
     */
    public function countIncompleteDeliveries($transporterId)
    {
        $transporterId = (int)$transporterId;
        if ($transporterId <= 0) {
            return 0;
        }

        $sql = "SELECT COUNT(*) AS total
                FROM delivery_requests
                WHERE transporter_id = :transporter_id
                AND LOWER(COALESCE(status, '')) NOT IN ('delivered', 'completed')";

        $row = $this->get_row($sql, ['transporter_id' => $transporterId]);
        return (int)($row->total ?? 0);
    }

    /**
     * Migrate existing orders to delivery requests (run once)
     */
    public function migrateExistingOrders()
    {
        $details = [];
        $successCount = 0;
        $errorCount = 0;

        // Get all orders that don't have delivery requests yet
        $sql = "SELECT DISTINCT o.*, 
                GROUP_CONCAT(DISTINCT oi.farmer_id) as farmer_ids
                FROM orders o
                LEFT JOIN delivery_requests dr ON dr.order_id = o.id
                INNER JOIN order_items oi ON o.id = oi.order_id
                WHERE dr.id IS NULL
                GROUP BY o.id";

        $orders = $this->query($sql, []);

        if (empty($orders) || !is_array($orders)) {
            $details[] = "No orders found that need migration.";
            return ['success' => $successCount, 'errors' => $errorCount, 'details' => $details];
        }

        $details[] = "Found " . count($orders) . " orders to migrate.";

        foreach ($orders as $order) {
            try {
                // Get farmer IDs for this order
                $farmerIds = explode(',', $order->farmer_ids);
                
                foreach ($farmerIds as $farmerId) {
                    // Get buyer details
                    $buyerSql = "SELECT u.name, bp.phone, bp.apartment_code, bp.street_name, bp.city, bp.district
                                FROM users u
                                LEFT JOIN buyer_profiles bp ON u.id = bp.user_id
                                WHERE u.id = :buyer_id";
                    $buyer = $this->get_row($buyerSql, ['buyer_id' => $order->buyer_id]);

                    if (!$buyer) {
                        $details[] = "✗ Order #{$order->id}: Buyer ID {$order->buyer_id} not found in users table";
                        $errorCount++;
                        continue;
                    }

                    // Get farmer details
                    $farmerSql = "SELECT u.name, fp.phone, fp.full_address, fp.district
                                FROM users u
                                LEFT JOIN farmer_profiles fp ON u.id = fp.user_id
                                WHERE u.id = :farmer_id";
                    $farmer = $this->get_row($farmerSql, ['farmer_id' => $farmerId]);

                    if (!$farmer) {
                        $details[] = "✗ Order #{$order->id}: Farmer ID {$farmerId} not found in users table";
                        $errorCount++;
                        continue;
                    }

                    // Get district IDs
                    $buyerDistrictId = $this->getDistrictIdByName($buyer->district ?? 'Colombo');
                    $farmerDistrictId = $this->getDistrictIdByName($farmer->district ?? 'Colombo');

                    // Calculate weight for this farmer's items
                    $weightSql = "SELECT COALESCE(SUM(weight_kg), 5.0) as total_weight
                                 FROM order_items
                                 WHERE order_id = :order_id AND farmer_id = :farmer_id";
                    $weightResult = $this->get_row($weightSql, [
                        'order_id' => $order->id,
                        'farmer_id' => $farmerId
                    ]);
                    $totalWeight = $weightResult ? $weightResult->total_weight : 5.0;

                    // Determine required vehicle type based on weight
                    $vehicleTypeSql = "SELECT id FROM vehicle_types 
                                      WHERE min_weight_kg <= :weight 
                                      AND max_weight_kg >= :weight 
                                      AND is_active = 1 
                                      ORDER BY min_weight_kg ASC LIMIT 1";
                    $vehicleTypeResult = $this->query($vehicleTypeSql, ['weight' => $totalWeight]);
                    $requiredVehicleTypeId = ($vehicleTypeResult && !empty($vehicleTypeResult)) ? $vehicleTypeResult[0]->id : null;

                    // Prepare buyer address
                    $buyerAddress = trim(($buyer->apartment_code ?? '') . ', ' . 
                                       ($buyer->street_name ?? '') . ', ' . 
                                       ($buyer->city ?? ''));
                    if (empty(trim($buyerAddress, ', '))) {
                        $buyerAddress = $order->delivery_address ?? 'Not provided';
                    }

                    // Insert delivery request
                    $insertSql = "INSERT INTO delivery_requests (
                        order_id, buyer_id, buyer_name, buyer_phone, buyer_address, buyer_city, buyer_district_id,
                        farmer_id, farmer_name, farmer_phone, farmer_address, farmer_city, farmer_district_id,
                        total_weight_kg, shipping_fee, distance_km, required_vehicle_type_id, status, 
                        created_at, updated_at
                    ) VALUES (
                        :order_id, :buyer_id, :buyer_name, :buyer_phone, :buyer_address, :buyer_city, :buyer_district_id,
                        :farmer_id, :farmer_name, :farmer_phone, :farmer_address, :farmer_city, :farmer_district_id,
                        :total_weight_kg, :shipping_fee, :distance_km, :required_vehicle_type_id, 'pending',
                        NOW(), NOW()
                    )";

                    $params = [
                        'order_id' => $order->id,
                        'buyer_id' => $order->buyer_id,
                        'buyer_name' => $buyer->name ?? 'Unknown',
                        'buyer_phone' => $buyer->phone ?? ($order->delivery_phone ?? ''),
                        'buyer_address' => $buyerAddress,
                        'buyer_city' => $buyer->city ?? ($order->delivery_city ?? ''),
                        'buyer_district_id' => $buyerDistrictId,
                        'farmer_id' => $farmerId,
                        'farmer_name' => $farmer->name ?? 'Unknown',
                        'farmer_phone' => $farmer->phone ?? '',
                        'farmer_address' => $farmer->full_address ?? '',
                        'farmer_city' => $farmer->district ?? '',
                        'farmer_district_id' => $farmerDistrictId,
                        'total_weight_kg' => $totalWeight,
                        'shipping_fee' => $order->shipping_cost ?? 0,
                        'distance_km' => null,
                        'required_vehicle_type_id' => $requiredVehicleTypeId
                    ];

                    try {
                        // Use a direct PDO connection to get better error info
                        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
                        $con = new PDO($string, DBUSER, DBPASS);
                        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stm = $con->prepare($insertSql);
                        $result = $stm->execute($params);

                        if ($result) {
                            $details[] = "✓ Order #{$order->id} (Farmer ID: {$farmerId}): Created - {$totalWeight}kg, Rs.{$order->shipping_cost}";
                            $successCount++;
                        } else {
                            $errorInfo = $stm->errorInfo();
                            $details[] = "✗ Order #{$order->id}: Insert failed - " . ($errorInfo[2] ?? 'Unknown error');
                            $errorCount++;
                        }
                    } catch (PDOException $e) {
                        $details[] = "✗ Order #{$order->id}: " . $e->getMessage();
                        $errorCount++;
                    }
                }

            } catch (Exception $e) {
                $details[] = "Order #{$order->id}: ERROR - " . $e->getMessage();
                $errorCount++;
            }
        }

        return ['success' => $successCount, 'errors' => $errorCount, 'details' => $details];
    }

    /**
     * Get district ID by name
     */
    private function getDistrictIdByName($districtName)
    {
        $sql = "SELECT id FROM districts WHERE district_name = :name LIMIT 1";
        $result = $this->query($sql, ['name' => $districtName]);
        if ($result && is_array($result) && !empty($result)) {
            return $result[0]->id;
        }
        // Default to Colombo if not found
        $result = $this->query($sql, ['name' => 'Colombo']);
        return ($result && is_array($result) && !empty($result)) ? $result[0]->id : 5;
    }
}
