<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('system_settings')->truncate();
        $sql = <<<'EOT'
INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'nama_madrasah', 'SMA NEGERI 1 CONTOH', NULL, '2025-11-02 03:09:03', '2025-12-07 03:36:10'),
(18, 'nama_sekolah', 'SMA NEGERI 1 CONTOH', NULL, '2025-11-02 03:09:03', '2025-12-07 03:36:10'),
(2, 'alamat', 'Jl. Lapangan Ampera No. 109, Desa Purwodadi, Kec. Gisting, Kab. Tanggamus', NULL, '2025-11-02 03:09:03', '2025-12-07 03:36:10'),
(3, 'telepon', '(021) 12345678', NULL, '2025-11-02 03:09:03', '2025-11-30 14:19:48'),
(4, 'email', 'user1@example.com', NULL, '2025-11-02 03:09:03', '2025-11-30 14:19:48'),
(6, 'kop_surat', '', 'Kop surat untuk dokumen resmi', '2025-11-02 03:09:03', '2025-11-02 03:09:03'),
(8, 'sidebar_logo', '1762052970_572d0ca9232ec2e9ec4d.png', NULL, '2025-11-02 03:09:30', '2025-11-02 03:09:30'),
(10, 'nama_kepala', 'Drs. Kepala Sekolah, M.Pd', NULL, '2025-11-27 17:29:42', '2025-11-30 14:19:48'),
(11, 'nip_kepala', '197005272007011022', NULL, '2025-11-28 06:14:52', '2025-11-30 14:19:48'),
(13, 'npsn', '60705691', NULL, '2025-12-07 03:36:10', '2025-12-07 03:36:10'),
(14, 'kecamatan', 'GISTING', NULL, '2025-12-07 03:36:10', '2025-12-07 03:36:10'),
(15, 'kabupaten', 'KABUPATEN TANGGAMUS', NULL, '2025-12-07 03:36:10', '2025-12-07 03:36:10'),
(16, 'provinsi', 'LAMPUNG', NULL, '2025-12-07 03:36:10', '2025-12-07 03:36:10'),
(17, 'logo', '1765078703_11bc06297eb0470138fd.png', NULL, '2025-12-07 03:38:24', '2025-12-07 03:38:24');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
