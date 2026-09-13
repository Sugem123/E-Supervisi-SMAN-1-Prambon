<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Instrumen Supervisi</h1>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php $currentTab = $activeTab ?? 'jenis'; ?>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-3" id="instrumenTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= ($currentTab === 'jenis') ? 'active' : '' ?>" id="jenis-tab" data-toggle="tab" href="#jenis-pane" role="tab" aria-controls="jenis-pane" aria-selected="<?= ($currentTab === 'jenis') ? 'true' : 'false' ?>">
                <i class="fas fa-layer-group mr-1"></i> Jenis Penilaian
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($currentTab === 'aspek') ? 'active' : '' ?>" id="aspek-tab" data-toggle="tab" href="#aspek-pane" role="tab" aria-controls="aspek-pane" aria-selected="<?= ($currentTab === 'aspek') ? 'true' : 'false' ?>">
                <i class="fas fa-list-check mr-1"></i> Aspek Penilaian
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="instrumenTabContent">
        <div class="tab-pane fade <?= ($currentTab === 'jenis') ? 'show active' : '' ?>" id="jenis-pane" role="tabpanel" aria-labelledby="jenis-tab">
            <?= $this->include('admin/instrumen/partials/tab_jenis_penilaian') ?>
        </div>
        <div class="tab-pane fade <?= ($currentTab === 'aspek') ? 'show active' : '' ?>" id="aspek-pane" role="tabpanel" aria-labelledby="aspek-tab">
            <?= $this->include('admin/instrumen/partials/tab_aspek_penilaian') ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTables for both tabs
    if (!$.fn.DataTable.isDataTable('#dataTableJenis')) {
        $('#dataTableJenis').DataTable({
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

    if (!$.fn.DataTable.isDataTable('#dataTableAspek')) {
        $('#dataTableAspek').DataTable({
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

    // Adjust columns on tab switch
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        var targetId = $(e.target).attr('href');
        var tabName = (targetId === '#aspek-pane') ? 'aspek' : 'jenis';
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabName;
        window.history.replaceState({ path: newUrl }, '', newUrl);
    });

    // Check URL query param or hash on initial load
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var hashParam = window.location.hash;

    if (tabParam === 'aspek' || hashParam === '#aspek' || hashParam === '#aspek-pane') {
        $('#aspek-tab').tab('show');
    } else if (tabParam === 'jenis' || hashParam === '#jenis' || hashParam === '#jenis-pane') {
        $('#jenis-tab').tab('show');
    }

    // Auto-increment urutan for create modal
    var maxUrutanData = <?= json_encode($max_urutan ?? []) ?>;
    $('#jenisPenilaianCreate').on('change', function() {
        var selectedJenisId = $(this).val();
        if (selectedJenisId) {
            var maxUrutan = maxUrutanData[selectedJenisId] || 0;
            $('#urutanCreate').val(parseInt(maxUrutan) + 1);
        } else {
            $('#urutanCreate').val('');
        }
    });
});

window.filterAspekByJenis = function(jenisName) {
    if ($.fn.DataTable.isDataTable('#dataTableAspek')) {
        $('#dataTableAspek').DataTable().column(1).search(jenisName).draw();
    }
};
</script>
<?= $this->endSection(); ?>
