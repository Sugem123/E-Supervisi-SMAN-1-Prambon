<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Kinerja Guru</h1>
    </div>
    


    <!-- Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Dashboard</h6>
        </div>
        <div class="card-body">
            <p>Dashboard ini menampilkan peringkat guru berdasarkan nilai akhir supervisi rata-rata. 
            Grafik garis menunjukkan perkembangan nilai per supervisi untuk setiap guru. 
            Gunakan filter berdasarkan tahun ajaran untuk melihat data yang lebih spesifik.</p>
            
            <?php if (isset($tahun_ajar) && $tahun_ajar): ?>
                <p><strong>Tahun Ajaran Aktif:</strong> <?= $tahun_ajar['tahun_ajar'] ?> - Semester <?= $tahun_ajar['semester'] ?></p>
            <?php else: ?>
                <p class="text-warning">Tidak ada tahun ajaran aktif saat ini.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('kepala/dashboard/performance') ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tahun_ajar_id">Tahun Ajaran</label>
                            <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control">
                                <option value="">Semua Tahun Ajaran</option>
                                <?php if (isset($tahun_ajaran_list)): ?>
                                    <?php foreach ($tahun_ajaran_list as $tahun): ?>
                                        <option value="<?= $tahun['id']; ?>" <?= (isset($_GET['tahun_ajar_id']) && $_GET['tahun_ajar_id'] == $tahun['id']) ? 'selected' : ''; ?>>
                                            <?= esc($tahun['tahun_ajar']); ?> - Semester <?= esc($tahun['semester']); ?>
                                            <?= $tahun['status_aktif'] == 'Aktif' ? '(Aktif)' : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            <a href="<?= base_url('kepala/dashboard/performance'); ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Peringkat Kinerja Guru</h6>
        </div>
        <div class="card-body">
            <?php if (isset($performance_data) && !empty($performance_data)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="performanceTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Rata-rata Nilai</th>
                                <th>Kategori</th>
                                <th>Jumlah Supervisi</th>
                                <th>Tren Kinerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; ?>
                            <?php foreach ($performance_data as $data): ?>
                                <tr>
                                    <td><?= $rank++ ?></td>
                                    <td><?= isset($data['guru']['nama']) ? esc($data['guru']['nama']) : 'N/A' ?></td>
                                    <td>
                                        <?php if (!empty($data['subjects'])):
                                            echo implode(', ', array_map('esc', $data['subjects']));
                                        else:
                                            echo isset($data['guru']['mata_pelajaran']) ? esc($data['guru']['mata_pelajaran']) : 'N/A';
                                        endif; ?>
                                    </td>
                                    <td><?= number_format($data['rata_rata'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $data['kategori_class'] ?>">
                                            <?= $data['kategori'] ?>
                                        </span>
                                    </td>
                                    <td><?= $data['jumlah_supervisi'] ?></td>
                                    <td>
                                        <?php if (!empty($data['trend'])): ?>
                                            <div class="performance-chart" 
                                                 data-trend='<?= json_encode($data['trend']) ?>' 
                                                 data-teacher='<?= isset($data['guru']['nama']) ? esc($data['guru']['nama']) : 'Unknown' ?>'
                                                 style="height: 50px; width: 150px;">
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada data</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Tidak ada data kinerja guru yang tersedia.
                    <?php if (isset($performance_data) && empty($performance_data) && ENVIRONMENT === 'development'): ?>
                        <pre>Empty array received. Print_r:</pre>
                        <pre><?php print_r($performance_data); ?></pre>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#performanceTable').DataTable({
        "order": [[ 3, "desc" ]], // Sort by average score descending
        "pageLength": 25
    });
    
    // Initialize charts for each teacher
    const chartElements = document.querySelectorAll('.performance-chart');
    chartElements.forEach(function(element) {
        const trendData = JSON.parse(element.getAttribute('data-trend'));
        const teacherName = element.getAttribute('data-teacher');
        
        // Extract dates and scores
        const dates = trendData.map(item => formatDate(item.tanggal));
        const scores = trendData.map(item => item.nilai);
        
        // Create chart
        const ctx = document.createElement('canvas');
        element.appendChild(ctx);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Nilai',
                    data: scores,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100
                    },
                    x: {
                        display: false
                    }
                }
            }
        });
    });
    
    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: '2-digit'
        });
    }
});
</script>
<?= $this->endSection() ?>