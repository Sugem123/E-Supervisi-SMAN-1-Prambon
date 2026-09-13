<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Kepala Sekolah Dashboard - Sistem Supervisi</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    
    <!-- Custom styles for this page -->
    <link href="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/kepala') ?>">
                <div class="sidebar-brand-icon">
                    <?php if (session()->get('sidebar_logo')): ?>
                        <img src="<?= base_url('uploads/' . session()->get('sidebar_logo')) ?>" alt="Logo" class="img-fluid" style="max-height: 40px;">
                    <?php else: ?>
                        <i class="fas fa-user-tie"></i>
                    <?php endif; ?>
                </div>
                <div class="sidebar-brand-text mx-3">Sistem Supervisi</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <?php 
            // Gunakan pendekatan yang lebih aman untuk menentukan segmen URI
            $uri = service('uri');
            $path = $uri->getPath();
            $segments = explode('/', trim($path, '/'));
            $segment2 = isset($segments[1]) ? $segments[1] : '';
            $segment3 = isset($segments[2]) ? $segments[2] : '';
            ?>
            <li class="nav-item <?= ($segment2 == '' || $segment2 == 'dashboard') && $segment3 != 'performance' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/kepala') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            
            <!-- Nav Item - Dashboard Dropdown -->
            <li class="nav-item <?= ($segment2 == 'dashboard' && $segment3 == 'performance') ? 'active' : '' ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDashboard"
                    aria-expanded="true" aria-controls="collapseDashboard">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <div id="collapseDashboard" class="collapse" aria-labelledby="headingDashboard" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Jenis Dashboard:</h6>
                        <a class="collapse-item" href="<?= base_url('/kepala') ?>">Dashboard Utama</a>
                        <a class="collapse-item" href="<?= base_url('/kepala/dashboard/performance') ?>">Dashboard Kinerja</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Supervisi
            </div>

            <!-- Nav Item - Jadwal Supervisi -->
            <li class="nav-item <?= $segment2 == 'jadwal' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/kepala/jadwal') ?>">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Jadwal Supervisi</span></a>
            </li>

            <!-- Nav Item - Penilaian -->
            <li class="nav-item <?= $segment2 == 'hasil' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/kepala/hasil') ?>">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Penilaian</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Laporan
            </div>

            <!-- Nav Item - Laporan -->
            <li class="nav-item <?= $segment2 == 'laporan' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/kepala/laporan') ?>">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Laporan</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Pengaturan
            </div>

            <!-- Nav Item - Profile -->
            <li class="nav-item <?= $segment2 == 'profile' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('/kepala/profile/edit') ?>">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Profil</span></a>
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
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?= session()->get('username') ?>
                                </span>
                                <?php if (!empty(session()->get('foto_profil'))): ?>
                                    <img class="img-profile rounded-circle"
                                        src="<?= base_url('uploads/profile/' . session()->get('foto_profil')) ?>">
                                <?php else: ?>
                                    <img class="img-profile rounded-circle"
                                        src="<?= base_url('assets/img/undraw_profile.svg') ?>">
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('/kepala/profile/edit') ?>">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
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
                    <?= $this->renderSection('content') ?>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistem Supervisi <?= date('Y') ?></span>
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
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" jika Anda yakin ingin mengakhiri sesi ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="<?= base_url('/auth/logout') ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>

    <!-- Page level plugins -->
    <script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

    <!-- Page level custom scripts -->
    <script src="<?= base_url('assets/js/demo/datatables-demo.js') ?>"></script>
    
    <?= $this->renderSection('scripts') ?>
    
</body>

</html>