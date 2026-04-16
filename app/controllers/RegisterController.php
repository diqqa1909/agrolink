<?php
class RegisterController
{
    use Controller;
    public function index($a = '', $b = '', $c = '')
    {
        if (redirectIfLoggedIn()) {
            return;
        }

        /* show($_POST); */
        $user = new UserModel;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if ($user->validate($_POST)) {
                $userId = $user->insert($_POST);
                
                // If user is a buyer, create buyer profile
                if (!empty($_POST['role']) && $_POST['role'] === 'buyer') {
                    $buyerModel = new BuyerModel();
                    $buyerModel->createProfile($userId, []);
                }
                
                // Redirect with a URL flag so the login page can show a JS notification (no server flash)
                redirect('login?registered=1');
            }
        }
        $data['errors'] = $user->errors;
        $this->view('register', $data);
    }
}
