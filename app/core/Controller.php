<?php
trait Controller
{
    public function view($name, $data = [])
    {

        if (!empty($data))
            extract($data);

        // Try root level first
        $filename = "../app/views/" . $name . ".view.php";
        if (file_exists($filename)) {
            require $filename;
            return;
        }

        // Try components subdirectory
        $componentsView = "../app/views/components/" . basename($name) . ".view.php";
        if (file_exists($componentsView)) {
            require $componentsView;
            return;
        }

        // Try role-based subdirectories (admin, buyer, farmer, transporter)
        $roles = ['admin', 'buyer', 'farmer', 'transporter'];
        foreach ($roles as $role) {
            $roleView = "../app/views/{$role}/" . $name . ".view.php";
            if (file_exists($roleView)) {
                require $roleView;
                return;
            }
        }

        // Fallback to 404
        $filename = "../app/views/404.view.php";
        require $filename;
    }
}
