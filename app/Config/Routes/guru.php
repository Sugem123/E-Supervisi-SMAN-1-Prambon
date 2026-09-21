<?php

/*
 * --------------------------------------------------------------------
 * Guru Routes
 * --------------------------------------------------------------------
 */

$routes->group('guru', ['filter' => 'auth:guru'], function ($routes) {
    $routes->get('', 'Guru\DashboardController::index');
    $routes->get('/', 'Guru\DashboardController::index');
    $routes->get('dashboard', 'Guru\DashboardController::index');
    $routes->get('dashboard/kinerja', 'Guru\DashboardController::kinerja');

    // Profile routes
    $routes->get('profile/edit', 'Guru\ProfileController::edit');
    $routes->post('profile/update', 'Guru\ProfileController::update');
    $routes->post('profile/update-photo', 'Guru\ProfileController::updatePhoto');

    // Jadwal routes
    $routes->get('jadwal', 'Guru\JadwalController::index');
    $routes->post('jadwal/(:num)/update-kelas', 'Guru\JadwalController::updateKelas/$1');
    $routes->post('jadwal/(:num)/ajukan-batal', 'Guru\JadwalController::ajukanBatal/$1');
    $routes->post('jadwal/(:num)/batalkan-ajuan', 'Guru\JadwalController::batalkanAjuan/$1');

    // Hasil routes
    $routes->get('hasil', 'Guru\HasilController::index');
    $routes->get('hasil/(:num)', 'Guru\HasilController::detail/$1');
    $routes->get('hasil/cetak/(:num)', 'Guru\PdfController::cetak/$1');

    // Dokumen Ajar routes
    $routes->get('dokumen-ajar', 'Guru\DokumenAjarController::list');
    $routes->get('dokumen-ajar/manage/(:num)', 'Guru\DokumenAjarController::index/$1');
    $routes->post('dokumen-ajar/save', 'Guru\DokumenAjarController::save');
    $routes->match(['get', 'post', 'delete'], 'dokumen-ajar/delete/(:num)', 'Guru\DokumenAjarController::delete/$1');
});
