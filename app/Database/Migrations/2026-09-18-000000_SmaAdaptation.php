<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SmaAdaptation extends Migration
{
    public function up()
    {
        // 1. ref_mapel (master mapel SMA)
        if (!$this->db->tableExists('ref_mapel')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT', 'constraint' => 11,
                    'unsigned' => true, 'auto_increment' => true,
                ],
                'nama_mapel' => ['type' => 'VARCHAR', 'constraint' => 100],
                'jenjang' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'SMA'],
                'kelompok' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'status' => [
                    'type' => 'ENUM', 'constraint' => ['Aktif', 'Nonaktif'],
                    'default' => 'Aktif',
                ],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['nama_mapel', 'jenjang']);
            $this->forge->createTable('ref_mapel');
        }

        // 2. guru: jenis_ptk + mapel_id + perluas status_kepegawaian (non-destruktif)
        $guruFields = $this->db->getFieldNames('guru');
        if (!in_array('jenis_ptk', $guruFields)) {
            $this->forge->addColumn('guru', [
                'jenis_ptk' => [
                    'type' => 'ENUM',
                    'constraint' => ['Guru', 'Tendik'],
                    'default' => 'Guru',
                    'null' => false,
                    'after' => 'status_kepegawaian',
                ],
            ]);
        }
        if (!in_array('mapel_id', $guruFields)) {
            $this->forge->addColumn('guru', [
                'mapel_id' => [
                    'type' => 'INT', 'constraint' => 11,
                    'unsigned' => true, 'null' => true,
                    'after' => 'jenis_ptk',
                ],
            ]);
            try {
                $this->db->query('ALTER TABLE guru ADD CONSTRAINT guru_mapel_id_foreign FOREIGN KEY (mapel_id) REFERENCES ref_mapel(id) ON DELETE SET NULL ON UPDATE CASCADE');
            } catch (\Throwable $e) {
                // FK opsional; abaikan bila engine tidak mendukung
            }
        }
        // Perluas ENUM status_kepegawaian: PNS, PPPK, GTT, PTT, Honorer (legacy), Kontrak
        try {
            $this->db->query("ALTER TABLE guru MODIFY status_kepegawaian ENUM('PNS','PPPK','GTT','PTT','Honorer','Kontrak') NULL");
        } catch (\Throwable $e) {
            // abaikan bila driver tidak mendukung MODIFY
        }

        // 3. kelas: tingkat X/XI/XII (nullable, non-destruktif)
        $kelasFields = $this->db->getFieldNames('kelas');
        if (!in_array('tingkat', $kelasFields)) {
            $this->forge->addColumn('kelas', [
                'tingkat' => [
                    'type' => 'ENUM',
                    'constraint' => ['X', 'XI', 'XII'],
                    'null' => true,
                    'after' => 'nama_kelas',
                ],
            ]);
        }
        if (!in_array('jurusan', $kelasFields)) {
            $this->forge->addColumn('kelas', [
                'jurusan' => [
                    'type' => 'VARCHAR', 'constraint' => 50,
                    'null' => true, 'after' => 'tingkat',
                ],
            ]);
        }

        // 4. kelompok supervisi (Supervisor-Anggota)
        if (!$this->db->tableExists('kelompok_supervisi')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT', 'constraint' => 11,
                    'unsigned' => true, 'auto_increment' => true,
                ],
                'nama_kelompok' => ['type' => 'VARCHAR', 'constraint' => 100],
                'supervisor_id' => [
                    'type' => 'INT', 'constraint' => 11, 'unsigned' => true,
                ],
                'tahun_ajar_id' => [
                    'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true,
                ],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('supervisor_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'SET NULL', 'SET NULL');
            $this->forge->createTable('kelompok_supervisi');
        }
        if (!$this->db->tableExists('kelompok_anggota')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT', 'constraint' => 11,
                    'unsigned' => true, 'auto_increment' => true,
                ],
                'kelompok_id' => [
                    'type' => 'INT', 'constraint' => 11, 'unsigned' => true,
                ],
                'guru_id' => [
                    'type' => 'INT', 'constraint' => 11, 'unsigned' => true,
                ],
                'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['kelompok_id', 'guru_id']);
            $this->forge->addForeignKey('kelompok_id', 'kelompok_supervisi', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('kelompok_anggota');
        }

        // 5. jadwal_supervisi: kelompok_id + mapel_id (konteks kelompok, opsional)
        $jadwalFields = $this->db->getFieldNames('jadwal_supervisi');
        if (!in_array('kelompok_id', $jadwalFields)) {
            $this->forge->addColumn('jadwal_supervisi', [
                'kelompok_id' => [
                    'type' => 'INT', 'constraint' => 11,
                    'unsigned' => true, 'null' => true,
                ],
            ]);
            try {
                $this->db->query('ALTER TABLE jadwal_supervisi ADD CONSTRAINT jadwal_kelompok_id_foreign FOREIGN KEY (kelompok_id) REFERENCES kelompok_supervisi(id) ON DELETE SET NULL ON UPDATE CASCADE');
            } catch (\Throwable $e) {
            }
        }
        if (!in_array('mapel_id', $jadwalFields)) {
            $this->forge->addColumn('jadwal_supervisi', [
                'mapel_id' => [
                    'type' => 'INT', 'constraint' => 11,
                    'unsigned' => true, 'null' => true,
                ],
            ]);
            try {
                $this->db->query('ALTER TABLE jadwal_supervisi ADD CONSTRAINT jadwal_mapel_id_foreign FOREIGN KEY (mapel_id) REFERENCES ref_mapel(id) ON DELETE SET NULL ON UPDATE CASCADE');
            } catch (\Throwable $e) {
            }
        }

        // 6. hasil_supervisi: bukti BA + link video + RTL (non-destruktif)
        $hasilFields = $this->db->getFieldNames('hasil_supervisi');
        if (!in_array('berita_acara_path', $hasilFields)) {
            $this->forge->addColumn('hasil_supervisi', [
                'berita_acara_path' => [
                    'type' => 'VARCHAR', 'constraint' => '255', 'null' => true,
                ],
            ]);
        }
        if (!in_array('link_video', $hasilFields)) {
            $this->forge->addColumn('hasil_supervisi', [
                'link_video' => [
                    'type' => 'VARCHAR', 'constraint' => '255', 'null' => true,
                ],
            ]);
        }
        if (!in_array('rtl', $hasilFields)) {
            $this->forge->addColumn('hasil_supervisi', [
                'rtl' => ['type' => 'TEXT', 'null' => true],
            ]);
        }

        // 7. foto_bukti: jenis_bukti foto/berita_acara (default foto, BC aman)
        $fotoFields = $this->db->getFieldNames('foto_bukti');
        if (!in_array('jenis_bukti', $fotoFields)) {
            $this->forge->addColumn('foto_bukti', [
                'jenis_bukti' => [
                    'type' => 'ENUM',
                    'constraint' => ['foto', 'berita_acara'],
                    'default' => 'foto',
                    'after' => 'file_path',
                ],
            ]);
        }

        // 8. Aturan arsip tahunan: 1 guru = 1 jadwal per tahun_ajar_id (DB-level guard).
        //    Data lama tetap tersimpan karena semua query index difilter tahun aktif.
        $this->ensureOneSchedulePerTeacherPerYear();
    }

    private function ensureOneSchedulePerTeacherPerYear(): void
    {
        // Bersihkan duplikat historis bila ada: pertahankan 1 baris tertua per (tahun_ajar_id, guru_id).
        try {
            $dupes = $this->db->query(
                'SELECT tahun_ajar_id, guru_id, MIN(id) AS keep_id, COUNT(*) AS c ' .
                'FROM jadwal_supervisi GROUP BY tahun_ajar_id, guru_id HAVING c > 1'
            )->getResultArray();
            foreach ($dupes as $d) {
                $this->db->query(
                    'DELETE FROM jadwal_supervisi WHERE tahun_ajar_id = ? AND guru_id = ? AND id <> ?',
                    [(int) $d['tahun_ajar_id'], (int) $d['guru_id'], (int) $d['keep_id']]
                );
            }
        } catch (\Throwable $e) {
            // Lanjut: guard unik tetap dicoba di bawah.
        }

        // Guard DB: 1 guru = 1 jadwal per baris tahun ajaran (Ganjil/Genap).
        // Aturan "1x per label tahun (mis. 2025/2026)" ditegakkan di layer aplikasi
        // karena label tahun disimpan di tabel tahun_ajar, bukan di jadwal.
        try {
            $exists = $this->db->query(
                "SELECT COUNT(*) AS c FROM information_schema.STATISTICS " .
                "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jadwal_supervisi' " .
                "AND INDEX_NAME = 'uq_jadwal_tahun_guru'"
            )->getRowArray();
            if (empty($exists) || (int) ($exists['c'] ?? 0) === 0) {
                $this->db->query(
                    'ALTER TABLE jadwal_supervisi ADD UNIQUE KEY uq_jadwal_tahun_guru (tahun_ajar_id, guru_id)'
                );
            }
        } catch (\Throwable $e) {
            // Abaikan bila engine/index tidak mendukung; validasi aplikasi tetap jalan.
        }
    }

    public function down()
    {
        // Lepas guard unik 1 guru = 1 jadwal per tahun bila ada
        try {
            $this->db->query('ALTER TABLE jadwal_supervisi DROP INDEX uq_jadwal_tahun_guru');
        } catch (\Throwable $e) {
        }
        // Urutan rollback: lepas FK baru bila ada, lalu drop kolom/tabel baru
        try {
            $this->db->query('ALTER TABLE jadwal_supervisi DROP FOREIGN KEY jadwal_kelompok_id_foreign');
        } catch (\Throwable $e) {
        }
        try {
            $this->db->query('ALTER TABLE jadwal_supervisi DROP FOREIGN KEY jadwal_mapel_id_foreign');
        } catch (\Throwable $e) {
        }
        try {
            $this->db->query('ALTER TABLE guru DROP FOREIGN KEY guru_mapel_id_foreign');
        } catch (\Throwable $e) {
        }

        foreach (['kelompok_id', 'mapel_id'] as $col) {
            if ($this->db->fieldExists($col, 'jadwal_supervisi')) {
                try {
                    $this->forge->dropColumn('jadwal_supervisi', $col);
                } catch (\Throwable $e) {
                }
            }
        }
        foreach (['berita_acara_path', 'link_video', 'rtl'] as $col) {
            if ($this->db->fieldExists($col, 'hasil_supervisi')) {
                try {
                    $this->forge->dropColumn('hasil_supervisi', $col);
                } catch (\Throwable $e) {
                }
            }
        }
        if ($this->db->fieldExists('jenis_bukti', 'foto_bukti')) {
            try {
                $this->forge->dropColumn('foto_bukti', 'jenis_bukti');
            } catch (\Throwable $e) {
            }
        }
        if ($this->db->fieldExists('jenis_ptk', 'guru')) {
            try {
                $this->forge->dropColumn('guru', 'jenis_ptk');
            } catch (\Throwable $e) {
            }
        }
        if ($this->db->fieldExists('mapel_id', 'guru')) {
            try {
                $this->forge->dropColumn('guru', 'mapel_id');
            } catch (\Throwable $e) {
            }
        }
        foreach (['tingkat', 'jurusan'] as $col) {
            if ($this->db->fieldExists($col, 'kelas')) {
                try {
                    $this->forge->dropColumn('kelas', $col);
                } catch (\Throwable $e) {
                }
            }
        }
        // Kembalikan ENUM legacy (PNS, PPPK, Honorer) — data GTT/PTT akan gagal bila masih ada,
        // sehingga down() hanya aman setelah data baru dibersihkan manual.
        try {
            $this->db->query("ALTER TABLE guru MODIFY status_kepegawaian ENUM('PNS','PPPK','Honorer') NULL");
        } catch (\Throwable $e) {
        }
        if ($this->db->tableExists('kelompok_anggota')) {
            $this->forge->dropTable('kelompok_anggota', true);
        }
        if ($this->db->tableExists('kelompok_supervisi')) {
            $this->forge->dropTable('kelompok_supervisi', true);
        }
        if ($this->db->tableExists('ref_mapel')) {
            $this->forge->dropTable('ref_mapel', true);
        }
    }
}
