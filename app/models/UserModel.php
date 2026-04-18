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
        'verification_status',
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

    public function findById($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }

        return $this->first(['id' => $id]);
    }

    public function updatePassword($userId, $newPassword)
    {
        $userId = (int) $userId;
        $newPassword = (string) $newPassword;

        if ($userId <= 0 || $newPassword === '') {
            return false;
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($hash === false) {
            return false;
        }

        return $this->write(
            "UPDATE {$this->table}
             SET password = :password,
                 password_updated_at = NOW()
             WHERE id = :id
             LIMIT 1",
            [
                'id' => $userId,
                'password' => $hash,
            ]
        ) !== false;
    }

    public function changeEmailWithAudit($userId, $newEmail)
    {
        $userId = (int) $userId;
        $newEmail = strtolower(trim((string) $newEmail));

        if ($userId <= 0 || $newEmail === '' || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $currentUser = $this->findById($userId);
        if (!$currentUser) {
            return false;
        }

        $existing = $this->findByEmail($newEmail);
        if ($existing && (int) ($existing->id ?? 0) !== $userId) {
            return false;
        }

        $updated = $this->write(
            "UPDATE {$this->table}
             SET email = :email
             WHERE id = :id
             LIMIT 1",
            [
                'id' => $userId,
                'email' => $newEmail,
            ]
        );

        if ($updated === false) {
            return false;
        }

        $oldEmail = (string) ($currentUser->email ?? '');
        if ($oldEmail !== '' && strcasecmp($oldEmail, $newEmail) !== 0) {
            $this->recordEmailChange($userId, $oldEmail, $newEmail);
        }

        return true;
    }

    public function deactivateAccount($userId, $reason = '')
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return false;
        }

        $reason = trim((string) $reason);
        if ($reason === '') {
            $reason = null;
        } else {
            $reason = substr($reason, 0, 500);
        }

        return $this->write(
            "UPDATE {$this->table}
             SET status = 'inactive',
                 deactivated_at = NOW(),
                 deactivation_reason = :reason
             WHERE id = :id
             LIMIT 1",
            [
                'id' => $userId,
                'reason' => $reason,
            ]
        ) !== false;
    }

    private function ensureEmailChangesTable()
    {
        static $checked = null;
        if ($checked !== null) {
            return $checked;
        }

        $row = $this->get_row(
            "SELECT 1 AS table_exists
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
               AND table_name = :table_name
             LIMIT 1",
            ['table_name' => $this->emailChangesTable]
        );

        $checked = (bool) $row;
        return $checked;
    }

    public function getEmailChangeCount($userId)
    {
        if (!$this->ensureEmailChangesTable())
            return 0;

        $row = $this->get_row(
            "SELECT COUNT(*) AS total FROM {$this->emailChangesTable} WHERE user_id = :user_id",
            ['user_id' => $userId]
        );

        return (int) ($row->total ?? 0);
    }

    public function recordEmailChange($userId, $oldEmail, $newEmail)
    {
        if (!$this->ensureEmailChangesTable())
            return false;

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

        if (empty($data)) {
            return false;
        }

        $keys = array_keys($data);
        $query = "update $this->table set ";

        foreach ($keys as $key) {
            $query .= $key . " = :" . $key . ", ";
        }

        $query = trim($query, ", ");
        $query .= " where $id_column = :$id_column ";

        $data[$id_column] = $id;

        $result = $this->write($query, $data);
        return $result === false ? false : (int) $result;
    }

    public function setVerificationStatus(int $userId, string $status): void
    {
        $this->write(
            "UPDATE {$this->table} SET verification_status = :status WHERE id = :id",
            [
                'status' => $status,
                'id' => $userId
            ]
        );
    }
}
