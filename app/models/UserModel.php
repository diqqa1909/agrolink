<?php
class UserModel
{
    use Model;

    protected $table = 'users';
    protected $allowedColumns = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function validate($data)
    {
        $this->errors = [];

        if (empty($data['email']))
            $this->errors['email'] = "Email is required";
        else {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
                $this->errors['email'] = "Email is incorrect";
            else {
                // Check if email already exists
                $existing = $this->first(['email' => $data['email']]);
                if ($existing)
                    $this->errors['email'] = "This email is already registered. Please use a different email or login.";
            }
        }

        if (empty($data['password']))
            $this->errors['password'] = "Password is required";
        else
                if (strlen($data['password']) < 8)
            $this->errors['password'] = "Password must be at least 8 characters long";

        if (empty($this->errors))
            return true;
        return false;
    }

    public function insert($data)
    {
        // Hash password before inserting
        /* if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } */

        // Remove unwanted data
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "insert into $this->table (" . implode(",", $keys) . ") values (:" . implode(",:", $keys) . ")";

        $this->query($query, $data);
        return 1;
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->first(['email' => $email]);
    }

    public function update($id, $data, $id_column = 'id')
    {
        // Hash password before updating (only if password is provided)
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Remove unwanted data
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "update $this->table set ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . ", ";
        }

        $query = trim($query, ", ");
        $query .= " where $id_column = :$id_column ";

        $data[$id_column] = $id;

        $this->query($query, $data);
        return 1;
    }
}
