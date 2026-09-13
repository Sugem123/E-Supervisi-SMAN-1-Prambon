<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileColumnsToGuruTable extends Migration
{
    public function up()
    {
        // Check if columns exist before adding them
        $fields = $this->db->getFieldNames('guru');
        
        if (!in_array('foto_profil', $fields)) {
            $this->forge->addColumn('guru', [
                'foto_profil' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                    'after' => 'user_id'
                ]
            ]);
        }
        
        if (!in_array('telepon', $fields)) {
            $this->forge->addColumn('guru', [
                'telepon' => [
                    'type' => 'VARCHAR',
                    'constraint' => '15',
                    'null' => true,
                    'after' => 'foto_profil'
                ]
            ]);
        }
        
        if (!in_array('alamat', $fields)) {
            $this->forge->addColumn('guru', [
                'alamat' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'telepon'
                ]
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('guru');
        
        if (in_array('alamat', $fields)) {
            $this->forge->dropColumn('guru', 'alamat');
        }
        
        if (in_array('telepon', $fields)) {
            $this->forge->dropColumn('guru', 'telepon');
        }
        
        if (in_array('foto_profil', $fields)) {
            $this->forge->dropColumn('guru', 'foto_profil');
        }
    }
}