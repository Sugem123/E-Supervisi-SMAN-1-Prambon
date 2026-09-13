<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuditLogsSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('audit_logs')->truncate();
        $sql = <<<'EOT'
INSERT INTO `audit_logs` (`id`, `user_id`, `activity_type`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'User admin logged in successfully', '192.168.1.131', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-30 20:45:03'),
(2, 1, 'create', 'User admin created new master data: aspek penilaian', '192.168.1.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-29 05:45:03'),
(3, 1, 'setting', 'User admin updated system settings', '192.168.1.165', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-29 09:45:03'),
(6, 7, 'login', 'User Sipulloh logged in successfully', '192.168.1.181', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-29 05:45:03'),
(7, 7, 'setting', 'User Sipulloh updated system settings', '192.168.1.172', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-29 02:45:03'),
(8, 8, 'login', 'User Samarudin logged in successfully', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-28 06:45:03'),
(9, 8, 'create', 'User Samarudin created new master data: aspek penilaian', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-30 08:45:03'),
(10, 8, 'setting', 'User Samarudin updated system settings', '192.168.1.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-29 19:45:03'),
(11, 9, 'login', 'User Ridwan logged in successfully', '192.168.1.155', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-31 00:45:03'),
(12, 9, 'setting', 'User Ridwan updated system settings', '192.168.1.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-10-30 00:45:03');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
