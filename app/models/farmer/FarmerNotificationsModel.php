<?php

class FarmerNotificationsModel
{
    private const SESSION_READ_KEY = 'FARMER_NOTIFICATIONS_READ';
    private const SESSION_SETTINGS_KEY = 'FARMER_NOTIFICATION_SETTINGS';

    public function getDefaultSettings()
    {
        return [
            'orders' => true,
            'crop_requests' => true,
            'deliveries' => true,
            'reviews' => true,
            'system' => true,
            'email_notifications' => true,
        ];
    }

    public function getSettings($farmerId)
    {
        $defaults = $this->getDefaultSettings();
        $stored = $_SESSION[self::SESSION_SETTINGS_KEY][$farmerId] ?? [];
        if (!is_array($stored)) {
            return $defaults;
        }

        foreach ($defaults as $key => $value) {
            if (array_key_exists($key, $stored)) {
                $defaults[$key] = (bool)$stored[$key];
            }
        }

        return $defaults;
    }

    public function saveSettings($farmerId, $settings)
    {
        $current = $this->getDefaultSettings();
        foreach ($current as $key => $value) {
            if (array_key_exists($key, $settings)) {
                $current[$key] = (bool)$settings[$key];
            }
        }

        if (!isset($_SESSION[self::SESSION_SETTINGS_KEY])) {
            $_SESSION[self::SESSION_SETTINGS_KEY] = [];
        }
        $_SESSION[self::SESSION_SETTINGS_KEY][$farmerId] = $current;

        return $current;
    }

    public function getNotifications($farmerId, $filter = 'all')
    {
        $settings = $this->getSettings($farmerId);
        $items = [];

        if ($settings['orders']) {
            $items = array_merge($items, $this->getOrderNotifications($farmerId));
        }
        if ($settings['crop_requests']) {
            $items = array_merge($items, $this->getCropRequestNotifications());
        }
        if ($settings['deliveries']) {
            $items = array_merge($items, $this->getDeliveryNotifications($farmerId));
        }
        if ($settings['reviews']) {
            $items = array_merge($items, $this->getReviewNotifications($farmerId));
        }
        if ($settings['system']) {
            $items = array_merge($items, $this->getSystemNotifications());
        }

        $readMap = $this->getReadMap($farmerId);
        foreach ($items as &$item) {
            $item['is_read'] = !empty($readMap[$item['id']]);
        }
        unset($item);

        usort($items, function ($a, $b) {
            $timeA = strtotime($a['created_at'] ?? '') ?: 0;
            $timeB = strtotime($b['created_at'] ?? '') ?: 0;
            return $timeB <=> $timeA;
        });

        return $this->applyFilter($items, $filter);
    }

    public function getUnreadCount($farmerId)
    {
        $all = $this->getNotifications($farmerId, 'all');
        $count = 0;
        foreach ($all as $item) {
            if (empty($item['is_read'])) {
                $count++;
            }
        }
        return $count;
    }

    public function markAllAsRead($farmerId)
    {
        $all = $this->getNotifications($farmerId, 'all');
        $readMap = $this->getReadMap($farmerId);
        foreach ($all as $item) {
            $readMap[$item['id']] = time();
        }
        $this->setReadMap($farmerId, $readMap);
    }

    private function applyFilter($items, $filter)
    {
        $normalized = strtolower(trim((string)$filter));
        if ($normalized === '' || $normalized === 'all') {
            return $items;
        }

        return array_values(array_filter($items, function ($item) use ($normalized) {
            if ($normalized === 'unread') {
                return empty($item['is_read']);
            }
            return ($item['category'] ?? '') === $normalized;
        }));
    }

    private function getReadMap($farmerId)
    {
        if (!isset($_SESSION[self::SESSION_READ_KEY][$farmerId]) || !is_array($_SESSION[self::SESSION_READ_KEY][$farmerId])) {
            return [];
        }
        return $_SESSION[self::SESSION_READ_KEY][$farmerId];
    }

    private function setReadMap($farmerId, $map)
    {
        if (!isset($_SESSION[self::SESSION_READ_KEY])) {
            $_SESSION[self::SESSION_READ_KEY] = [];
        }
        $_SESSION[self::SESSION_READ_KEY][$farmerId] = $map;
    }

    private function getOrderNotifications($farmerId)
    {
        $farmerModel = new FarmerModel();
        $orders = $farmerModel->getFarmerOrders($farmerId);
        $notifications = [];

        foreach (array_slice($orders, 0, 20) as $order) {
            $status = strtolower((string)($order->status ?? 'pending'));
            $statusText = ucwords(str_replace('_', ' ', $status));

            $title = $status === 'pending' ? 'New Order Received' : 'Order Status Updated';
            $amount = number_format((float)($order->my_order_total ?? 0), 2);

            $notifications[] = [
                'id' => 'order_' . (int)$order->id . '_' . $status,
                'category' => 'orders',
                'icon' => 'orders',
                'title' => $title,
                'message' => 'Order #' . (int)$order->id . ' • ' . $statusText . ' • Your earning: Rs. ' . $amount,
                'created_at' => $order->updated_at ?? $order->created_at ?? date('Y-m-d H:i:s'),
                'link' => 'farmerorders',
            ];
        }

        return $notifications;
    }

    private function getCropRequestNotifications()
    {
        $cropModel = new CropRequestModel();
        $requests = $cropModel->findAll();
        if (!is_array($requests)) {
            $requests = [];
        }

        $notifications = [];
        foreach (array_slice($requests, 0, 20) as $request) {
            $status = strtolower((string)($request->status ?? 'pending'));
            $statusText = ucwords(str_replace('_', ' ', $status));
            $crop = ucfirst((string)($request->crop_name ?? 'Crop'));
            $qty = (float)($request->quantity ?? 0);

            $title = $status === 'pending' ? 'New Crop Request' : 'Crop Request Updated';
            $notifications[] = [
                'id' => 'crop_' . (int)$request->id . '_' . $status,
                'category' => 'crop_requests',
                'icon' => 'crop_requests',
                'title' => $title,
                'message' => $crop . ' • ' . rtrim(rtrim(number_format($qty, 2), '0'), '.') . ' kg • ' . $statusText,
                'created_at' => $request->updated_at ?? $request->created_at ?? date('Y-m-d H:i:s'),
                'link' => 'farmercroprequests',
            ];
        }

        return $notifications;
    }

    private function getDeliveryNotifications($farmerId)
    {
        $farmerModel = new FarmerModel();
        $deliveries = $farmerModel->getFarmerDeliveryRequests($farmerId);
        $notifications = [];

        foreach (array_slice($deliveries, 0, 20) as $delivery) {
            $status = strtolower((string)($delivery->status ?? 'pending'));
            $statusText = ucwords(str_replace('_', ' ', $status));
            $title = $status === 'accepted' ? 'Delivery Accepted' : 'Delivery Update';
            $deliveryId = (int)($delivery->id ?? 0);
            $orderId = (int)($delivery->order_id ?? 0);

            $notifications[] = [
                'id' => 'delivery_' . $deliveryId . '_' . $status,
                'category' => 'deliveries',
                'icon' => 'deliveries',
                'title' => $title,
                'message' => 'Delivery #' . $deliveryId . ' for Order #' . $orderId . ' • ' . $statusText,
                'created_at' => $delivery->updated_at ?? $delivery->created_at ?? date('Y-m-d H:i:s'),
                'link' => 'farmerdeliveries',
            ];
        }

        return $notifications;
    }

    private function getReviewNotifications($farmerId)
    {
        $reviewModel = new ReviewModel();
        $reviews = $reviewModel->getReviewsByFarmer($farmerId);
        if (!is_array($reviews)) {
            $reviews = [];
        }

        $notifications = [];
        foreach (array_slice($reviews, 0, 20) as $review) {
            $rating = (int)($review->rating ?? 0);
            $buyer = (string)($review->buyer_name ?? 'Buyer');
            $product = ucfirst((string)($review->product_name ?? 'product'));

            $notifications[] = [
                'id' => 'review_' . (int)$review->id,
                'category' => 'reviews',
                'icon' => 'reviews',
                'title' => 'New Review Received',
                'message' => $buyer . ' gave ' . $rating . '-star review for ' . $product,
                'created_at' => $review->created_at ?? date('Y-m-d H:i:s'),
                'link' => 'farmerreviews',
            ];
        }

        return $notifications;
    }

    private function getSystemNotifications()
    {
        return [
            [
                'id' => 'system_profile',
                'category' => 'system',
                'icon' => 'system',
                'title' => 'Keep Profile Updated',
                'message' => 'Ensure profile and payout details are up to date to avoid payout delays.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'link' => 'farmerprofile',
            ],
            [
                'id' => 'system_shipping',
                'category' => 'system',
                'icon' => 'system',
                'title' => 'System Update',
                'message' => 'Delivery status and earnings views were improved for better tracking.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'link' => 'farmerdashboard',
            ],
        ];
    }
}

