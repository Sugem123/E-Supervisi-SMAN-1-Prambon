<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HasilSupervisiSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->table('hasil_supervisi')->truncate();
        $sql = <<<'EOT'
INSERT INTO `hasil_supervisi` (`id`, `jadwal_supervisi_id`, `jenis_penilaian_id`, `perencanaan_skor`, `perencanaan_catatan`, `pelaksanaan_skor`, `pelaksanaan_catatan`, `penilaian_skor`, `penilaian_catatan`, `total_skor`, `nilai_akhir`, `ketercapaian`, `rekomendasi`, `created_at`) VALUES
(11, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bukti dukung sudah sesuai pertahankan ', NULL),
(12, 3, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pertahankan pada SUPERVISI PROSES PEMBELAJARAN sudah baik', NULL),
(13, 3, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pada SUPERVISI EVALUASI PEMBELAJARAN sudah baik Pertahankan\r\n', NULL),
(14, 3, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pada PENGEMBANGAN DIRI GURU sudah baik pertahankan\r\n', NULL),
(15, 5, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lanjutkan', NULL),
(16, 5, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lanjutkan', NULL),
(17, 5, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lanjutkan', NULL),
(18, 5, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lanjutkan', NULL),
(19, 6, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dokumen administrasi lengkap dan disusun secara sistematis sesuai kaidah kurikulum. RPP sudah memuat langkah pembelajaran yang runut. Disarankan untuk mulai memasukkan unsur literasi dan numerasi secara lebih eksplisit.', NULL),
(20, 6, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aktif mengikuti berbagai webinar dan pelatihan mandiri (misal: PMM). Sertifikat pengembangan diri memadai. Guru mampu menjelaskan hal baru yang dipelajari. Tingkatkan dengan mulai mendiseminasikan ilmu kepada rekan sejawat.', NULL),
(21, 6, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Penilaian sudah mencakup tiga ranah (sikap, pengetahuan, keterampilan). Teknik penilaian bervariasi (tes, observasi, unjuk kerja). Analisis butir soal sudah dilakukan dengan baik. Pertahankan ketertiban administrasi nilai ini.', NULL),
(22, 6, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Proses pembelajaran berjalan interaktif. Guru mampu menghidupkan suasana dan menggunakan media ajar yang relevan. Interaksi antar siswa dan guru terjalin baik. Manajemen waktu perlu sedikit diperhatikan agar penutup tidak terburu-buru.', NULL),
(23, 7, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"Dokumen administrasi lengkap dan disusun secara sistematis sesuai kaidah kurikulum. RPP sudah memuat langkah pembelajaran yang runut. Disarankan untuk mulai memasukkan unsur literasi dan numerasi secara lebih eksplisit.\"', NULL),
(24, 7, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"Sudah mengikuti kegiatan KKG/MGMP rutin, namun belum terlihat imbas atau tindak lanjut nyata dari hasil pelatihan tersebut ke dalam praktik kelas. Disarankan untuk mulai menerapkan satu metode baru yang didapat dari pelatihan.\"', NULL),
(25, 7, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"Penilaian sudah mencakup tiga ranah (sikap, pengetahuan, keterampilan). Teknik penilaian bervariasi (tes, observasi, unjuk kerja). Analisis butir soal sudah dilakukan dengan baik. Pertahankan ketertiban administrasi nilai ini.\"', NULL),
(26, 7, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '\"Proses pembelajaran berjalan interaktif. Guru mampu menghidupkan suasana dan menggunakan media ajar yang relevan. Interaksi antar siswa dan guru terjalin baik. Manajemen waktu perlu sedikit diperhatikan agar penutup tidak terburu-buru.\"', NULL),
(27, 8, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Perencanaan pembelajaran sangat istimewa. Modul ajar disusun dengan sangat detail, kreatif, dan mengakomodasi pembelajaran berdiferensiasi (kesiapan, minat, profil belajar siswa). Administrasi ini layak menjadi model bagi guru lain.', NULL),
(28, 8, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aktif mengikuti berbagai webinar dan pelatihan mandiri (misal: PMM). Sertifikat pengembangan diri memadai. Guru mampu menjelaskan hal baru yang dipelajari. Tingkatkan dengan mulai mendiseminasikan ilmu kepada rekan sejawat', NULL),
(29, 8, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Proses pembelajaran berjalan interaktif. Guru mampu menghidupkan suasana dan menggunakan media ajar yang relevan. Interaksi antar siswa dan guru terjalin baik. Manajemen waktu perlu sedikit diperhatikan agar penutup tidak terburu-buru.', NULL),
(30, 8, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Penilaian sudah mencakup tiga ranah (sikap, pengetahuan, keterampilan). Teknik penilaian bervariasi (tes, observasi, unjuk kerja). Analisis butir soal sudah dilakukan dengan baik. Pertahankan ketertiban administrasi nilai ini.', NULL);
EOT;
        $this->db->query($sql);
        $this->db->enableForeignKeyChecks();
    }
}
