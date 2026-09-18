<?php

namespace App\Controllers\Kepala;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\GuruModel;
use App\Models\AspekPenilaianModel;
use App\Models\FotoBuktiModel;

// Include Dompdf library
require_once APPPATH . '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HasilController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $detailModel;
    protected $guruModel;
    protected $aspekModel;
    protected $fotoModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
        $this->guruModel = new GuruModel();
        $this->aspekModel = new AspekPenilaianModel();
        $this->fotoModel = new FotoBuktiModel();
    }

    public function index()
    {
        // Get completed supervision schedules (without supervisor filter for kepala)
        // Menggunakan join dengan users.id = jadwal_supervisi.supervisor_id karena data tidak konsisten
        $jadwalSelesai = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.status', 'Selesai')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $data = [
            'jadwalSelesai' => $jadwalSelesai
        ];

        return view('kepala/hasil/index', $data);
    }

    public function detail($id)
    {
        // Get schedule details
        // Menggunakan join dengan users.id = jadwal_supervisi.supervisor_id karena data tidak konsisten
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }

        // Get all assessment results
        $hasilPenilaian = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis, jenis_penilaian.skor_maksimal')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('hasil_supervisi.jadwal_supervisi_id', $id)
            ->findAll();

        // Get detail results and calculate nilai_akhir and ketercapaian dynamically
        $detailResults = [];
        $processedHasilPenilaian = [];

        foreach ($hasilPenilaian as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();

            $detailResults[$hasil['jenis_penilaian_id']] = $details;

            // Calculate nilai_akhir and ketercapaian for this jenis penilaian
            $totalSkor = 0;
            foreach ($details as $detail) {
                $totalSkor += $detail['skor'];
            }

            // Calculate percentage
            // Skor maksimal per komponen (4 x jumlah aspek)
            $maxScore = count($details) * 4;
            $nilaiAkhir = $maxScore > 0 ? ($totalSkor / $maxScore) * 100 : 0;

            // Determine ketercapaian based on nilai_akhir
            if ($nilaiAkhir >= 86) {
                $ketercapaian = 'Baik Sekali';
            } elseif ($nilaiAkhir >= 70) {
                $ketercapaian = 'Baik';
            } elseif ($nilaiAkhir >= 55) {
                $ketercapaian = 'Cukup';
            } else {
                $ketercapaian = 'Kurang';
            }

            // Update the hasil data with calculated values
            $hasil['nilai_akhir'] = $nilaiAkhir;
            $hasil['ketercapaian'] = $ketercapaian;
            $hasil['total_skor'] = $totalSkor;

            $processedHasilPenilaian[] = $hasil;
        }

        // Get uploaded photos
        $uploadedPhotos = $this->fotoModel
            ->where('jadwal_supervisi_id', $id)
            ->findAll();

        $data = [
            'schedule' => $schedule,
            'hasilList' => $processedHasilPenilaian,
            'detailResults' => $detailResults,
            'uploadedPhotos' => $uploadedPhotos
        ];

        return view('kepala/hasil/detail', $data);
    }

    public function exportToExcel($id)
    {
        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        // Menambahkan nama kepala sekolah sebagai pengganti nama supervisor
        $schedule['nama_supervisor'] = get_pengaturan('nama_kepala', 'Kepala Sekolah');

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }

        // Get all assessment results
        $hasilPenilaian = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis, jenis_penilaian.skor_maksimal')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('hasil_supervisi.jadwal_supervisi_id', $id)
            ->findAll();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()->setCreator('Sistem Supervisi')
            ->setTitle('Hasil Supervisi - ' . $schedule['nama_guru']);

        // Header
        $sheet->setCellValue('A1', 'HASIL SUPERVISI PEMBELAJARAN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // School info (SMA). NOTE: legacy key 'alamat_madrasah' never existed; correct key is 'alamat'.
        $sheet->setCellValue('A3', 'Nama Sekolah: ' . get_nama_sekolah());
        $sheet->setCellValue('A4', 'Alamat: ' . get_pengaturan('alamat', ''));

        // Teacher info
        $sheet->setCellValue('A6', 'DATA GURU');
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->setCellValue('A7', 'Nama Guru');
        $sheet->setCellValue('B7', ': ' . $schedule['nama_guru']);
        $sheet->setCellValue('A8', 'NIP');
        $sheet->setCellValue('B8', ': ' . ($schedule['nip'] ?? '-'));
        $sheet->setCellValue('A9', 'Pangkat/Golongan');
        $sheet->setCellValue('B9', ': ' . ($schedule['pangkat_golongan'] ?? '-'));
        $sheet->setCellValue('A10', 'Mata Pelajaran');
        $sheet->setCellValue('B10', ': ' . $schedule['guru_mata_pelajaran']);
        $sheet->setCellValue('A11', 'Tanggal Supervisi');
        $sheet->setCellValue('B11', ': ' . format_tanggal_indonesia($schedule['tanggal_supervisi']));
        $sheet->setCellValue('A12', 'Supervisor');
        $sheet->setCellValue('B12', ': ' . ($schedule['nama_supervisor'] ?? '-'));

        $currentRow = 14;

        // Assessment results
        $grandTotalSkor = 0;
        $grandTotalMaxScore = 0;

        if (!empty($hasilPenilaian)) {
            foreach ($hasilPenilaian as $hasil) {
                $details = $this->detailModel
                    ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                    ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id')
                    ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                    ->findAll();

                // Calculate nilai_akhir and ketercapaian for this jenis penilaian
                $totalSkor = 0;
                foreach ($details as $detail) {
                    $totalSkor += $detail['skor'];
                }

                // Calculate percentage
                // Skor maksimal per komponen (4 x jumlah aspek)
                $maxScore = count($details) * 4;

                $grandTotalSkor += $totalSkor;
                $grandTotalMaxScore += $maxScore;
                $nilaiAkhir = $maxScore > 0 ? ($totalSkor / $maxScore) * 100 : 0;

                // Determine ketercapaian based on nilai_akhir
                if ($nilaiAkhir >= 86) {
                    $ketercapaian = 'Baik Sekali';
                } elseif ($nilaiAkhir >= 70) {
                    $ketercapaian = 'Baik';
                } elseif ($nilaiAkhir >= 55) {
                    $ketercapaian = 'Cukup';
                } else {
                    $ketercapaian = 'Kurang';
                }

                $sheet->setCellValue('A' . $currentRow, 'HASIL PENILAIAN: ' . strtoupper($hasil['nama_jenis']));
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                // Table header
                $sheet->setCellValue('A' . $currentRow, 'No');
                $sheet->setCellValue('B' . $currentRow, 'Aspek Penilaian');
                $sheet->setCellValue('C' . $currentRow, 'Skor');
                $sheet->setCellValue('D' . $currentRow, 'Catatan');
                $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                // Details
                $no = 1;
                foreach ($details as $detail) {
                    $sheet->setCellValue('A' . $currentRow, $no++);
                    $sheet->setCellValue('B' . $currentRow, $detail['nama_aspek']);
                    $sheet->setCellValue('C' . $currentRow, $detail['skor']);
                    $sheet->setCellValue('D' . $currentRow, $detail['catatan'] ?? '-');
                    $currentRow++;
                }

                // Total and final score
                $sheet->setCellValue('A' . $currentRow, '');
                $sheet->setCellValue('B' . $currentRow, 'Total Skor');
                $sheet->setCellValue('C' . $currentRow, $totalSkor);
                $sheet->setCellValue('D' . $currentRow, '');
                $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                $sheet->setCellValue('A' . $currentRow, '');
                $sheet->setCellValue('B' . $currentRow, 'Nilai Akhir');
                $sheet->setCellValue('C' . $currentRow, number_format($nilaiAkhir, 2));
                $sheet->setCellValue('D' . $currentRow, $ketercapaian);
                $sheet->getStyle('A' . $currentRow . ':D' . $currentRow)->getFont()->setBold(true);
                $currentRow += 2;

                // Recommendation if exists
                if (!empty($hasil['rekomendasi'])) {
                    $sheet->setCellValue('A' . $currentRow, 'Rekomendasi:');
                    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                    $currentRow++;
                    $sheet->setCellValue('A' . $currentRow, $hasil['rekomendasi']);
                    $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                    $currentRow += 2;
                }
            }
        } else {
            $sheet->setCellValue('A' . $currentRow, 'Belum ada hasil penilaian untuk jadwal ini.');
            $currentRow++;
        }

        // Overall final score
        // Calculate final accumulated values if strict 'nilai_akhir' is missing from DB
        $finalNilaiAkhir = $grandTotalMaxScore > 0 ? ($grandTotalSkor / $grandTotalMaxScore) * 100 : 0;

        // Determine Ketercapaian
        if ($finalNilaiAkhir >= 86) {
            $finalKetercapaian = 'Baik Sekali';
        } elseif ($finalNilaiAkhir >= 70) {
            $finalKetercapaian = 'Baik';
        } elseif ($finalNilaiAkhir >= 55) {
            $finalKetercapaian = 'Cukup';
        } else {
            $finalKetercapaian = 'Kurang';
        }

        $sheet->setCellValue('A' . $currentRow, 'NILAI AKHIR KESELURUHAN');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Nilai Akhir');
        $sheet->setCellValue('B' . $currentRow, ': ' . number_format($finalNilaiAkhir, 2));
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Ketercapaian');
        $sheet->setCellValue('B' . $currentRow, ': ' . $finalKetercapaian);
        $currentRow++;

        // Adjust column widths
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Hasil_Supervisi_' . str_replace(' ', '_', $schedule['nama_guru']) . '.xlsx"');
        header('Cache-Control: max-age=0');

        // Save to output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function generatePdf($id)
    {
        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor, users.nip as nip_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        // Menambahkan nama kepala sekolah
        $schedule['nama_kepala'] = get_pengaturan('nama_kepala', 'Kepala Sekolah');
        $schedule['nip_kepala'] = get_pengaturan('nip_kepala', '-');

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }

        // Get all assessment results
        $hasilPenilaian = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis, jenis_penilaian.skor_maksimal')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('hasil_supervisi.jadwal_supervisi_id', $id)
            ->findAll();

        // Get detail results and calculate nilai_akhir and ketercapaian dynamically
        $detailResults = [];
        $processedHasilPenilaian = [];

        foreach ($hasilPenilaian as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();

            $detailResults[$hasil['jenis_penilaian_id']] = $details;

            // Calculate nilai_akhir and ketercapaian for this jenis penilaian
            $totalSkor = 0;
            foreach ($details as $detail) {
                $totalSkor += $detail['skor'];
            }

            // Calculate percentage
            // Skor maksimal per komponen (4 x jumlah aspek)
            $maxScore = count($details) * 4;
            $nilaiAkhir = $maxScore > 0 ? ($totalSkor / $maxScore) * 100 : 0;

            // Determine ketercapaian based on nilai_akhir
            if ($nilaiAkhir >= 86) {
                $ketercapaian = 'Baik Sekali';
            } elseif ($nilaiAkhir >= 70) {
                $ketercapaian = 'Baik';
            } elseif ($nilaiAkhir >= 55) {
                $ketercapaian = 'Cukup';
            } else {
                $ketercapaian = 'Kurang';
            }

            // Update the hasil data with calculated values
            $hasil['nilai_akhir'] = $nilaiAkhir;
            $hasil['ketercapaian'] = $ketercapaian;
            $hasil['total_skor'] = $totalSkor;

            $processedHasilPenilaian[] = $hasil;
        }

        // Get uploaded photos
        $uploadedPhotos = $this->fotoModel
            ->where('jadwal_supervisi_id', $id)
            ->findAll();

        $data = [
            'schedule' => $schedule,
            'hasilList' => $processedHasilPenilaian, // Changed to match view expectation
            'detailResults' => $detailResults,
            'fotoBukti' => $uploadedPhotos // Changed to match view expectation
        ];

        // Generate PDF using Dompdf
        $dompdf = new Dompdf();

        // Enable remote file access for images
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        // Use the supervisor's PDF view
        $html = view('supervisor/hasil/pdf_view', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Hasil_Supervisi_' . str_replace(' ', '_', $schedule['nama_guru']) . '.pdf', ['Attachment' => 0]);
    }
}
