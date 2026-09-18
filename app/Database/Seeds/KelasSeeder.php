<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('kelas')->truncate();

        // 30 rombel baku: X-1 s.d. X-10, XI-1 s.d. XI-10, XII-1 s.d. XII-10.
        // Wali kelas dibiarkan NULL agar admin menetapkan manual via halaman Kelas.
        $rows = [];
        $id = 1;
        foreach (['X' => 10, 'XI' => 10, 'XII' => 10] as $tingkat => $jumlah) {
            for ($i = 1; $i <= $jumlah; $i++) {
                $rows[] = sprintf(
                    "(%d, 6, '%s-%d', '%s', 'Umum', NULL, 'Aktif')",
                    $id++, $tingkat, $i, $tingkat
                );
            }
        }
        $this->db->query('INSERT INTO `kelas` (`id`, `tahun_ajar_id`, `nama_kelas`, `tingkat`, `jurusan`, `wali_kelas`, `status`) VALUES ' . implode(',', $rows));
        $this->db->enableForeignKeyChecks();
    }
}
