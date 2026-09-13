<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalSupervisiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tahun_ajar_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'guru_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'supervisor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'mata_pelajaran' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'kelas' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'jam_ke' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'hari' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'tanggal_supervisi' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Terjadwal', 'Selesai', 'Dibatalkan'],
                'default' => 'Terjadwal',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('supervisor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jadwal_supervisi');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal_supervisi', true);
    }
}