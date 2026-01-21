<?php

class BuyerNotificationsController
{
    use Controller;

    public function index()
    {
        // Check if user is logged in and is a buyer
        if (!isset($_SESSION['USER']) || $_SESSION['USER']->role !== 'buyer') {
            redirect('login');
            return;
        }

        // Redirect to dashboard with notifications section hash - notifications section is in buyerDashboard.view.php
        redirect('buyerDashboard#notifications');
    }
}
