<?php
class BuyerDashboard {
    use Controller;
    
    public function index() {

            $data=[];
            if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer'){
                $data['username'] = $_SESSION['USER']->name;
            }
        
        // Load the view
        $this->view('buyerDashboard', $data);
    }
}