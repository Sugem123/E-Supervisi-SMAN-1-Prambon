<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Laporan Statistik Pengguna</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pengguna</h6>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('admin/laporan/statistik-pengguna') ?>">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="role">Peran</label>
                                    <select name="role" id="role" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="guru" <?= (isset($role) && $role == 'guru') ? 'selected' : '' ?>>Guru</option>
                                        <option value="supervisor" <?= (isset($role) && $role == 'supervisor') ? 'selected' : '' ?>>Supervisor</option>
                                        <option value="kepala" <?= (isset($role) && $role == 'kepala') ? 'selected' : '' ?>>Kepala</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="aktif" <?= (isset($status) && $status == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                        <option value="non-aktif" <?= (isset($status) && $status == 'non-aktif') ? 'selected' : '' ?>>Non-aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td><?= $user['username'] ?></td>
                                        <td><?= $user['email'] ?></td>
                                        <td><?= $user['role'] ?></td>
                                        <td><?= (strtolower($user['status']) == 'aktif') ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Non-aktif</span>' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Peran Pengguna</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="myPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pie Chart Example
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($roleCounts as $role) { echo "'" . ucfirst($role['role']) . "', "; } ?>],
            datasets: [{
                data: [<?php foreach ($roleCounts as $role) { echo $role['count'] . ", "; } ?>],
                backgroundColor: ['#ff0000ff', '#0c7705ff', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: true
            },
            cutoutPercentage: 80,
        },
    });
</script>
<?= $this->endSection() ?>
