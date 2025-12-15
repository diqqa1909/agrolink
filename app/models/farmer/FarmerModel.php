<?php

class FarmerModel
{
    use Database;

    protected $table = 'farmer_profiles';
    protected $userTable = 'users';

    /**
     * Get farmer profile by user ID
     */
    public function getProfileByUserId($userId)
    {
        $sql = "SELECT fp.*, u.name, u.email 
                FROM {$this->table} fp
                LEFT JOIN {$this->userTable} u ON u.id = fp.user_id
                WHERE fp.user_id = :user_id";

        return $this->get_row($sql, ['user_id' => $userId]);
    }

    /**
     * Create farmer profile
     */
    public function createProfile($userId, $data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, phone, district, crops_selling, full_address, profile_photo, created_at, updated_at)
                VALUES (:user_id, :phone, :district, :crops_selling, :full_address, :profile_photo, NOW(), NOW())";

        $params = [
            'user_id' => $userId,
            'phone' => $data['phone'] ?? null,
            'district' => $data['district'] ?? null,
            'crops_selling' => $data['crops_selling'] ?? null,
            'full_address' => $data['full_address'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null
        ];

        return $this->write($sql, $params);
    }

    /**
     * Update farmer profile
     */
    public function updateProfile($userId, $data)
    {
        $allowed = ['phone', 'district', 'crops_selling', 'full_address', 'profile_photo'];
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

        // Validate phone (optional but if provided, must be valid)
        if (!empty($data['phone'])) {
            // Remove all non-digit characters for validation
            $cleanPhone = preg_replace('/[^0-9+]/', '', $data['phone']);

            if (!preg_match('/^(\+?94|0)[0-9]{9}$/', $cleanPhone)) {
                $errors['phone'] = 'Phone number must be a valid Sri Lankan number (e.g., +94XXXXXXXXX or 0XXXXXXXXX)';
            }
        }

        // Validate district (if provided)
        if (!empty($data['district'])) {
            $validDistricts = [
                'Ampara',
                'Anuradhapura',
                'Badulla',
                'Batticaloa',
                'Colombo',
                'Galle',
                'Gampaha',
                'Jaffna',
                'Kalutara',
                'Kandy',
                'Kegalle',
                'Kilinochchi',
                'Kurunegala',
                'Mannar',
                'Matale',
                'Matara',
                'Mullaitivu',
                'Nuwara Eliya',
                'Polonnaruwa',
                'Puttalam',
                'Ratnapura',
                'Trincomalee',
                'Vavuniya'
            ];

            if (!in_array($data['district'], $validDistricts)) {
                $errors['district'] = 'Please select a valid district';
            }
        }

        // Validate crops selling (optional but if provided, must be valid)
        if (!empty($data['crops_selling'])) {
            if (strlen($data['crops_selling']) < 3) {
                $errors['crops_selling'] = 'Crops information must be at least 3 characters';
            } elseif (strlen($data['crops_selling']) > 500) {
                $errors['crops_selling'] = 'Crops information is too long (max 500 characters)';
            }
        }

        // Validate full address (optional but if provided, must be valid)
        if (!empty($data['full_address'])) {
            if (strlen($data['full_address']) < 5) {
                $errors['full_address'] = 'Address must be at least 5 characters';
            } elseif (strlen($data['full_address']) > 500) {
                $errors['full_address'] = 'Address is too long (max 500 characters)';
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

        $sql = "UPDATE {$this->userTable} SET password = :password WHERE id = :id";

        return $this->write($sql, [
            'id' => $userId,
            'password' => $hashedPassword
        ]);
    }
}
