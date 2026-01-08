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
        
        // get_row returns 1 if no result, so check if it's actually a row
        return (is_object($result) && isset($result->id)) ? $result : false;
    }

    /**
     * Create buyer profile
     */
    public function createProfile($userId, $data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, phone, city, delivery_address, created_at, updated_at)
                VALUES (:user_id, :phone, :city, :delivery_address, NOW(), NOW())";

        $params = [
            'user_id' => $userId,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'delivery_address' => $data['delivery_address'] ?? null
        ];

        return $this->write($sql, $params);
    }

    /**
     * Update buyer profile
     */
    public function updateProfile($userId, $data)
    {
        $allowed = ['phone', 'city', 'delivery_address'];
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

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . ", updated_at = NOW() WHERE user_id = :user_id";

        $result = $this->write($sql, $params);
        
        // write() returns:
        // - true on successful UPDATE/DELETE (when lastInsertId is 0)
        // - insert ID (int > 0) on successful INSERT
        // - 1 on failure
        // So for UPDATE: true = success, 1 = failure
        return $result;
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
        
        return !empty($profile->phone) && !empty($profile->city) && !empty($profile->delivery_address);
    }
}

