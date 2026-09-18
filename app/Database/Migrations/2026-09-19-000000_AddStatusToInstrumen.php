<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToInstrumen extends Migration
{
    public function up()
    {
        $jenisFields = $this->db->getFieldNames('jenis_penilaian');
        if (!in_array('status', $jenisFields, true)) {
            $this->forge->addColumn('jenis_penilaian', [
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Aktif', 'Nonaktif'],
                    'default'    => 'Aktif',
                    'null'       => false,
                    'after'      => 'kategori_skor',
                ],
            ]);
        }

        $aspekFields = $this->db->getFieldNames('aspek_penilaian');
        if (!in_array('status', $aspekFields, true)) {
            $this->forge->addColumn('aspek_penilaian', [
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['Aktif', 'Nonaktif'],
                    'default'    => 'Aktif',
                    'null'       => false,
                    'after'      => 'urutan',
                ],
            ]);
        }
    }

    public function down()
    {
        if (in_array('status', $this->db->getFieldNames('aspek_penilaian'), true)) {
            try {
                $this->forge->dropColumn('aspek_penilaian', 'status');
            } catch (\Throwable $e) {
            }
        }
        if (in_array('status', $this->db->getFieldNames('jenis_penilaian'), true)) {
            try {
                $this->forge->dropColumn('jenis_penilaian', 'status');
            } catch (\Throwable $e) {
            }
        }
    }
}
