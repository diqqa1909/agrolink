<?php

class ProductsModel
{
    use Database;

    protected $table = 'products';

    public function create(array $data)
    {
        try {
            $sql = "INSERT INTO {$this->table}
                    (farmer_id, name, price, quantity, description, image, location, category, listing_date)
                    VALUES (:farmer_id, :name, :price, :quantity, :description, :image, :location, :category, :listing_date)";

            $result = $this->write($sql, $data);

            if ($result === false) {
                error_log("ProductsModel::create - Insert failed for data: " . print_r($data, true));
            } else {
                error_log("ProductsModel::create - Insert successful, ID: " . $result);
            }

            return $result;
        } catch (Exception $e) {
            error_log("ProductsModel::create - Exception: " . $e->getMessage());
            error_log("ProductsModel::create - Data: " . print_r($data, true));
            return false;
        }
    }

    public function updateByFarmer(int $id, int $farmerId, array $data)
    {
        // Allow dynamic updates for provided fields
        $allowed = ['name', 'price', 'quantity', 'description', 'location', 'category', 'listing_date', 'image'];
        $set = [];
        $params = ['id' => $id, 'farmer_id' => $farmerId];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $set[] = "$field=:$field";
                $params[$field] = $data[$field];
            }
        }
        if (empty($set)) return false;
        $sql = "UPDATE {$this->table} SET " . implode(',', $set) . " WHERE id=:id AND farmer_id=:farmer_id";
        return $this->write($sql, $params);
    }

    public function deleteByFarmer(int $id, int $farmerId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id=:id AND farmer_id=:farmer_id";
        return $this->write($sql, ['id' => $id, 'farmer_id' => $farmerId]);
    }

    public function getByFarmer(int $farmerId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE farmer_id=:farmer_id ORDER BY created_at DESC";
        $result = $this->query($sql, ['farmer_id' => $farmerId]);
        return $result ?: [];
    }

    public function getById(int $id)
    {
        $sql = "SELECT p.*, u.name AS farmer_name
                FROM {$this->table} p
                JOIN users u ON u.id = p.farmer_id
                WHERE p.id=:id";
        return $this->get_row($sql, ['id' => $id]);
    }

    public function getAvailable(array $filters = [])
    {
        $params = [];
        $where = "p.quantity > 0";

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['max_price'])) {
            $where .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        if (!empty($filters['location'])) {
            $where .= " AND p.location = :location";
            $params['location'] = $filters['location'];
        }

        $sql = "SELECT p.*, u.name AS farmer_name
                FROM {$this->table} p
                JOIN users u ON u.id = p.farmer_id
                WHERE {$where}
                ORDER BY p.created_at DESC";

        $result = $this->query($sql, $params);
        return $result ?: [];
    }

    /**
     * Get all products with farmer details for buyer dashboard
     */
    public function getWithFarmerDetails($conditions = [])
    {
        $params = [];
        $where = "p.quantity > 0"; // Only show products with stock

        // Add optional conditions
        if (!empty($conditions['category'])) {
            $where .= " AND p.category = :category";
            $params['category'] = $conditions['category'];
        }

        if (!empty($conditions['location'])) {
            $where .= " AND p.location = :location";
            $params['location'] = $conditions['location'];
        }

        if (!empty($conditions['min_price'])) {
            $where .= " AND p.price >= :min_price";
            $params['min_price'] = $conditions['min_price'];
        }

        if (!empty($conditions['max_price'])) {
            $where .= " AND p.price <= :max_price";
            $params['max_price'] = $conditions['max_price'];
        }

        $sql = "SELECT p.*, u.name as farmer_name, u.email as farmer_email 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.farmer_id = u.id 
                WHERE {$where}
                ORDER BY p.created_at DESC";

        $result = $this->query($sql, $params);
        return $result ?: [];
    }
}
