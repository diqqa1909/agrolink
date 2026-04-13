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

        if (empty($data['email'])) {
            $this->errors['email'] = 'Email is required';
        } else {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'Email is incorrect';
            } else {
                $existing = $this->first(['email' => $data['email']]);
                if ($existing) {
                    $this->errors['email'] = 'This email is already registered. Please use a different email or login.';
                }
            }
        }

        if (empty($data['password'])) {
            $this->errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $this->errors['password'] = 'Password must be at least 8 characters long';
        }

        return empty($this->errors);
    }

    public function insert($data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns, true)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "INSERT INTO {$this->table} (" . implode(',', $keys) . ") VALUES (:" . implode(',:', $keys) . ')';

        return $this->write($query, $data);
    }

    public function findByEmail($email)
    {
        return $this->first(['email' => $email]);
    }

    public function findById($id)
    {
        return $this->first(['id' => (int)$id]);
    }

    public function getEmailChangeCount($userId)
    {
        $row = $this->get_row(
            "SELECT COUNT(*) AS total FROM {$this->emailChangesTable} WHERE user_id = :user_id",
            ['user_id' => (int)$userId]
        );

        return (int)($row->total ?? 0);
    }

    public function recordEmailChange($userId, $oldEmail, $newEmail)
    {
        return $this->write(
            "INSERT INTO {$this->emailChangesTable} (user_id, old_email, new_email, changed_at)
             VALUES (:user_id, :old_email, :new_email, NOW())",
            [
                'user_id' => (int)$userId,
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
            ]
        ) !== false;
    }

    public function changeEmailWithAudit($userId, $newEmail)
    {
        $userId = (int)$userId;
        $newEmail = strtolower(trim((string)$newEmail));

        if ($userId <= 0 || $newEmail === '') {
            return false;
        }

        $currentUser = $this->findById($userId);
        if (!$currentUser) {
            return false;
        }

        $oldEmail = (string)($currentUser->email ?? '');
        if ($oldEmail === '' || strcasecmp($oldEmail, $newEmail) === 0) {
            return false;
        }

        $existing = $this->findByEmail($newEmail);
        if ($existing && (int)$existing->id !== $userId) {
            return false;
        }

        $dsn = 'mysql:hostname=' . DBHOST . ';dbname=' . DBNAME;
        $con = new PDO($dsn, DBUSER, DBPASS);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $con->beginTransaction();

            $insertStmt = $con->prepare(
                "INSERT INTO {$this->emailChangesTable} (user_id, old_email, new_email, changed_at)
                 VALUES (:user_id, :old_email, :new_email, NOW())"
            );
            $insertStmt->execute([
                'user_id' => $userId,
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
            ]);

            $updateStmt = $con->prepare(
                "UPDATE {$this->table}
                 SET email = :new_email, updated_at = NOW()
                 WHERE id = :user_id"
            );
            $updateStmt->execute([
                'new_email' => $newEmail,
                'user_id' => $userId,
            ]);

            $con->commit();
            return true;
        } catch (Throwable $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            error_log('UserModel::changeEmailWithAudit error: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($userId, $newPassword)
    {
        $userId = (int)$userId;
        if ($userId <= 0 || trim((string)$newPassword) === '') {
            return false;
        }

        $hashedPassword = password_hash((string)$newPassword, PASSWORD_DEFAULT);

        return $this->write(
            "UPDATE {$this->table}
             SET password = :password,
                 password_updated_at = NOW(),
                 updated_at = NOW()
             WHERE id = :id",
            [
                'id' => $userId,
                'password' => $hashedPassword,
            ]
        ) !== false;
    }

    public function deactivateAccount($userId, $reason = null)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            return false;
        }

        $cleanReason = trim((string)$reason);
        if ($cleanReason === '') {
            $cleanReason = null;
        } elseif (strlen($cleanReason) > 500) {
            $cleanReason = substr($cleanReason, 0, 500);
        }

        return $this->write(
            "UPDATE {$this->table}
             SET status = 'inactive',
                 deactivated_at = NOW(),
                 deactivation_reason = :reason,
                 updated_at = NOW()
             WHERE id = :id",
            [
                'id' => $userId,
                'reason' => $cleanReason,
            ]
        ) !== false;
    }

    public function update($id, $data, $id_column = 'id')
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns, true)) {
                    unset($data[$key]);
                }
            }
        }

        if (empty($data)) {
            return false;
        }

        $keys = array_keys($data);
        $query = "UPDATE {$this->table} SET ";

        foreach ($keys as $key) {
            $query .= $key . ' = :' . $key . ', ';
        }

        $query = trim($query, ', ');
        $query .= ', updated_at = NOW()';
        $query .= " WHERE {$id_column} = :{$id_column}";

        $data[$id_column] = $id;

        return $this->write($query, $data) !== false;
    }
}
