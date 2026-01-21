<?php

class BuyerReviewsController
{
    use Controller;

    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        // Redirect to dashboard with reviews section hash - reviews section is in buyerDashboard.view.php
        redirect('buyerDashboard#reviews');
    }
}
