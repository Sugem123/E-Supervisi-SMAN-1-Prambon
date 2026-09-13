<!-- Kop Instansi Card -->
<div class="card shadow mb-4" id="cardKopSurat">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-invoice mr-2"></i>Kop Instansi & Surat Resmi (Header Cetak PDF)</h6>
        <span class="badge badge-info"><i class="fas fa-print mr-1"></i> Digunakan pada Seluruh Dokumen Cetak</span>
    </div>
    <div class="card-body">
        <!-- Live Preview Box -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="small font-weight-bold text-dark mb-0"><i class="fas fa-eye mr-1"></i> Pratinjau Kop Surat:</label>
            <small class="text-muted">Pratinjau langsung tampilan kop pada dokumen PDF</small>
        </div>
        <div class="p-3 mb-4 bg-white border rounded shadow-sm" style="background-color: #fff; overflow-x: auto;">
            <?= render_kop_surat() ?>
        </div>

        <form action="<?= base_url('admin/pengaturan/update-kop') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="kop_baris_1" class="small font-weight-bold">Baris 1 (Instansi Pusat/Kementerian)</label>
                <input type="text" class="form-control" id="kop_baris_1" name="kop_baris_1" 
                    value="<?= esc($kop['baris_1'] ?? '') ?>" 
                    placeholder="Contoh: KEMENTERIAN AGAMA REPUBLIK INDONESIA" required>
                <small class="form-text text-muted">Ditampilkan di baris paling atas (huruf kapital).</small>
            </div>

            <div class="form-group">
                <label for="kop_baris_2" class="small font-weight-bold">Baris 2 (Instansi Daerah / Kantor Wilayah/Kabupaten)</label>
                <input type="text" class="form-control" id="kop_baris_2" name="kop_baris_2" 
                    value="<?= esc($kop['baris_2'] ?? '') ?>" 
                    placeholder="Contoh: KANTOR KEMENTERIAN AGAMA KABUPATEN TANGGAMUS">
                <small class="form-text text-muted">Ditampilkan di baris kedua.</small>
            </div>

            <div class="form-group">
                <label for="kop_baris_3" class="small font-weight-bold">Baris 3 (Nama Madrasah / Satuan Pendidikan)</label>
                <input type="text" class="form-control font-weight-bold" id="kop_baris_3" name="kop_baris_3" 
                    value="<?= esc($kop['baris_3'] ?? '') ?>" 
                    placeholder="Contoh: MADRASAH IBTIDAIYAH NEGERI 2 TANGGAMUS" required>
                <small class="form-text text-muted">Ditampilkan tebal (bold) sebagai nama utama madrasah.</small>
            </div>

            <div class="form-group">
                <label for="kop_baris_4" class="small font-weight-bold">Baris 4 (Alamat Lengkap)</label>
                <input type="text" class="form-control" id="kop_baris_4" name="kop_baris_4" 
                    value="<?= esc($kop['baris_4'] ?? '') ?>" 
                    placeholder="Contoh: Jl. Lapangan Ampera Purwodadi No. 109 Kec. Gisting Kab. Tanggamus">
                <small class="form-text text-muted">Alamat jalan, desa/kelurahan, kecamatan, kabupaten, provinsi.</small>
            </div>

            <div class="form-group">
                <label for="kop_baris_5" class="small font-weight-bold">Baris 5 (Kontak / Telepon / Email / Website / Kode Pos)</label>
                <input type="text" class="form-control" id="kop_baris_5" name="kop_baris_5" 
                    value="<?= esc($kop['baris_5'] ?? '') ?>" 
                    placeholder="Contoh: Website: https://min2tanggamus.sch.id | Email: min2tanggamus@kemenag.go.id">
                <small class="form-text text-muted">Ditampilkan dengan format miring (italic) di baris bawah.</small>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="kop_tampilkan_logo" name="kop_tampilkan_logo" value="1" 
                            <?= ($kop['tampilkan_logo'] == '1') ? 'checked' : '' ?>>
                        <label class="custom-control-label small font-weight-bold" for="kop_tampilkan_logo">
                            Tampilkan Logo Madrasah pada Kop Surat
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="kop_tampilkan_garis" name="kop_tampilkan_garis" value="1" 
                            <?= ($kop['tampilkan_garis'] == '1') ? 'checked' : '' ?>>
                        <label class="custom-control-label small font-weight-bold" for="kop_tampilkan_garis">
                            Tampilkan Garis Ganda Pemisah Kop Surat
                        </label>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Pengaturan Kop
                </button>
            </div>
        </form>
    </div>
</div>
