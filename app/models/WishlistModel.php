<?php

class WishlistModel
{
    use Database;

    protected $table = 'wishlist';

    public function getByUserId($user_id)
    {
        $sql = "SELECT w.*, p.name, p.price, p.image, p.quantity as available_quantity
                FROM {$this->table} w
                LEFT JOIN products p ON w.product_id = p.id
                WHERE w.user_id = :user_id
                ORDER BY w.created_at DESC";

        $result = $this->query($sql, ['user_id' => $user_id]);
        return is_array($result) ? $result : [];
    }

    public function exists($user_id, $product_id)
    {
        $sql = "SELECT id FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id LIMIT 1";
        $result = $this->query($sql, [
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);

        return is_array($result) && count($result) > 0;
    }

    public function add($user_id, $product_id)
    {
        if ($this->exists($user_id, $product_id)) {
            return true; // Already exists, that's fine
        }

        $sql = "INSERT INTO {$this->table} (user_id, product_id, created_at) VALUES (:user_id, :product_id, NOW())";
        $result = $this->write($sql, [
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);
        
        // write() returns insert ID on success or 1 on failure
        return $result !== 1 ? true : false;
    }

    public function remove($user_id, $product_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id";
        $result = $this->write($sql, [
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);
        
        // write() returns row count or 1 on failure
        return $result !== 1 ? true : false;
    }

    public function clear($user_id)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        $result = $this->write($sql, ['user_id' => $user_id]);
        
        // write() returns row count or 1 on failure
        return $result !== 1 ? true : false;
    }

    public function countByUser($user_id)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = :user_id";
        $result = $this->query($sql, ['user_id' => $user_id]);
        if (is_array($result) && count($result) && isset($result[0]->total)) {
            return (int)$result[0]->total;
        }
        return 0;
    }
}

