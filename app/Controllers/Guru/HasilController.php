<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\HasilSupervisiModel;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;
use App\Models\DetailHasilPenilaianModel;
use App\Models\FotoBuktiModel;
use App\Models\TahunAjarModel;

class HasilController extends BaseController
{
    protected $hasilModel;
    protected $jadwalModel;
    protected $guruModel;
    protected $detailHasilModel;
    protected $fotoBuktiModel;
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->hasilModel = new HasilSupervisiModel();
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->guruModel = new GuruModel();
        $this->detailHasilModel = new DetailHasilPenilaianModel();
        $this->fotoBuktiModel = new FotoBuktiModel();
        $this->tahunAjarModel = new TahunAjarModel();
    }

    public function index()
    {
        $userId = session()->get('id');
        
        // Get guru data by user ID
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }
        
        // Get jadwal where guru_id matches and status is Selesai
        $jadwal = $this->jadwalModel
            ->select('jadwal_supervisi.*, users.username as supervisor_name, tahun_ajar.tahun_ajar as tahun_ajaran, tahun_ajar.semester, kelas.nama_kelas')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->where('jadwal_supervisi.guru_id', $guru['id'])
            ->where('jadwal_supervisi.status', 'Selesai')
            ->orderBy('jadwal_supervisi.tanggal_supervisi', 'DESC')
            ->findAll();
            
        // For each jadwal, get the average nilai_akhir and determine ketercapaian
        foreach ($jadwal as &$item) {
            // Get all hasil records for this jadwal
            $hasilRecords = $this->hasilModel
                ->where('jadwal_supervisi_id', $item['id'])
                ->findAll();
                
            if (!empty($hasilRecords)) {
                // Calculate average nilai_akhir by dynamically computing each record's nilai
                $totalNilai = 0;
                $countNilai = 0;
                
                foreach ($hasilRecords as $hasil) {
                    // Get details for this hasil to calculate the actual nilai_akhir
                    $details = $this->detailHasilModel
                        ->where('hasil_supervisi_id', $hasil['id'])
                        ->findAll();
                    
                    if (!empty($details)) {
                        // Calculate nilai_akhir for this jenis penilaian
                        $totalSkor = 0;
                        foreach ($details as $detail) {
                            $totalSkor += $detail['skor'];
                        }
                        
                        // Calculate percentage (maximum score per aspect is 4)
                        $maxScore = count($details) * 4;
                        $nilaiAkhir = $maxScore > 0 ? ($totalSkor / $maxScore) * 100 : 0;
                        
                        $totalNilai += $nilaiAkhir;
                        $countNilai++;
                    }
                }
                
                if ($countNilai > 0) {
                    $item['nilai_akhir'] = $totalNilai / $countNilai;
                    
                    // Determine ketercapaian based on average nilai
                    $avgNilai = $item['nilai_akhir'];
                    if ($avgNilai >= 86) {
                        $item['ketercapaian'] = 'Baik Sekali';
                    } elseif ($avgNilai >= 70) {
                        $item['ketercapaian'] = 'Baik';
                    } elseif ($avgNilai >= 55) {
                        $item['ketercapaian'] = 'Cukup';
                    } else {
                        $item['ketercapaian'] = 'Kurang';
                    }
                } else {
                    $item['nilai_akhir'] = null;
                    $item['ketercapaian'] = null;
                }
            } else {
                $item['nilai_akhir'] = null;
                $item['ketercapaian'] = null;
            }
        }
        
        // Calculate overall stats for header cards
        $totalSupervisi = count($jadwal);
        $totalSemuaNilai = 0;
        $supervisiDinilai = 0;
        foreach ($jadwal as $item) {
            if (!is_null($item['nilai_akhir'])) {
                $totalSemuaNilai += $item['nilai_akhir'];
                $supervisiDinilai++;
            }
        }
        $rataRataNilai = $supervisiDinilai > 0 ? ($totalSemuaNilai / $supervisiDinilai) : 0;
        
        $predikatUmum = '-';
        if ($supervisiDinilai > 0) {
            if ($rataRataNilai >= 86) {
                $predikatUmum = 'Baik Sekali';
            } elseif ($rataRataNilai >= 70) {
                $predikatUmum = 'Baik';
            } elseif ($rataRataNilai >= 55) {
                $predikatUmum = 'Cukup';
            } else {
                $predikatUmum = 'Kurang';
            }
        }

        $stats = [
            'total_supervisi' => $totalSupervisi,
            'rata_rata_nilai' => $rataRataNilai,
            'predikat_umum' => $predikatUmum,
            'supervisi_dinilai' => $supervisiDinilai
        ];

        // Get active tahun ajaran
        $activeTahunAjar = $this->tahunAjarModel->where('status_aktif', 'Aktif')->first();
        
        $data = [
            'hasil' => $jadwal,
            'guru' => $guru,
            'stats' => $stats,
            'activeTahunAjar' => $activeTahunAjar
        ];

        return view('guru/hasil/index', $data);
    }

    public function detail($jadwalId)
    {
        $userId = session()->get('id');
        
        // Get guru data by user ID
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data guru tidak ditemukan');
        }
        
        // Get schedule details with joins to verify it exists and belongs to this guru
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, kelas.nama_kelas, tahun_ajar.tahun_ajar, tahun_ajar.semester, users.username as supervisor_name')
            ->join('kelas', 'kelas.id = jadwal_supervisi.kelas_id', 'left')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.guru_id', $guru['id'])
            ->where('jadwal_supervisi.status', 'Selesai')
            ->first();
            
        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }
        
        // Get all hasil supervisi for this jadwal
        $hasilList = $this->hasilModel
            ->select('hasil_supervisi.*, jadwal_supervisi.tanggal_supervisi, jadwal_supervisi.jam_ke, users.username as supervisor_name')
            ->join('jadwal_supervisi', 'jadwal_supervisi.id = hasil_supervisi.jadwal_supervisi_id')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->findAll();
        
        if (empty($hasilList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil supervisi tidak ditemukan');
        }
        
        // Get foto bukti for this jadwal
        $fotoBukti = $this->fotoBuktiModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();
        
        // Get detail hasil penilaian for each hasil and calculate nilai_akhir and ketercapaian dynamically
        $detailResults = [];
        $processedHasilList = [];
        
        foreach ($hasilList as $hasil) {
            $details = $this->detailHasilModel
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
            
            // Calculate percentage (maximum score per aspect is 4)
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
            
            $processedHasilList[] = $hasil;
        }
        
        $data = [
            'schedule' => $schedule,
            'hasilList' => $processedHasilList,
            'detailResults' => $detailResults,
            'fotoBukti' => $fotoBukti,
            'guru' => $guru
        ];

        return view('guru/hasil/detail', $data);
    }
}