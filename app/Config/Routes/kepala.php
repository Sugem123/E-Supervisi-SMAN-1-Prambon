<?php

use CodeIgniter\Router\RouteCollection;

$routes->group('kepala', ['filter' => 'auth', 'namespace' => 'App\Controllers\Kepala'], function (RouteCollection $routes) {
    // Dashboard routes
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/performance', 'DashboardController::performance');

    // Jadwal routes
    $routes->get('jadwal', 'JadwalController::index');
    $routes->get('jadwal/create', 'JadwalController::create');
    $routes->post('jadwal/store', 'JadwalController::store');
    $routes->get('jadwal/(:num)', 'JadwalController::detail/$1');
    $routes->get('jadwal/detail/(:num)', 'JadwalController::detail/$1');
    $routes->get('jadwal/edit/(:num)', 'JadwalController::edit/$1');
    $routes->post('jadwal/update/(:num)', 'JadwalController::update/$1');
    $routes->get('jadwal/export-excel', 'JadwalController::exportExcel');
    $routes->get('jadwal/export-pdf', 'JadwalController::exportPdf');

    // Penilaian routes
    $routes->get('penilaian/form/(:num)', 'PenilaianController::form/$1');
    $routes->post('penilaian/save', 'PenilaianController::save');
    $routes->get('penilaian/view/(:num)', 'PenilaianController::view/$1');
    $routes->post('penilaian/complete/(:num)', 'PenilaianController::complete/$1');
    $routes->get('penilaian/print/(:num)', 'PenilaianController::print/$1');

    // Hasil routes
    $routes->get('hasil', 'HasilController::index');
    $routes->get('hasil/detail/(:num)', 'HasilController::detail/$1');
    $routes->get('hasil/export/(:num)', 'HasilController::exportToExcel/$1');
    $routes->get('hasil/pdf/(:num)', 'HasilController::generatePdf/$1');

    // Foto Bukti routes
    $routes->get('foto-bukti/upload/(:num)', 'FotoBuktiController::uploadForm/$1');
    $routes->post('foto-bukti/upload/(:num)', 'FotoBuktiController::upload/$1');
    $routes->post('foto-bukti/delete/(:num)', 'FotoBuktiController::delete/$1');

    // Profile routes
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('profile/update-photo', 'ProfileController::updatePhoto');
    $routes->post('profile/delete-photo', 'ProfileController::deletePhoto');

    // Laporan routes
    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/detail/(:num)', 'LaporanController::detail/$1');

    // Dokumen Ajar routes
    $routes->get('dokumen-ajar/view/(:num)', 'DokumenAjarController::index/$1');
    $routes->post('dokumen-ajar/verify/(:num)', 'DokumenAjarController::verify/$1');
});
