<?php

class ProductModel
{
    use Model;
    protected $table = 'products';
    protected $allowedColumns = ['name', 'price', 'quantity', 'location', 'user_id'];
}

class ProductsModel
{
    use Database;

    protected $table = 'products';

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table}
                (farmer_id, name, price, quantity, description, image, location, category, listing_date)
                VALUES (:farmer_id, :name, :price, :quantity, :description, :image, :location, :category, :listing_date)";
        return $this->write($sql, $data);
    }

    public function updateByFarmer(int $id, int $farmerId, array $data)
    {
        $sql = "UPDATE {$this->table}
                SET name=:name, price=:price, quantity=:quantity, description=:description, location=:location
                WHERE id=:id AND farmer_id=:farmer_id";
        $data['id'] = $id;
        $data['farmer_id'] = $farmerId;
        return $this->write($sql, $data);
    }

    public function deleteByFarmer(int $id, int $farmerId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id=:id AND farmer_id=:farmer_id";
        return $this->write($sql, ['id' => $id, 'farmer_id' => $farmerId]);
    }

    public function getByFarmer(int $farmerId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE farmer_id=:farmer_id ORDER BY created_at DESC";
        return $this->query($sql, ['farmer_id' => $farmerId]) ?: [];
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
        return $this->query($sql, $params) ?: [];
    }
}
