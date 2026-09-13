<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\GuruModel;
use App\Models\FotoBuktiModel;
use App\Models\UserModel;

// Include Dompdf library
require_once APPPATH . '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $detailModel;
    protected $guruModel;
    protected $fotoModel;
    protected $userModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
        $this->guruModel = new GuruModel();
        $this->fotoModel = new FotoBuktiModel();
        $this->userModel = new UserModel();
    }

    public function cetak($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian, users.username as nama_supervisor, users.nip as nip_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }

        // Get all assessment results for this schedule
        $hasilList = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();
            
        if (empty($hasilList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data hasil tidak ditemukan');
        }
        
        // Group detail results by jenis_penilaian_id
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id', 'left')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
        }

        // Get photo evidence
        $fotoBukti = $this->fotoModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get kepala sekolah data
        $kepalaSekolah = $this->userModel
            ->select('username, nip')
            ->where('role', 'kepala')
            ->first();
            
        // Set default values
        $schedule['nama_kepala'] = 'Kepala Sekolah';
        $schedule['nip_kepala'] = '';
        
        // Set nama dan nip kepala sekolah
        if ($kepalaSekolah) {
            $schedule['nama_kepala'] = $kepalaSekolah['username'] ?? 'Kepala Sekolah';
            $schedule['nip_kepala'] = $kepalaSekolah['nip'] ?? '';
        }

        // Ensure supervisor data is properly set
        if (empty($schedule['nama_supervisor'])) {
            // Coba cari supervisor berdasarkan ID
            $supervisor = $this->userModel
                ->select('username, nip')
                ->where('id', $supervisorId)
                ->first();
                
            if ($supervisor) {
                $schedule['nama_supervisor'] = $supervisor['username'] ?? 'Supervisor';
                $schedule['nip_supervisor'] = $supervisor['nip'] ?? '';
            } else {
                $schedule['nama_supervisor'] = 'Supervisor';
                $schedule['nip_supervisor'] = '';
            }
        }
        
        // Pastikan NIP supervisor selalu ada
        if (!isset($schedule['nip_supervisor']) || empty($schedule['nip_supervisor'])) {
            $schedule['nip_supervisor'] = '';
        }

        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];

        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Render view khusus PDF
        $html = view('supervisor/hasil/pdf_view', $data);

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Stream the PDF directly to the browser
        $dompdf->stream("hasil_supervisi_" . $schedule['nama_guru'] . ".pdf", ['Attachment' => false]);
        
        // Exit to prevent any additional output
        exit();
    }
}