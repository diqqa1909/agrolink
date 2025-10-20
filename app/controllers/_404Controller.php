<?php

class _404
{
    use Controller;

    public function index()
    {
        $data['title'] = "404 - Page Not Found";
        $this->view('404', $data);
    }
}
