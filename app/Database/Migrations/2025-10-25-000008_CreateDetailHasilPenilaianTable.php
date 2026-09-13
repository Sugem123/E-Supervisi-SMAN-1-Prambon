<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailHasilPenilaianTable extends Migration
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
            'hasil_supervisi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'aspek_penilaian_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'skor' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('hasil_supervisi_id', 'hasil_supervisi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('aspek_penilaian_id', 'aspek_penilaian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_hasil_penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('detail_hasil_penilaian', true);
    }
}