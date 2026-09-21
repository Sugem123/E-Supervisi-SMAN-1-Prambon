<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JenisPenilaianModel;
use App\Models\AspekPenilaianModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InstrumenController extends BaseController
{
    protected $jenisPenilaianModel;
    protected $aspekPenilaianModel;

    public function __construct()
    {
        $this->jenisPenilaianModel = new JenisPenilaianModel();
        $this->aspekPenilaianModel = new AspekPenilaianModel();
    }

    private function hasStatusColumn(string $table): bool
    {
        try {
            return in_array('status', \Config\Database::connect()->getFieldNames($table), true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?? 'jenis';
        if (!in_array($activeTab, ['jenis', 'aspek'])) {
            $activeTab = 'jenis';
        }

        $data['activeTab'] = $activeTab;
        $data['hasJenisStatus'] = $this->hasStatusColumn('jenis_penilaian');
        $data['hasAspekStatus'] = $this->hasStatusColumn('aspek_penilaian');
        $data['jenis_penilaians'] = $this->jenisPenilaianModel->findAllWithMappedColumns();
        // Admin lihat semua (Aktif + Nonaktif); histori tidak hilang.
        // Tambahkan status jenis agar badge parent bisa tampil di tab aspek.
        $aspekBuilder = $this->aspekPenilaianModel
            ->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis' . ($data['hasJenisStatus'] ? ', jenis_penilaian.status as jenis_status' : ''))
            ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id');
        $data['aspek_penilaians'] = $aspekBuilder->findAll();
        // Dropdown "tambah aspek" hanya tawarkan jenis Aktif (bila kolom ada).
        $data['jenis_aktif'] = $data['hasJenisStatus']
            ? array_values(array_filter($data['jenis_penilaians'], static fn ($j) => ($j['status'] ?? 'Aktif') === 'Aktif'))
            : $data['jenis_penilaians'];

        $db = \Config\Database::connect();
        $maxUrutanResult = $db->table('aspek_penilaian')
            ->select('jenis_penilaian_id, MAX(urutan) as max_urutan')
            ->groupBy('jenis_penilaian_id')
            ->get()
            ->getResultArray();

        $data['max_urutan'] = [];
        foreach ($maxUrutanResult as $row) {
            $data['max_urutan'][$row['jenis_penilaian_id']] = $row['max_urutan'];
        }

        return view('admin/instrumen/index', $data);
    }

    public function jenisPenilaian()
    {
        return redirect()->to('/admin/instrumen?tab=jenis');
    }

    public function aspekPenilaian()
    {
        return redirect()->to('/admin/instrumen?tab=aspek');
    }

    private function resolveStatusInput(): string
    {
        $status = $this->request->getPost('status');
        return in_array($status, ['Aktif', 'Nonaktif'], true) ? $status : 'Aktif';
    }

    public function createJenisPenilaian()
    {
        $jenisPenilaianData = [
            'nama' => $this->request->getPost('nama_jenis'),
            'skor_maksimal' => $this->request->getPost('skor_maksimal') ?? 48,
            'kategori_skor' => $this->request->getPost('kategori_skor') ?? '{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}'
        ];
        if ($this->hasStatusColumn('jenis_penilaian')) {
            $jenisPenilaianData['status'] = $this->resolveStatusInput();
        }
        
        $result = $this->jenisPenilaianModel->insert($jenisPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('success', 'Jenis penilaian berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jenis penilaian');
        }
    }

    public function updateJenisPenilaian($id)
    {
        $jenisPenilaianData = [
            'nama' => $this->request->getPost('nama_jenis'),
            'skor_maksimal' => $this->request->getPost('skor_maksimal'),
            'kategori_skor' => $this->request->getPost('kategori_skor')
        ];
        if ($this->hasStatusColumn('jenis_penilaian')) {
            $jenisPenilaianData['status'] = $this->resolveStatusInput();
        }
        
        $result = $this->jenisPenilaianModel->update($id, $jenisPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('success', 'Jenis penilaian berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate jenis penilaian');
        }
    }

    public function deleteJenisPenilaian($id)
    {
        // Check if this jenis penilaian is being used by any aspek penilaian
        $aspekCount = $this->aspekPenilaianModel->where('jenis_penilaian_id', $id)->countAllResults();
        
        if ($aspekCount > 0) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Tidak dapat menghapus jenis penilaian ini karena masih digunakan oleh aspek penilaian');
        }
        
        $result = $this->jenisPenilaianModel->delete($id);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('success', 'Jenis penilaian berhasil dihapus');
        } else {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Gagal menghapus jenis penilaian');
        }
    }

    public function toggleJenisStatus($id)
    {
        $row = $this->jenisPenilaianModel->find($id);
        if (!$row) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Jenis penilaian tidak ditemukan');
        }
        // BC: migrasi belum jalan -> jangan fatal, arahkan dengan pesan jelas
        if (!$this->hasStatusColumn('jenis_penilaian')) {
            return redirect()->to('/admin/instrumen?tab=jenis')->with('error', 'Kolom status belum tersedia. Jalankan migrasi database dulu.');
        }
        $current = $row['status'] ?? 'Aktif';
        $next = $current === 'Aktif' ? 'Nonaktif' : 'Aktif';

        // Peringatan bila menonaktifkan jenis yang masih punya aspek Aktif
        $warning = '';
        if ($next === 'Nonaktif' && $this->hasStatusColumn('aspek_penilaian')) {
            $aktifAspek = $this->aspekPenilaianModel
                ->where('jenis_penilaian_id', $id)
                ->where('status', 'Aktif')
                ->countAllResults();
            if ($aktifAspek > 0) {
                $warning = " ({$aktifAspek} aspek Aktif ikut disembunyikan dari form penilaian)";
            }
        }

        $this->jenisPenilaianModel->update($id, ['status' => $next]);

        return redirect()->to('/admin/instrumen?tab=jenis')->with('success', "Jenis penilaian {$next}{$warning}");
    }

    public function toggleAspekStatus($id)
    {
        $row = $this->aspekPenilaianModel->find($id);
        if (!$row) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Aspek penilaian tidak ditemukan');
        }
        if (!$this->hasStatusColumn('aspek_penilaian')) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Kolom status belum tersedia. Jalankan migrasi database dulu.');
        }
        $current = $row['status'] ?? 'Aktif';
        $next = $current === 'Aktif' ? 'Nonaktif' : 'Aktif';
        $this->aspekPenilaianModel->update($id, ['status' => $next]);

        return redirect()->to('/admin/instrumen?tab=aspek')->with('success', "Aspek penilaian {$next}");
    }

    public function createAspekPenilaian()
    {
        $aspekPenilaianData = [
            'jenis_penilaian_id' => $this->request->getPost('jenis_penilaian_id'),
            'nama_aspek' => $this->request->getPost('nama_aspek'),
            'urutan' => $this->request->getPost('urutan'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($this->hasStatusColumn('aspek_penilaian')) {
            $aspekPenilaianData['status'] = $this->resolveStatusInput();
        }
        
        $result = $this->aspekPenilaianModel->insert($aspekPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan aspek penilaian');
        }
    }

    public function updateAspekPenilaian($id)
    {
        $aspekPenilaianData = [
            'jenis_penilaian_id' => $this->request->getPost('jenis_penilaian_id'),
            'nama_aspek' => $this->request->getPost('nama_aspek'),
            'urutan' => $this->request->getPost('urutan')
        ];
        if ($this->hasStatusColumn('aspek_penilaian')) {
            $aspekPenilaianData['status'] = $this->resolveStatusInput();
        }
        
        $result = $this->aspekPenilaianModel->update($id, $aspekPenilaianData);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate aspek penilaian');
        }
    }

    public function deleteAspekPenilaian($id)
    {
        // Check if this aspek penilaian is being used in detail hasil penilaian
        $db = \Config\Database::connect();
        $detailCount = $db->table('detail_hasil_penilaian')
                          ->where('aspek_penilaian_id', $id)
                          ->countAllResults();
        
        if ($detailCount > 0) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Tidak dapat menghapus aspek penilaian ini karena masih digunakan dalam penilaian');
        }
        
        $result = $this->aspekPenilaianModel->delete($id);
        
        if ($result) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', 'Aspek penilaian berhasil dihapus');
        } else {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Gagal menghapus aspek penilaian');
        }
    }

    /**
     * Download template Excel untuk impor Aspek Penilaian Supervisi.
     */
    public function downloadTemplateAspek()
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Template Aspek Penilaian
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Aspek');

        // Headers
        $sheet->setCellValue('A1', 'JENIS_PENILAIAN');
        $sheet->setCellValue('B1', 'NAMA_ASPEK');
        $sheet->setCellValue('C1', 'URUTAN');
        $sheet->setCellValue('D1', 'STATUS');

        // Ambil daftar jenis penilaian aktif
        $jenisList = $this->jenisPenilaianModel->findAll();
        if ($this->hasStatusColumn('jenis_penilaian')) {
            $activeList = array_filter($jenisList, static fn ($j) => ($j['status'] ?? 'Aktif') === 'Aktif');
            if (!empty($activeList)) {
                $jenisList = array_values($activeList);
            }
        }

        // Sheet 2: Referensi Jenis Penilaian
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Ref_Jenis');
        $refSheet->setCellValue('A1', 'DAFTAR JENIS PENILAIAN TERSEDIA');
        $refSheet->getStyle('A1')->getFont()->setBold(true);

        $rIdx = 2;
        foreach ($jenisList as $j) {
            $refSheet->setCellValue('A' . $rIdx, $j['nama']);
            $rIdx++;
        }
        $refSheet->getColumnDimension('A')->setWidth(65);

        // Kembali ke Sheet Utama
        $spreadsheet->setActiveSheetIndex(0);

        // Validasi Dropdown Kolom A (JENIS_PENILAIAN)
        $maxRefRow = max(2, $rIdx - 1);
        $jenisValidation = new DataValidation();
        $jenisValidation->setType(DataValidation::TYPE_LIST);
        $jenisValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $jenisValidation->setAllowBlank(false);
        $jenisValidation->setShowDropDown(true);
        $jenisValidation->setPromptTitle('Pilih Jenis Penilaian');
        $jenisValidation->setPrompt('Pilih dari daftar jenis penilaian atau ketik nama baru');
        $jenisValidation->setFormula1("Ref_Jenis!\$A\$2:\$A\$" . $maxRefRow);
        $sheet->setDataValidation('A2:A500', $jenisValidation);

        // Validasi Dropdown Kolom D (STATUS)
        $statusValidation = new DataValidation();
        $statusValidation->setType(DataValidation::TYPE_LIST);
        $statusValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $statusValidation->setAllowBlank(false);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setPromptTitle('Status Aspek');
        $statusValidation->setFormula1('"Aktif,Nonaktif"');
        $sheet->setDataValidation('D2:D500', $statusValidation);

        // Sample Data Standar Supervisi SMA
        $sampleData = [
            ['SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)', 'Kesesuaian Alur Tujuan Pembelajaran (ATP) dengan Capaian Pembelajaran (CP)', 1, 'Aktif'],
            ['SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)', 'Kelengkapan Komponen Modul Ajar / RPP (Tujuan, Langkah Pembelajaran, Asesmen)', 2, 'Aktif'],
            ['SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)', 'Kesesuaian Bahan Ajar, Media Pembelajaran, dan LKPD dengan Karakteristik Materi & Siswa', 3, 'Aktif'],
            ['SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)', 'Rancangan Penilaian Formatif, Asesmen Diagnostik Awal, dan Asesmen Sumatif', 4, 'Aktif'],
            ['SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)', 'Kegiatan Pendahuluan (Apersepsi, Motivasi, Penyampaian Tujuan & Skenario Pembelajaran)', 1, 'Aktif'],
            ['SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)', 'Penerapan Model/Metode Pembelajaran Aktif, Berpikir Kritis, dan Berdiferensiasi', 2, 'Aktif'],
            ['SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)', 'Pemanfaatan Media Pembelajaran dan Integrasi Teknologi (IT/Digital)', 3, 'Aktif'],
            ['SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)', 'Pengelolaan Kelas, Keterlibatan Aktif Siswa, dan Interaksi Komunikatif', 4, 'Aktif'],
            ['SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)', 'Penyusunan Kisi-kisi dan Kualitas Instrumen Asesmen Sesuai Indikator Capaian', 1, 'Aktif'],
            ['SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)', 'Analisis Hasil Belajar dan Pelaksanaan Program Tindak Lanjut (Remedial/Pengayaan)', 2, 'Aktif'],
        ];

        $rowNum = 2;
        foreach ($sampleData as $row) {
            $sheet->setCellValue('A' . $rowNum, $row[0]);
            $sheet->setCellValue('B' . $rowNum, $row[1]);
            $sheet->setCellValue('C' . $rowNum, $row[2]);
            $sheet->setCellValue('D' . $rowNum, $row[3]);
            $rowNum++;
        }

        // Lebar Kolom
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(75);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(15);

        // Styling Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4E73DF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Alignment Data
        $sheet->getStyle('C2:C500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_aspek_penilaian.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /**
     * Proses impor file Excel Aspek Penilaian.
     */
    public function processImportAspek()
    {
        $validationRule = [
            'excel_file' => [
                'label' => 'File Excel',
                'rules' => [
                    'uploaded[excel_file]',
                    'ext_in[excel_file,xls,xlsx]',
                    'max_size[excel_file,10240]'
                ]
            ]
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->to('/admin/instrumen?tab=aspek')->withInput()->with('error', 'File tidak valid. Unggah file Excel berekstensi .xlsx atau .xls (maksimal 10MB).');
        }

        $file = $this->request->getFile('excel_file');
        if (!$file->isValid() || $file->hasMoved()) {
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'File tidak valid atau gagal diunggah.');
        }

        try {
            $filePath = $file->getTempName();
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getSheet(0); // Sheet pertama
            $rows = $worksheet->toArray();

            // Hapus baris header
            array_shift($rows);

            if (empty($rows)) {
                return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'File Excel kosong atau tidak memiliki data aspek.');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // Opsi: Bersihkan aspek lama yang belum memiliki hasil supervisi jika dicentang
            $replaceExisting = $this->request->getPost('replace_existing') === '1';
            $deletedOldCount = 0;
            if ($replaceExisting) {
                $usedAspekIds = $db->table('detail_hasil_penilaian')->select('aspek_penilaian_id')->distinct()->get()->getResultArray();
                $protectedIds = !empty($usedAspekIds) ? array_column($usedAspekIds, 'aspek_penilaian_id') : [];

                $delBuilder = $db->table('aspek_penilaian');
                if (!empty($protectedIds)) {
                    $delBuilder->whereNotIn('id', $protectedIds);
                }
                $delBuilder->delete();
                $deletedOldCount = $db->affectedRows();
            }

            // Cache data jenis_penilaian yang ada
            $allJenis = $this->jenisPenilaianModel->findAll();
            $jenisByName = [];
            foreach ($allJenis as $j) {
                $clean = strtolower(trim($j['nama']));
                $jenisByName[$clean] = (int) $j['id'];
            }

            $insertedCount = 0;
            $updatedCount  = 0;
            $hasAspekStatus = $this->hasStatusColumn('aspek_penilaian');
            $hasJenisStatus = $this->hasStatusColumn('jenis_penilaian');

            foreach ($rows as $row) {
                $namaJenisRaw = trim((string) ($row[0] ?? ''));
                $namaAspek    = trim((string) ($row[1] ?? ''));
                $urutanInput  = trim((string) ($row[2] ?? ''));
                $statusInput  = ucfirst(strtolower(trim((string) ($row[3] ?? 'Aktif'))));
                $status       = in_array($statusInput, ['Aktif', 'Nonaktif'], true) ? $statusInput : 'Aktif';

                if ($namaAspek === '') {
                    continue;
                }

                // Resolusi jenis penilaian
                $cleanJenisName = strtolower($namaJenisRaw);
                $targetJenisId = null;

                if ($cleanJenisName !== '' && isset($jenisByName[$cleanJenisName])) {
                    $targetJenisId = $jenisByName[$cleanJenisName];
                } elseif ($cleanJenisName !== '') {
                    // Buat jenis penilaian baru secara otomatis
                    $newJenisData = [
                        'nama'          => $namaJenisRaw,
                        'skor_maksimal' => 100,
                        'kategori_skor' => '{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}'
                    ];
                    if ($hasJenisStatus) {
                        $newJenisData['status'] = 'Aktif';
                    }
                    $targetJenisId = (int) $this->jenisPenilaianModel->insert($newJenisData);
                    $jenisByName[$cleanJenisName] = $targetJenisId;
                } elseif (!empty($jenisByName)) {
                    // Fallback ke jenis penilaian pertama
                    $targetJenisId = reset($jenisByName);
                } else {
                    // Buat jenis default jika database belum ada jenis
                    $defaultName = 'SUPERVISI PEMBELAJARAN';
                    $newJenisData = [
                        'nama'          => $defaultName,
                        'skor_maksimal' => 100,
                        'kategori_skor' => '{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}'
                    ];
                    if ($hasJenisStatus) {
                        $newJenisData['status'] = 'Aktif';
                    }
                    $targetJenisId = (int) $this->jenisPenilaianModel->insert($newJenisData);
                    $jenisByName[strtolower($defaultName)] = $targetJenisId;
                }

                // Resolusi urutan
                if (is_numeric($urutanInput) && (int) $urutanInput > 0) {
                    $urutan = (int) $urutanInput;
                } else {
                    $maxRow = $db->table('aspek_penilaian')
                        ->where('jenis_penilaian_id', $targetJenisId)
                        ->selectMax('urutan')
                        ->get()
                        ->getRowArray();
                    $urutan = ($maxRow && isset($maxRow['urutan'])) ? ((int) $maxRow['urutan'] + 1) : 1;
                }

                // Cek apakah aspek dengan nama yang sama sudah ada di jenis ini
                $existing = $this->aspekPenilaianModel
                    ->where('jenis_penilaian_id', $targetJenisId)
                    ->where('nama_aspek', $namaAspek)
                    ->first();

                if ($existing) {
                    $updatePayload = ['urutan' => $urutan];
                    if ($hasAspekStatus) {
                        $updatePayload['status'] = $status;
                    }
                    $this->aspekPenilaianModel->update($existing['id'], $updatePayload);
                    $updatedCount++;
                } else {
                    $insertPayload = [
                        'jenis_penilaian_id' => $targetJenisId,
                        'nama_aspek'         => $namaAspek,
                        'urutan'             => $urutan,
                        'created_at'         => date('Y-m-d H:i:s')
                    ];
                    if ($hasAspekStatus) {
                        $insertPayload['status'] = $status;
                    }
                    $this->aspekPenilaianModel->insert($insertPayload);
                    $insertedCount++;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Gagal memproses impor aspek penilaian. Transaksi database dibatalkan.');
            }

            $msg = "Impor aspek penilaian berhasil: {$insertedCount} aspek baru ditambahkan, {$updatedCount} aspek diperbarui.";
            if ($deletedOldCount > 0) {
                $msg .= " Sebanyak {$deletedOldCount} aspek lama yang belum memiliki riwayat penilaian telah dibersihkan.";
            }

            return redirect()->to('/admin/instrumen?tab=aspek')->with('success', $msg);

        } catch (\Throwable $e) {
            log_message('error', 'Error import aspek penilaian: ' . $e->getMessage());
            return redirect()->to('/admin/instrumen?tab=aspek')->with('error', 'Terjadi kesalahan saat membaca file Excel: ' . $e->getMessage());
        }
    }
}