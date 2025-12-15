<?php

class RequestModel
{
    use Database;

    protected $table = 'requests';

    /**
     * Get all requests for a user with product details
     */
    public function getByUserId($user_id)
    {
        $sql = "SELECT r.*, p.name as product_name, p.price as product_price, p.image as product_image,
                       u.name as farmer_name
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON p.farmer_id = u.id
                WHERE r.user_id = :user_id 
                ORDER BY r.created_at DESC";

        $result = $this->query($sql, ['user_id' => $user_id]);
        return is_array($result) ? $result : [];
    }

    /**
     * Get a specific request by ID
     */
    public function getById($id, $user_id = null)
    {
        $sql = "SELECT r.*, p.name as product_name, p.price as product_price, p.image as product_image,
                       u.name as farmer_name
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON p.farmer_id = u.id
                WHERE r.id = :id";
        
        $params = ['id' => $id];
        
        if ($user_id !== null) {
            $sql .= " AND r.user_id = :user_id";
            $params['user_id'] = $user_id;
        }
        
        $sql .= " LIMIT 1";
        
        $result = $this->query($sql, $params);
        return (is_array($result) && !empty($result)) ? $result[0] : null;
    }

    /**
     * Create a new request
     */
    public function create($data)
    {
        // Handle NULL product_id properly
        $product_id = isset($data['product_id']) && $data['product_id'] !== '' && $data['product_id'] !== '0' 
            ? (int)$data['product_id'] 
            : null;
        
        $sql = "INSERT INTO {$this->table} 
                (user_id, product_id, quantity, target_price, details, status) 
                VALUES (:user_id, :product_id, :quantity, :target_price, :details, :status)";

        $params = [
            'user_id' => (int)$data['user_id'],
            'product_id' => $product_id,
            'quantity' => (float)$data['quantity'],
            'target_price' => (isset($data['target_price']) && $data['target_price'] !== '' && $data['target_price'] !== null) 
                ? (float)$data['target_price'] 
                : null,
            'details' => (isset($data['details']) && $data['details'] !== '') 
                ? trim($data['details']) 
                : null,
            'status' => isset($data['status']) ? $data['status'] : 'pending'
        ];

        $result = $this->write($sql, $params);
        
        // write() returns the insert ID (int) if successful, true for UPDATE/DELETE, or false on failure
        return $result;
    }

    /**
     * Update a request
     */
    public function update($id, $user_id, $data)
    {
        $allowed = ['product_id', 'quantity', 'target_price', 'details', 'status'];
        $set = [];
        $params = ['id' => $id, 'user_id' => $user_id];
        
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $set[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($set)) {
            return false;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . 
               " WHERE id = :id AND user_id = :user_id";
        
        $result = $this->write($sql, $params);
        return $result !== false;
    }

    /**
     * Delete a request
     */
    public function delete($id, $user_id)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE id = :id AND user_id = :user_id";

        $result = $this->write($sql, [
            'id' => $id,
            'user_id' => $user_id
        ]);

        return $result !== false;
    }

    /**
     * Get requests by status
     */
    public function getByStatus($user_id, $status)
    {
        $sql = "SELECT r.*, p.name as product_name, p.price as product_price, p.image as product_image,
                       u.name as farmer_name
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON p.farmer_id = u.id
                WHERE r.user_id = :user_id AND r.status = :status
                ORDER BY r.created_at DESC";

        $result = $this->query($sql, [
            'user_id' => $user_id,
            'status' => $status
        ]);
        
        return is_array($result) ? $result : [];
    }

    /**
     * Get all pending requests for farmers (all farmers can see all pending requests)
     */
    public function getAllPending()
    {
        $sql = "SELECT r.*, 
                       p.name as product_name, 
                       p.price as product_price, 
                       p.image as product_image,
                       u.name as buyer_name,
                       u.email as buyer_email
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.status = 'pending'
                ORDER BY r.created_at DESC";

        $result = $this->query($sql, []);
        return is_array($result) ? $result : [];
    }

    /**
     * Accept a request (update status to accepted)
     */
    public function acceptRequest($id, $farmer_id)
    {
        // First check if request exists and is pending
        $request = $this->getById($id);
        if (!$request || $request->status !== 'pending') {
            return false;
        }

        $sql = "UPDATE {$this->table} 
                SET status = 'accepted' 
                WHERE id = :id AND status = 'pending'";

        $result = $this->write($sql, ['id' => $id]);
        return $result !== false;
    }

    /**
     * Validate request data
     */
    public function validate($data)
    {
        $errors = [];

        if (empty($data['quantity']) || $data['quantity'] <= 0) {
            $errors['quantity'] = 'Quantity is required and must be greater than 0';
        }

        if (isset($data['target_price']) && $data['target_price'] < 0) {
            $errors['target_price'] = 'Target price cannot be negative';
        }

        return $errors;
    }
}

