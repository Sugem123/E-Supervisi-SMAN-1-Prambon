<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('kelas')->truncate();
        $sql = <<<'EOT'
INSERT INTO `kelas` (`id`, `tahun_ajar_id`, `nama_kelas`, `wali_kelas`, `status`) VALUES
(19, 6, '1A', 12, 'Aktif'),
(20, 6, '1B', 18, 'Aktif'),
(21, 6, '1C', 23, 'Aktif'),
(22, 6, '2A', 14, 'Aktif'),
(23, 6, '2B', 26, 'Aktif'),
(24, 6, '2C', NULL, 'Aktif'),
(25, 6, '3A', 13, 'Aktif'),
(26, 6, '3B', 30, 'Aktif'),
(27, 6, '3C', NULL, 'Aktif'),
(28, 6, '4A', 17, 'Aktif'),
(29, 6, '4B', 27, 'Aktif'),
(30, 6, '4C', 28, 'Aktif'),
(31, 6, '5A', 16, 'Aktif'),
(32, 6, '5B', 9, 'Aktif'),
(33, 6, '5C', 21, 'Aktif'),
(34, 6, '6A', 11, 'Aktif'),
(35, 6, '6B', 15, 'Aktif'),
(36, 6, '6C', NULL, 'Aktif');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
