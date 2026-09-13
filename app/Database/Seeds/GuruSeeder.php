<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('guru')->truncate();
        $sql = <<<'EOT'
INSERT INTO `guru` (`id`, `user_id`, `foto_profil`, `telepon`, `alamat`, `nama`, `nip`, `pangkat_golongan`, `mata_pelajaran`, `status_kepegawaian`, `is_supervisor`, `created_at`) VALUES
(6, 7, NULL, NULL, NULL, 'Sipulloh, M.Pd', '197005272007011022', 'Penata Muda / III d', 'Kamad', 'PNS', 0, NULL),
(7, 8, NULL, NULL, NULL, 'Samarudin, S.Pd.I', '196701022005011002', 'Penata Muda / III d', 'Guru Al-Qur\'an Hadist', 'PNS', 0, NULL),
(8, 9, NULL, NULL, NULL, 'Ridwan, S.Pd.I', '196707232003121002', 'Penata Muda / III d', 'Guru Fiqih', 'PNS', 0, NULL),
(9, 10, NULL, NULL, NULL, 'Muhammad Nur Syafi\'i,S.Pd.SD', '196703072005011005', 'Penata Muda / III d', 'Guru Kelas V.a', 'PNS', 0, NULL),
(10, 11, '1764512037_2e064a53a813d0cc6fcc.png', NULL, NULL, 'Dian Suherman, S.Pd.I', '198104202007101002', 'Penata Muda / III d', 'Guru SKI', 'PNS', 0, NULL),
(11, 12, NULL, NULL, NULL, 'Ismangil, S.Pd.I', '197702022007101005', 'Penata Muda / III d', 'Guru Kelas VI.a', 'PNS', 0, NULL),
(12, 13, NULL, NULL, NULL, 'Sri Hadna, S.Pd.I', '196908212007012029', 'Penata Muda / III d', 'Guru Kelas I.a', 'PNS', 0, NULL),
(13, 14, NULL, NULL, NULL, 'Sa\'diyah, S.Pd.I', '196605082007012029', 'Penata /III c', 'Guru kelas IV.a', 'PNS', 0, NULL),
(14, 15, NULL, NULL, NULL, 'Neti Herawati, S,Pd', '196908102007012053', 'Penata /III c', 'Guru Kelas III.c', 'PNS', 0, NULL),
(15, 16, NULL, NULL, NULL, 'Misri Kurniati, S.Pd.I', '197703232007102004', 'Penata /III c', 'Guru kelas VI.b', 'PNS', 0, NULL),
(16, 17, NULL, NULL, NULL, 'Ariyani, S.Pd.I', '198801152019032011', 'Penata Muda tingkat 1 /III b', 'Guru kelas V.b', 'PNS', 0, NULL),
(17, 18, '1762054754_8c164b7204b45c4b4143.png', '', '', 'Aan Rozanah S, S.Pd.I', '198405032023212030', 'GOL IX', 'Guru kelas IV.a', 'PPPK', 0, NULL),
(18, 19, NULL, NULL, NULL, 'Napiah, S.Pd.I', '197009162023212005', 'GOL IX', 'Guru kelas II.b', 'PPPK', 0, NULL),
(19, 20, NULL, NULL, NULL, 'Dwi Putri Ayu Andari,S.Pd', '199305312023212036', 'GOL IX', 'Guru penjas', 'PPPK', 0, NULL),
(20, 21, NULL, NULL, NULL, 'Fitria Sani, S.Pd.I', '199304112023212042', 'GOL IX', 'Guru Aqidah Ahlak', 'PPPK', 0, NULL),
(21, 22, NULL, NULL, NULL, 'DEVI APRIANI.S.Pd', '199304052023212046', 'GOL IX', 'Guru kelas IVb', 'PPPK', 0, NULL),
(22, 23, NULL, NULL, NULL, 'Helmaini, S.Pd.I', '196802122025212001', 'GOL IX', 'Guru Aqidah Ahlak', 'PPPK', 0, NULL),
(23, 24, NULL, NULL, NULL, 'Rosa linda, S.Pd', '199706122025212001', 'GOL IX', 'Guru kelas I.c', 'PPPK', 0, NULL),
(24, 25, NULL, NULL, NULL, 'Angita Eka Rostianti, S.Pd', '199512052025212008', 'GOL IX', 'Guru Bahasa Inggris', 'PPPK', 0, NULL),
(25, 26, NULL, NULL, NULL, 'Zulkarnain, S.Pd.I', '', '', 'Guru B. Arab', 'Honorer', 0, NULL),
(26, 27, NULL, NULL, NULL, 'Winda Fitriana, S.Pd.I', '', '', 'Guru kelas II.b', 'Honorer', 0, NULL),
(27, 28, NULL, NULL, NULL, 'Septia Hasanah, S.Pd', '', '', 'Guru kelas IV.b', 'Honorer', 0, NULL),
(28, 29, NULL, NULL, NULL, 'Syifa Khulwiyah, S.Pd.I', '', '', 'Guru Kelas 2.c', 'Honorer', 0, NULL),
(29, 30, NULL, NULL, NULL, 'Luthvia Rohmaini, S.Pd', '', '', 'Guru Mulok (Bahasa Lampung)', 'Honorer', 0, NULL),
(30, 31, NULL, NULL, NULL, 'Suci Maharani, S.Pd', '', '', 'Guru kelas III.a', 'Honorer', 0, NULL),
(31, 32, NULL, NULL, NULL, 'Anita', '', '', 'Pembina Tahfidz', 'Honorer', 0, NULL);
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
