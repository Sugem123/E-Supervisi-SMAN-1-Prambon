<?php

/*
 * --------------------------------------------------------------------
 * Admin Routes
 * --------------------------------------------------------------------
 */

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Admin routes
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    // Dashboard route
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Profile routes
    $routes->get('profile', 'ProfileController::index');
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('profile/update-photo', 'ProfileController::updatePhoto');
    $routes->post('profile/change-password', 'ProfileController::changePassword');

    // Backup & Restore routes
    $routes->get('backup', 'BackupController::index');
    $routes->post('backup/create', 'BackupController::backup');
    $routes->post('backup/restore', 'BackupController::restore');
    $routes->post('backup/restore-file/(:any)', 'BackupController::restoreExisting/$1');
    $routes->get('backup/download/(:any)', 'BackupController::download/$1');
    $routes->get('backup/delete/(:any)', 'BackupController::delete/$1');

    // User management routes
    $routes->get('users', 'UserController::index');
    $routes->get('users/create', 'UserController::create');
    $routes->post('users/store', 'UserController::store');
    $routes->get('users/(:num)/edit', 'UserController::edit/$1');
    $routes->post('users/(:num)/update', 'UserController::update/$1');
    $routes->get('users/(:num)/delete', 'UserController::delete/$1');
    $routes->post('users/bulk-delete', 'UserController::bulkDelete');

    // Guru routes
    $routes->get('guru', 'GuruController::index');
    $routes->get('guru/create', 'GuruController::create');
    $routes->post('guru/store', 'GuruController::store');
    $routes->get('guru/(:num)/edit', 'GuruController::edit/$1');
    $routes->post('guru/(:num)/update', 'GuruController::update/$1');
    $routes->get('guru/(:num)/delete', 'GuruController::delete/$1');
    $routes->get('guru/import', 'GuruController::import');
    $routes->post('guru/import', 'GuruController::processImport');

    // Supervisor routes
    $routes->get('supervisor', 'SupervisorController::index');
    $routes->get('supervisor/create', 'SupervisorController::create');
    $routes->post('supervisor/store', 'SupervisorController::store');
    $routes->get('supervisor/(:num)/edit', 'SupervisorController::edit/$1');
    $routes->post('supervisor/(:num)/update', 'SupervisorController::update/$1');
    $routes->get('supervisor/(:num)/delete', 'SupervisorController::delete/$1');

    // Pengguna routes
    $routes->get('pengguna', 'PenggunaController::index');
    $routes->get('pengguna/semua', 'PenggunaController::semua');
    $routes->get('pengguna/guru', 'PenggunaController::guru');
    $routes->get('pengguna/supervisor', 'PenggunaController::supervisor');
    $routes->get('pengguna/create', 'PenggunaController::create');
    $routes->post('pengguna/store', 'PenggunaController::store');
    $routes->get('pengguna/(:num)/edit', 'PenggunaController::edit/$1');
    $routes->post('pengguna/(:num)/update', 'PenggunaController::update/$1');
    $routes->get('pengguna/guru/edit', 'PenggunaController::editGuru');
    $routes->get('pengguna/guru/(:num)/edit', 'PenggunaController::editGuru/$1');
    $routes->post('pengguna/guru/(:num)/update', 'PenggunaController::updateGuru/$1');
    $routes->get('pengguna/guru/(:num)/make-supervisor', 'PenggunaController::makeSupervisor/$1');
    $routes->get('pengguna/sync-guru-usernames', 'PenggunaController::syncGuruUsernames');
    $routes->get('pengguna/sync-guru-nip', 'PenggunaController::syncGuruNip');
    $routes->get('pengguna/sync-guru-data', 'PenggunaController::syncGuruData');
    $routes->get('pengguna/supervisor/(:num)/edit', 'PenggunaController::editSupervisor/$1');
    $routes->post('pengguna/supervisor/(:num)/update', 'PenggunaController::updateSupervisor/$1');
    $routes->get('pengguna/delete', 'PenggunaController::delete');
    $routes->get('pengguna/(:num)/delete', 'PenggunaController::delete/$1');
    $routes->get('pengguna/(:num)/toggle-status', 'PenggunaController::toggleStatus/$1');
    $routes->post('pengguna/(:num)/reset-password', 'PenggunaController::resetPassword/$1');
    $routes->get('pengguna/(:num)/reset-password', 'PenggunaController::resetPassword/$1');

    // Import Guru routes
    $routes->get('pengguna/import-guru', 'PenggunaController::importGuru');
    $routes->get('pengguna/import-guru/template', 'PenggunaController::downloadTemplate');
    $routes->post('pengguna/import-guru/process', 'PenggunaController::processImportGuru');

    // Akademik routes
    $routes->get('akademik/tahun-ajar', 'AkademikController::tahunAjar');
    $routes->post('akademik/tahun-ajar/create', 'AkademikController::createTahunAjar');
    $routes->post('akademik/tahun-ajar/(:num)/update', 'AkademikController::updateTahunAjar/$1');
    $routes->get('akademik/kelas', 'AkademikController::kelas');
    $routes->post('akademik/kelas/create', 'AkademikController::createKelas');
    $routes->get('akademik/kelas/(:num)/edit', 'AkademikController::editKelas/$1');
    $routes->post('akademik/kelas/(:num)/update', 'AkademikController::updateKelas/$1');

    // Instrumen routes
    $routes->get('instrumen', 'InstrumenController::index');
    $routes->get('instrumen/jenis-penilaian', 'InstrumenController::jenisPenilaian');
    $routes->post('instrumen/jenis-penilaian/create', 'InstrumenController::createJenisPenilaian');
    $routes->post('instrumen/jenis-penilaian/(:num)/update', 'InstrumenController::updateJenisPenilaian/$1');
    $routes->get('instrumen/jenis-penilaian/(:num)/delete', 'InstrumenController::deleteJenisPenilaian/$1');
    $routes->get('instrumen/aspek-penilaian', 'InstrumenController::aspekPenilaian');
    $routes->post('instrumen/aspek-penilaian/create', 'InstrumenController::createAspekPenilaian');
    $routes->post('instrumen/aspek-penilaian/(:num)/update', 'InstrumenController::updateAspekPenilaian/$1');
    $routes->get('instrumen/aspek-penilaian/(:num)/delete', 'InstrumenController::deleteAspekPenilaian/$1');

    // Jadwal routes
    $routes->get('jadwal', 'JadwalController::index');
    $routes->get('jadwal/create', 'JadwalController::create');
    $routes->post('jadwal/store', 'JadwalController::store');
    $routes->get('jadwal/generate', 'GenerateJadwalController::index');
    $routes->post('jadwal/generate-preview', 'GenerateJadwalController::preview');
    $routes->post('jadwal/generate-save', 'GenerateJadwalController::save');
    $routes->post('jadwal/bulk-delete', 'JadwalController::bulkDelete');
    $routes->get('jadwal/(:num)', 'JadwalController::show/$1'); // Added this missing route
    $routes->get('jadwal/(:num)/edit', 'JadwalController::edit/$1');
    $routes->post('jadwal/(:num)/update', 'JadwalController::update/$1');
    $routes->get('jadwal/(:num)/delete', 'JadwalController::delete/$1');
    $routes->get('jadwal/cetak-pdf', 'JadwalController::cetakPdf');

    // Penilaian routes
    $routes->get('penilaian', 'PenilaianController::index');
    $routes->get('penilaian/form/(:num)', 'PenilaianController::form/$1');
    $routes->post('penilaian/save', 'PenilaianController::save');
    $routes->post('penilaian/quick-update', 'PenilaianController::quickUpdate');
    $routes->post('penilaian/complete/(:num)', 'PenilaianController::complete/$1');
    $routes->get('penilaian/(:num)/edit', 'PenilaianController::form/$1');
    $routes->get('penilaian/(:num)', 'PenilaianController::detail/$1');
    $routes->get('penilaian/detail/(:num)', 'PenilaianController::detail/$1');
    $routes->get('penilaian/(:num)/cetak-pdf', 'PenilaianController::cetakPdf/$1');

    // Foto Bukti routes
    $routes->get('foto-bukti/upload/(:num)', 'FotoBuktiController::uploadForm/$1');
    $routes->post('foto-bukti/upload/(:num)', 'FotoBuktiController::upload/$1');
    $routes->post('foto-bukti/delete/(:num)', 'FotoBuktiController::delete/$1');

    // Laporan routes
    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/cetak-rekap-detail', 'LaporanController::exportPdfRekapDetail');
    $routes->get('laporan/statistik-pengguna', 'LaporanController::statistikPengguna');
    $routes->get('laporan/hasil-supervisi', 'LaporanController::hasilSupervisi');
    $routes->get('laporan/hasil-supervisi/detail/(:num)', 'LaporanController::detail/$1');
    $routes->get('laporan/hasil-supervisi/edit/(:num)', 'PenilaianController::form/$1');
    $routes->get('laporan/hasil-supervisi/cetak-detail/(:num)', 'LaporanController::cetakDetail/$1');
    $routes->get('laporan/hasil-supervisi/pdf', 'LaporanController::exportPdf');
    $routes->get('laporan/hasil-supervisi/excel', 'LaporanController::exportExcel');
    $routes->post('laporan/hasil-supervisi/batch-excel', 'LaporanController::batchExcel');
    $routes->get('laporan/hasil-supervisi/batch-excel-all', 'BatchExcelController::exportAll');
    $routes->get('laporan/export', 'LaporanController::export');
    $routes->get('laporan/export-pdf', 'LaporanController::exportPdf');
    $routes->get('laporan/audit-log', 'AuditLogController::index');
    $routes->get('laporan/audit-log/delete/(:num)', 'AuditLogController::delete/$1');
    $routes->post('laporan/audit-log/bulk-delete', 'AuditLogController::bulkDelete');

    // Pengaturan routes
    $routes->get('pengaturan', 'PengaturanController::index');
    $routes->get('pengaturan/identitas-madrasah', 'PengaturanController::identitasMadrasah');
    $routes->post('pengaturan/identitas-madrasah/update', 'PengaturanController::updateIdentitasMadrasah');
    $routes->post('pengaturan/update-pimpinan', 'PengaturanController::updatePimpinan');
    $routes->post('pengaturan/update-password', 'PengaturanController::updatePassword');
    $routes->post('pengaturan/upload-asset/(:any)', 'PengaturanController::uploadAsset/$1');
    $routes->get('pengaturan/sync-profile', 'PengaturanController::syncProfile');
    $routes->post('pengaturan/update-identitas', 'PengaturanController::updateIdentitas');
    $routes->post('pengaturan/update-kop', 'PengaturanController::updateKop');
    $routes->get('pengaturan/identitas-madrasah/delete-logo/(:any)', 'PengaturanController::deleteLogo/$1');

    // Tahun Ajaran Routes
    $routes->get('pengaturan/tahun-ajar', 'TahunAjarController::index');
    $routes->post('pengaturan/tahun-ajar/store', 'TahunAjarController::store');
    $routes->post('pengaturan/tahun-ajar/update/(:num)', 'TahunAjarController::update/$1');
    $routes->delete('pengaturan/tahun-ajar/delete/(:num)', 'TahunAjarController::delete/$1');
    $routes->get('pengaturan/tahun-ajar/activate/(:num)', 'TahunAjarController::activate/$1');
});
