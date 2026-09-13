<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TahunAjarSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('tahun_ajar')->truncate();
        $sql = <<<'EOT'
INSERT INTO `tahun_ajar` (`id`, `tahun_ajar`, `semester`, `status_aktif`, `created_at`) VALUES
(1, '2024/2025', 'Ganjil', 'Nonaktif', '2025-10-28 09:39:33'),
(2, '2024/2025', 'Genap', 'Nonaktif', '2025-10-28 09:39:33'),
(3, '2025/2026', 'Genap', 'Nonaktif', '2025-10-28 09:39:33'),
(4, '2024/2025', 'Ganjil', 'Nonaktif', '2025-10-28 09:39:33'),
(5, '2024/2025', 'Genap', 'Nonaktif', '2025-10-28 09:39:33'),
(6, '2025/2026', 'Ganjil', 'Aktif', '2025-10-28 09:39:33');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
