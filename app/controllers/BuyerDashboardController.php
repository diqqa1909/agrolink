<?php
class Buyerdashboard{
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
        
        // Load Products model
        $productModel = new ProductsModel();
        
        // Fetch all available products with farmer details
        $products = $productModel->getWithFarmerDetails();
        
        $data = [
            'username' => $_SESSION['USER']->name,
            'cartItemCount' => $cartItemCount,
            'products' => $products ?: []
        ];
        
        // Load the view
        $this->view('buyerDashboard', $data);
    }
}