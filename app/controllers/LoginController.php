<?php
class LoginController
{
    use Controller;
    public function index($a = '', $b = '', $c = '')
    {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // DEBUG: Check what's being received
            error_log("POST Data: " . print_r($_POST, true));
            error_log("Email empty? " . (empty($_POST['email']) ? 'YES' : 'NO'));
            error_log("Password empty? " . (empty($_POST['password']) ? 'YES' : 'NO'));

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
                    if ($row->password === $_POST['password']) {
                        $_SESSION['USER'] = $row;

                        //REDIRECT BASED ON USER ROLE
                        $this->redirectBasedOnRole($row->role);
                        return;
                    }
                }
                $user->errors['email'] = "Wrong Email and Password";
                $data['errors'] = $user->errors;
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