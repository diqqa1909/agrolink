<?php
class LogoutController
{
    use Controller;
    
    public function index()
    {
        if (isset($_SESSION['USER'])) {
            unset($_SESSION['USER']);
        }
        
        session_destroy();
        redirect('home');
    }
}