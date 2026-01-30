<?php

class ReviewModel
{
    use Database;

    protected $table = 'reviews';

    // Create a new review
    public function createReview($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (order_id, product_id, buyer_id, farmer_id, rating, comment, created_at) 
                VALUES (:order_id, :product_id, :buyer_id, :farmer_id, :rating, :comment, NOW())";
        
        return $this->write($sql, $data);
    }

    // Check if a review already exists for an order item
    public function exists($orderId, $productId, $buyerId)
    {
        $sql = "SELECT id FROM {$this->table} 
                WHERE order_id = :order_id AND product_id = :product_id AND buyer_id = :buyer_id";
        
        $result = $this->get_row($sql, [
            'order_id' => $orderId,
            'product_id' => $productId,
            'buyer_id' => $buyerId
        ]);

        return $result ? true : false;
    }

    // Get reviews by buyer ID
    public function getReviewsByBuyer($buyerId)
    {
        $sql = "SELECT r.*, p.name as product_name, p.image as product_image, u.name as farmer_name
                FROM {$this->table} r
                JOIN products p ON r.product_id = p.id
                JOIN users u ON r.farmer_id = u.id
                WHERE r.buyer_id = :buyer_id
                ORDER BY r.created_at DESC";
        
        return $this->query($sql, ['buyer_id' => $buyerId]);
    }

    // Get reviews for a farmer
    public function getReviewsByFarmer($farmerId)
    {
        $sql = "SELECT r.*, p.name as product_name, u.name as buyer_name, u.email as buyer_email
                FROM {$this->table} r
                JOIN products p ON r.product_id = p.id
                JOIN users u ON r.buyer_id = u.id
                WHERE r.farmer_id = :farmer_id
                ORDER BY r.created_at DESC";
        
        return $this->query($sql, ['farmer_id' => $farmerId]);
    }

    // Add a reply to a review
    public function replyToReview($reviewId, $reply)
    {
        $sql = "UPDATE {$this->table} 
                SET reply = :reply, replied_at = NOW() 
                WHERE id = :id";
        
        return $this->write($sql, ['reply' => $reply, 'id' => $reviewId]);
    }
}
