<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToDokumenAjar extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Disetujui', 'Ditolak'],
                'default'    => 'Pending',
                'after'      => 'keterangan', // Position it appropriately
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status',
            ],
        ];
        $this->forge->addColumn('dokumen_ajar', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('dokumen_ajar', ['status', 'feedback']);
    }
}
