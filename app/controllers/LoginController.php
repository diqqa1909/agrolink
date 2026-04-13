<?php
class LoginController
{
    use Controller;
    public function index($a = '', $b = '', $c = '')
    {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Check if POST data exists
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $user = new UserModel;
                $user->errors['email'] = "Please fill in all fields";
                $data['errors'] = $user->errors;
            } else {
                $user = new UserModel;
                $arr['email'] = $_POST['email'];

                $row = $user->first($arr);

                if ($row) {
                    $status = strtolower((string)($row->status ?? 'active'));
                    if ($status !== 'active') {
                        $user->errors['email'] = "Your account is deactivated. Please contact admin for reactivation.";
                        $data['errors'] = $user->errors;
                    } elseif (password_verify((string)$_POST['password'], (string)$row->password)) {
                        $_SESSION['USER'] = $row;

                        //REDIRECT BASED ON USER ROLE
                        $this->redirectBasedOnRole($row->role);
                        return;
                    }
                }
                if (empty($data['errors'])) {
                    $user->errors['email'] = "Wrong Email and Password";
                    $data['errors'] = $user->errors;
                }
            }
        }
        $this->view('login', $data);
    }

    private function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'buyer':
                redirect('buyerDashboard');
                break;

            case 'farmer':
                redirect('farmerDashboard');
                break;

            case 'transporter':
                redirect('transporterDashboard');
                break;

            case 'admin':
                redirect('adminDashboard');
                break;

            default:
                redirect('home');
                break;
        }
    }
}
