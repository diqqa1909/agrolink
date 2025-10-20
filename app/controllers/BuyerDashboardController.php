<?php
class BuyerDashboardController {
    use Controller;
    
    private $cartModel;
    
    public function __construct() {
        $this->cartModel = new CartModel();
    }
    
    public function index() {
        $data = [];
        
        // Check if user is logged in and is a buyer
        if(!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer'){
            redirect('login');
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);
        
        $data = [
            'username' => $_SESSION['USER']->name,
            'cartItemCount' => $cartItemCount
        ];
        
        // Load the view
        $this->view('buyerDashboard', $data);
    }
}