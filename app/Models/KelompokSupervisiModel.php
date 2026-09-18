<?php

namespace App\Models;

use CodeIgniter\Model;

class KelompokSupervisiModel extends Model
{
    protected $table = 'kelompok_supervisi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_kelompok', 'supervisor_id', 'tahun_ajar_id'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Cari kelompok supervisi yang menaungi jadwal/guru/supervisor tertentu.
     *
     * @param array $schedule Baris jadwal supervisi
     * @return array|null
     */
    public function resolveKelompokForSchedule(array $schedule): ?array
    {
        // 1. Jika jadwal sudah punya kelompok_id langsung
        if (!empty($schedule['kelompok_id'])) {
            $k = $this->find((int) $schedule['kelompok_id']);
            if ($k) {
                return $k;
            }
        }

        $guruId = (int) ($schedule['guru_id'] ?? 0);
        $supervisorId = (int) ($schedule['supervisor_id'] ?? 0);
        $tahunAjarId = (int) ($schedule['tahun_ajar_id'] ?? 0);

        // 2. Cari kelompok lewat anggota guru + supervisor (+ tahun ajar)
        if ($guruId > 0 && $supervisorId > 0) {
            $builder = $this->db->table('kelompok_supervisi ks')
                ->select('ks.*')
                ->join('kelompok_anggota ka', 'ka.kelompok_id = ks.id')
                ->where('ka.guru_id', $guruId)
                ->where('ks.supervisor_id', $supervisorId);

            if ($tahunAjarId > 0) {
                $builder->where('ks.tahun_ajar_id', $tahunAjarId);
            }

            $row = $builder->orderBy('ks.id', 'DESC')->get()->getRowArray();
            if ($row) {
                return $row;
            }
        }

        // 3. Fallback: cari kelompok berdasarkan supervisor dan tahun ajar
        if ($supervisorId > 0) {
            $builder = $this->where('supervisor_id', $supervisorId);
            if ($tahunAjarId > 0) {
                $builder->where('tahun_ajar_id', $tahunAjarId);
            }
            $row = $builder->orderBy('id', 'DESC')->first();
            if ($row) {
                return $row;
            }
        }

        return null;
    }

    /**
     * Ambil jenis penilaian yang ditugaskan ke kelompok (hanya yang Aktif).
     *
     * @param int $kelompokId
     * @return array
     */
    public function getAssignedJenisPenilaian(int $kelompokId): array
    {
        $kjModel = new KelompokJenisPenilaianModel();
        return $kjModel->getJenisByKelompok($kelompokId);
    }

    /**
     * Ambil ID jenis penilaian yang ditugaskan ke kelompok.
     *
     * @param int $kelompokId
     * @return int[]
     */
    public function getAssignedJenisIds(int $kelompokId): array
    {
        $kjModel = new KelompokJenisPenilaianModel();
        return $kjModel->getJenisIdsByKelompok($kelompokId);
    }
}
