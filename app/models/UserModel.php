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
}
