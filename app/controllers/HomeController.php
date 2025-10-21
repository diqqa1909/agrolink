<?php
    class HomeController{
        use Controller;
        public function index($a='', $b='', $c=''){

            $data=[];
            if(!empty($_SESSION['USER'])){
                $data['username'] = $_SESSION['USER']->name;
            }
/* 
            show($_SESSION['USER']); */

            $this->view('home', $data);
        }
    }