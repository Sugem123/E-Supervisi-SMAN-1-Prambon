<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToUsersTable extends Migration
{
    public function up()
    {
        // Check if email column already exists
        if (!$this->db->fieldExists('email', 'users')) {
            $fields = [
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                    'after' => 'password'
                ]
            ];
            
            $this->forge->addColumn('users', $fields);
            
            // Add unique index for email
            $this->forge->addUniqueKey('users', 'email');
        } else {
            // If email column exists, check if it has a unique constraint
            $indexes = $this->db->getIndexData('users');
            $hasEmailIndex = false;
            foreach ($indexes as $index) {
                if (in_array('email', $index->fields)) {
                    $hasEmailIndex = true;
                    break;
                }
            }
            
            if (!$hasEmailIndex) {
                // Add unique index for email if it doesn't exist
                $this->forge->addUniqueKey('users', 'email');
            }
        }
    }

    public function down()
    {
        // Only drop column if it exists
        if ($this->db->fieldExists('email', 'users')) {
            $this->forge->dropColumn('users', 'email');
        }
    }
}