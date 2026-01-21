<?php

class BuyerProductsController
{
    use Controller;

    private $productModel;
    private $cartModel;
    private $wishlistModel;

    public function __construct()
    {
        $this->productModel = new ProductsModel();
        $this->cartModel = new CartModel();
        $this->wishlistModel = new WishlistModel();
    }

    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        $user_id = $_SESSION['USER']->id;
        $cartItemCount = $this->cartModel->getCartItemCount($user_id);

        // Fetch all available products with farmer details
        $products = $this->productModel->getWithFarmerDetails();
        $wishlistItems = $this->wishlistModel->getByUserId($user_id);

        $data = [
            'pageTitle' => 'Browse Products',
            'activePage' => 'products',
            'username' => $_SESSION['USER']->name,
            'cartItemCount' => $cartItemCount,
            'products' => $products ?: [],
            'wishlistItems' => $wishlistItems ?: [],
            'pageScript' => 'buyerDashboard.js',
            'contentView' => 'buyer/products.view.php'
        ];

        // Load the view through main layout
        $this->view('components/buyerLayout', $data);
    }
}
