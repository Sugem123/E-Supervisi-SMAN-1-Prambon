<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Log the request path
        $path = $request->getUri()->getPath();
        log_message('info', 'AuthFilter checking path: ' . $path);
        
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            log_message('info', 'User not logged in, redirecting to login');
            // Prevent redirect loop by checking if we're already going to login page
            if ($path !== '/auth/login') {
                return redirect()->to('/auth/login');
            }
            log_message('info', 'Already on login page, allowing');
            return;
        }
        
        // Check role-based access if arguments provided
        if (!empty($arguments)) {
            $userRole = session()->get('role');
            log_message('info', 'Checking role-based access for role: ' . $userRole);
            
            if (!in_array($userRole, $arguments)) {
                log_message('info', 'Role not allowed, redirecting to role dashboard');
                
                // Set flash message for unauthorized access to admin area
                if (in_array('admin', $arguments)) {
                    session()->setFlashdata('error', 'Anda tidak memiliki izin untuk mengakses halaman administrator.');
                }
                
                // Prevent redirect loop by checking current path
                $currentPath = $path;
                $targetPath = '/' . $userRole;
                
                // Only redirect if we're not already going to the target path
                if ($currentPath !== $targetPath && $currentPath !== $targetPath . '/' && $currentPath !== $targetPath . '/dashboard') {
                    // Redirect to appropriate dashboard based on role
                    return redirect()->to('/' . $userRole);
                }
                log_message('info', 'Already on target path, allowing');
            }
        }
        
        log_message('info', 'AuthFilter allowing access to: ' . $path);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
    }
}