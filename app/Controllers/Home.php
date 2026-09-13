<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Redirect to dashboard which will redirect based on user role
        return redirect()->to('/dashboard');
    }
}