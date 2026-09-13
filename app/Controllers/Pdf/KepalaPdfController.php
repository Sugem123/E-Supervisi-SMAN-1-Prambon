<?php

namespace App\Controllers\Pdf;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\GuruModel;
use App\Models\AspekPenilaianModel;
use App\Models\FotoBuktiModel;
use App\Models\UserModel;

// Include Dompdf library
require_once APPPATH . '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class KepalaPdfController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $detailModel;
    protected $guruModel;
    protected $aspekModel;
    protected $fotoModel;
    protected $userModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
        $this->guruModel = new GuruModel();
        $this->aspekModel = new AspekPenilaianModel();
        $this->fotoModel = new FotoBuktiModel();
        $this->userModel = new UserModel();
    }

    public function cetak($jadwalId)
    {
        // Get schedule details (without supervisor filter for kepala)
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }

        // Get all assessment results for this schedule
        $hasilList = $this->hasilModel
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

        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Render view khusus PDF
        $html = view('kepala/hasil/pdf_view', $data);

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
    
    public function testCetak($jadwalId)
    {
        // Get schedule details (without supervisor filter for kepala)
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.mata_pelajaran as guru_mata_pelajaran, guru.status_kepegawaian, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }

        // Get all assessment results for this schedule
        $hasilList = $this->hasilModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();
            
        if (empty($hasilList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data hasil tidak ditemukan');
        }
        
        // Group detail results by jenis_penilaian_id (ambil hanya satu untuk test)
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $this->detailModel
                ->select('detail_hasil_penilaian.*, aspek_penilaian.nama_aspek')
                ->join('aspek_penilaian', 'aspek_penilaian.id = detail_hasil_penilaian.aspek_penilaian_id', 'left')
                ->where('detail_hasil_penilaian.hasil_supervisi_id', $hasil['id'])
                ->findAll();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
            break; // Hanya ambil satu kategori untuk test
        }

        // Get kepala sekolah data
        $kepalaSekolah = $this->userModel
            ->select('username, nip')
            ->where('role', 'kepala')
            ->where('status', 'Aktif')
            ->first();
            
        if ($kepalaSekolah) {
            $schedule['nama_kepala'] = $kepalaSekolah['username'];
            $schedule['nip_kepala'] = $kepalaSekolah['nip'] ?? '';
        }

        $data = [
            'schedule' => $schedule,
            'detailResults' => $detailResults
        ];

        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Render view khusus PDF
        $html = view('kepala/hasil/test_pdf_view', $data);

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', false); // Disable remote for test
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Stream the PDF directly to the browser
        $dompdf->stream("test_hasil_supervisi_" . $schedule['nama_guru'] . ".pdf", ['Attachment' => false]);
        
        // Exit to prevent any additional output
        exit();
    }
}