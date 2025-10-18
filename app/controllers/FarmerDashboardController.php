<?php
class FarmerDashboard {
    use Controller;
    
    public function index() {

            $data=[];
            if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer'){
                $data['username'] = $_SESSION['USER']->name;
            }
        
        // Load the view
        $this->view('farmerDashboard', $data);
    }
}