<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunAjarTable extends Migration
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
            'tahun_ajar' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
            ],
            'semester' => [
                'type' => 'ENUM',
                'constraint' => ['Ganjil', 'Genap'],
            ],
            'status_aktif' => [
                'type' => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif'],
                'default' => 'Nonaktif',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('tahun_ajar');
    }

    public function down()
    {
        $this->forge->dropTable('tahun_ajar', true);
    }
}