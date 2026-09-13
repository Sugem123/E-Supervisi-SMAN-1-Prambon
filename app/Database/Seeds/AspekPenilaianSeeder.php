<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AspekPenilaianSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('aspek_penilaian')->truncate();
        $sql = <<<'EOT'
INSERT INTO `aspek_penilaian` (`id`, `jenis_penilaian_id`, `nama_aspek`, `urutan`, `created_at`) VALUES
(1, 1, 'Kalender Pendidikan', 1, '2025-10-28 09:40:31'),
(2, 1, 'Program Tahunan', 2, '2025-10-28 09:40:31'),
(3, 1, 'Program Semester', 3, '2025-10-28 09:40:31'),
(4, 1, 'Silabus', 4, '2025-10-28 09:40:31'),
(5, 1, 'Modul Ajar', 5, '2025-10-28 09:40:31'),
(6, 1, 'Jadwal Tatap Muka', 6, '2025-10-28 09:40:31'),
(7, 1, 'Agenda Harian', 7, '2025-10-28 09:40:31'),
(8, 1, 'Daftar Nilai', 8, '2025-10-28 09:40:31'),
(9, 1, 'KKM', 9, '2025-10-28 09:40:31'),
(10, 1, 'Absensi Peserta Didik', 10, '2025-10-28 09:40:31'),
(11, 1, 'Buku Pegangan Guru', 11, '2025-10-28 09:40:31'),
(12, 1, 'Buku Teks Siswa', 12, '2025-10-28 09:40:31'),
(13, 2, 'Pendahuluan', 1, '2025-10-28 09:40:31'),
(14, 2, 'Kegiatan Inti Eksplorasi', 2, '2025-10-28 09:40:31'),
(15, 2, 'Kegiatan Inti Elaborasi', 3, '2025-10-28 09:40:31'),
(16, 2, 'Kegiatan Inti Konfirmasi', 4, '2025-10-28 09:40:31'),
(17, 2, 'Penutup', 5, '2025-10-28 09:40:31'),
(18, 2, 'Metode Pembelajaran', 6, '2025-10-28 09:40:31'),
(19, 2, 'Media Pembelajaran', 7, '2025-10-28 09:40:31'),
(20, 2, 'Sumber Belajar', 8, '2025-10-28 09:40:31'),
(21, 2, 'Evaluasi', 9, '2025-10-28 09:40:31'),
(22, 2, 'Karakteristik Siswa', 10, '2025-10-28 09:40:31'),
(23, 2, 'Interaksi Guru-Murid', 11, '2025-10-28 09:40:31'),
(24, 2, 'Penggunaan Waktu', 12, '2025-10-28 09:40:31'),
(25, 3, 'Prinsip Penilaian', 1, '2025-10-28 09:40:31'),
(26, 3, 'Teknik Penilaian', 2, '2025-10-28 09:40:31'),
(27, 3, 'Bentuk Instrumen', 3, '2025-10-28 09:40:31'),
(28, 3, 'Butir Soal', 4, '2025-10-28 09:40:31'),
(29, 3, 'Analisis Butir Soal', 5, '2025-10-28 09:40:31'),
(30, 3, 'Analisis Hasil Penilaian', 6, '2025-10-28 09:40:31'),
(31, 3, 'Tindak Lanjut Hasil Penilaian', 7, '2025-10-28 09:40:31'),
(32, 3, 'Pelaporan Hasil Penilaian', 8, '2025-10-28 09:40:31'),
(33, 3, 'Manfaat Hasil Penilaian', 9, '2025-10-28 09:40:31'),
(34, 3, 'Keterlibatan Peserta Didik', 10, '2025-10-28 09:40:31'),
(35, 3, 'Portofolio', 11, '2025-10-28 09:40:31'),
(36, 3, 'Karya Siswa', 12, '2025-10-28 09:40:31'),
(37, 4, 'Mengikuti Pelatihan', 1, '2025-10-28 09:40:31'),
(38, 4, 'Mengikuti Workshop', 2, '2025-10-28 09:40:31'),
(39, 4, 'Melaksanakan Penelitian Tindakan Kelas', 3, '2025-10-28 09:40:31'),
(40, 4, 'Menyusun Karya Tulis Ilmiah', 4, '2025-10-28 09:40:31'),
(41, 4, 'Melaksanakan Pengabdian Kepada Masyarakat', 5, '2025-10-28 09:40:31'),
(42, 4, 'Mengikuti Organisasi Profesi', 6, '2025-10-28 09:40:31'),
(43, 4, 'Mengembangkan Bahan Ajar', 7, '2025-10-28 09:40:31'),
(44, 4, 'Melaksanakan Bimbingan Konseling', 8, '2025-10-28 09:40:31'),
(45, 4, 'Melaksanakan Bimbingan Penelitian', 9, '2025-10-28 09:40:31'),
(46, 4, 'Melaksanakan Bimbingan Akademik', 10, '2025-10-28 09:40:31'),
(47, 4, 'Melaksanakan Tugas Tambahan', 11, '2025-10-28 09:40:31'),
(48, 4, 'Meningkatkan Kualifikasi Pendidikan', 12, '2025-10-28 09:40:31');
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
