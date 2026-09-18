<?php

namespace App\Models;

use CodeIgniter\Model;

class KelompokJenisPenilaianModel extends Model
{
    protected $table = 'kelompok_jenis_penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kelompok_id', 'jenis_penilaian_id', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Ambil array ID jenis penilaian yang ditugaskan ke kelompok tertentu.
     *
     * @param int $kelompokId
     * @return int[]
     */
    public function getJenisIdsByKelompok(int $kelompokId): array
    {
        $rows = $this->select('jenis_penilaian_id')
            ->where('kelompok_id', $kelompokId)
            ->findAll();

        return array_map('intval', array_column($rows, 'jenis_penilaian_id'));
    }

    /**
     * Ambil daftar jenis penilaian lengkap yang ditugaskan ke kelompok (hanya yang Aktif).
     *
     * @param int $kelompokId
     * @return array
     */
    public function getJenisByKelompok(int $kelompokId): array
    {
        $builder = $this->select('jenis_penilaian.*')
            ->join('jenis_penilaian', 'jenis_penilaian.id = kelompok_jenis_penilaian.jenis_penilaian_id')
            ->where('kelompok_jenis_penilaian.kelompok_id', $kelompokId);

        try {
            $fields = $this->db->getFieldNames('jenis_penilaian');
            if (in_array('status', $fields, true)) {
                $builder->where('jenis_penilaian.status', 'Aktif');
            }
        } catch (\Throwable $e) {
        }

        return $builder->orderBy('jenis_penilaian.id', 'ASC')->findAll();
    }

    /**
     * Simpan / sinkronisasi daftar jenis penilaian untuk suatu kelompok.
     *
     * @param int $kelompokId
     * @param int[] $jenisIds
     * @return void
     */
    public function syncJenis(int $kelompokId, array $jenisIds): void
    {
        $this->where('kelompok_id', $kelompokId)->delete();

        $uniqueIds = array_values(array_unique(array_filter(array_map('intval', $jenisIds))));
        if (empty($uniqueIds)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $batch = [];
        foreach ($uniqueIds as $jId) {
            $batch[] = [
                'kelompok_id'        => $kelompokId,
                'jenis_penilaian_id' => $jId,
                'created_at'         => $now,
            ];
        }

        $this->insertBatch($batch);
    }

    /**
     * Salin jenis penilaian dari satu kelompok ke kelompok lain (dipakai saat carry-over).
     *
     * @param int $fromKelompokId
     * @param int $toKelompokId
     * @return int Jumlah jenis penilaian yang disalin
     */
    public function copyJenis(int $fromKelompokId, int $toKelompokId): int
    {
        $ids = $this->getJenisIdsByKelompok($fromKelompokId);
        if (!empty($ids)) {
            $this->syncJenis($toKelompokId, $ids);
            return count($ids);
        }
        return 0;
    }
}
