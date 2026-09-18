<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\TahunAjarModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BatchExcelController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->tahunAjarModel = new TahunAjarModel();
    }

    public function exportAll()
    {
        try {
            // Get filter parameters
            $tahun_ajar_id = $this->request->getGet('tahun_ajar_id') ?? $this->request->getPost('tahun_ajar_id');
            $status = $this->request->getGet('status') ?? $this->request->getPost('status');
            
            // Build query for completed schedules
            $jadwalQuery = $this->jadwalModel
                ->select('jadwal_supervisi.*, tahun_ajar.tahun_ajar, tahun_ajar.semester, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran, kelas.nama_kelas')
                ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
                ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
                ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
                ->where('jadwal_supervisi.status', 'Selesai');
                
            if ($tahun_ajar_id) {
                $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahun_ajar_id);
            }
            
            if ($status) {
                $jadwalQuery->where('jadwal_supervisi.status', $status);
            }
            
            $jadwalQuery->orderBy('guru.nama', 'ASC');
            
            $jadwals = $jadwalQuery->findAll();
            
            if (empty($jadwals)) {
                return redirect()->back()->with('error', 'Tidak ada data hasil supervisi yang selesai untuk diexport');
            }
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            
            // Remove default worksheet
            $spreadsheet->removeSheetByIndex(0);
            
            // Group schedules by guru
            $groupedJadwals = [];
            foreach ($jadwals as $jadwal) {
                $guruId = $jadwal['guru_id'];
                if (!isset($groupedJadwals[$guruId])) {
                    $groupedJadwals[$guruId] = [
                        'guru' => [
                            'nama' => $jadwal['nama_guru'],
                            'nip' => $jadwal['nip_guru'],
                            'mata_pelajaran' => $jadwal['mata_pelajaran']
                        ],
                        'jadwals' => []
                    ];
                }
                $groupedJadwals[$guruId]['jadwals'][] = $jadwal;
            }
            
            // Process each guru's data
            foreach ($groupedJadwals as $guruId => $guruData) {
                // Create a new worksheet for each guru
                $sheetName = substr($guruData['guru']['nama'], 0, 31); // Excel sheet name max 31 chars
                // Clean sheet name to remove invalid characters
                $sheetName = str_replace(['*', '?', '[', ']', ':', '\\', '/'], '', $sheetName);
                if (empty($sheetName)) {
                    $sheetName = 'Sheet' . $guruId;
                }
                
                $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
                $spreadsheet->addSheet($sheet);
                $sheetIndex = $spreadsheet->getIndex($sheet);
                $sheet = $spreadsheet->getSheet($sheetIndex);
                
                // Set document properties
                $spreadsheet->getProperties()
                    ->setCreator("Sistem Supervisi")
                    ->setLastModifiedBy("Sistem Supervisi")
                    ->setTitle("Laporan Hasil Supervisi")
                    ->setSubject("Laporan Hasil Supervisi")
                    ->setDescription("Laporan hasil supervisi guru");
                
                // Header
                $sheet->setCellValue('A1', 'LAPORAN HASIL SUPERVISI GURU');
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // School name (SMA, legacy wrapper kept in helper).
                $sheet->setCellValue('A2', get_nama_sekolah());
                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Guru info
                $sheet->setCellValue('A4', 'Nama Guru');
                $sheet->setCellValue('B4', ': ' . $guruData['guru']['nama']);
                $sheet->setCellValue('A5', 'NIP');
                $sheet->setCellValue('B5', ': ' . ($guruData['guru']['nip'] ?? '-'));
                $sheet->setCellValue('A6', 'Mata Pelajaran');
                $sheet->setCellValue('B6', ': ' . ($guruData['guru']['mata_pelajaran'] ?? '-'));
                
                // Style for labels
                $sheet->getStyle('A4:A6')->getFont()->setBold(true);
                
                // Table header
                $headerRow = 8;
                $sheet->setCellValue('A' . $headerRow, 'No');
                $sheet->setCellValue('B' . $headerRow, 'Tahun Ajaran');
                $sheet->setCellValue('C' . $headerRow, 'Kelas');
                $sheet->setCellValue('D' . $headerRow, 'Tanggal Supervisi');
                $sheet->setCellValue('E' . $headerRow, 'Materi Supervisi');
                $sheet->setCellValue('F' . $headerRow, 'Nilai Akhir');
                $sheet->setCellValue('G' . $headerRow, 'Kategori');
                $sheet->setCellValue('H' . $headerRow, 'Rekomendasi');
                
                // Style table header
                $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4e73df');
                $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
                
                // Table data
                $row = $headerRow + 1;
                $no = 1;
                
                foreach ($guruData['jadwals'] as $jadwal) {
                    // Get hasil supervisi data
                    $hasilRecords = $this->hasilModel->where('jadwal_supervisi_id', $jadwal['id'])->findAll();
                    
                    // Get detail results grouped by jenis_penilaian_id
                    $db = \Config\Database::connect();
                    $detailResults = [];
                    foreach ($hasilRecords as $hasil) {
                        $details = $db->table('detail_hasil_penilaian dhp')
                            ->select('dhp.*, ap.nama_aspek')
                            ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id')
                            ->where('dhp.hasil_supervisi_id', $hasil['id'])
                            ->get()
                            ->getResultArray();
                            
                        $detailResults[$hasil['jenis_penilaian_id']] = $details;
                    }
                    
                    // Calculate nilai akhir
                    $nilaiAkhir = $this->calculateNilaiAkhir($hasilRecords);
                    $kategori = $this->getKategori($nilaiAkhir);
                    
                    // Get rekomendasi (combine all recommendations)
                    $rekomendasi = '';
                    if (!empty($hasilRecords)) {
                        $rekomendasiArray = array_filter(array_column($hasilRecords, 'rekomendasi'));
                        $rekomendasi = implode('; ', $rekomendasiArray);
                    }
                    
                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $jadwal['tahun_ajar'] . ' - ' . $jadwal['semester']);
                    $sheet->setCellValue('C' . $row, $jadwal['nama_kelas'] ?? $jadwal['kelas'] ?? '-');
                    $sheet->setCellValue('D' . $row, format_tanggal_indonesia($jadwal['tanggal_supervisi']));
                    $sheet->setCellValue('E' . $row, $jadwal['materi_supervisi'] ?? '-');
                    $sheet->setCellValue('F' . $row, $nilaiAkhir !== null ? number_format($nilaiAkhir, 2) : '-');
                    $sheet->setCellValue('G' . $row, $kategori);
                    $sheet->setCellValue('H' . $row, $rekomendasi);
                    
                    // Add detail penilaian section
                    $row++;
                    $row++;
                    $sheet->setCellValue('A' . $row, 'Detail Penilaian:');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;
                    
                    // Add detail penilaian table for each jenis penilaian
                    $nama_komponen = [
                        1 => 'SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)',
                        2 => 'SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)',
                        3 => 'SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)',
                        4 => 'SUPERVISI PENGEMBANGAN DIRI GURU'
                    ];
                    
                    foreach ($hasilRecords as $hasil) {
                        $jenisId = $hasil['jenis_penilaian_id'];
                        $jenisNama = $nama_komponen[$jenisId] ?? 'Komponen Lain';
                        
                        // Calculate nilai for this jenis penilaian
                        $total_skor = 0;
                        $jumlah_aspek = 0;
                        if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])) {
                            foreach ($detailResults[$jenisId] as $detail) {
                                $total_skor += isset($detail['skor']) ? $detail['skor'] : 0;
                                $jumlah_aspek++;
                            }
                        }
                        $skor_maksimal = $jumlah_aspek * 4;
                        $nilai = $skor_maksimal > 0 ? ($total_skor / $skor_maksimal) * 100 : 0;
                        
                        $row++;
                        $sheet->setCellValue('A' . $row, $jenisNama . ' (Nilai: ' . number_format($nilai, 2) . '%)');
                        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                        $row++;
                        
                        // Detail penilaian table header
                        $sheet->setCellValue('A' . $row, 'No');
                        $sheet->setCellValue('B' . $row, 'Aspek Penilaian');
                        $sheet->setCellValue('C' . $row, 'Skor');
                        $sheet->setCellValue('D' . $row, 'Catatan');
                        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
                        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFE0E0E0');
                        $row++;
                        
                        // Detail penilaian table data
                        if (isset($detailResults[$jenisId]) && !empty($detailResults[$jenisId])) {
                            $no_detail = 1;
                            foreach ($detailResults[$jenisId] as $detail) {
                                $sheet->setCellValue('A' . $row, $no_detail++);
                                $sheet->setCellValue('B' . $row, $detail['nama_aspek'] ?? '');
                                $sheet->setCellValue('C' . $row, $detail['skor'] ?? '0');
                                $sheet->setCellValue('D' . $row, $detail['catatan'] ?? '-');
                                $row++;
                            }
                        } else {
                            $sheet->setCellValue('A' . $row, '');
                            $sheet->setCellValue('B' . $row, 'Tidak ada detail penilaian');
                            $sheet->mergeCells('B' . $row . ':D' . $row);
                            $row++;
                        }
                        
                        // Add rekomendasi if exists
                        if (!empty($hasil['rekomendasi'])) {
                            $row++;
                            $sheet->setCellValue('A' . $row, 'Rekomendasi:');
                            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                            $row++;
                            $sheet->setCellValue('A' . $row, $hasil['rekomendasi']);
                            $sheet->mergeCells('A' . $row . ':D' . $row);
                            $row++;
                        }
                        
                        $row++;
                    }
                    
                    $row++;
                }
                
                // Auto-size columns
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Freeze the first row
                $sheet->freezePane('A' . ($headerRow + 1));
            }
            
            // Set active sheet to first sheet
            $spreadsheet->setActiveSheetIndex(0);
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan-hasil-supervisi-' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            // Create Excel writer
            $writer = new Xlsx($spreadsheet);
            
            // Clear any previous output
            ob_end_clean();
            
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Batch Excel Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }
    
    private function calculateNilaiAkhir($hasilRecords)
    {
        if (empty($hasilRecords)) {
            return null;
        }
        
        $db = \Config\Database::connect();
        $totalSkor = 0;
        $totalMaksimal = 0;
        
        foreach ($hasilRecords as $hasil) {
            // Get detail hasil penilaian for this hasil record
            $detailHasil = $db->table('detail_hasil_penilaian')
                ->where('hasil_supervisi_id', $hasil['id'])
                ->selectSum('skor')
                ->get()
                ->getRow();
            
            // Get count of detail penilaian to calculate max possible score
            $detailCount = $db->table('detail_hasil_penilaian')
                ->where('hasil_supervisi_id', $hasil['id'])
                ->countAllResults();
            
            // Add to totals
            $totalSkor += $detailHasil->skor ?? 0;
            // Assuming max score per item is 4 (based on system specs)
            $totalMaksimal += $detailCount * 4;
        }
        
        // Calculate nilai_akhir: (actual score sum / max possible score) × 100
        return ($totalMaksimal > 0) ? ($totalSkor / $totalMaksimal) * 100 : 0;
    }
    
    private function getKategori($nilaiAkhir)
    {
        if ($nilaiAkhir === null) {
            return '-';
        }
        
        if ($nilaiAkhir >= 86) {
            return 'Baik Sekali';
        } elseif ($nilaiAkhir >= 70) {
            return 'Baik';
        } elseif ($nilaiAkhir >= 55) {
            return 'Cukup';
        } else {
            return 'Kurang';
        }
    }
}