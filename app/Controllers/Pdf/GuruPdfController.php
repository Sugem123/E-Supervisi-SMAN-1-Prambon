<?php

namespace App\Controllers\Pdf;

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

class GuruPdfController extends BaseController
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
        
        // Ensure date helper is loaded
        helper('date');
    }

    public function cetak($jadwalId)
    {
        $userId = session()->get('id');

        // Get guru data by user ID
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }

        // Get schedule details
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian, users.username as nama_supervisor, users.nip as nip_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.guru_id', $guru['id']) // Use guru ID, not user ID
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
            ->where('status', 'Aktif')
            ->first();
            
        if ($kepalaSekolah) {
            $schedule['nama_kepala'] = $kepalaSekolah['username'];
            $schedule['nip_kepala'] = isset($kepalaSekolah['nip']) ? $kepalaSekolah['nip'] : '';
        }

        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];

        // Generate PDF
        $html = view('supervisor/hasil/pdf_view', $data);

        // Setup Dompdf
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF
        $output = $dompdf->output();
        $filename = 'Hasil_Supervisi_' . $schedule['nama_guru'] . '_' . date('Y-m-d') . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $output;
        exit;
    }
}