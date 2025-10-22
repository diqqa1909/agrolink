<?php
class RegisterController
{
    use Controller;
    public function index($a = '', $b = '', $c = '')
    {

        /* show($_POST); */
        $user = new UserModel;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if ($user->validate($_POST)) {
                $user->insert($_POST);
                // Redirect with a URL flag so the login page can show a JS notification (no server flash)
                redirect('login?registered=1');
            }
        }
        $data['errors'] = $user->errors;
        $this->view('register', $data);
    }
}
