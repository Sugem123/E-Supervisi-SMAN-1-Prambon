<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FotoBuktiSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('foto_bukti')->truncate();
        $sql = <<<'EOT'
INSERT INTO `foto_bukti` (`id`, `jadwal_supervisi_id`, `file_path`, `keterangan`, `created_at`) VALUES
(8, 6, 'uploads/foto_bukti/guru_11/1764309284_26d6d86a5407eb017c6d.jpeg', '', '2025-11-28 12:54:44'),
(10, 6, 'uploads/foto_bukti/guru_11/1764309306_ab11cc02e7e861fca7e9.jpeg', '', '2025-11-28 12:55:06'),
(11, 3, 'uploads/foto_bukti/guru_17/1764314255_cdc966d96b62dcf7d42b.jpeg', '', '2025-11-28 14:17:36'),
(12, 3, 'uploads/foto_bukti/guru_17/1764314256_fcbe5172d79309268b6b.jpeg', '', '2025-11-28 14:17:36'),
(13, 7, 'uploads/foto_bukti/guru_10/1764511394_01fa6ccb0f6f56547f97.jpeg', '', '2025-11-30 21:03:14'),
(14, 7, 'uploads/foto_bukti/guru_10/1764511394_ec15496d4ddfe765d5f4.jpeg', '', '2025-11-30 21:03:14'),
(15, 8, 'uploads/foto_bukti/guru_9/1764981903_f09f8be400bf46533ee9.jpeg', '', '2025-12-06 07:45:03'),
(16, 8, 'uploads/foto_bukti/guru_9/1764981904_a7b691fe0b63f1846039.jpeg', '', '2025-12-06 07:45:04');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
