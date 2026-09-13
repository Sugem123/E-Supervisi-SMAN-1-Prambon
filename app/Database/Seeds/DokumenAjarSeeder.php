<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DokumenAjarSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('dokumen_ajar')->truncate();
        $sql = <<<'EOT'
INSERT INTO `dokumen_ajar` (`id`, `jadwal_id`, `nama_dokumen`, `link_drive`, `keterangan`, `status`, `feedback`, `created_at`, `updated_at`) VALUES
(1, 6, 'Mdul Ajar test', 'https://drive.google.com/file/d/1hkbe_GrLfbMqePfvX9TcShtPBudf-s2X/view?usp=drive_link', 'modul ajar kelas test', 'Disetujui', '', '2025-12-07 09:49:41', '2025-12-07 10:16:21');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
