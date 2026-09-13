<?php

/*
 * --------------------------------------------------------------------
 * Supervisor Routes
 * --------------------------------------------------------------------
 */

$routes->group('supervisor', ['filter' => 'role:supervisor'], function ($routes) {
    $routes->get('/', 'Supervisor\DashboardController::index');
    $routes->get('dashboard', 'Supervisor\DashboardController::index');

    // Profile routes
    $routes->get('profile/edit', 'Supervisor\ProfileController::edit');
    $routes->post('profile/update', 'Supervisor\ProfileController::update');
    $routes->post('profile/update-photo', 'Supervisor\ProfileController::updatePhoto');

    // Penilaian routes
    $routes->get('penilaian', 'Supervisor\JadwalController::index'); // Redirect to jadwal page
    $routes->get('penilaian/form/(:num)', 'Supervisor\PenilaianController::form/$1');
    $routes->post('penilaian/save', 'Supervisor\PenilaianController::save');
    $routes->get('penilaian/view/(:num)', 'Supervisor\PenilaianController::view/$1');
    $routes->post('penilaian/complete/(:num)', 'Supervisor\PenilaianController::complete/$1');
    $routes->get('penilaian/print/(:num)', 'Supervisor\PdfController::cetak/$1');

    // Foto bukti routes
    $routes->get('foto-bukti/upload/(:num)', 'Supervisor\FotoBuktiController::uploadForm/$1');
    $routes->post('foto-bukti/upload/(:num)', 'Supervisor\FotoBuktiController::upload/$1');
    $routes->post('foto-bukti/delete/(:num)', 'Supervisor\FotoBuktiController::delete/$1');

    // Jadwal routes
    $routes->get('jadwal', 'Supervisor\JadwalController::index');
    $routes->get('jadwal/(:num)', 'Supervisor\JadwalController::detail/$1');

    // Hasil routes
    $routes->get('hasil', 'Supervisor\HasilController::index');
    $routes->get('hasil/detail/(:num)', 'Supervisor\HasilController::detail/$1');
    $routes->get('hasil/cetak/(:num)', 'Supervisor\PdfController::cetak/$1');

    // Dokumen Ajar routes
    $routes->get('dokumen-ajar/view/(:num)', 'Supervisor\DokumenAjarController::index/$1');
    $routes->post('dokumen-ajar/verify/(:num)', 'Supervisor\DokumenAjarController::verify/$1');
});
