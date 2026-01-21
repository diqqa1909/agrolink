<?php
class BuyerDashboardController
{
    use Controller;

    private $cartModel;
    private $wishlistModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->wishlistModel = new WishlistModel();
    }

    public function index()
    {
        $data = [];

        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
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
            'pageTitle' => 'Dashboard',
            'activePage' => 'dashboard',
            'username' => $_SESSION['USER']->name,
            'cartItemCount' => $cartItemCount,
            'products' => $products ?: [],
            'wishlistItems' => $wishlistItems ?: [],
            'pageScript' => 'buyerDashboard.js',
            'contentView' => 'buyer/buyerDashboard.view.php'
        ];

        // Load the view through main layout
        $this->view('components/buyerLayout', $data);
    }
}
