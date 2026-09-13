<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupervisorSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('supervisor')->truncate();
        $sql = <<<'EOT'
INSERT INTO `supervisor` (`id`, `user_id`, `tanggal_penugasan`, `status`, `created_at`) VALUES
(6, 8, '2025-10-29', 'Aktif', NULL),
(7, 10, '2025-10-29', 'Aktif', NULL);
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
