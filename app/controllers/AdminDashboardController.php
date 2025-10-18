<?php
class AdminDashboard {
    use Controller;
    
    public function index() {

            $data=[];
            if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'admin'){
                $data['username'] = $_SESSION['USER']->name;
            }
        
        // Load the view
        $this->view('adminDashboard', $data);
    }
}