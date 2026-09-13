<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        // If arguments provided, check if user has the required role
        if (!empty($arguments)) {
            $requiredRole = $arguments[0] ?? 'admin';
            if (session()->get('role') !== $requiredRole) {
                // Set flash message for unauthorized access
                session()->setFlashdata('error', 'Anda tidak memiliki izin untuk mengakses halaman administrator.');
                
                // Redirect to appropriate dashboard based on role
                $role = session()->get('role');
                return redirect()->to('/' . $role);
            }
        } else {
            // Default behavior - check if user has admin role
            if (session()->get('role') !== 'admin') {
                // Set flash message for unauthorized access
                session()->setFlashdata('error', 'Anda tidak memiliki izin untuk mengakses halaman administrator.');
                
                // Redirect to appropriate dashboard based on role
                $role = session()->get('role');
                return redirect()->to('/' . $role);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
    }
}