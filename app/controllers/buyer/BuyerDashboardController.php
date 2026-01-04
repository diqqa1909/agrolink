<?php
class BuyerDashboardController{
    use Controller;
    
    private $cartModel;
    private $wishlistModel;
    
    public function __construct() {
        $this->cartModel = new CartModel();
        $this->wishlistModel = new WishlistModel();
    }
    
    public function index() {
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
        $wishlistItems = $this->wishlistModel->getByUserId($user_id);
        
        $data = [
            'username' => $_SESSION['USER']->name,
            'cartItemCount' => $cartItemCount,
            'products' => $products ?: [],
            'wishlistItems' => $wishlistItems ?: [],
            'pageTitle' => 'Buyer Dashboard',
            'activePage' => 'dashboard',
            'contentView' => '../app/views/buyer/buyerDashboard.view.php'
        ];
        
        // Load the layout wrapper
        $this->view('components/buyerLayout', $data);
    }
}