<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clock mr-1"></i> Konfigurasi Jam Pelajaran & Bel KBM (SMA)
            </h6>
            <small class="text-muted">
                Sesuaikan jam pelajaran dan waktu istirahat sekolah. Slot bertipe <strong>KBM</strong> otomatis dipakai untuk alokasi jadwal supervisi.
            </small>
        </div>
        <div class="mt-2 mt-sm-0">
            <form action="<?= base_url('admin/pengaturan/reset-jam-pelajaran'); ?>" method="post" class="d-inline" onsubmit="return confirm('Kembalikan ke jadwal Jam Pelajaran Standar SMA (10 Jam Pelajaran @ 45 menit)? Data yang belum disimpan akan digantikan.');">
                <?= csrf_field(); ?>
                <button type="submit" class="btn btn-outline-warning btn-sm shadow-sm" title="Kembalikan ke susunan jam standar SMA">
                    <i class="fas fa-undo-alt mr-1"></i> Reset ke Standar SMA
                </button>
            </form>
            <button type="button" class="btn btn-success btn-sm shadow-sm ml-2" id="btnAddJamRow">
                <i class="fas fa-plus mr-1"></i> Tambah Baris Jam
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center py-2 px-3 mb-3">
            <i class="fas fa-info-circle fa-2x mr-3 text-info"></i>
            <div class="small">
                <strong>Catatan Alokasi Supervisi:</strong>
                Slot dengan tipe <strong>KBM (Mengajar)</strong> akan menjadi pilihan sesi jam supervisi di seluruh sistem. Slot bertipe <strong>Istirahat</strong> tidak akan dialokasikan sebagai jam mengajar, sehingga supervisi tidak bertabrakan dengan waktu istirahat guru/siswa.
            </div>
        </div>

        <form action="<?= base_url('admin/pengaturan/update-jam-pelajaran'); ?>" method="post" id="formJamPelajaran">
            <?= csrf_field(); ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-3" id="tableJamPelajaran">
                    <thead class="thead-light">
                        <tr class="text-center small font-weight-bold">
                            <th width="4%">No</th>
                            <th width="10%">Jam Ke-</th>
                            <th width="16%">Waktu Mulai</th>
                            <th width="16%">Waktu Selesai</th>
                            <th width="18%">Tipe Slot</th>
                            <th>Keterangan</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="jamTableBody">
                        <?php 
                        $no = 1;
                        $slots = $jam_pelajaran ?? get_default_jam_pelajaran();
                        foreach ($slots as $idx => $slot): 
                            $isIstirahat = (!empty($slot['is_istirahat']) && (string)$slot['is_istirahat'] === '1');
                            $jamKeVal = $slot['jam_ke'] ?? (string)($idx + 1);
                            $waktuDari = substr($slot['waktu_dari'] ?? '07:00', 0, 5);
                            $waktuSampai = substr($slot['waktu_sampai'] ?? '07:45', 0, 5);
                            $keterangan = $slot['keterangan'] ?? ($isIstirahat ? 'Istirahat' : "Jam Ke-{$jamKeVal} (KBM)");
                        ?>
                            <tr class="jam-row <?= $isIstirahat ? 'bg-light text-muted' : ''; ?>">
                                <td class="text-center row-number align-middle font-weight-bold small text-muted">
                                    <?= $no++; ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-center font-weight-bold input-jam-ke" 
                                           name="jam_ke[]" 
                                           value="<?= esc($jamKeVal); ?>" 
                                           placeholder="1, 2, dll" required>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-play text-muted" style="font-size:0.7rem;"></i></span>
                                        </div>
                                        <input type="time" class="form-control form-control-sm input-waktu-dari" 
                                               name="waktu_dari[]" 
                                               value="<?= esc($waktuDari); ?>" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-stop text-muted" style="font-size:0.7rem;"></i></span>
                                        </div>
                                        <input type="time" class="form-control form-control-sm input-waktu-sampai" 
                                               name="waktu_sampai[]" 
                                               value="<?= esc($waktuSampai); ?>" required>
                                    </div>
                                </td>
                                <td>
                                    <select class="form-control form-control-sm select-tipe-slot font-weight-bold" name="is_istirahat[]">
                                        <option value="0" <?= !$isIstirahat ? 'selected' : ''; ?>>📖 KBM (Mengajar)</option>
                                        <option value="1" <?= $isIstirahat ? 'selected' : ''; ?>>☕ Istirahat</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm input-keterangan" 
                                           name="keterangan[]" 
                                           value="<?= esc($keterangan); ?>" 
                                           placeholder="Contoh: Jam Ke-1 (KBM)">
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete-row" title="Hapus jam ini">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <small class="text-muted">
                    <i class="fas fa-check-circle text-success mr-1"></i> Perubahan akan langsung disinkronkan ke seluruh form jadwal supervisi.
                </small>
                <div>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Jam Pelajaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tableBody = document.getElementById('jamTableBody');
    var btnAddRow = document.getElementById('btnAddJamRow');

    function renumberRows() {
        if (!tableBody) return;
        var rows = tableBody.querySelectorAll('.jam-row');
        var kbmCount = 0;
        rows.forEach(function(row, idx) {
            var numCell = row.querySelector('.row-number');
            if (numCell) numCell.textContent = (idx + 1);

            var selectTipe = row.querySelector('.select-tipe-slot');
            var isIstirahat = selectTipe && selectTipe.value === '1';
            if (isIstirahat) {
                row.classList.add('bg-light');
            } else {
                row.classList.remove('bg-light');
            }
        });
    }

    if (tableBody) {
        // Hapus baris
        tableBody.addEventListener('click', function(e) {
            var btn = e.target.closest('.btn-delete-row');
            if (!btn) return;

            var rows = tableBody.querySelectorAll('.jam-row');
            if (rows.length <= 1) {
                alert('Minimal harus ada 1 baris jam pelajaran.');
                return;
            }

            var row = btn.closest('.jam-row');
            if (row) {
                row.remove();
                renumberRows();
            }
        });

        // Toggle tipe slot (KBM vs Istirahat)
        tableBody.addEventListener('change', function(e) {
            var select = e.target.closest('.select-tipe-slot');
            if (!select) return;

            var row = select.closest('.jam-row');
            var jamKeInput = row.querySelector('.input-jam-ke');
            var ketInput = row.querySelector('.input-keterangan');

            if (select.value === '1') {
                row.classList.add('bg-light');
                if (jamKeInput && (jamKeInput.value === '' || !isNaN(jamKeInput.value))) {
                    jamKeInput.value = '-';
                }
                if (ketInput && (ketInput.value === '' || ketInput.value.indexOf('Jam Ke-') === 0)) {
                    ketInput.value = 'Istirahat';
                }
            } else {
                row.classList.remove('bg-light');
                if (jamKeInput && jamKeInput.value === '-') {
                    jamKeInput.value = '1';
                }
                if (ketInput && ketInput.value === 'Istirahat') {
                    ketInput.value = 'Jam Ke-' + (jamKeInput ? jamKeInput.value : '1') + ' (KBM)';
                }
            }
        });
    }

    // Tambah baris baru
    if (btnAddRow && tableBody) {
        btnAddRow.addEventListener('click', function() {
            var rows = tableBody.querySelectorAll('.jam-row');
            var lastRow = rows.length > 0 ? rows[rows.length - 1] : null;

            var nextDari = '15:45';
            var nextSampai = '16:30';
            var nextJamKe = String(rows.length + 1);

            if (lastRow) {
                var lastSampai = lastRow.querySelector('.input-waktu-sampai');
                if (lastSampai && lastSampai.value) {
                    nextDari = lastSampai.value;
                    // default +45 menit
                    var parts = nextDari.split(':');
                    if (parts.length === 2) {
                        var h = parseInt(parts[0], 10);
                        var m = parseInt(parts[1], 10) + 45;
                        if (m >= 60) {
                            h += Math.floor(m / 60);
                            m = m % 60;
                        }
                        nextSampai = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
                    }
                }
            }

            var tr = document.createElement('tr');
            tr.className = 'jam-row';
            tr.innerHTML = 
                '<td class="text-center row-number align-middle font-weight-bold small text-muted">' + (rows.length + 1) + '</td>' +
                '<td><input type="text" class="form-control form-control-sm text-center font-weight-bold input-jam-ke" name="jam_ke[]" value="' + nextJamKe + '" placeholder="1, 2, dll" required></td>' +
                '<td><div class="input-group input-group-sm"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-play text-muted" style="font-size:0.7rem;"></i></span></div><input type="time" class="form-control form-control-sm input-waktu-dari" name="waktu_dari[]" value="' + nextDari + '" required></div></td>' +
                '<td><div class="input-group input-group-sm"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-stop text-muted" style="font-size:0.7rem;"></i></span></div><input type="time" class="form-control form-control-sm input-waktu-sampai" name="waktu_sampai[]" value="' + nextSampai + '" required></div></td>' +
                '<td><select class="form-control form-control-sm select-tipe-slot font-weight-bold" name="is_istirahat[]"><option value="0" selected>📖 KBM (Mengajar)</option><option value="1">☕ Istirahat</option></select></td>' +
                '<td><input type="text" class="form-control form-control-sm input-keterangan" name="keterangan[]" value="Jam Ke-' + nextJamKe + ' (KBM)" placeholder="Keterangan"></td>' +
                '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm btn-delete-row" title="Hapus jam ini"><i class="fas fa-trash"></i></button></td>';

            tableBody.appendChild(tr);
            renumberRows();
            tr.querySelector('.input-waktu-sampai').focus();
        });
    }
});
</script>
