<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelasIdToJadwalSupervisiTable extends Migration
{
    public function up()
    {
        // Check if the kelas_id column doesn't exist yet
        if (!$this->db->fieldExists('kelas_id', 'jadwal_supervisi')) {
            $fields = [
                'kelas_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                ],
            ];
            
            $this->forge->addColumn('jadwal_supervisi', $fields);
            
            // Add foreign key constraint
            $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'SET NULL');
            $this->forge->processIndexes('jadwal_supervisi');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('kelas_id', 'jadwal_supervisi')) {
            try {
                $this->forge->dropForeignKey('jadwal_supervisi', 'jadwal_supervisi_kelas_id_foreign');
            } catch (\Throwable $e) {
                // Ignore if foreign key doesn't exist
            }
            $this->forge->dropColumn('jadwal_supervisi', 'kelas_id');
        }
    }
}