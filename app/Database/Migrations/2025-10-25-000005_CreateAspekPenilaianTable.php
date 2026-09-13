<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAspekPenilaianTable extends Migration
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
            'jenis_penilaian_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nama_aspek' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jenis_penilaian_id', 'jenis_penilaian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('aspek_penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('aspek_penilaian', true);
    }
}