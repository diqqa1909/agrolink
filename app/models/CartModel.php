<?php
class CartModel {
    use Model;

    protected $table = 'cart';
    protected $allowedColumns = [
        'user_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'farmer_name',
        'farmer_location',
        'product_image',
    ];

    public function validate($data) {
        $this->errors = [];

        if(empty($data['user_id']))
            $this->errors['user_id'] = "User ID is required";
        
        if(empty($data['product_id']))
            $this->errors['product_id'] = "Product ID is required";
        
        if(empty($data['product_name']))
            $this->errors['product_name'] = "Product name is required";
        
        if(empty($data['product_price']))
            $this->errors['product_price'] = "Product price is required";
        else
            if(!is_numeric($data['product_price']) || $data['product_price'] <= 0)
                $this->errors['product_price'] = "Product price must be a positive number";
        
        if(empty($data['quantity']))
            $this->errors['quantity'] = "Quantity is required";
        else
            if(!is_numeric($data['quantity']) || $data['quantity'] <= 0)
                $this->errors['quantity'] = "Quantity must be a positive number";
        
        if(empty($data['farmer_name']))
            $this->errors['farmer_name'] = "Farmer name is required";

        if(empty($this->errors))
            return true;
        return false;
    }

    /**
     * Get all cart items for a specific user
     */
    public function getUserCart($user_id) {
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id ORDER BY created_at DESC";
        return $this->query($query, ['user_id' => $user_id]);
    }

    /**
     * Get a specific cart item
     */
    public function getCartItem($user_id, $product_id) {
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id AND product_id = :product_id";
        return $this->get_row($query, ['user_id' => $user_id, 'product_id' => $product_id]);
    }

    /**
     * Add item to cart or update quantity if exists
     */
    public function addToCart($data) {
        // Check if item already exists in cart
        $existingItem = $this->getCartItem($data['user_id'], $data['product_id']);
        
        if($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $data['quantity'];
            return $this->updateQuantity($data['user_id'], $data['product_id'], $newQuantity);
        } else {
            // Add new item
            $result = $this->insert($data);
            return $result !== false;
        }
    }

    /**
     * Update quantity of a cart item
     */
    public function updateQuantity($user_id, $product_id, $quantity) {
        if($quantity <= 0) {
            return $this->removeFromCart($user_id, $product_id);
        }
        
        $query = "UPDATE $this->table SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $result = $this->query($query, [
            'quantity' => $quantity,
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);
        return $result !== false;
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($user_id, $product_id) {
        $query = "DELETE FROM $this->table WHERE user_id = :user_id AND product_id = :product_id";
        $result = $this->query($query, ['user_id' => $user_id, 'product_id' => $product_id]);
        return $result !== false;
    }

    /**
     * Clear entire cart for a user
     */
    public function clearCart($user_id) {
        $query = "DELETE FROM $this->table WHERE user_id = :user_id";
        $result = $this->query($query, ['user_id' => $user_id]);
        return $result !== false;
    }

    /**
     * Get cart total for a user
     */
    public function getCartTotal($user_id) {
        $query = "SELECT SUM(product_price * quantity) as total FROM $this->table WHERE user_id = :user_id";
        $result = $this->get_row($query, ['user_id' => $user_id]);
        return $result ? $result->total : 0;
    }

    /**
     * Get cart item count for a user
     */
    public function getCartItemCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM $this->table WHERE user_id = :user_id";
        $result = $this->get_row($query, ['user_id' => $user_id]);
        return $result ? $result->count : 0;
    }

    /**
     * Check if cart is empty for a user
     */
    public function isCartEmpty($user_id) {
        return $this->getCartItemCount($user_id) == 0;
    }
}
