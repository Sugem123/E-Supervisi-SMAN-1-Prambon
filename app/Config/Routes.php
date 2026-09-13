<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index', ['as' => 'home']);

// Route untuk waktu WIB
$routes->get('/waktu', 'WaktuController::index');
$routes->get('/waktu/current', 'WaktuController::getCurrentTime');

// Authentication routes
$routes->get('/auth/login', 'Auth::login', ['as' => 'login']);
$routes->post('/auth/attemptLogin', 'Auth::attemptLogin', ['as' => 'attempt-login']);
$routes->get('/auth/logout', 'Auth::logout', ['as' => 'logout']);

// Dashboard route
$routes->get('/dashboard', 'Dashboard::index', ['as' => 'dashboard']);

// Daftar modul routes yang akan dimuat
$modules = [
    'auth',
    'dashboard',
    'admin',
    'supervisor',
    'guru',
    'kepala'
];

$routes->group('guru', ['namespace' => 'App\Controllers\Guru'], function($routes) {
    $routes->get('/', 'DashboardController::index', ['as' => 'guru/dashboard']);
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'guru/dashboard']);
    $routes->get('dashboard/kinerja', 'DashboardController::kinerja');
});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('/', 'DashboardController::index', ['as' => 'admin/dashboard']);
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin/dashboard']);
});

$routes->group('supervisor', ['namespace' => 'App\Controllers\Supervisor'], function($routes) {
    $routes->get('/', 'DashboardController::index', ['as' => 'supervisor/dashboard']);
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'supervisor/dashboard']);
});

$routes->group('kepala', ['namespace' => 'App\Controllers\Kepala'], function($routes) {
    $routes->get('/', 'DashboardController::index', ['as' => 'kepala/dashboard']);
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'kepala/dashboard']);
});

// Memuat file routes modular jika ada
foreach ($modules as $module) {
    $routeFile = APPPATH . 'Config/Routes/' . $module . '.php';
    if (is_file($routeFile)) {
        require $routeFile;
    }
}

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */

if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}