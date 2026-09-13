<?php

/*
 * --------------------------------------------------------------------
 * Dashboard Routes
 * --------------------------------------------------------------------
 */

// Dashboard routes
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('/admin/dashboard', 'Admin\DashboardController::index', ['filter' => 'auth', 'args' => ['admin']]);
$routes->get('/supervisor/dashboard', 'Supervisor\DashboardController::index', ['filter' => 'auth', 'args' => ['supervisor']]);
$routes->get('/guru/dashboard', 'Guru\DashboardController::index', ['filter' => 'auth', 'args' => ['guru']]);
$routes->get('/kepala/dashboard', 'Kepala\DashboardController::index', ['filter' => 'auth', 'args' => ['kepala']]);