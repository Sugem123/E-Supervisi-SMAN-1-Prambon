<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganToFotoBuktiTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('foto_bukti', [
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'file_path'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('foto_bukti', 'keterangan');
    }
}