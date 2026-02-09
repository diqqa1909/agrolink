<?php

class BuyerProfileModel
{
    use Database;

    protected $table = 'buyer_profiles';
    protected $userTable = 'users';

    /**
     * Get buyer profile by user ID
     */
    public function getProfileByUserId($userId)
    {
        $sql = "SELECT bp.*, u.name, u.email 
                FROM {$this->table} bp
                LEFT JOIN {$this->userTable} u ON u.id = bp.user_id
                WHERE bp.user_id = :user_id";

        $result = $this->get_row($sql, ['user_id' => $userId]);
        
        // get_row returns false if no result
        return $result !== false ? $result : false;
    }

    /**
     * Create buyer profile
     */
    public function createProfile($userId, $data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, phone, apartment_code, street_name, city, district, postal_code, created_at, updated_at)
                VALUES (:user_id, :phone, :apartment_code, :street_name, :city, :district, :postal_code, NOW(), NOW())";

        $params = [
            'user_id' => $userId,
            'phone' => $data['phone'] ?? null,
            'apartment_code' => $data['apartment_code'] ?? null,
            'street_name' => $data['street_name'] ?? null,
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'postal_code' => $data['postal_code'] ?? null
        ];

        return $this->write($sql, $params);
    }

    /**
     * Update buyer profile
     */
    public function updateProfile($userId, $data)
    {
        $allowed = ['phone', 'apartment_code', 'street_name', 'city', 'district', 'postal_code', 'profile_photo'];
        $set = [];
        $params = ['user_id' => $userId];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $set[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($set)) {
            return false;
        }

        // First check if profile exists
        $checkSql = "SELECT id FROM {$this->table} WHERE user_id = :user_id";
        $profileExists = $this->get_row($checkSql, ['user_id' => $userId]);
        
        if (!$profileExists) {
            // Profile doesn't exist, create it with the new data
            $data['user_id'] = $userId;
            return $this->createProfile($userId, $data);
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . ", updated_at = NOW() WHERE user_id = :user_id";

        $result = $this->write($sql, $params);
        
        // write() returns:
        // - true on successful UPDATE/DELETE (when lastInsertId is 0)
        // - insert ID (int > 0) on successful INSERT
        // - false on failure
        // So for UPDATE: true/1 = success, false = failure
        return $result !== false ? true : false;
    }

    /**
     * Check if buyer has delivery details
     */
    public function hasDeliveryDetails($userId)
    {
        $profile = $this->getProfileByUserId($userId);
        if (!$profile) {
            return false;
        }
        
        return !empty($profile->phone) && !empty($profile->street_name) && !empty($profile->city) && !empty($profile->postal_code);
    }

    /**
     * Validate profile data
     */
    public function validateProfile($data)
    {
        $errors = [];

        // Validate required fields
        $requiredFields = ['street_name', 'city', 'postal_code', 'district'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate phone
        if (!empty($data['phone'])) {
            $cleanPhone = str_replace([' ', '-', '(', ')'], '', $data['phone']);
            if (!preg_match('/^(\+94|0)\d{9}$/', $cleanPhone)) {
                $errors['phone'] = 'Invalid phone number format';
            }
        }

        // Validate street name
        if (!empty($data['street_name'])) {
            if (strlen($data['street_name']) < 3) {
                $errors['street_name'] = 'Street name must be at least 3 characters';
            }
        }

        // Validate city
        if (!empty($data['city'])) {
            if (strlen($data['city']) < 2) {
                $errors['city'] = 'City must be at least 2 characters';
            }
        }

        // Validate postal code
        if (!empty($data['postal_code'])) {
            if (!preg_match('/^\d{5}$/', $data['postal_code'])) {
                $errors['postal_code'] = 'Postal code must be 5 digits';
            }
        }

        // Validate apartment code (optional but if provided must be valid)
        if (!empty($data['apartment_code'])) {
            if (strlen($data['apartment_code']) < 1) {
                $errors['apartment_code'] = 'Apartment code is invalid';
            }
        }

        // Validate district
        if (!empty($data['district'])) {
            $validDistricts = [
                'Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha',
                'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar',
                'Matale', 'Matara', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam',
                'Ratnapura', 'Trincomalee', 'Vavuniya'
            ];
            if (!in_array($data['district'], $validDistricts)) {
                $errors['district'] = 'Invalid district selected';
            }
        }

        return empty($errors) ? true : $errors;
    }
}

