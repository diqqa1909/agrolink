<?php
    class App{
        private $controller = 'home';
        private $method = 'index';
            
        private function splitURL(){
            $URL = $_GET['url']??'home';
            $URL = explode("/", trim($URL,"/"));
            return($URL);
        }

        public function loadController(){
            
            $URL = $this->splitURL();
            
            //SELECT CONTROLLER
            $filename = "../app/controllers/".ucfirst($URL[0])."Controller.php";
            if(file_exists($filename)){
                require $filename;
                // Controller class names include the 'Controller' suffix (e.g. BuyerDashboardController)
                $this->controller = ucfirst($URL[0]) . 'Controller';
                unset($URL[0]);
            }else{
                $filename = "../app/controllers/_404.php";
                require $filename;
                $this->controller = "_404";
            }
            
            // Try to instantiate the controller class. Some controllers in the repo
            // use the 'Controller' suffix (e.g., BuyerDashboardController) while
            // others don't (e.g., Home). We'll try both forms.
            $controllerClass = $this->controller;
            if (!class_exists($controllerClass)) {
                // Fallback: maybe controller was defined without 'Controller' suffix
                $alt = preg_replace('/Controller$/', '', $controllerClass);
                if (class_exists($alt)) {
                    $controllerClass = $alt;
                }
            }

            if (!class_exists($controllerClass)) {
                // If still not found, throw a clear error
                throw new Exception("Controller class '$controllerClass' not found.");
            }

            $controller = new $controllerClass;
            
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
