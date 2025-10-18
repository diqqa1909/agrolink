<?php
class TransporterDashboard {
    use Controller;
    
    public function index() {

            $data=[];
            if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'transporter'){
                $data['username'] = $_SESSION['USER']->name;
            }
        
        // Load the view
        $this->view('transporterDashboard', $data);
    }
}