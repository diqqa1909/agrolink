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
    protected $emailChangesTable = 'user_email_changes';

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
        $query = "insert into $this->table (" . implode(",", $keys) . ") values (:" . implode(",:", $keys) . ")";

        return $this->write($query, $data);
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->first(['email' => $email]);
    }

    private function ensureEmailChangesTable()
    {
        static $checked = false;
        if ($checked) return true;

        $sql = "CREATE TABLE IF NOT EXISTS {$this->emailChangesTable} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    old_email VARCHAR(255) NOT NULL,
                    new_email VARCHAR(255) NOT NULL,
                    changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $checked = (bool)$this->write($sql);
        return $checked;
    }

    public function getEmailChangeCount($userId)
    {
        if (!$this->ensureEmailChangesTable()) return 0;

        $row = $this->get_row(
            "SELECT COUNT(*) AS total FROM {$this->emailChangesTable} WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        return (int)($row->total ?? 0);
    }

    public function recordEmailChange($userId, $oldEmail, $newEmail)
    {
        if (!$this->ensureEmailChangesTable()) return false;

        return $this->write(
            "INSERT INTO {$this->emailChangesTable} (user_id, old_email, new_email) VALUES (:user_id, :old_email, :new_email)",
            [
                'user_id' => $userId,
                'old_email' => $oldEmail,
                'new_email' => $newEmail
            ]
        );
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
