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
INSERT INTO `jadwal_supervisi` (`id`, `tahun_ajar_id`, `guru_id`, `supervisor_id`, `mata_pelajaran`, `kelas`, `jam_ke`, `hari`, `tanggal_supervisi`, `status`, `created_at`, `kelas_id`, `waktu_dari`, `waktu_sampai`, `materi_supervisi`) VALUES
(3, 6, 17, 10, 'Pendidikan Pancasila', '4A', '1', 'Kamis', '2025-05-15', 'Selesai', NULL, 28, '08:00:00', '09:00:00', 'benika tungal ika 2'),
(5, 6, 30, 8, 'Matematika', '3B', '1', 'Sabtu', '2025-11-01', 'Selesai', NULL, 26, NULL, NULL, NULL),
(6, 6, 11, 7, 'IPAS', '6A', '3', 'Rabu', '2025-04-16', 'Selesai', NULL, 34, '08:14:00', '08:45:00', 'Penggunaan alat listrik secara aman dan benar'),
(7, 6, 10, 7, 'Sejarah Kebudayaan Islam', '4A', '7', 'Senin', '2025-10-13', 'Selesai', NULL, 28, '10:00:00', '11:00:00', 'Hijrahnya Nabi Muhammad SAW'),
(8, 6, 9, 7, 'Bahasa Indonesia', '5B', '1', 'Rabu', '2025-05-14', 'Selesai', NULL, 32, '07:30:00', '08:30:00', 'Pariwisata');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
