<?php
    class Register{
        use Controller;
        public function index($a='', $b='', $c=''){

            /* show($_POST); */
            $user = new UserModel;
            if($_SERVER['REQUEST_METHOD']=='POST'){
                
                if ($user->validate($_POST)) {
                    $user->insert($_POST);
                    redirect('login');
                }
                
            }
            $data['errors'] = $user->errors;
            $this->view('register', $data);
    

        }
    }
