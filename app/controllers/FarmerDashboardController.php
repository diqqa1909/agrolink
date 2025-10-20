<?php
class FarmerDashboard
{
    use Controller;

    public function index()
    {
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'farmer') {
            return redirect('login'); // or $this->view('login');
        }
        $data = ['username' => $_SESSION['USER']->name];
        $this->view('farmerDashboard', $data);
    }
}
