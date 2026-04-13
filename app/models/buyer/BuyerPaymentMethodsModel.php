<?php

class BuyerPaymentMethodsModel
{
    use Database;

    protected $table = 'buyer_saved_cards';

    private function normalizeCardRow($row)
    {
        return [
            'id' => (int)($row->id ?? 0),
            'holderName' => (string)($row->card_holder_name ?? ''),
            'brand' => (string)($row->card_brand ?? 'Card'),
            'last4' => (string)($row->card_last_four ?? ''),
            'expiryMonth' => str_pad((string)($row->expiry_month ?? ''), 2, '0', STR_PAD_LEFT),
            'expiryYear' => (string)($row->expiry_year ?? ''),
            'isDefault' => ((int)($row->is_default ?? 0)) === 1,
            'createdAt' => (string)($row->created_at ?? ''),
        ];
    }

    private function validateCardPayload(array $payload)
    {
        $errors = [];

        $holder = trim((string)($payload['card_holder_name'] ?? ''));
        $brand = trim((string)($payload['card_brand'] ?? 'Card'));
        $lastFour = preg_replace('/\D/', '', (string)($payload['card_last_four'] ?? $payload['card_last4'] ?? ''));
        $month = preg_replace('/\D/', '', (string)($payload['expiry_month'] ?? ''));
        $yearRaw = preg_replace('/\D/', '', (string)($payload['expiry_year'] ?? ''));

        if ($holder === '') {
            $errors['card_holder_name'] = 'Card holder name is required';
        } elseif (!preg_match('/^[A-Za-z][A-Za-z\s.\'\-]{1,79}$/', $holder)) {
            $errors['card_holder_name'] = 'Enter a valid card holder name';
        }

        if ($brand === '') {
            $errors['card_brand'] = 'Card brand is required';
        } elseif (strlen($brand) > 40) {
            $errors['card_brand'] = 'Card brand is too long';
        }

        if (!preg_match('/^\d{4}$/', $lastFour)) {
            $errors['card_last_four'] = 'Card last 4 digits are invalid';
        }

        $monthNum = (int)$month;
        if (!preg_match('/^\d{1,2}$/', $month) || $monthNum < 1 || $monthNum > 12) {
            $errors['expiry_month'] = 'Expiry month must be between 01 and 12';
        }

        if (strlen($yearRaw) === 2) {
            $yearRaw = '20' . $yearRaw;
        }
        $yearNum = (int)$yearRaw;
        $currentYear = (int)date('Y');

        if (!preg_match('/^\d{4}$/', $yearRaw)) {
            $errors['expiry_year'] = 'Expiry year must be 4 digits';
        } elseif ($yearNum < $currentYear || $yearNum > ($currentYear + 20)) {
            $errors['expiry_year'] = 'Expiry year is out of allowed range';
        }

        if (!empty($errors)) {
            return $errors;
        }

        return [
            'card_holder_name' => $holder,
            'card_brand' => $brand,
            'card_last_four' => $lastFour,
            'expiry_month' => str_pad((string)$monthNum, 2, '0', STR_PAD_LEFT),
            'expiry_year' => $yearNum,
            'is_default' => !empty($payload['is_default']) ? 1 : 0,
        ];
    }

    public function getCards($buyerId)
    {
        $buyerId = (int)$buyerId;
        if ($buyerId <= 0) {
            return [];
        }

        $sql = "SELECT id, card_holder_name, card_brand, card_last_four, expiry_month, expiry_year, is_default, created_at
                FROM {$this->table}
                WHERE buyer_id = :buyer_id
                ORDER BY is_default DESC, created_at DESC, id DESC";

        $rows = $this->query($sql, ['buyer_id' => $buyerId]);
        if (!is_array($rows) || empty($rows)) {
            return [];
        }

        return array_map([$this, 'normalizeCardRow'], $rows);
    }

    public function addCard($buyerId, array $payload)
    {
        $buyerId = (int)$buyerId;
        if ($buyerId <= 0) {
            return ['success' => false, 'error' => 'Invalid buyer'];
        }

        $normalized = $this->validateCardPayload($payload);
        if (!is_array($normalized) || isset($normalized['card_holder_name']) === false) {
            return ['success' => false, 'errors' => $normalized];
        }

        $con = $this->connect();
        try {
            $con->beginTransaction();

            $countStmt = $con->prepare("SELECT COUNT(*) AS total_cards FROM {$this->table} WHERE buyer_id = :buyer_id");
            $countStmt->execute(['buyer_id' => $buyerId]);
            $countRow = $countStmt->fetch(PDO::FETCH_OBJ);
            $existingCount = (int)($countRow->total_cards ?? 0);

            $shouldBeDefault = ((int)$normalized['is_default'] === 1) || $existingCount === 0;

            if ($shouldBeDefault) {
                $clearStmt = $con->prepare("UPDATE {$this->table} SET is_default = 0, updated_at = NOW() WHERE buyer_id = :buyer_id");
                $clearStmt->execute(['buyer_id' => $buyerId]);
            }

            $insertStmt = $con->prepare(
                "INSERT INTO {$this->table}
                    (buyer_id, card_holder_name, card_last_four, card_brand, expiry_month, expiry_year, is_default, created_at, updated_at)
                 VALUES
                    (:buyer_id, :card_holder_name, :card_last_four, :card_brand, :expiry_month, :expiry_year, :is_default, NOW(), NOW())"
            );
            $insertStmt->execute([
                'buyer_id' => $buyerId,
                'card_holder_name' => $normalized['card_holder_name'],
                'card_last_four' => $normalized['card_last_four'],
                'card_brand' => $normalized['card_brand'],
                'expiry_month' => $normalized['expiry_month'],
                'expiry_year' => $normalized['expiry_year'],
                'is_default' => $shouldBeDefault ? 1 : 0,
            ]);

            $con->commit();
        } catch (Throwable $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            error_log('BuyerPaymentMethodsModel::addCard error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to save card'];
        }

        return ['success' => true, 'cards' => $this->getCards($buyerId)];
    }

    public function setDefaultCard($buyerId, $cardId)
    {
        $buyerId = (int)$buyerId;
        $cardId = (int)$cardId;

        if ($buyerId <= 0 || $cardId <= 0) {
            return ['success' => false, 'error' => 'Invalid card'];
        }

        $target = $this->get_row(
            "SELECT id FROM {$this->table} WHERE id = :id AND buyer_id = :buyer_id",
            ['id' => $cardId, 'buyer_id' => $buyerId]
        );

        if (!$target) {
            return ['success' => false, 'error' => 'Card not found'];
        }

        $con = $this->connect();
        try {
            $con->beginTransaction();

            $clearStmt = $con->prepare("UPDATE {$this->table} SET is_default = 0, updated_at = NOW() WHERE buyer_id = :buyer_id");
            $clearStmt->execute(['buyer_id' => $buyerId]);

            $setStmt = $con->prepare(
                "UPDATE {$this->table}
                 SET is_default = 1, updated_at = NOW()
                 WHERE id = :id AND buyer_id = :buyer_id"
            );
            $setStmt->execute(['id' => $cardId, 'buyer_id' => $buyerId]);

            $con->commit();
        } catch (Throwable $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            error_log('BuyerPaymentMethodsModel::setDefaultCard error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to set default card'];
        }

        return ['success' => true, 'cards' => $this->getCards($buyerId)];
    }

    public function removeCard($buyerId, $cardId)
    {
        $buyerId = (int)$buyerId;
        $cardId = (int)$cardId;

        if ($buyerId <= 0 || $cardId <= 0) {
            return ['success' => false, 'error' => 'Invalid card'];
        }

        $target = $this->get_row(
            "SELECT id, is_default FROM {$this->table} WHERE id = :id AND buyer_id = :buyer_id",
            ['id' => $cardId, 'buyer_id' => $buyerId]
        );
        if (!$target) {
            return ['success' => false, 'error' => 'Card not found'];
        }

        $con = $this->connect();
        try {
            $con->beginTransaction();

            $deleteStmt = $con->prepare("DELETE FROM {$this->table} WHERE id = :id AND buyer_id = :buyer_id");
            $deleteStmt->execute(['id' => $cardId, 'buyer_id' => $buyerId]);

            if ((int)($target->is_default ?? 0) === 1) {
                $fallbackStmt = $con->prepare(
                    "SELECT id FROM {$this->table}
                     WHERE buyer_id = :buyer_id
                     ORDER BY created_at DESC, id DESC
                     LIMIT 1"
                );
                $fallbackStmt->execute(['buyer_id' => $buyerId]);
                $fallback = $fallbackStmt->fetch(PDO::FETCH_OBJ);

                if ($fallback && isset($fallback->id)) {
                    $setFallbackStmt = $con->prepare(
                        "UPDATE {$this->table}
                         SET is_default = 1, updated_at = NOW()
                         WHERE id = :id AND buyer_id = :buyer_id"
                    );
                    $setFallbackStmt->execute(['id' => (int)$fallback->id, 'buyer_id' => $buyerId]);
                }
            }

            $con->commit();
        } catch (Throwable $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            error_log('BuyerPaymentMethodsModel::removeCard error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to remove card'];
        }

        return ['success' => true, 'cards' => $this->getCards($buyerId)];
    }
}
