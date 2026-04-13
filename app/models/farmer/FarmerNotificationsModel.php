<?php

class FarmerNotificationsModel
{
    private $store;
    private $settingsStore;

    public function __construct()
    {
        $this->store = new NotificationsModel();
        $this->settingsStore = new NotificationSettingsModel();
    }

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
        return $this->settingsStore->getSettings((int)$farmerId, $this->getDefaultSettings());
    }

    public function saveSettings($farmerId, $settings)
    {
        return $this->settingsStore->saveSettings((int)$farmerId, (array)$settings, $this->getDefaultSettings());
    }

    private function getManagedTypes()
    {
        return [
            'orders',
            'crop_requests',
            'deliveries',
            'reviews',
            'system',
        ];
    }

    public function getNotifications($farmerId, $filter = 'all')
    {
        $farmerId = (int)$farmerId;
        if ($farmerId <= 0) {
            return [];
        }

        $this->store->syncNotifications($farmerId, $this->buildAllNotifications($farmerId), $this->getManagedTypes());
        $settings = $this->getSettings($farmerId);
        $allowedTypes = $this->getEnabledTypes($settings);
        if (empty($allowedTypes)) {
            return [];
        }

        return $this->store->listNotifications($farmerId, $filter, $allowedTypes);
    }

    public function getUnreadCount($farmerId)
    {
        $farmerId = (int)$farmerId;
        if ($farmerId <= 0) {
            return 0;
        }

        $this->store->syncNotifications($farmerId, $this->buildAllNotifications($farmerId), $this->getManagedTypes());
        $settings = $this->getSettings($farmerId);
        $allowedTypes = $this->getEnabledTypes($settings);
        if (empty($allowedTypes)) {
            return 0;
        }

        return $this->store->getUnreadCount($farmerId, $allowedTypes);
    }

    public function markAllAsRead($farmerId)
    {
        $farmerId = (int)$farmerId;
        if ($farmerId <= 0) {
            return false;
        }

        return $this->store->markAllAsRead($farmerId);
    }

    public function markAsRead($farmerId, $notificationId)
    {
        $farmerId = (int)$farmerId;
        $notificationId = (int)$notificationId;

        if ($farmerId <= 0 || $notificationId <= 0) {
            return false;
        }

        return $this->store->markAsRead($farmerId, $notificationId);
    }

    private function getEnabledTypes(array $settings)
    {
        $types = [];
        foreach ($settings as $key => $enabled) {
            if ($key === 'email_notifications') {
                continue;
            }

            if (!empty($enabled)) {
                $types[] = $key;
            }
        }

        return $types;
    }

    private function asArray($rows)
    {
        return is_array($rows) ? $rows : [];
    }

    private function buildAllNotifications($farmerId)
    {
        $items = [];
        $items = array_merge($items, $this->getOrderNotifications($farmerId));
        $items = array_merge($items, $this->getCropRequestNotifications());
        $items = array_merge($items, $this->getDeliveryNotifications($farmerId));
        $items = array_merge($items, $this->getReviewNotifications($farmerId));
        $items = array_merge($items, $this->getSystemNotifications($farmerId));

        return $items;
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
                'related_id' => (int)$order->id,
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
                'related_id' => (int)$request->id,
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
                'related_id' => $deliveryId,
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
                'related_id' => (int)$review->id,
                'created_at' => $review->created_at ?? date('Y-m-d H:i:s'),
                'link' => 'farmerreviews',
            ];
        }

        return $notifications;
    }

    private function getSystemNotifications($farmerId)
    {
        $farmerId = (int)$farmerId;
        if ($farmerId <= 0) {
            return [];
        }

        $notifications = [];
        $farmerModel = new FarmerModel();
        $profile = $farmerModel->getProfileByUserId($farmerId);
        $productsModel = new ProductsModel();
        $payoutModel = new PayoutAccountsModel();

        $profileObj = is_object($profile) ? $profile : null;
        $missingFields = [];

        if (trim((string)($profileObj->phone ?? '')) === '') {
            $missingFields[] = 'phone number';
        }
        if (trim((string)($profileObj->district ?? '')) === '') {
            $missingFields[] = 'district';
        }
        if (trim((string)($profileObj->full_address ?? '')) === '') {
            $missingFields[] = 'farm address';
        }

        if (!empty($missingFields)) {
            $notifications[] = [
                'id' => 'system_profile_missing_' . $farmerId,
                'category' => 'system',
                'icon' => 'system',
                'title' => 'Complete Profile Details',
                'message' => 'Add ' . implode(', ', array_slice($missingFields, 0, 3)) . ' to keep order and delivery operations smooth.',
                'created_at' => date('Y-m-d H:i:s'),
                'link' => 'farmerprofile',
            ];
        }

        $payoutAccount = $payoutModel->getDefaultAccountByUserId($farmerId);
        if (!$payoutAccount) {
            $notifications[] = [
                'id' => 'system_payout_missing_' . $farmerId,
                'category' => 'system',
                'icon' => 'system',
                'title' => 'Add Payout Account',
                'message' => 'Set your payout account details to receive earnings without delay.',
                'created_at' => date('Y-m-d H:i:s'),
                'link' => 'farmerprofile',
            ];
        }

        $products = $productsModel->getByFarmer($farmerId);
        if (!is_array($products) || empty($products)) {
            $notifications[] = [
                'id' => 'system_no_products_' . $farmerId,
                'category' => 'system',
                'icon' => 'system',
                'title' => 'No Products Listed',
                'message' => 'Add products to start receiving orders from buyers.',
                'created_at' => date('Y-m-d H:i:s'),
                'link' => 'farmerproducts',
            ];
        }

        return $notifications;
    }
}
