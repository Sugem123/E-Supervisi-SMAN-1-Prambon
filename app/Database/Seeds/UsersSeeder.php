<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('users')->truncate();
        $sql = <<<'EOT'
INSERT INTO `users` (`id`, `username`, `password`, `email`, `nip`, `foto_profil`, `telepon`, `alamat`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$0IawOIIndd/pBikLSQpg.uX5qlIo8w/akQuMM34HwdZr0W8zOgodW', 'user1@example.com', NULL, '1762676928_885ed3a087262d4d05ad.jpeg', NULL, NULL, 'admin', 'Aktif', '2025-12-14 06:09:59', '2025-10-28 09:39:16', NULL),
(7, 'Sipulloh, M.Pd', '$2y$10$GJYJ8Q5fV7n4FiswqRDXmOEsCH2nbzvyO8KIIWZI.NqI51emZeokO', 'sipulloh@min2tanggamus.sch.id', '197005272007011022', '1764511899_a980b9b92bb3a0f96807.png', NULL, NULL, 'kepala', 'Aktif', '2025-12-12 03:26:33', NULL, NULL),
(8, 'Samarudin', '$2y$10$SrcBhwICxF/JUHCFAQHBBu7jK7.qfNev3uvrhSoseHRF/DD7B9ukG', 'samarudin@min2tanggamus.sch.id', '196701022005011002', NULL, NULL, NULL, 'supervisor', 'Aktif', '2025-11-01 16:08:50', NULL, NULL),
(9, 'Ridwan', '$2y$10$VHZM0UccrP9PaXBIGgROpO0iCjHoL8N9JPvE7DuCLOJHzNE1Whkt2', 'ridwan@min2tanggamus.sch.id', '196707232003121002', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(10, 'Muhammad Nur Syafi\'i,S.Pd.SD', '$2y$10$RjcQE/nnZLVXnPLTLvH6j.YaipJhDRpM8ZjOyGur1YUD6K3TjGOYG', 'nursyafi\'i@min2tanggamus.sch.id', '196703072005011005', NULL, NULL, NULL, 'supervisor', 'Aktif', '2025-12-07 09:27:36', NULL, NULL),
(11, 'Dian_Suherman', '$2y$10$BtBWajHiTRLx.2bnrCkMDOBPEkOl4DhqTKlYj7E/TXmF5PyynMKVW', 'dian@min2tanggamus.sch.id', '198104202007101002', '1764512037_2e064a53a813d0cc6fcc.png', NULL, NULL, 'guru', 'Aktif', '2025-11-30 14:12:26', NULL, NULL),
(12, 'Ismangil', '$2y$10$jeq9KLoPgor6aWiZrHc.EOUicp.ZPZoAyp0q6Mx8xJ6f1nRrnDjHC', 'ismangil@min2tanggamus.sch.id', '197702022007101005', NULL, NULL, NULL, 'guru', 'Aktif', '2025-12-07 03:17:02', NULL, NULL),
(13, 'Sri_Hadna', '$2y$10$XpZXXVkVNxNGK9Aqhfui/.0PVm7AeB837fy/oiFlTaTOM3y1z0pkC', 'sri@min2tanggamus.sch.id', '196908212007012029', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(14, 'Sa\'diyah', '$2y$10$d6LY5Le7BirXGTYGzJ9bdOyCtSpoG.MoXkZYydQk2J2NL7CrLDu2G', 'sadiyah@min2tanggamus.sch.id', '196605082007012029', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(15, 'Neti_Herawati', '$2y$10$waN2UekdHZPrXaGEIKoq6O/0RrUGe6bDURlRDDalFKMrVR4ITwcZq', 'neti@min2tanggamus.sch.id', '196908102007012053', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(16, 'Misri_Kurniati', '$2y$10$Y9fpDwu1FfIOYeMVImlwYex1lUiWSh.q5pgJnZRTpDYT6LIWqqO3W', 'misri@min2tanggamus.sch.id', '197703232007102004', NULL, NULL, NULL, 'guru', 'Aktif', '2025-10-29 01:46:42', NULL, NULL),
(17, 'Ariyani', '$2y$10$wn4nFvH75pA6PEfXa/dPKugXQim7Z.nMnjn4xrFI8XLct.rGPQPoe', 'ariyani@min2tanggamus.sch.id', '198801152019032011', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(18, 'Aan_Rozanah_S', '$2y$10$d4qwKrea51/QNIqbAn5HfOMNCh0K5S03/7RjfIzAuapOQitkuNcfm', 'aan@min2tanggamus.sch.id', '198405032023212030', '1762054754_8c164b7204b45c4b4143.png', '', '', 'guru', 'Aktif', '2025-12-07 09:39:15', NULL, NULL),
(19, 'Napiah', '$2y$10$4qR30CTYimpb60m4fcDWn.rd0ISNbV0s1oCT2cHWWafci09rm2Llu', 'napiah@min2tanggamus.sch.id', '197009162023212005', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(20, 'Dwi_Putri_Ayu_Andari', '$2y$10$r4EJwfHSPdZewRZDK8C8r.PS76VrypsWkURergTzcQxU800ru1h3W', 'dwi@min2tanggamus.sch.id', '199305312023212036', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(21, 'Fitria_Sani', '$2y$10$BHdMuVnGOGlxbPkZdz8miOA7Fz2fg8pkAq/WwXrfjw8K.wVFvwPAu', 'fitria@min2tanggamus.sch.id', '199304112023212042', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(22, 'DEVI_APRIANI.S.Pd', '$2y$10$QSbgL389rCrBAx6SSKlRdOvi/7/wHDVXY4uTczB1IpUuAwBRPMigm', 'devi@min2tanggamus.sch.id', '199304052023212046', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(23, 'Helmaini', '$2y$10$WQBNiZKBEeoim/OtYFBzluSEGBhAIefflbExi1ORKrcglMN4OPf1C', 'helmaini@min2tanggamus.sch.id', '196802122025212001', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(24, 'Rosa_linda', '$2y$10$Ckhh4WDkIl7TM4VV6T4ZPOP/A.DV1dztw2Oqpmck7dxkas8Ezsk1a', 'rosa@min2tanggamus.sch.id', '199706122025212001', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(25, 'Angita_Eka_Rostianti', '$2y$10$8znpprYJ.y9cFuqKQmbRLeqs3vviNxJ481eVjIfpFIHwwFJEKV3de', 'angita@min2tanggamus.sch.id', '199512052025212008', NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(26, 'Zulkarnain', '$2y$10$qQ.Emmnqs4T7BkGX39KEmuS2O2bgvkbzMX41c4YJbg3bQL2ZjhqpG', 'zulkarnain@min2tanggamus.sch.id', NULL, NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(27, 'Winda_Fitriana', '$2y$10$uD8uEQxu3iaC1dRARHM1mOGiiKGwhvQu8XshE2/DSgLITzmr5zpSK', 'winda@min2tanggamus.sch.id', NULL, NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(28, 'Septia_Hasanah', '$2y$10$ywEL5ruHZgMlBoFOMOW2/eKO7s7qrri5is7izSjJ4ylDhK9ob2cCa', 'septia@min2tanggamus.sch.id', NULL, NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(29, 'Syifa_Khulwiyah', '$2y$10$gRumUk1p5va3nJZkQE9jIeHV0N4RfNb2aM4/yWFXcIcIGR09MG5/S', 'syifa@min2tanggamus.sch.id', NULL, NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(30, 'Luthvia_Rohmaini', '$2y$10$FMmIP5U5BkqRNfTx/t9YyOMGQYiM2ItSeXZAWBFQZBOJYYs1H57Sa', 'luthvia@min2tanggamus.sch.id', NULL, NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(31, 'Suci_Maharani', '$2y$10$NZT6I7Un/MDRUZnCW6eHVupLX2CYx94hh6bvdyQnHn2JBRhPlv8kC', 'suci@min2tanggamus.sch.id', NULL, NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL),
(32, 'Anita', '$2y$10$JiLwgoWXAI8hii/324vzbOqJfU0OXxy23iWrHcFrzc9lYYjlMM62a', 'anita@min2tanggamus.sch.id', NULL, NULL, NULL, NULL, 'guru', 'Aktif', NULL, NULL, NULL);
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
