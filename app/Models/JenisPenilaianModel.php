<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisPenilaianModel extends Model
{
    protected $table = 'jenis_penilaian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'skor_maksimal', 'kategori_skor', 'created_at'];
    protected $useTimestamps = false;
    
    // Method to get data with mapped column names for our application
    public function findAllWithMappedColumns()
    {
        $results = $this->findAll();
        return $this->mapColumns($results);
    }
    
    public function findWithMappedColumns($id)
    {
        $result = $this->find($id);
        return $this->mapSingleColumn($result);
    }
    
    private function mapColumns($results)
    {
        if (is_array($results)) {
            foreach ($results as &$row) {
                $row = $this->mapSingleColumn($row);
            }
        }
        return $results;
    }
    
    private function mapSingleColumn($row)
    {
        if (is_array($row)) {
            // Map database column names to expected names
            if (isset($row['nama'])) {
                $row['nama_jenis'] = $row['nama'];
                unset($row['nama']);
            }
            
            // Add description based on score category
            if (isset($row['kategori_skor'])) {
                switch ($row['kategori_skor']) {
                    case 'numeric':
                        $row['deskripsi'] = 'Skor Numerik Maksimal: ' . ($row['skor_maksimal'] ?? 'N/A');
                        break;
                    case 'range':
                        $row['deskripsi'] = 'Rentang Skor: 0 - ' . ($row['skor_maksimal'] ?? 'N/A');
                        break;
                    case 'binary':
                        $row['deskripsi'] = 'Skor Ya/Tidak: 0 atau ' . ($row['skor_maksimal'] ?? 'N/A');
                        break;
                    default:
                        $row['deskripsi'] = 'Skor Maksimal: ' . ($row['skor_maksimal'] ?? 'N/A');
                }
            }
            
            // Add human-readable category name
            if (isset($row['kategori_skor'])) {
                $row['kategori_skor_label'] = ucfirst($row['kategori_skor']);
            }
        }
        return $row;
    }
}