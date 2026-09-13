<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaktuAndMateriToJadwalSupervisiTable extends Migration
{
    public function up()
    {
        $fields = [
            'waktu_dari' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'waktu_sampai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'materi_supervisi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        
        $this->forge->addColumn('jadwal_supervisi', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('jadwal_supervisi', ['waktu_dari', 'waktu_sampai', 'materi_supervisi']);
    }
}