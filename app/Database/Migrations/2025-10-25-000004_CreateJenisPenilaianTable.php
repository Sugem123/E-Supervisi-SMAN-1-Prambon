<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisPenilaianTable extends Migration
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
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'skor_maksimal' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 100,
            ],
            'kategori_skor' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis_penilaian', true);
    }

    public function down()
    {
        $this->forge->dropTable('jenis_penilaian', true);
    }
}