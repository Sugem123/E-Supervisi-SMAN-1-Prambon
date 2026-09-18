<?php

/*
 * --------------------------------------------------------------------
 * Dashboard Routes
 * --------------------------------------------------------------------
 */

// Dashboard routes (role dashboards get canonical names used by Auth redirects)
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('/admin/dashboard', 'Admin\DashboardController::index', ['as' => 'admin/dashboard', 'filter' => 'auth:admin']);
$routes->get('/supervisor/dashboard', 'Supervisor\DashboardController::index', ['as' => 'supervisor/dashboard', 'filter' => 'auth:supervisor']);
$routes->get('/guru/dashboard', 'Guru\DashboardController::index', ['as' => 'guru/dashboard', 'filter' => 'auth:guru']);
$routes->get('/kepala/dashboard', 'Kepala\DashboardController::index', ['as' => 'kepala/dashboard', 'filter' => 'auth:kepala']);