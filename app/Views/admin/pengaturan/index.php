<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Sistem</h1>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 pl-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php 
    $currentTab = $activeTab ?? 'identitas';
    if (!in_array($currentTab, ['identitas', 'kop', 'tahun-ajar'])) {
        $currentTab = 'identitas';
    }
    ?>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" id="pengaturanTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= ($currentTab === 'identitas') ? 'active' : '' ?>" id="identitas-tab" data-toggle="tab" href="#identitas-pane" role="tab" aria-controls="identitas-pane" aria-selected="<?= ($currentTab === 'identitas') ? 'true' : 'false' ?>">
                <i class="fas fa-school mr-1"></i> Identitas Sekolah
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($currentTab === 'kop') ? 'active' : '' ?>" id="kop-tab" data-toggle="tab" href="#kop-pane" role="tab" aria-controls="kop-pane" aria-selected="<?= ($currentTab === 'kop') ? 'true' : 'false' ?>">
                <i class="fas fa-file-invoice mr-1"></i> Kop Instansi (PDF)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($currentTab === 'tahun-ajar') ? 'active' : '' ?>" id="tahun-ajar-tab" data-toggle="tab" href="#tahun-ajar-pane" role="tab" aria-controls="tahun-ajar-pane" aria-selected="<?= ($currentTab === 'tahun-ajar') ? 'true' : 'false' ?>">
                <i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran & Semester
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="pengaturanTabContent">
        <div class="tab-pane fade <?= ($currentTab === 'identitas') ? 'show active' : '' ?>" id="identitas-pane" role="tabpanel" aria-labelledby="identitas-tab">
            <?= $this->include('admin/pengaturan/partials/tab_identitas') ?>
        </div>
        <div class="tab-pane fade <?= ($currentTab === 'kop') ? 'show active' : '' ?>" id="kop-pane" role="tabpanel" aria-labelledby="kop-tab">
            <?= $this->include('admin/pengaturan/partials/tab_kop_surat') ?>
        </div>
        <div class="tab-pane fade <?= ($currentTab === 'tahun-ajar') ? 'show active' : '' ?>" id="tahun-ajar-pane" role="tabpanel" aria-labelledby="tahun-ajar-tab">
            <?= $this->include('admin/pengaturan/partials/tab_tahun_ajar') ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTables for Tahun Ajaran
    if (!$.fn.DataTable.isDataTable('#dataTableTahunAjar')) {
        $('#dataTableTahunAjar').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }

    // Adjust columns and update URL on tab switch
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        var targetId = $(e.target).attr('href');
        var tabName = 'identitas';
        if (targetId === '#kop-pane') {
            tabName = 'kop';
        } else if (targetId === '#tahun-ajar-pane') {
            tabName = 'tahun-ajar';
        }
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabName;
        window.history.replaceState({ path: newUrl }, '', newUrl);
    });

    // Check URL query param or hash on initial load
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var hashParam = window.location.hash;

    if (tabParam === 'kop' || hashParam === '#kop' || hashParam === '#kop-pane' || hashParam === '#cardKopSurat') {
        $('#kop-tab').tab('show');
    } else if (tabParam === 'tahun-ajar' || hashParam === '#tahun-ajar' || hashParam === '#tahun-ajar-pane') {
        $('#tahun-ajar-tab').tab('show');
    } else if (tabParam === 'identitas' || hashParam === '#identitas' || hashParam === '#identitas-pane') {
        $('#identitas-tab').tab('show');
    }
});
</script>
<?= $this->endSection(); ?>