<?php

class TransporterFeedbackModel
{
    use Database;

    private $table = 'transporter_feedback';

    public function createFeedback($data)
    {
        $rating = max(1, min(5, (int)($data['rating'] ?? 0)));
        $validSatisfactionStatuses = ['very_satisfied', 'satisfied', 'neutral', 'dissatisfied', 'very_dissatisfied'];
        $satisfactionStatus = in_array(($data['satisfaction_status'] ?? 'neutral'), $validSatisfactionStatuses, true)
            ? $data['satisfaction_status']
            : 'neutral';

        $sql = "INSERT INTO {$this->table}
                (reviewer_type, reviewer_id, transporter_id, order_id, delivery_request_id, rating, review_text, on_time_flag, satisfaction_status, complaint_text, complaint_status, created_at, updated_at)
                VALUES
                (:reviewer_type, :reviewer_id, :transporter_id, :order_id, :delivery_request_id, :rating, :review_text, :on_time_flag, :satisfaction_status, :complaint_text, :complaint_status, NOW(), NOW())";

        return $this->write($sql, [
            'reviewer_type' => $data['reviewer_type'],
            'reviewer_id' => (int)$data['reviewer_id'],
            'transporter_id' => (int)$data['transporter_id'],
            'order_id' => (int)$data['order_id'],
            'delivery_request_id' => !empty($data['delivery_request_id']) ? (int)$data['delivery_request_id'] : null,
            'rating' => $rating,
            'review_text' => trim((string)($data['review_text'] ?? '')),
            'on_time_flag' => !empty($data['on_time_flag']) ? 1 : 0,
            'satisfaction_status' => $satisfactionStatus,
            'complaint_text' => trim((string)($data['complaint_text'] ?? '')) ?: null,
            'complaint_status' => !empty($data['complaint_text']) ? ($data['complaint_status'] ?? 'open') : 'none',
        ]);
    }

    public function hasFeedback($reviewerType, $reviewerId, $transporterId, $orderId)
    {
        $result = $this->get_row(
            "SELECT id
             FROM {$this->table}
             WHERE reviewer_type = :reviewer_type
             AND reviewer_id = :reviewer_id
             AND transporter_id = :transporter_id
             AND order_id = :order_id
             LIMIT 1",
            [
                'reviewer_type' => $reviewerType,
                'reviewer_id' => (int)$reviewerId,
                'transporter_id' => (int)$transporterId,
                'order_id' => (int)$orderId,
            ]
        );

        return $result !== false;
    }

    public function getFeedbackByTransporter($transporterId)
    {
        $sql = "SELECT
                    tf.*,
                    reviewer.name AS reviewer_name,
                    reviewer.email AS reviewer_email,
                    dr.farmer_name,
                    dr.buyer_name,
                    dr.distance_km,
                    dr.status AS delivery_status,
                    dr.expected_delivery_at,
                    dr.delivered_at
                FROM {$this->table} tf
                INNER JOIN users reviewer ON reviewer.id = tf.reviewer_id
                LEFT JOIN delivery_requests dr ON dr.id = tf.delivery_request_id
                WHERE tf.transporter_id = :transporter_id
                ORDER BY tf.created_at DESC";

        $result = $this->query($sql, ['transporter_id' => (int)$transporterId]);
        return is_array($result) ? $result : [];
    }

    public function getSubmittedOrderIdsForReviewer($reviewerType, $reviewerId)
    {
        $rows = $this->query(
            "SELECT order_id
             FROM {$this->table}
             WHERE reviewer_type = :reviewer_type
             AND reviewer_id = :reviewer_id",
            [
                'reviewer_type' => $reviewerType,
                'reviewer_id' => (int)$reviewerId,
            ]
        );

        if (!is_array($rows)) {
            return [];
        }

        return array_map(function ($row) {
            return (int)$row->order_id;
        }, $rows);
    }
}
