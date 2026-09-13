<?php

namespace App\Controllers\Kepala;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\TahunAjarModel;
use Dompdf\Dompdf;

class LaporanController extends BaseController
{
    protected $jadwalModel;
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->tahunAjarModel = new TahunAjarModel();
    }

    public function index()
    {
        $guruModel = new \App\Models\GuruModel();
        $userModel = new \App\Models\UserModel();

        // Basic stats
        $data['total_guru'] = $guruModel->countAll();
        $data['total_supervisor'] = $userModel->where('role', 'supervisor')->countAllResults();

        // Supervision stats
        $data['totalScheduled'] = $this->jadwalModel->countAllResults();
        $data['totalCompleted'] = $this->jadwalModel->where('status', 'Selesai')->countAllResults();
        $data['totalPending'] = $this->jadwalModel->where('status !=', 'Selesai')->countAllResults();

        // Completed supervisions list for the table
        $data['completed_supervisions'] = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.status', 'Selesai')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        $data['tahunAjarans'] = $this->tahunAjarModel->findAll();
        return view('kepala/laporan/index', $data);
    }

    public function hasilSupervisi()
    {
        $tahunAjarId = $this->request->getGet('tahun_ajar_id');
        
        $jadwalQuery = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.status', 'Selesai');
            
        if ($tahunAjarId) {
            $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahunAjarId);
            $data['filter_tahun_ajar'] = $this->tahunAjarModel->find($tahunAjarId);
        }
        
        $data['jadwals'] = $jadwalQuery->findAll();
        $data['tahunAjarans'] = $this->tahunAjarModel->findAll();
        
        return view('kepala/laporan/hasil_supervisi', $data);
    }

    public function detailHasilSupervisi($id)
    {
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.status_kepegawaian, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();
            
        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan');
        }
        
        // Get assessment results
        $db = \Config\Database::connect();
        $hasilQuery = $db->table('hasil_supervisi hs')
            ->select('hs.*, jp.nama as nama_jenis')
            ->join('jenis_penilaian jp', 'jp.id = hs.jenis_penilaian_id')
            ->where('hs.jadwal_supervisi_id', $id);
            
        $hasilList = $hasilQuery->get()->getResultArray();
        
        // Get detail results
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $db->table('detail_hasil_penilaian dhp')
                ->select('dhp.*, ap.nama_aspek')
                ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id', 'left')
                ->where('dhp.hasil_supervisi_id', $hasil['id'])
                ->get()
                ->getResultArray();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
        }
        
        // Get photo evidence
        $fotoBukti = $db->table('foto_bukti')
            ->where('jadwal_supervisi_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];
        
        return view('kepala/laporan/detail', $data);
    }

    public function cetakHasilSupervisi()
    {
        $tahunAjarId = $this->request->getGet('tahun_ajar_id');
        
        $jadwalQuery = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.status', 'Selesai');
            
        if ($tahunAjarId) {
            $jadwalQuery->where('jadwal_supervisi.tahun_ajar_id', $tahunAjarId);
            $data['filter_tahun_ajar'] = $this->tahunAjarModel->find($tahunAjarId);
        }
        
        $data['jadwals'] = $jadwalQuery->findAll();
        
        $html = view('kepala/laporan/pdf_hasil_supervisi', $data);
        
        // Setup dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output the PDF
        $dompdf->stream('laporan-hasil-supervisi.pdf', ['Attachment' => 0]);
    }

    public function cetakDetailHasilSupervisi($id)
    {
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip, guru.pangkat_golongan, guru.status_kepegawaian, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $id)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();
            
        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan');
        }
        
        // Get assessment results
        $db = \Config\Database::connect();
        $hasilQuery = $db->table('hasil_supervisi hs')
            ->select('hs.*, jp.nama as nama_jenis')
            ->join('jenis_penilaian jp', 'jp.id = hs.jenis_penilaian_id')
            ->where('hs.jadwal_supervisi_id', $id);
            
        $hasilList = $hasilQuery->get()->getResultArray();
        
        // Get detail results
        $detailResults = [];
        foreach ($hasilList as $hasil) {
            $details = $db->table('detail_hasil_penilaian dhp')
                ->select('dhp.*, ap.nama_aspek')
                ->join('aspek_penilaian ap', 'ap.id = dhp.aspek_penilaian_id', 'left')
                ->where('dhp.hasil_supervisi_id', $hasil['id'])
                ->get()
                ->getResultArray();
                
            $detailResults[$hasil['jenis_penilaian_id']] = $details;
        }
        
        // Get photo evidence
        $fotoBukti = $db->table('foto_bukti')
            ->where('jadwal_supervisi_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];
        
        $html = view('kepala/laporan/pdf_detail_hasil_supervisi', $data);
        
        // Setup dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output the PDF
        $dompdf->stream('detail-hasil-supervisi-'.$id.'.pdf', ['Attachment' => 0]);
    }
}