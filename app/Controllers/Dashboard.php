<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        // Redirect based on user role
        $role = session()->get('role');
        
        switch ($role) {
            case 'admin':
                return redirect()->to('/admin');
            case 'guru':
                return redirect()->to('/guru');
            case 'supervisor':
                return redirect()->to('/supervisor');
            case 'kepala':
                return redirect()->to('/kepala');
            default:
                // If role is not recognized, logout user
                session()->destroy();
                return redirect()->to('/auth/login');
        }
    }
}