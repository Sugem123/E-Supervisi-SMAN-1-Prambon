<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokumenAjarTable extends Migration
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
            'jadwal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_dokumen' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'link_drive' => [
                'type' => 'TEXT',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jadwal_id', 'jadwal_supervisi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokumen_ajar');
    }

    public function down()
    {
        $this->forge->dropTable('dokumen_ajar', true);
    }
}
