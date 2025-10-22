<?php

class VehicleModel
{
    use Model;

    protected $table = 'vehicles';
    protected $allowedColumns = [
        'user_id',
        'type',
        'registration',
        'capacity',
        'fuel_type',
        'model',
        'status'
    ];

    public function getByUserId($user_id)
    {
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id ORDER BY created_at DESC";
        return $this->query($query, ['user_id' => $user_id]);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM $this->table WHERE id = :id LIMIT 1";
        $result = $this->query($query, ['id' => $id]);
        return $result ? $result[0] : false;
    }

    public function create($data)
    {
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        
        return $this->insert($data);
    }

    public function updateVehicle($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteVehicle($id)
    {
        return $this->delete($id);
    }

    public function setActiveVehicle($vehicle_id, $user_id)
    {
        $query = "UPDATE $this->table SET status = 'inactive' WHERE user_id = :user_id";
        $this->query($query, ['user_id' => $user_id]);
        
        $query = "UPDATE $this->table SET status = 'active' WHERE id = :id AND user_id = :user_id";
        return $this->query($query, ['id' => $vehicle_id, 'user_id' => $user_id]);
    }

    public function deactivateAllVehicles($user_id)
    {
        $query = "UPDATE $this->table SET status = 'inactive' WHERE user_id = :user_id";
        return $this->query($query, ['user_id' => $user_id]);
    }

    public function validate($data)
    {
        $this->errors = [];

        if (empty($data['type'])) {
            $this->errors['type'] = "Vehicle type is required";
        }

        if (empty($data['registration'])) {
            $this->errors['registration'] = "Registration number is required";
        }

        if (empty($data['capacity']) || !is_numeric($data['capacity']) || $data['capacity'] <= 0) {
            $this->errors['capacity'] = "Valid capacity is required";
        }

        if (!empty($data['registration'])) {
            $existing = $this->where(['registration' => $data['registration']]);
            if ($existing && (!isset($data['id']) || $existing[0]->id != $data['id'])) {
                $this->errors['registration'] = "This registration number is already registered";
            }
        }

        return empty($this->errors);
    }
}
