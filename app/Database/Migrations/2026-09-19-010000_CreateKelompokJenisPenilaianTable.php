<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelompokJenisPenilaianTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('kelompok_jenis_penilaian')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'kelompok_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'jenis_penilaian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['kelompok_id', 'jenis_penilaian_id'], 'uq_kelompok_jenis');
            $this->forge->addForeignKey('kelompok_id', 'kelompok_supervisi', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('jenis_penilaian_id', 'jenis_penilaian', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('kelompok_jenis_penilaian', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('kelompok_jenis_penilaian')) {
            $this->forge->dropTable('kelompok_jenis_penilaian', true);
        }
    }
}
