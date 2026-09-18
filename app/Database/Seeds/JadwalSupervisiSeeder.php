<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JadwalSupervisiSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('jadwal_supervisi')->truncate();
        $sql = <<<'EOT'
INSERT INTO `jadwal_supervisi` (`id`, `tahun_ajar_id`, `guru_id`, `supervisor_id`, `mata_pelajaran`, `mapel_id`, `kelas`, `jam_ke`, `hari`, `tanggal_supervisi`, `status`, `created_at`, `kelas_id`, `waktu_dari`, `waktu_sampai`, `materi_supervisi`) VALUES
(3, 6, 17, 10, 'Sosiologi', 18, 'XII IPA 2', '1', 'Kamis', '2025-05-15', 'Selesai', NULL, 28, '08:00:00', '09:00:00', 'Stratifikasi sosial'),
(5, 6, 30, 8, 'Administrasi Perpustakaan', NULL, 'XI Bahasa 1', '1', 'Sabtu', '2025-11-01', 'Selesai', NULL, 26, NULL, NULL, NULL),
(6, 6, 11, 7, 'Matematika', 4, 'XII IPA 3', '3', 'Rabu', '2025-04-16', 'Selesai', NULL, 34, '08:14:00', '08:45:00', 'Fungsi kuadrat'),
(7, 6, 10, 7, 'Seni dan Budaya', 7, 'XII IPA 2', '7', 'Senin', '2025-10-13', 'Selesai', NULL, 28, '10:00:00', '11:00:00', 'Apresiasi seni rupa'),
(8, 6, 9, 7, 'Bahasa Indonesia', 3, 'X-4', '1', 'Rabu', '2025-05-14', 'Selesai', NULL, 32, '07:30:00', '08:30:00', 'Teks eksposisi');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
