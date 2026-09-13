<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHasilSupervisiTable extends Migration
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
            'jadwal_supervisi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'jenis_penilaian_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'perencanaan_skor' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'perencanaan_catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pelaksanaan_skor' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'pelaksanaan_catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'penilaian_skor' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'penilaian_catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'total_skor' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'nilai_akhir' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'ketercapaian' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'rekomendasi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jadwal_supervisi_id', 'jadwal_supervisi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jenis_penilaian_id', 'jenis_penilaian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('hasil_supervisi');
    }

    public function down()
    {
        $this->forge->dropTable('hasil_supervisi', true);
    }
}