<?php

namespace App\Models;

use CodeIgniter\Model;

class AspekPenilaianModel extends Model
{
    protected $table = 'aspek_penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jenis_penilaian_id', 'nama_aspek', 'urutan', 'status', 'created_at'];
    protected $useTimestamps = false;

    /** Aspek Aktif milik jenis Aktif — dipakai form penilaian baru. */
    public function findAllActiveWithJenis()
    {
        $builder = $this->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis')
            ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id');
        try {
            $fields = $this->db->getFieldNames($this->table);
            if (in_array('status', $fields, true)) {
                $builder->where('aspek_penilaian.status', 'Aktif');
            }
            $jenisFields = $this->db->getFieldNames('jenis_penilaian');
            if (in_array('status', $jenisFields, true)) {
                $builder->where('jenis_penilaian.status', 'Aktif');
            }
        } catch (\Throwable $e) {
            // BC: kolom belum ada -> tanpa filter status
        }
        return $builder
            ->orderBy('aspek_penilaian.jenis_penilaian_id', 'ASC')
            ->orderBy('aspek_penilaian.urutan', 'ASC')
            ->findAll();
    }

    public function isActive(int $id): bool
    {
        try {
            $row = $this->find($id);
            if (!$row) {
                return false;
            }
            if (!array_key_exists('status', $row)) {
                return true;
            }
            return ($row['status'] ?? 'Aktif') === 'Aktif';
        } catch (\Throwable $e) {
            return true;
        }
    }
    
    // Ensure we're getting the proper data with joins
    public function getAspekWithJenis()
    {
        return $this->select('aspek_penilaian.*, jenis_penilaian.nama as nama_jenis')
                    ->join('jenis_penilaian', 'jenis_penilaian.id = aspek_penilaian.jenis_penilaian_id')
                    ->findAll();
    }
}