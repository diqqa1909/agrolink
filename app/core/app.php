<?php
class App
{
    private $controller = 'Home';
    private $method = 'index';

    private function splitURL()
    {
        $URL = $_GET['url'] ?? 'home';
        $URL = explode("/", trim($URL, "/"));
        return ($URL);
    }

    public function loadController()
    {

        $URL = $this->splitURL();

        //SELECT CONTROLLER - Try root level first
        $filename = "../app/controllers/" . ucfirst($URL[0]) . "Controller.php";
        if (file_exists($filename)) {
            require $filename;
            $this->controller = ucfirst($URL[0]) . 'Controller';
            unset($URL[0]);
        } else {
            // Try role-based subdirectories (admin, buyer, farmer, transporter)
            $roles = ['admin', 'buyer', 'farmer', 'transporter'];
            $found = false;

            foreach ($roles as $role) {
                $roleController = "../app/controllers/{$role}/" . ucfirst($URL[0]) . "Controller.php";
                if (file_exists($roleController)) {
                    require $roleController;
                    $this->controller = ucfirst($URL[0]) . 'Controller';
                    $found = true;
                    unset($URL[0]);
                    break;
                }
            }

            if (!$found) {
                $filename = "../app/controllers/_404.php";
                require $filename;
                $this->controller = "_404";
            }
        }

        $controller = new $this->controller;

        //SELECT METHOD
        if (!empty($URL[1])) {
            if (method_exists($controller, $URL[1])) {
                $this->method = $URL[1];
                unset($URL[1]);
            }
        }
        call_user_func_array([$controller, $this->method], $URL);
    }
}
