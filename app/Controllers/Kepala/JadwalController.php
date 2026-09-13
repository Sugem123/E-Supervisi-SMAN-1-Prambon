<?php

namespace App\Controllers\Kepala;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\TahunAjarModel;
use App\Models\KelasModel;
use App\Models\UserModel;
use App\Models\SupervisorModel;

// Include Dompdf library
require_once APPPATH . '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $tahunAjarModel;
    protected $kelasModel;
    protected $userModel;
    protected $supervisorModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel = new GuruModel();
        $this->tahunAjarModel = new TahunAjarModel();
        $this->kelasModel = new KelasModel();
        $this->userModel = new UserModel();
        $this->supervisorModel = new SupervisorModel();
    }

    public function index()
    {
        // Get all schedules (without supervisor filter for kepala)
        // Menggunakan join dengan users.id = jadwal_supervisi.supervisor_id karena data tidak konsisten
        $schedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $data = [
            'schedules' => $schedules
        ];

        return view('kepala/jadwal/index', $data);
    }

    public function create()
    {
        // Get active tahun ajar
        $tahunAjarAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();

        // Get all active classes
        $kelases = [];

        if ($tahunAjarAktif) {
            $kelases = $this->kelasModel->where('tahun_ajar_id', $tahunAjarAktif['id'])->where('status', 'Aktif')->findAll();
        }

        // Get supervisors with their user data (including kepala users)
        $supervisors = $this->supervisorModel
            ->select('supervisor.*, users.username, users.email')
            ->join('users', 'users.id = supervisor.user_id')
            ->where('users.status', 'Aktif')
            ->findAll();

        // Also get kepala users who can act as supervisors
        $kepalaUsers = $this->userModel
            ->where('role', 'kepala')
            ->where('status', 'Aktif')
            ->findAll();

        $data['tahun_ajar'] = $tahunAjarAktif;
        $data['kelases'] = $kelases;
        $data['gurus'] = $this->guruModel->findAll();
        $data['supervisors'] = $supervisors;
        $data['kepala_users'] = $kepalaUsers;

        return view('kepala/jadwal/create', $data);
    }

    public function store()
    {
        // Get class name
        $kelas = $this->kelasModel->find($this->request->getPost('kelas_id'));
        // Sanitize mata_pelajaran input and collapse whitespace
        $mpRaw = $this->request->getPost('mata_pelajaran');
        $mp = $mpRaw !== null ? trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $mpRaw))) : '';

        // If mata_pelajaran is empty, fallback to guru's stored mata_pelajaran
        if ($mp === '' && $this->request->getPost('guru_id')) {
            $guru = $this->guruModel->find($this->request->getPost('guru_id'));
            $mp = $guru['mata_pelajaran'] ?? '';
        }

        $jadwalData = [
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'guru_id' => $this->request->getPost('guru_id'),
            'supervisor_id' => $this->request->getPost('supervisor_id'),
            'mata_pelajaran' => $mp,
            'kelas' => $kelas ? $kelas['nama_kelas'] : '', // Store class name for backward compatibility
            'kelas_id' => $this->request->getPost('kelas_id'),
            'jam_ke' => $this->request->getPost('jam_ke'),
            'hari' => $this->request->getPost('hari'),
            'tanggal_supervisi' => $this->request->getPost('tanggal_supervisi'),
            'status' => 'Terjadwal',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->jadwalModel->insert($jadwalData);

        if ($result) {
            return redirect()->to('/kepala/jadwal')->with('success', 'Jadwal supervisi berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jadwal supervisi');
        }
    }

    public function detail($id)
    {
        // Get schedule details
        // Menggunakan join dengan users.id = jadwal_supervisi.supervisor_id karena data tidak konsisten
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian, users.username as nama_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan');
        }

        $data = [
            'schedule' => $schedule
        ];

        return view('kepala/jadwal/detail', $data);
    }

    public function edit($id)
    {
        // Get schedule details
        $schedule = $this->jadwalModel->find($id);
        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan');
        }

        // Get active tahun ajar
        $tahunAjarAktif = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();

        // Get all tahun ajar for dropdown
        $tahunAjars = $this->tahunAjarModel->findAll();

        // Get all active classes
        $kelases = [];

        if ($tahunAjarAktif) {
            $kelases = $this->kelasModel->where('tahun_ajar_id', $tahunAjarAktif['id'])->where('status', 'Aktif')->findAll();
        }

        // Get supervisors with their user data
        $supervisors = $this->supervisorModel
            ->select('supervisor.*, users.username, users.email')
            ->join('users', 'users.id = supervisor.user_id')
            ->where('users.status', 'Aktif')
            ->findAll();

        // Also get kepala users who can act as supervisors
        $kepalaUsers = $this->userModel
            ->where('role', 'kepala')
            ->where('status', 'Aktif')
            ->findAll();

        $data['jadwal'] = $schedule;
        $data['tahun_ajars'] = $tahunAjars;
        $data['tahun_ajar'] = $tahunAjarAktif;
        $data['kelases'] = $kelases;
        $data['gurus'] = $this->guruModel->findAll();
        $data['supervisors'] = $supervisors;
        $data['kepala_users'] = $kepalaUsers;

        return view('kepala/jadwal/edit', $data);
    }

    public function update($id)
    {
        // Get class name
        $kelas = $this->kelasModel->find($this->request->getPost('kelas_id'));
        // Sanitize mata_pelajaran input and only overwrite if non-empty
        $mpRaw = $this->request->getPost('mata_pelajaran');
        $mp = $mpRaw !== null ? trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $mpRaw))) : null;

        $jadwalData = [
            'tahun_ajar_id' => $this->request->getPost('tahun_ajar_id'),
            'guru_id' => $this->request->getPost('guru_id'),
            'supervisor_id' => $this->request->getPost('supervisor_id'),
            'kelas' => $kelas ? $kelas['nama_kelas'] : '', // Store class name for backward compatibility
            'kelas_id' => $this->request->getPost('kelas_id'),
            'jam_ke' => $this->request->getPost('jam_ke'),
            'hari' => $this->request->getPost('hari'),
            'tanggal_supervisi' => $this->request->getPost('tanggal_supervisi'),
        ];

        // Only set mata_pelajaran if a non-empty value was provided
        if ($mp !== null) {
            if ($mp !== '') {
                $jadwalData['mata_pelajaran'] = $mp;
            } else {
                // don't overwrite with empty
            }
        }

        $result = $this->jadwalModel->update($id, $jadwalData);

        if ($result) {
            return redirect()->to('/kepala/jadwal')->with('success', 'Jadwal supervisi berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jadwal supervisi');
        }
    }
    public function exportExcel()
    {
        // Get all schedules
        $schedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set properties
        $spreadsheet->getProperties()->setCreator('Sistem Supervisi')
            ->setTitle('Jadwal Supervisi');

        // Header
        $sheet->setCellValue('A1', 'JADWAL SUPERVISI PEMBELAJARAN');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Column headers
        $currentRow = 3;
        $sheet->setCellValue('A' . $currentRow, 'No');
        $sheet->setCellValue('B' . $currentRow, 'Tanggal');
        $sheet->setCellValue('C' . $currentRow, 'Guru');
        $sheet->setCellValue('D' . $currentRow, 'Supervisor');
        $sheet->setCellValue('E' . $currentRow, 'Mata Pelajaran');
        $sheet->setCellValue('F' . $currentRow, 'Kelas');
        $sheet->setCellValue('G' . $currentRow, 'Jam Ke');
        $sheet->setCellValue('H' . $currentRow, 'Status');

        $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $currentRow++;
        $no = 1;

        foreach ($schedules as $schedule) {
            $sheet->setCellValue('A' . $currentRow, $no++);
            $sheet->setCellValue('B' . $currentRow, date('d/m/Y', strtotime($schedule['tanggal_supervisi'])));
            $sheet->setCellValue('C' . $currentRow, $schedule['nama_guru']);
            $sheet->setCellValue('D' . $currentRow, $schedule['nama_supervisor'] ?? 'Tidak ditentukan');
            $sheet->setCellValue('E' . $currentRow, $schedule['mata_pelajaran']);
            $sheet->setCellValue('F' . $currentRow, $schedule['kelas']);
            $sheet->setCellValue('G' . $currentRow, $schedule['jam_ke']);
            $sheet->setCellValue('H' . $currentRow, $schedule['status']);

            $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $currentRow++;
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Jadwal_Supervisi_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        // Get all schedules
        $schedules = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $data = [
            'schedules' => $schedules,
            'title' => 'Laporan Jadwal Supervisi'
        ];

        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        // We can reuse a simple view for PDF or create a generic table view. 
        // For simplicity, I'll inline a simple HTML structure or better, modify existing layouts to support PDF mode?
        // Or cleaner: create a new view 'kepala/jadwal/pdf_view'. I will assume this view needs to be created.

        $html = view('kepala/jadwal/pdf_view', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Landscape might be better for table
        $dompdf->render();

        $dompdf->stream('Jadwal_Supervisi_' . date('Y-m-d') . '.pdf', ['Attachment' => 0]);
    }
}
