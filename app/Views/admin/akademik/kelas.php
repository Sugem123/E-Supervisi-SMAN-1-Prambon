<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Manajemen Kelas</h1>
            <p class="text-muted small mb-0">
                Tahun Ajaran Aktif: <strong><?= esc($tahun_aktif ? ($tahun_aktif['tahun_ajar'] . ' - ' . $tahun_aktif['semester']) : 'Belum Ada Tahun Aktif') ?></strong>
            </p>
        </div>
        <div class="mt-3 mt-sm-0">
            <a href="<?= base_url('/admin/akademik/kelas/template') ?>" class="btn btn-outline-success btn-sm shadow-sm mr-2">
                <i class="fas fa-file-excel mr-1"></i> Download Template Excel
            </a>
            <button type="button" class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#importKelasModal">
                <i class="fas fa-file-import mr-1"></i> Impor Kelas Excel
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <?php
    $countX = 0; $countXI = 0; $countXII = 0;
    foreach (($kelases ?? []) as $rowCount) {
        if (($rowCount['tingkat'] ?? '') === 'X') { $countX++; }
        elseif (($rowCount['tingkat'] ?? '') === 'XI') { $countXI++; }
        elseif (($rowCount['tingkat'] ?? '') === 'XII') { $countXII++; }
    }
    ?>
    <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <strong>30 rombel baku SMA:</strong> X (<?= (int) $countX ?>/10) &bull; XI (<?= (int) $countXI ?>/10) &bull; XII (<?= (int) $countXII ?>/10) &bull; Total: <strong><?= count($kelases ?? []); ?> rombel</strong>
        </div>
        <small class="text-muted">Tahun Ajaran otomatis terkunci pada tahun ajaran yang sedang aktif.</small>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-graduation-cap mr-1"></i> Daftar Kelas (Tahun Ajaran Aktif)</h6>
            <span class="badge badge-success px-2 py-1">
                <i class="fas fa-calendar-check mr-1"></i> <?= esc($tahun_aktif ? ($tahun_aktif['tahun_ajar'] . ' - ' . $tahun_aktif['semester']) : 'Tanpa Tahun') ?>
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            <th>Nama Kelas</th>
                            <th width="10%">Tingkat</th>
                            <th width="15%">Jurusan</th>
                            <th>Tahun Ajaran</th>
                            <th width="22%">Wali Kelas</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($kelases as $kelas): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle"><strong><?= esc($kelas['nama_kelas']) ?></strong></td>
                                <td class="align-middle"><span class="badge badge-light border font-weight-bold"><?= esc($kelas['tingkat'] ?? '-') ?></span></td>
                                <td class="align-middle"><?= esc($kelas['jurusan'] ?? '-') ?></td>
                                <td class="align-middle"><?= esc($kelas['tahun_ajar'] . ' (' . $kelas['semester'] . ')') ?></td>
                                <td class="align-middle" data-order="<?= esc($kelas['nama_wali'] ?? '') ?>" style="min-width: 200px;">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light text-muted border-right-0 py-0" style="font-size: 0.75rem;">
                                                <i class="fas fa-user-tie"></i>
                                            </span>
                                        </div>
                                        <select class="form-control form-control-sm select-wali-inline border-left-0" 
                                                data-kelas-id="<?= $kelas['id'] ?>"
                                                data-kelas-nama="<?= esc($kelas['nama_kelas']) ?>"
                                                title="Pilih Wali Kelas langsung untuk <?= esc($kelas['nama_kelas']) ?>"
                                                style="font-size: 0.84rem; cursor: pointer;">
                                            <option value="">-- Pilih Wali Kelas --</option>
                                            <?php foreach ($gurus as $guru): ?>
                                                <option value="<?= $guru['id'] ?>" <?= ((int)($kelas['wali_kelas'] ?? 0) === (int)$guru['id']) ? 'selected' : '' ?>>
                                                    <?= esc($guru['nama']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($kelas['status'] == 'Aktif'): ?>
                                        <span class="badge badge-success px-2 py-1">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle" style="white-space: nowrap;">
                                    <a href="<?= base_url('/admin/akademik/kelas/' . $kelas['id'] . '/edit') ?>" class="btn btn-warning btn-sm mr-1" title="Edit Kelas">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="<?= base_url('/admin/akademik/kelas/' . $kelas['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas <?= esc(addslashes($kelas['nama_kelas'])) ?>?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Kelas">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($kelases)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data kelas untuk tahun ajaran aktif ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-1"></i> Tambah Kelas Baru</h6>
        </div>
        <div class="card-body">
            <?php if (!$tahun_aktif): ?>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Belum ada Tahun Ajaran yang berstatus <strong>Aktif</strong>. Silakan aktifkan tahun ajaran terlebih dahulu di menu <strong>Pengaturan Sistem &gt; Tahun Ajaran</strong> agar dapat menambah atau mengimpor kelas.
                </div>
            <?php else: ?>
                <form action="<?= base_url('/admin/akademik/kelas/create') ?>" method="post">
                    <?= csrf_field() ?>
                    <!-- Tahun Ajaran otomatis terkunci ke tahun ajaran aktif -->
                    <input type="hidden" name="tahun_ajar_id" value="<?= esc($tahun_aktif['id']) ?>">

                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold small">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_kelas" placeholder="Contoh: X-1 atau X IPA 1" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold small">Tingkat</label>
                            <select class="form-control" name="tingkat">
                                <option value="">Pilih Tingkat</option>
                                <option value="X">X (Sepuluh)</option>
                                <option value="XI">XI (Sebelas)</option>
                                <option value="XII">XII (Dua Belas)</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold small">Jurusan</label>
                            <input type="text" class="form-control" name="jurusan" placeholder="Contoh: Umum / IPA / IPS" value="Umum">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold small">
                                Tahun Ajaran <span class="badge badge-success ml-1"><i class="fas fa-lock mr-1"></i>Terkunci (Aktif)</span>
                            </label>
                            <input type="text" class="form-control bg-light font-weight-bold" value="<?= esc($tahun_aktif['tahun_ajar'] . ' - ' . $tahun_aktif['semester']) ?>" readonly title="Tahun ajaran selalu mengikuti tahun ajaran aktif">
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold small">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold small">Wali Kelas <small class="text-muted">(Opsional, total guru: <?= count($gurus) ?>)</small></label>
                            <select class="form-control" name="wali_kelas" id="wali_kelas_select">
                                <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                                <?php foreach ($gurus as $guru): ?>
                                    <option value="<?= $guru['id'] ?>"><?= esc($guru['nama']) ?><?= !empty($guru['nip']) ? ' (NIP: ' . esc($guru['nip']) . ')' : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end justify-content-end mb-3">
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Kelas</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Impor Kelas Excel -->
<div class="modal fade" id="importKelasModal" tabindex="-1" role="dialog" aria-labelledby="importKelasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/akademik/kelas/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="importKelasModalLabel">
                        <i class="fas fa-file-excel mr-1"></i> Impor Data Kelas dari Excel
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small mb-3">
                        <i class="fas fa-info-circle mr-1"></i> Seluruh data kelas yang diimpor akan otomatis masuk ke <strong>Tahun Ajaran Aktif: <?= esc($tahun_aktif ? ($tahun_aktif['tahun_ajar'] . ' - ' . $tahun_aktif['semester']) : 'Belum Ada Tahun Aktif') ?></strong>. Jika nama kelas sudah ada pada tahun ajaran ini, data tingkat dan jurusannya akan otomatis diperbarui.
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold small">1. Download Template Format:</label>
                        <div>
                            <a href="<?= base_url('/admin/akademik/kelas/template') ?>" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download mr-1"></i> Download File Template Kelas (.xlsx)
                            </a>
                        </div>
                        <small class="text-muted d-block mt-1">Gunakan template resmi agar kolom dan format terbaca dengan tepat oleh sistem.</small>
                    </div>

                    <div class="form-group mb-0">
                        <label for="excel_file" class="font-weight-bold small">2. Pilih File Excel (.xlsx / .xls) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file border rounded p-2" id="excel_file" name="excel_file" accept=".xlsx, .xls" required>
                        <small class="text-muted">Ukuran berkas maksimal 10MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-upload mr-1"></i> Upload & Impor Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    let csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
    });

    // Simpan nilai sebelumnya untuk rollback jika request gagal
    $(document).on('focus', '.select-wali-inline', function() {
        $(this).data('prev-val', $(this).val());
    });

    $(document).on('change', '.select-wali-inline', function() {
        const $select = $(this);
        const kelasId = $select.data('kelas-id');
        const kelasNama = $select.data('kelas-nama');
        const prevVal = $select.data('prev-val') !== undefined ? $select.data('prev-val') : '';
        const selectedVal = $select.val();
        const selectedText = $select.find('option:selected').text().trim();

        $select.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('/admin/akademik/kelas') ?>/' + kelasId + '/update-wali',
            type: 'POST',
            dataType: 'json',
            data: {
                [csrfName]: csrfHash,
                kelas_id: kelasId,
                wali_kelas: selectedVal
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash
            },
            success: function(res) {
                $select.prop('disabled', false);
                if (res && res.csrf_hash) {
                    csrfHash = res.csrf_hash;
                    $('input[name="' + csrfName + '"]').val(csrfHash);
                }

                if (res && res.status === 'success') {
                    $select.data('prev-val', selectedVal);
                    // Update data-order pada parent td untuk sorting DataTables
                    $select.closest('td').attr('data-order', selectedVal ? selectedText : '');
                    
                    // Efek visual sukses
                    $select.addClass('is-valid');
                    setTimeout(() => $select.removeClass('is-valid'), 1500);

                    Toast.fire({
                        icon: 'success',
                        title: res.message || 'Wali kelas berhasil disimpan!'
                    });
                } else {
                    $select.val(prevVal);
                    $select.addClass('is-invalid');
                    setTimeout(() => $select.removeClass('is-invalid'), 2000);
                    Toast.fire({
                        icon: 'error',
                        title: (res && res.message) ? res.message : 'Gagal menyimpan wali kelas.'
                    });
                }
            },
            error: function(xhr, status, err) {
                $select.prop('disabled', false);
                $select.val(prevVal);
                $select.addClass('is-invalid');
                setTimeout(() => $select.removeClass('is-invalid'), 2000);

                let errMsg = 'Terjadi kesalahan saat menyimpan wali kelas.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Toast.fire({
                    icon: 'error',
                    title: errMsg
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
