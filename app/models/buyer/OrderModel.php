<?php

class OrderModel
{
    use Database;

    protected $table = 'orders';
    protected $orderItemsTable = 'order_items';

    /**
     * Create a new order
     */
    public function createOrder($orderData)
    {
        $sql = "INSERT INTO {$this->table} 
                (buyer_id, total_amount, shipping_cost, order_total, payment_method, 
                 delivery_address, delivery_city, delivery_district_id, delivery_town_id,
                 delivery_phone, status, created_at)
                VALUES (:buyer_id, :total_amount, :shipping_cost, :order_total, :payment_method,
                        :delivery_address, :delivery_city, :delivery_district_id, :delivery_town_id,
                        :delivery_phone, :status, NOW())";

        $result = $this->write($sql, $orderData);
        
        if ($result && $result !== false && $result !== 1) {
            return $result; // Returns order ID
        }
        
        return false;
    }

    /**
     * Add order items
     */
    public function addOrderItem($itemData)
    {
        $sql = "INSERT INTO {$this->orderItemsTable} 
                (order_id, product_id, product_name, product_price, quantity, farmer_id, created_at)
                VALUES (:order_id, :product_id, :product_name, :product_price, :quantity, :farmer_id, NOW())";

        $result = $this->write($sql, $itemData);
        return $result !== false && $result !== 1;
    }

    /**
     * Get order by ID
     */
    public function getOrderById($orderId)
    {
        $sql = "SELECT o.*, u.name as buyer_name, u.email as buyer_email, d.district_name
                FROM {$this->table} o
                LEFT JOIN users u ON o.buyer_id = u.id
                LEFT JOIN districts d ON o.delivery_district_id = d.id
                WHERE o.id = :order_id";
        
        return $this->get_row($sql, ['order_id' => $orderId]);
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, p.image as product_image, u.name as farmer_name
                FROM {$this->orderItemsTable} oi
                LEFT JOIN products p ON oi.product_id = p.id
                LEFT JOIN users u ON oi.farmer_id = u.id
                WHERE oi.order_id = :order_id";
        
        $result = $this->query($sql, ['order_id' => $orderId]);
        return is_array($result) ? $result : [];
    }

    /**
     * Get orders by buyer ID
     */
    public function getOrdersByBuyer($buyerId)
    {
        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM {$this->orderItemsTable} WHERE order_id = o.id) as item_count
                FROM {$this->table} o
                WHERE o.buyer_id = :buyer_id
                ORDER BY o.created_at DESC";
        
        $result = $this->query($sql, ['buyer_id' => $buyerId]);
        return is_array($result) ? $result : [];
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :order_id";
        return $this->write($sql, ['order_id' => $orderId, 'status' => $status]);
    }

    /**
     * Update product quantity after order
     * Ensures quantity doesn't go below 0
     */
    public function updateProductQuantity($productId, $quantity)
    {
        // First check current quantity
        $sql = "SELECT quantity FROM products WHERE id = :product_id";
        $current = $this->get_row($sql, ['product_id' => $productId]);
        
        if (!$current) {
            return false;
        }
        
        $currentQuantity = (int)($current->quantity ?? 0);
        $newQuantity = max(0, $currentQuantity - $quantity);
        
        $sql = "UPDATE products SET quantity = :new_quantity WHERE id = :product_id";
        return $this->write($sql, ['product_id' => $productId, 'new_quantity' => $newQuantity]);
    }

    /**
     * Restore stock for cancelled order
     */
    public function restoreOrderStock($orderId)
    {
        $items = $this->getOrderItems($orderId);
        foreach ($items as $item) {
            // Increment quantity directly
            $sql = "UPDATE products SET quantity = quantity + :qty WHERE id = :id";
            $this->write($sql, ['qty' => $item->quantity, 'id' => $item->product_id]);
        }
        return true;
    }
}
