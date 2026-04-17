<?php

class TransporterModel
{
    use Database;

    protected $table = 'transporter_profiles';
    protected $userTable = 'users';
    private $existingColumns = null;

    private function getExistingColumns()
    {
        if (is_array($this->existingColumns)) {
            return $this->existingColumns;
        }

        $rows = $this->query("SHOW COLUMNS FROM {$this->table}", []);
        $this->existingColumns = [];

        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!empty($row->Field)) {
                    $this->existingColumns[] = $row->Field;
                }
            }
        }

        return $this->existingColumns;
    }

    private function hasColumn($column)
    {
        return in_array($column, $this->getExistingColumns(), true);
    }

    /**
     * Get transporter profile by user ID
     */
    public function getProfileByUserId($userId)
    {
        $select = ["tp.*", "u.name", "u.email"];
        $joins = ["LEFT JOIN {$this->userTable} u ON u.id = tp.user_id"];

        if ($this->hasColumn('district_id')) {
            $select[] = "d.district_name AS district_name";
            $joins[] = "LEFT JOIN districts d ON d.id = tp.district_id";
        }

        if ($this->hasColumn('town_id')) {
            $select[] = "t.town_name AS town_name";
            $joins[] = "LEFT JOIN towns t ON t.id = tp.town_id";
        }

        if ($this->hasColumn('current_district_id')) {
            $select[] = "cd.district_name AS current_district_name";
            $joins[] = "LEFT JOIN districts cd ON cd.id = tp.current_district_id";
        }

        if ($this->hasColumn('current_town_id')) {
            $select[] = "ct.town_name AS current_town_name";
            $joins[] = "LEFT JOIN towns ct ON ct.id = tp.current_town_id";
        }

        $sql = "SELECT " . implode(', ', $select) . "
                FROM {$this->table} tp
                " . implode("\n", $joins) . "
                WHERE tp.user_id = :user_id";

        return $this->get_row($sql, ['user_id' => $userId]);
    }

    /**
     * Create transporter profile
     */
    public function createProfile($userId, $data)
    {
        $columns = ['user_id'];
        $params = ['user_id' => $userId];

        $fieldMap = [
            'phone' => isset($data['phone']) ? normalize_phone_number($data['phone']) : null,
            'apartment_code' => $data['apartment_code'] ?? null,
            'street_name' => $data['street_name'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'district_id' => !empty($data['district_id']) ? (int)$data['district_id'] : null,
            'town_id' => !empty($data['town_id']) ? (int)$data['town_id'] : null,
            'postal_code' => $data['postal_code'] ?? null,
            'full_address' => $data['full_address'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'availability' => $data['availability'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null,
            'current_district_id' => !empty($data['current_district_id']) ? (int)$data['current_district_id'] : (!empty($data['district_id']) ? (int)$data['district_id'] : null),
            'current_town_id' => !empty($data['current_town_id']) ? (int)$data['current_town_id'] : (!empty($data['town_id']) ? (int)$data['town_id'] : null),
            'current_location_updated_at' => (!empty($data['current_district_id']) || !empty($data['district_id'])) ? date('Y-m-d H:i:s') : null,
        ];

        foreach ($fieldMap as $field => $value) {
            if ($this->hasColumn($field)) {
                $columns[] = $field;
                $params[$field] = $value;
            }
        }

        if ($this->hasColumn('created_at')) {
            $columns[] = 'created_at';
        }

        if ($this->hasColumn('updated_at')) {
            $columns[] = 'updated_at';
        }

        $values = array_map(function ($column) {
            return in_array($column, ['created_at', 'updated_at'], true) ? 'NOW()' : ':' . $column;
        }, $columns);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $values) . ")";

        return $this->write($sql, $params);
    }

    /**
     * Update transporter profile
     */
    public function updateProfile($userId, $data)
    {
        $allowed = [
            'phone',
            'apartment_code',
            'street_name',
            'city',
            'district',
            'district_id',
            'town_id',
            'postal_code',
            'full_address',
            'company_name',
            'license_number',
            'license_verified',
            'license_verified_at',
            'vehicle_type',
            'availability',
            'profile_photo',
            'current_district_id',
            'current_town_id',
        ];
        $set = [];
        $params = ['user_id' => $userId];

        foreach ($allowed as $field) {
            if ($this->hasColumn($field) && array_key_exists($field, $data)) {
                $set[] = "$field = :$field";
                $params[$field] = $field === 'phone'
                    ? normalize_phone_number($data[$field])
                    : $data[$field];
            }
        }

        if ($this->hasColumn('current_location_updated_at') && (array_key_exists('current_district_id', $data) || array_key_exists('current_town_id', $data))) {
            $set[] = "current_location_updated_at = NOW()";
        }

        if (empty($set)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set);
        if ($this->hasColumn('updated_at')) {
            $sql .= ", updated_at = NOW()";
        }
        $sql .= " WHERE user_id = :user_id";

        return $this->write($sql, $params);
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
        $locationModel = new LocationModel();

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

        // Validate phone (required, exactly 10 digits)
        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required';
        } elseif (!is_valid_phone_number($data['phone'])) {
            $errors['phone'] = 'Phone number must contain exactly 10 digits';
        }

        if (empty($data['street_name'])) {
            $errors['street_name'] = 'Street name is required';
        } elseif (strlen($data['street_name']) > 150) {
            $errors['street_name'] = 'Street name is too long (max 150 characters)';
        }

        if (empty($data['district_id'])) {
            $errors['district'] = 'District is required';
        } else {
            $district = $locationModel->getDistrictById((int)$data['district_id']);
            if (!$district) {
                $errors['district'] = 'Please select a valid district';
            }
        }

        // town_id is optional — some districts may have no towns registered
        if (!empty($data['town_id']) && !empty($data['district_id'])) {
            $town = $locationModel->getTownById((int)$data['town_id']);
            if (!$town || (int)$town->district_id !== (int)$data['district_id']) {
                $errors['city'] = 'Please select a valid town/city for the chosen district';
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
        if (empty($data['license_number'])) {
            $errors['license_number'] = 'License number is required';
        } elseif (!preg_match('/^[A-Z0-9\-\/]{5,30}$/i', (string)$data['license_number'])) {
            $errors['license_number'] = 'License number format is invalid';
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

    public function getProfileCompletionStatus($userId)
    {
        $profile = $this->getProfileByUserId($userId);

        $missing = [];
        if (!$profile) {
            $missing = ['phone', 'street_name', 'district', 'city', 'license_number'];
        } else {
            if (!is_valid_phone_number($profile->phone ?? '')) {
                $missing[] = 'phone';
            }
            if (empty($profile->street_name)) {
                $missing[] = 'street_name';
            }
            $hasDistrict = $this->hasColumn('district_id') ? !empty($profile->district_id) : !empty($profile->district);
            if (!$hasDistrict) {
                $missing[] = 'district';
            }
            $hasTown = $this->hasColumn('town_id') ? !empty($profile->town_id) : !empty($profile->city);
            if (!$hasTown) {
                $missing[] = 'city';
            }
            if (empty($profile->license_number)) {
                $missing[] = 'license_number';
            }
        }

        return [
            'complete' => empty($missing),
            'missing' => $missing,
        ];
    }

    public function updateCurrentLocation($userId, $districtId, $townId)
    {
        if (!$this->hasColumn('current_district_id') || !$this->hasColumn('current_town_id')) {
            return false;
        }

        $locationModel = new LocationModel();
        $district = $locationModel->getDistrictById((int)$districtId);
        $town = $locationModel->getTownById((int)$townId);

        if (!$district || !$town || (int)$town->district_id !== (int)$districtId) {
            return false;
        }

        $sql = "UPDATE {$this->table}
                SET current_district_id = :current_district_id,
                    current_town_id = :current_town_id,
                    current_location_updated_at = NOW(),
                    updated_at = NOW()
                WHERE user_id = :user_id";

        return $this->write($sql, [
            'current_district_id' => (int)$districtId,
            'current_town_id' => (int)$townId,
            'user_id' => (int)$userId,
        ]);
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
        $sql = "SELECT 
                    dr.*,
                    o.status as order_status,
                    vt.vehicle_name as required_vehicle_type,
                    bd.district_name as buyer_district_name,
                    fd.district_name as farmer_district_name,
                    bt.town_name as buyer_town_name,
                    ft.town_name as farmer_town_name
                FROM delivery_requests dr
                INNER JOIN orders o ON dr.order_id = o.id
                LEFT JOIN vehicle_types vt ON dr.required_vehicle_type_id = vt.id
                LEFT JOIN districts bd ON dr.buyer_district_id = bd.id
                LEFT JOIN districts fd ON dr.farmer_district_id = fd.id
                LEFT JOIN towns bt ON dr.buyer_town_id = bt.id
                LEFT JOIN towns ft ON dr.farmer_town_id = ft.id
                WHERE dr.status = 'pending'
                AND o.status IN ('pending', 'confirmed')
                AND EXISTS (
                    SELECT 1
                    FROM vehicles v
                    INNER JOIN vehicle_types candidate_type ON candidate_type.id = v.vehicle_type_id
                    WHERE v.transporter_id = :transporter_id
                    AND v.status = 'active'
                    AND dr.total_weight_kg >= candidate_type.min_weight_kg
                    AND dr.total_weight_kg <= candidate_type.max_weight_kg
                )
                ORDER BY dr.created_at DESC";
        
        $result = $this->query($sql, [
            'transporter_id' => (int)$transporterId,
        ]);
        
        return is_array($result) ? $result : [];
    }

    /**
     * Get delivery requests by transporter ID
     */
    public function getMyDeliveryRequests($transporterId, $status = null, $vehicleId = null)
    {
        $sql = "SELECT 
                    dr.*,
                    o.status as order_status,
                    vt.vehicle_name as required_vehicle_type,
                    bd.district_name as buyer_district_name,
                    fd.district_name as farmer_district_name,
                    bt.town_name as buyer_town_name,
                    ft.town_name as farmer_town_name,
                    v.registration as vehicle_registration,
                    v.model as vehicle_model
                FROM delivery_requests dr
                INNER JOIN orders o ON dr.order_id = o.id
                LEFT JOIN vehicle_types vt ON dr.required_vehicle_type_id = vt.id
                LEFT JOIN districts bd ON dr.buyer_district_id = bd.id
                LEFT JOIN districts fd ON dr.farmer_district_id = fd.id
                LEFT JOIN towns bt ON dr.buyer_town_id = bt.id
                LEFT JOIN towns ft ON dr.farmer_town_id = ft.id
                LEFT JOIN vehicles v ON v.id = dr.vehicle_id
                WHERE dr.transporter_id = :transporter_id";

        $params = ['transporter_id' => $transporterId];

        if ($status !== null) {
            $sql .= " AND dr.status = :status";
            $params['status'] = $status;
        }

        if ($vehicleId !== null && (int)$vehicleId > 0) {
            $sql .= " AND dr.vehicle_id = :vehicle_id";
            $params['vehicle_id'] = (int)$vehicleId;
        }

        $sql .= " ORDER BY dr.created_at DESC";

        $result = $this->query($sql, $params);
        return is_array($result) ? $result : [];
    }

    /**
     * Accept a delivery request
     */
    public function acceptDeliveryRequest($requestId, $transporterId, $vehicleId)
    {
        // First check if request is still available
        $checkSql = "SELECT * FROM delivery_requests WHERE id = :id AND status = 'pending'";
        $request = $this->get_row($checkSql, ['id' => $requestId]);

        if (!$request) {
            return false;
        }

        $vehicleSql = "SELECT v.id
                       FROM vehicles v
                       INNER JOIN vehicle_types vt ON vt.id = v.vehicle_type_id
                       WHERE v.id = :vehicle_id
                       AND v.transporter_id = :transporter_id
                       AND v.status = 'active'
                       AND :weight BETWEEN vt.min_weight_kg AND vt.max_weight_kg
                       LIMIT 1";
        $vehicle = $this->get_row($vehicleSql, [
            'vehicle_id' => (int)$vehicleId,
            'transporter_id' => (int)$transporterId,
            'weight' => (float)$request->total_weight_kg,
        ]);

        if (!$vehicle) {
            return false;
        }

        // Update the request
        $sql = "UPDATE delivery_requests 
                SET transporter_id = :transporter_id, 
                    vehicle_id = :vehicle_id,
                    status = 'accepted',
                    accepted_at = NOW(),
                    updated_at = NOW()
                WHERE id = :id AND status = 'pending'";

        $result = $this->write($sql, [
            'id' => $requestId,
            'transporter_id' => $transporterId,
            'vehicle_id' => $vehicleId,
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
                    delivered_at = CASE WHEN :status = 'delivered' THEN NOW() ELSE delivered_at END,
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
                    fd.district_name as farmer_district_name,
                    bt.town_name as buyer_town_name,
                    ft.town_name as farmer_town_name,
                    v.registration as vehicle_registration,
                    v.model as vehicle_model
                FROM delivery_requests dr
                INNER JOIN orders o ON dr.order_id = o.id
                LEFT JOIN vehicle_types vt ON dr.required_vehicle_type_id = vt.id
                LEFT JOIN districts bd ON dr.buyer_district_id = bd.id
                LEFT JOIN districts fd ON dr.farmer_district_id = fd.id
                LEFT JOIN towns bt ON dr.buyer_town_id = bt.id
                LEFT JOIN towns ft ON dr.farmer_town_id = ft.id
                LEFT JOIN vehicles v ON v.id = dr.vehicle_id
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

    public function getPerformanceMetrics($transporterId)
    {
        $deliveryMetrics = $this->get_row(
            "SELECT
                COUNT(*) AS completed_deliveries,
                SUM(CASE WHEN delivered_at IS NOT NULL AND expected_delivery_at IS NOT NULL AND delivered_at <= expected_delivery_at THEN 1 ELSE 0 END) AS on_time_deliveries
             FROM delivery_requests
             WHERE transporter_id = :transporter_id
             AND status = 'delivered'",
            ['transporter_id' => (int)$transporterId]
        );

        $feedbackMetrics = $this->get_row(
            "SELECT
                COUNT(*) AS feedback_count,
                AVG(rating) AS average_rating,
                SUM(CASE WHEN satisfaction_status IN ('satisfied', 'very_satisfied') THEN 1 ELSE 0 END) AS satisfied_count
             FROM transporter_feedback
             WHERE transporter_id = :transporter_id",
            ['transporter_id' => (int)$transporterId]
        );

        $summary = $this->getEarningsSummary($transporterId);
        $completed = (int)($deliveryMetrics->completed_deliveries ?? 0);
        $onTime = (int)($deliveryMetrics->on_time_deliveries ?? 0);
        $feedbackCount = (int)($feedbackMetrics->feedback_count ?? 0);
        $totalEarnings = (float)($summary->total_earnings ?? 0);

        return (object)[
            'completed_deliveries' => $completed,
            'average_rating' => round((float)($feedbackMetrics->average_rating ?? 0), 2),
            'on_time_delivery_rate' => $completed > 0 ? round(($onTime / $completed) * 100, 2) : 0,
            'customer_satisfaction_rate' => $feedbackCount > 0 ? round((((int)($feedbackMetrics->satisfied_count ?? 0)) / $feedbackCount) * 100, 2) : 0,
            'earnings_per_delivery' => $completed > 0 ? round($totalEarnings / $completed, 2) : 0,
            'feedback_count' => $feedbackCount,
        ];
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
