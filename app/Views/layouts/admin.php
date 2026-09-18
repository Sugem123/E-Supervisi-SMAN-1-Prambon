<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin Dashboard - Sistem Supervisi</title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/supervisi-premium.css') ?>" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/admin') ?>">
                <div class="sidebar-brand-icon">
                    <?php $sbLogo = session()->get('sidebar_logo') ?: session()->get('logo'); ?>
                    <?php if (!empty($sbLogo)): ?>
                        <img src="<?= base_url('uploads/' . $sbLogo) ?>" onerror="this.onerror=null;this.src='<?= base_url('assets/img/logo-placeholder.svg') ?>'" alt="Logo" class="img-fluid" style="max-height: 40px;">
                    <?php else: ?>
                        <img src="<?= base_url('assets/img/logo-placeholder.svg') ?>" alt="Logo sekolah belum diatur" class="img-fluid" style="max-height: 40px;background:#fff;border-radius:10px;padding:3px;">
                    <?php endif; ?>
                </div>
                <div class="sidebar-brand-text mx-3">Sistem Supervisi</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <?php 
                $currUri = uri_string();
                $isPengguna = (strpos($currUri, 'admin/pengguna') === 0);
                $isInstrumen = (strpos($currUri, 'admin/instrumen') === 0);
            ?>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item <?= ($currUri === 'admin' || $currUri === 'admin/dashboard' || $currUri === '') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading 1: Master Data & Instrumen -->
            <div class="sidebar-heading">
                Master Data & Instrumen
            </div>

            <!-- Nav Item - Manajemen Pengguna Collapse -->
            <li class="nav-item <?= $isPengguna ? 'active' : '' ?>">
                <a class="nav-link <?= $isPengguna ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapsePengguna"
                    aria-expanded="<?= $isPengguna ? 'true' : 'false' ?>" aria-controls="collapsePengguna">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Manajemen Pengguna</span>
                </a>
                <div id="collapsePengguna" class="collapse <?= $isPengguna ? 'show' : '' ?>" aria-labelledby="headingPengguna" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Daftar Pengguna:</h6>
                        <a class="collapse-item <?= $currUri === 'admin/pengguna/guru' ? 'active font-weight-bold' : '' ?>" href="<?= base_url('/admin/pengguna/guru') ?>">Guru</a>
                        <a class="collapse-item <?= $currUri === 'admin/pengguna/supervisor' ? 'active font-weight-bold' : '' ?>" href="<?= base_url('/admin/pengguna/supervisor') ?>">Supervisor</a>
                        <a class="collapse-item <?= $currUri === 'admin/pengguna/semua' ? 'active font-weight-bold' : '' ?>" href="<?= base_url('/admin/pengguna/semua') ?>">Semua Pengguna</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Kelas / Rombel (Direct Link) -->
            <li class="nav-item <?= strpos($currUri, 'admin/akademik/kelas') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/akademik/kelas') ?>">
                    <i class="fas fa-fw fa-graduation-cap"></i>
                    <span>Kelas & Rombel</span>
                </a>
            </li>

            <!-- Nav Item - Kelompok Supervisi -->
            <li class="nav-item <?= strpos($currUri, 'admin/kelompok') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/kelompok') ?>">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Kelompok Supervisi</span>
                </a>
            </li>

            <!-- Nav Item - Instrumen Supervisi Collapse -->
            <li class="nav-item <?= $isInstrumen ? 'active' : '' ?>">
                <a class="nav-link <?= $isInstrumen ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#collapseInstrumen"
                    aria-expanded="<?= $isInstrumen ? 'true' : 'false' ?>" aria-controls="collapseInstrumen">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Instrumen Supervisi</span>
                </a>
                <div id="collapseInstrumen" class="collapse <?= $isInstrumen ? 'show' : '' ?>" aria-labelledby="headingInstrumen" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Komponen Instrumen:</h6>
                        <a class="collapse-item <?= (strpos($currUri, 'admin/instrumen') === 0 && strpos($_SERVER['QUERY_STRING'] ?? '', 'tab=aspek') === false) ? 'active font-weight-bold' : '' ?>" href="<?= base_url('/admin/instrumen?tab=jenis') ?>">Jenis Penilaian</a>
                        <a class="collapse-item <?= (strpos($_SERVER['QUERY_STRING'] ?? '', 'tab=aspek') !== false) ? 'active font-weight-bold' : '' ?>" href="<?= base_url('/admin/instrumen?tab=aspek') ?>">Aspek Penilaian</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading 2: Pelaksanaan & Laporan -->
            <div class="sidebar-heading">
                Pelaksanaan & Laporan
            </div>

            <!-- Nav Item - Jadwal Supervisi -->
            <li class="nav-item <?= strpos($currUri, 'admin/jadwal') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/jadwal') ?>">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Jadwal Supervisi</span>
                </a>
            </li>

            <!-- Nav Item - Hasil Supervisi -->
            <li class="nav-item <?= strpos($currUri, 'admin/laporan/hasil-supervisi') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/laporan/hasil-supervisi') ?>">
                    <i class="fas fa-fw fa-clipboard-check"></i>
                    <span>Hasil Supervisi</span>
                </a>
            </li>

            <!-- Nav Item - Laporan & Rekapitulasi -->
            <li class="nav-item <?= ($currUri === 'admin/laporan' || ($currUri !== 'admin/laporan/hasil-supervisi' && $currUri !== 'admin/laporan/audit-log' && strpos($currUri, 'admin/laporan') === 0)) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/laporan') ?>">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Laporan & Rekapitulasi</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading 3: Pengaturan & Sistem -->
            <div class="sidebar-heading">
                Pengaturan & Sistem
            </div>

            <!-- Nav Item - Pengaturan Lembaga -->
            <li class="nav-item <?= strpos($currUri, 'admin/pengaturan') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/pengaturan') ?>">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Pengaturan Sistem</span>
                </a>
            </li>

            <!-- Nav Item - Profil Saya -->
            <li class="nav-item <?= strpos($currUri, 'admin/profile') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/profile/edit') ?>">
                    <i class="fas fa-fw fa-user-cog"></i>
                    <span>Profil Saya</span>
                </a>
            </li>

            <!-- Nav Item - Backup & Restore -->
            <li class="nav-item <?= strpos($currUri, 'admin/backup') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/backup') ?>">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Backup & Restore</span>
                </a>
            </li>

            <!-- Nav Item - Audit & Log Sistem -->
            <li class="nav-item <?= strpos($currUri, 'admin/laporan/audit-log') === 0 ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/admin/laporan/audit-log') ?>">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Audit & Log Sistem</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php if (!empty(session()->get('foto_profil'))): ?>
                                    <img class="img-profile rounded-circle mr-2"
                                        src="<?= base_url('uploads/profile/' . session()->get('foto_profil')) ?>"
                                        alt="Profile" width="30" height="30">
                                <?php else: ?>
                                    <img class="img-profile rounded-circle mr-2"
                                        src="<?= base_url('assets/img/undraw_profile.svg') ?>"
                                        alt="Profile" width="30" height="30">
                                <?php endif; ?>
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= session()->get('username') ?></span>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('/admin/profile/edit') ?>">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Flash Message -->
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?= $this->renderSection('content') ?>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; TIM IT SMAN 1 PRAMBON <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?= base_url('/auth/logout') ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>

    <!-- Page level plugins -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="<?= base_url('assets/js/demo/datatables-demo.js') ?>"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= addslashes(session()->getFlashdata('success')) ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: '<?= addslashes(session()->getFlashdata('error')) ?>',
            });
        <?php endif; ?>
    </script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>