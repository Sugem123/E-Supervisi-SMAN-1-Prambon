<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\HasilSupervisiModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\FotoBuktiModel;
use Dompdf\Dompdf;

class HasilController extends BaseController
{
    protected $jadwalModel;
    protected $hasilModel;
    protected $detailModel;
    protected $fotoModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->hasilModel = new HasilSupervisiModel();
        $this->detailModel = new DetailHasilPenilaianModel();
        $this->fotoModel = new FotoBuktiModel();
    }

    public function index()
    {
        $supervisorId = session()->get('id');
        
        // Get all completed schedules for this supervisor
        $jadwalSelesai = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();

        // For each schedule, calculate nilai_akhir and ketercapaian
        foreach ($jadwalSelesai as &$jadwal) {
            $hasilList = $this->hasilModel
                ->where('jadwal_supervisi_id', $jadwal['id'])
                ->findAll();

            if (!empty($hasilList)) {
                $totalSkor = 0;
                $jumlahAspek = 0;

                foreach ($hasilList as $hasil) {
                    $details = $this->detailModel
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->findAll();

                    foreach ($details as $detail) {
                        $totalSkor += $detail['skor'];
                        $jumlahAspek++;
                    }
                }

                $nilaiAkhir = $jumlahAspek > 0 ? ($totalSkor / ($jumlahAspek * 4)) * 100 : 0;

                $ketercapaian = match (true) {
                    $nilaiAkhir >= 86 => 'Baik Sekali',
                    $nilaiAkhir >= 70 => 'Baik',
                    $nilaiAkhir >= 55 => 'Cukup',
                    default => 'Kurang',
                };

                $jadwal['nilai_akhir'] = round($nilaiAkhir, 2);
                $jadwal['ketercapaian'] = $ketercapaian;
            } else {
                $jadwal['nilai_akhir'] = null;
                $jadwal['ketercapaian'] = null;
            }
        }

        $data = [
            'jadwalSelesai' => $jadwalSelesai
        ];

        return view('supervisor/hasil/index', $data);
    }

    public function detail($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Get schedule details
        // Mengambil mata_pelajaran dari tabel jadwal_supervisi, bukan dari tabel guru
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.pangkat_golongan as pangkat_golongan, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil penilaian tidak ditemukan');
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

        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];

        return view('supervisor/hasil/detail', $data);
    }

    public function cetak($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Get schedule details
        // Mengambil mata_pelajaran dari tabel jadwal_supervisi, bukan dari tabel guru
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.pangkat_golongan as pangkat_golongan, users.username as nama_supervisor')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil penilaian tidak ditemukan');
        }

        // Get all assessment results for this schedule
        $hasilList = $this->hasilModel
            ->select('hasil_supervisi.*, jenis_penilaian.nama as nama_jenis')
            ->join('jenis_penilaian', 'jenis_penilaian.id = hasil_supervisi.jenis_penilaian_id')
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();
            
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

        $data = [
            'schedule' => $schedule,
            'hasilList' => $hasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti
        ];

        return view('supervisor/hasil/cetak', $data);
    }
}