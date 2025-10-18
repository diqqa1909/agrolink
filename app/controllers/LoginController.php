<?php
    class Login{
        use Controller;
        public function index($a='', $b='', $c=''){
            $data=[];
            if($_SERVER['REQUEST_METHOD']=="POST"){
                $user = new UserModel;
                $arr['email'] = $_POST['email'];

                $row = $user->first($arr);
                if($row){
                    if($row->password === $_POST['password']){
                        $_SESSION['USER'] = $row;
                        
                        //REDIRECT BASED ON USER ROLE
                        $this->redirectBasedOnRole($row->role);
                        return;
                    }
                }
                $user->errors['email'] = "Wrong Email and Password";
                $data['errors'] = $user->errors;
            }
            $this->view('login', $data);
            
        }

        private function redirectBasedOnRole($role){
            switch ($role) {
                case 'admin':
                    redirect('adminDashboard');
                    break;
                    
                case 'buyer':
                    redirect('buyerDashboard');
                    break;
                    
                case 'farmer':
                    redirect('farmerDashboard');
                    break;
                    
                case 'transporter':
                    redirect('transporterDashboard');
                    break;
            }
        }
    }
