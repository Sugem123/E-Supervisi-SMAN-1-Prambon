<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JenisPenilaianSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('jenis_penilaian')->truncate();
        $sql = <<<'EOT'
INSERT INTO `jenis_penilaian` (`id`, `nama`, `skor_maksimal`, `kategori_skor`, `created_at`) VALUES
(1, 'SUPERVISI ADMINISTRASI GURU (PERENCANAAN PEMBELAJARAN)', 100, '{\"86-100\":\"Baik Sekali\",\"70-85\":\"Baik\",\"55-69\":\"Cukup\",\"0-54\":\"Kurang\"}', '2025-10-28 09:40:18'),
(2, 'SUPERVISI PROSES PEMBELAJARAN (PELAKSANAAN PEMBELAJARAN)', 100, '{\"86-100\":\"Baik Sekali\",\"70-85\":\"Baik\",\"55-69\":\"Cukup\",\"0-54\":\"Kurang\"}', '2025-10-28 09:40:18'),
(3, 'SUPERVISI EVALUASI PEMBELAJARAN (PENILAIAN PEMBELAJARAN)', 100, '{\"86-100\":\"Baik Sekali\",\"70-85\":\"Baik\",\"55-69\":\"Cukup\",\"0-54\":\"Kurang\"}', '2025-10-28 09:40:18'),
(4, 'SUPERVISI PENGEMBANGAN DIRI GURU', 100, '{\"86-100\":\"Baik Sekali\",\"70-85\":\"Baik\",\"55-69\":\"Cukup\",\"0-54\":\"Kurang\"}', '2025-10-28 09:40:18');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
