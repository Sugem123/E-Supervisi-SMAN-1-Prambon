# Rekomendasi Penambahan Fitur Modul Guru

Berdasarkan analisis struktur direktori saat ini (`app/Views/guru` dan `app/Controllers/Guru`) serta kebutuhan umum sistem supervisi akademik, berikut adalah rekomendasi penambahan fitur untuk meningkatkan efektivitas aplikasi bagi pengguna Guru.

## 1. Modul Supervisi (Penyempurnaan)
Saat ini fitur yang tersedia berfokus pada **Jadwal** dan **Hasil**. Disarankan untuk melengkapinya dengan siklus Pra dan Pasca supervisi.

### A. Upload Dokumen Ajar (Pra-Supervisi)
**Masalah**: Guru belum memiliki tempat khusus untuk mengunggah RPP/Modul Ajar atau Bahan Ajar yang akan disupervisi.
**Solusi**: Tambahkan fitur upload dokumen pada menu Jadwal atau menu terpisah.
- **Fitur**: Input File PDF/Docx untuk RPP/Modul Ajar.
- **Manfaat**: Supervisor dapat meninjau dokumen sebelum pelaksanaan supervisi.

### B. Tindak Lanjut & Refleksi (Pasca-Supervisi)
**Masalah**: Hasil supervisi hanya bersifat satu arah (dilihat saja).
**Solusi**: Tambahkan form input untuk Guru memberikan tanggapan atau rencana tindak lanjut atas catatan supervisor.
- **Fitur**: Form input teks "Refleksi Guru" dan "Rencana Tindak Lanjut" pada detail Hasil Supervisi.
- **Manfaat**: Menciptakan komunikasi dua arah dan komitmen perbaikan.

## 2. Modul Administrasi KBM (Kegiatan Belajar Mengajar)
Jika aplikasi ini bertujuan menjadi portal lengkap bagi Guru (tidak hanya untuk supervisi), fitur administrasi harian sangat direkomendasikan.

### A. Jurnal Mengajar Harian
**Deskripsi**: Mencatat materi yang diajarkan, kelas, dan catatan kejadian di kelas hari itu.
- **Penting**: Sebagai bukti fisik pelaksanaan pembelajaran yang sering diperiksa saat supervisi.

### B. Absensi Siswa
**Deskripsi**: Fitur untuk melakukan presensi kehadiran siswa per jam pelajaran atau per hari.
- **Integrasi**: Data kehadiran ini bisa otomatis masuk ke laporan jurnal.

### C. Kalender Akademik / Kalender Mengajar
**Deskripsi**: Tampilan visual agenda sekolah dan jadwal mengajar guru tersebut.
- **Fitur**: Penanda tanggal supervisi, libur nasional, dan acara sekolah.

## 3. Peningkatan Dashboard
**Saran**: Tambahkan widget "Quick Action" atau notifikasi.
- **Notifikasi**: "Anda memiliki jadwal supervisi besok" atau "Hasil supervisi tanggal X telah terbit".
- **Statistik Personal**: Grafik perkembangan nilai supervisi dari waktu ke waktu (Grafik garis).

---
*Catatan: Jika fokus aplikasi strictly hanya untuk 'Pencatatan Nilai Supervisi oleh Kepala Sekolah', maka poin 1 (Upload Dokumen & Tindak Lanjut) adalah prioritas utama. Poin 2 bersifat opsional tergantung cakupan sistem.*
