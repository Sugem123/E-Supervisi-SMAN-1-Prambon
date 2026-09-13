<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
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
            'nama_kelas' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'wali_kelas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Aktif', 'Nonaktif'],
                'default' => 'Aktif',
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tahun_ajar_id', 'tahun_ajar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('wali_kelas', 'guru', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('kelas');
    }

    public function down()
    {
        $this->forge->dropTable('kelas', true);
    }
}