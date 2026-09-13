<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileColumnsToUsersTable extends Migration
{
    public function up()
    {
        // Check if columns exist before adding them
        $fields = $this->db->getFieldNames('users');
        
        if (!in_array('foto_profil', $fields)) {
            $this->forge->addColumn('users', [
                'foto_profil' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                    'after' => 'email'
                ]
            ]);
        }
        
        if (!in_array('telepon', $fields)) {
            $this->forge->addColumn('users', [
                'telepon' => [
                    'type' => 'VARCHAR',
                    'constraint' => '15',
                    'null' => true,
                    'after' => 'foto_profil'
                ]
            ]);
        }
        
        if (!in_array('alamat', $fields)) {
            $this->forge->addColumn('users', [
                'alamat' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'telepon'
                ]
            ]);
        }
        
        if (!in_array('updated_at', $fields)) {
            $this->forge->addColumn('users', [
                'updated_at' => [
                    'type' => 'TIMESTAMP',
                    'null' => true,
                    'after' => 'created_at'
                ]
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('users');
        
        if (in_array('updated_at', $fields)) {
            $this->forge->dropColumn('users', 'updated_at');
        }
        
        if (in_array('alamat', $fields)) {
            $this->forge->dropColumn('users', 'alamat');
        }
        
        if (in_array('telepon', $fields)) {
            $this->forge->dropColumn('users', 'telepon');
        }
        
        if (in_array('foto_profil', $fields)) {
            $this->forge->dropColumn('users', 'foto_profil');
        }
    }
}