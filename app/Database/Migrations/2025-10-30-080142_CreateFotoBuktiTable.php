<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFotoBuktiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jadwal_supervisi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jadwal_supervisi_id', 'jadwal_supervisi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('foto_bukti');
    }

    public function down()
    {
        $this->forge->dropTable('foto_bukti', true);
    }
}