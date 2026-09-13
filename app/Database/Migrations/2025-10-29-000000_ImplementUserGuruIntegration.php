<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ImplementUserGuruIntegration extends Migration
{
    public function up()
    {
        // Ensure email column exists
        if (!$this->db->fieldExists('email', 'users')) {
            $this->forge->addColumn('users', [
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                    'after' => 'password'
                ]
            ]);
        }

        // Modify existing email column to make it required and unique if needed
        // First update any NULL emails to a default value to avoid errors
        $this->db->query("UPDATE users SET email = CONCAT('user', id, '@example.com') WHERE email IS NULL OR email = ''");
        
        // Then make the column NOT NULL
        $this->db->query("ALTER TABLE users MODIFY email VARCHAR(100) NOT NULL");
        
        // Add unique constraint if not exists
        try {
            $this->db->query("ALTER TABLE users ADD UNIQUE(email)");
        } catch (\Exception $e) {
            // Unique constraint might already exist, ignore the error
        }
        
        // Modify role column to have default value
        $this->db->query("ALTER TABLE users MODIFY role ENUM('admin','kepala','supervisor','guru') NOT NULL DEFAULT 'guru'");
        
        // Check if is_supervisor column exists in guru table
        try {
            $guruFields = $this->db->getFieldNames('guru');
            $supervisorFieldExists = in_array('is_supervisor', $guruFields);
        } catch (\Exception $e) {
            $supervisorFieldExists = false;
        }
        
        if (!$supervisorFieldExists) {
            // Add is_supervisor column to guru table
            $fields = [
                'is_supervisor' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'after' => 'status_kepegawaian'
                ]
            ];
            $this->forge->addColumn('guru', $fields);
        }
    }

    public function down()
    {
        // Check if is_supervisor column exists
        try {
            $guruFields = $this->db->getFieldNames('guru');
            $supervisorFieldExists = in_array('is_supervisor', $guruFields);
        } catch (\Exception $e) {
            $supervisorFieldExists = false;
        }
        
        if ($supervisorFieldExists) {
            // Remove is_supervisor column if it exists
            $this->forge->dropColumn('guru', 'is_supervisor');
        }
        
        // Revert users table modifications (but don't remove email column as it may be needed)
        $this->db->query("ALTER TABLE users MODIFY role ENUM('admin','kepala','supervisor','guru') NOT NULL");
    }
}