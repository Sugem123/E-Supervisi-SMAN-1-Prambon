<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjarModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AkademikController extends BaseController
{
    protected $tahunAjarModel;
    protected $kelasModel;
    protected $guruModel;

    public function __construct()
    {
        $this->tahunAjarModel = new TahunAjarModel();
        $this->kelasModel     = new KelasModel();
        $this->guruModel      = new GuruModel();
    }

    public function tahunAjar()
    {
        return redirect()->to('/admin/pengaturan/tahun-ajar');
    }

    public function kelas()
    {
        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        $query = $this->kelasModel
            ->select('kelas.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_wali')
            ->join('tahun_ajar', 'tahun_ajar.id = kelas.tahun_ajar_id')
            ->join('guru', 'guru.id = kelas.wali_kelas', 'left');

        // Lembaran baru per tahun: daftar kelas hanya tahun aktif.
        if ($tahunAktif) {
            $query->where('kelas.tahun_ajar_id', $tahunAktif['id']);
        }

        $data['kelases']     = $query->orderBy('kelas.nama_kelas', 'ASC')->findAll();
        $data['tahun_ajars'] = $this->tahunAjarModel->findAll();
        $data['tahun_aktif'] = $tahunAktif;
        $data['gurus']       = $this->guruModel->orderBy('nama', 'ASC')->findAll();

        return view('admin/akademik/kelas', $data);
    }

    public function createKelas()
    {
        // Tahun ajaran terkunci otomatis ke tahun aktif
        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        if (!$tahunAktif) {
            return redirect()->back()->withInput()->with('error', 'Tidak dapat menambah kelas karena belum ada Tahun Ajaran yang Aktif. Aktifkan tahun ajaran terlebih dahulu.');
        }

        if (!$this->validate([
            'nama_kelas' => 'required',
            'tingkat'    => 'permit_empty|in_list[X,XI,XII]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Tingkat harus X, XI, atau XII.');
        }

        $waliKelas = $this->request->getPost('wali_kelas');
        $waliKelas = ($waliKelas === '' || $waliKelas === null) ? null : (int) $waliKelas;

        $namaKelas = trim($this->request->getPost('nama_kelas'));

        // Cek duplikasi nama kelas di tahun aktif yang sama
        $existing = $this->kelasModel
            ->where('tahun_ajar_id', (int) $tahunAktif['id'])
            ->where('nama_kelas', $namaKelas)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Kelas dengan nama '{$namaKelas}' sudah ada pada Tahun Ajaran {$tahunAktif['tahun_ajar']} ({$tahunAktif['semester']}).");
        }

        $kelasData = [
            'tahun_ajar_id' => (int) $tahunAktif['id'], // Terkunci otomatis ke tahun ajaran aktif
            'nama_kelas'    => $namaKelas,
            'tingkat'       => $this->request->getPost('tingkat') ?: null,
            'jurusan'       => $this->request->getPost('jurusan') ?: null,
            'wali_kelas'    => $waliKelas,
            'status'        => $this->request->getPost('status') ?: 'Aktif',
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $result = $this->kelasModel->insert($kelasData);

        if ($result) {
            return redirect()->to('/admin/akademik/kelas')->with('success', "Kelas '{$namaKelas}' berhasil ditambahkan ke Tahun Ajaran {$tahunAktif['tahun_ajar']} ({$tahunAktif['semester']}).");
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kelas');
        }
    }

    public function editKelas($id)
    {
        $kelas = $this->kelasModel
            ->select('kelas.*, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('tahun_ajar', 'tahun_ajar.id = kelas.tahun_ajar_id', 'left')
            ->where('kelas.id', $id)
            ->first();

        if (!$kelas) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak ditemukan');
        }

        $data['kelas']       = $kelas;
        $data['tahun_ajars'] = $this->tahunAjarModel->findAll();
        $data['tahun_aktif'] = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        $data['gurus']       = $this->guruModel->orderBy('nama', 'ASC')->findAll();

        return view('admin/akademik/edit_kelas', $data);
    }

    public function updateKelas($id)
    {
        $kelas = $this->kelasModel->find($id);
        if (!$kelas) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak ditemukan');
        }

        // Tahun ajaran tetap terkunci sesuai data kelas/tahun aktif
        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        $targetTahunAjarId = $tahunAktif ? (int) $tahunAktif['id'] : (int) $kelas['tahun_ajar_id'];

        if (!$this->validate([
            'nama_kelas' => 'required',
            'tingkat'    => 'permit_empty|in_list[X,XI,XII]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Tingkat harus X, XI, atau XII.');
        }

        $waliKelas = $this->request->getPost('wali_kelas');
        $waliKelas = ($waliKelas === '' || $waliKelas === null) ? null : (int) $waliKelas;

        $kelasData = [
            'tahun_ajar_id' => $targetTahunAjarId, // Terkunci sesuai tahun aktif
            'nama_kelas'    => trim($this->request->getPost('nama_kelas')),
            'tingkat'       => $this->request->getPost('tingkat') ?: null,
            'jurusan'       => $this->request->getPost('jurusan') ?: null,
            'wali_kelas'    => $waliKelas,
            'status'        => $this->request->getPost('status') ?: 'Aktif'
        ];

        $result = $this->kelasModel->update($id, $kelasData);

        if ($result) {
            return redirect()->to('/admin/akademik/kelas')->with('success', 'Kelas berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate kelas');
        }
    }

    /**
     * Hapus data kelas (melepaskan relasi jadwal_supervisi secara aman).
     */
    public function deleteKelas($id)
    {
        $kelas = $this->kelasModel->find($id);
        if (!$kelas) {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Kelas tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        // Lepas referensi kelas_id pada jadwal_supervisi agar histori supervisi tetap aman
        $db->table('jadwal_supervisi')->where('kelas_id', $id)->update(['kelas_id' => null]);

        $result = $this->kelasModel->delete($id);
        if ($result) {
            return redirect()->to('/admin/akademik/kelas')->with('success', "Kelas '{$kelas['nama_kelas']}' berhasil dihapus.");
        } else {
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Gagal menghapus data kelas.');
        }
    }

    /**
     * Update wali kelas secara langsung (inline via AJAX atau POST).
     */
    public function updateWaliKelas($id = null)
    {
        $kelasId = $id ?: $this->request->getPost('kelas_id');
        $kelas = $this->kelasModel->find($kelasId);

        if (!$kelas) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data kelas tidak ditemukan.'
                ]);
            }
            return redirect()->to('/admin/akademik/kelas')->with('error', 'Data kelas tidak ditemukan.');
        }

        $waliKelasInput = $this->request->getPost('wali_kelas');
        $waliKelasId = null;
        $namaWali = '-';

        if (!empty($waliKelasInput)) {
            $guru = $this->guruModel->find($waliKelasInput);
            if ($guru) {
                $waliKelasId = (int) $guru['id'];
                $namaWali = $guru['nama'];
            }
        }

        $this->kelasModel->update($kelasId, [
            'wali_kelas' => $waliKelasId
        ]);

        $message = $waliKelasId 
            ? "Wali kelas {$kelas['nama_kelas']} berhasil diatur ke {$namaWali}."
            : "Wali kelas {$kelas['nama_kelas']} berhasil dikosongkan.";

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => $message,
                'kelas_id'  => (int) $kelasId,
                'nama_wali' => $namaWali,
                'csrf_token'=> csrf_token(),
                'csrf_hash' => csrf_hash()
            ]);
        }

        return redirect()->to('/admin/akademik/kelas')->with('success', $message);
    }

    /**
     * Download template Excel untuk impor kelas baku (otomatis untuk tahun ajaran aktif).
     */
    public function downloadTemplateKelas()
    {
        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        $labelTahun = $tahunAktif ? "{$tahunAktif['tahun_ajar']} - {$tahunAktif['semester']}" : "Tahun Aktif";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kelas');

        // Headers
        $sheet->setCellValue('A1', 'NAMA_KELAS');
        $sheet->setCellValue('B1', 'TINGKAT');
        $sheet->setCellValue('C1', 'JURUSAN');
        $sheet->setCellValue('D1', 'WALI_KELAS');
        $sheet->setCellValue('E1', 'STATUS');

        // Data validation for TINGKAT (X, XI, XII)
        $tingkatValidation = new DataValidation();
        $tingkatValidation->setType(DataValidation::TYPE_LIST);
        $tingkatValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $tingkatValidation->setAllowBlank(true);
        $tingkatValidation->setShowDropDown(true);
        $tingkatValidation->setPromptTitle('Pilih Tingkat');
        $tingkatValidation->setPrompt('Pilih X, XI, atau XII');
        $tingkatValidation->setFormula1('"X,XI,XII"');
        $sheet->setDataValidation('B2:B500', $tingkatValidation);

        // Data validation for STATUS (Aktif, Nonaktif)
        $statusValidation = new DataValidation();
        $statusValidation->setType(DataValidation::TYPE_LIST);
        $statusValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $statusValidation->setAllowBlank(false);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setPromptTitle('Status Kelas');
        $statusValidation->setFormula1('"Aktif,Nonaktif"');
        $sheet->setDataValidation('E2:E500', $statusValidation);

        // Sample Data Rows (Standar Rombel SMA: X, XI, XII)
        $sampleData = [
            ['X-1', 'X', 'Umum', '', 'Aktif'],
            ['X-2', 'X', 'Umum', '', 'Aktif'],
            ['X-3', 'X', 'Umum', '', 'Aktif'],
            ['XI-1', 'XI', 'Umum', '', 'Aktif'],
            ['XI-2', 'XI', 'Umum', '', 'Aktif'],
            ['XII-1', 'XII', 'Umum', '', 'Aktif'],
            ['XII-2', 'XII', 'Umum', '', 'Aktif'],
        ];

        $rowNum = 2;
        foreach ($sampleData as $row) {
            $sheet->setCellValue('A' . $rowNum, $row[0]);
            $sheet->setCellValue('B' . $rowNum, $row[1]);
            $sheet->setCellValue('C' . $rowNum, $row[2]);
            $sheet->setCellValue('D' . $rowNum, $row[3]);
            $sheet->setCellValue('E' . $rowNum, $row[4]);
            $rowNum++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(26);
        $sheet->getColumnDimension('E')->setWidth(14);

        // Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4E73DF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_kelas.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /**
     * Proses impor file Excel kelas ke Tahun Ajaran Aktif.
     */
    public function processImportKelas()
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
            return redirect()->back()->withInput()->with('error', 'File tidak valid. Unggah file Excel berekstensi .xlsx atau .xls (maksimal 10MB).');
        }

        $tahunAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tidak dapat mengimpor kelas karena belum ada Tahun Ajaran yang Aktif.');
        }

        $file = $this->request->getFile('excel_file');
        if (!$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        try {
            $filePath = $file->getTempName();
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Hapus baris header 1
            array_shift($rows);

            if (empty($rows)) {
                return redirect()->back()->with('error', 'File Excel kosong atau tidak memiliki data.');
            }

            // Siapkan pencocokan wali kelas berdasarkan NIP atau Nama
            $allGurus = $this->guruModel->findAll();
            $guruByNip = [];
            $guruByName = [];
            foreach ($allGurus as $g) {
                if (!empty($g['nip'])) {
                    $guruByNip[trim($g['nip'])] = (int) $g['id'];
                }
                $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $g['nama'])));
                if ($cleanName !== '') {
                    $guruByName[$cleanName] = (int) $g['id'];
                }
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $insertedCount = 0;
            $updatedCount  = 0;
            $tahunAjarId   = (int) $tahunAktif['id'];

            foreach ($rows as $row) {
                $namaKelas = trim((string) ($row[0] ?? ''));
                if ($namaKelas === '') {
                    continue;
                }

                $tingkat = strtoupper(trim((string) ($row[1] ?? '')));
                if (!in_array($tingkat, ['X', 'XI', 'XII'], true)) {
                    if (stripos($namaKelas, 'XII') === 0 || stripos($namaKelas, '12') === 0) {
                        $tingkat = 'XII';
                    } elseif (stripos($namaKelas, 'XI') === 0 || stripos($namaKelas, '11') === 0) {
                        $tingkat = 'XI';
                    } elseif (stripos($namaKelas, 'X') === 0 || stripos($namaKelas, '10') === 0) {
                        $tingkat = 'X';
                    } else {
                        $tingkat = null;
                    }
                }

                $jurusan = trim((string) ($row[2] ?? '')) ?: 'Umum';

                // Wali kelas
                $waliKelasInput = trim((string) ($row[3] ?? ''));
                $waliKelasId = null;
                if ($waliKelasInput !== '') {
                    if (isset($guruByNip[$waliKelasInput])) {
                        $waliKelasId = $guruByNip[$waliKelasInput];
                    } else {
                        $cleanInput = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $waliKelasInput)));
                        if (isset($guruByName[$cleanInput])) {
                            $waliKelasId = $guruByName[$cleanInput];
                        }
                    }
                }

                $statusInput = ucfirst(strtolower(trim((string) ($row[4] ?? 'Aktif'))));
                $status = in_array($statusInput, ['Aktif', 'Nonaktif'], true) ? $statusInput : 'Aktif';

                // Cek apakah kelas dengan nama yang sama sudah ada di tahun aktif ini
                $existing = $this->kelasModel
                    ->where('tahun_ajar_id', $tahunAjarId)
                    ->where('nama_kelas', $namaKelas)
                    ->first();

                if ($existing) {
                    $updatePayload = [
                        'tingkat' => $tingkat,
                        'jurusan' => $jurusan,
                        'status'  => $status,
                    ];
                    if ($waliKelasId !== null) {
                        $updatePayload['wali_kelas'] = $waliKelasId;
                    }
                    $this->kelasModel->update($existing['id'], $updatePayload);
                    $updatedCount++;
                } else {
                    $this->kelasModel->insert([
                        'tahun_ajar_id' => $tahunAjarId,
                        'nama_kelas'    => $namaKelas,
                        'tingkat'       => $tingkat,
                        'jurusan'       => $jurusan,
                        'wali_kelas'    => $waliKelasId,
                        'status'        => $status,
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                    $insertedCount++;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memproses impor kelas. Transaksi database dibatalkan.');
            }

            $msg = "Impor kelas berhasil: {$insertedCount} kelas baru ditambahkan, {$updatedCount} kelas diperbarui pada Tahun Ajaran {$tahunAktif['tahun_ajar']} ({$tahunAktif['semester']}).";
            return redirect()->to('/admin/akademik/kelas')->with('success', $msg);

        } catch (\Throwable $e) {
            log_message('error', 'Error import kelas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membaca file Excel: ' . $e->getMessage());
        }
    }
}
