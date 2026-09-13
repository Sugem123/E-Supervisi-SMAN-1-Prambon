<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixGuruData extends Migration
{
    public function up()
    {
        // Delete guru records for users who are not actually gurus
        // Keep only records for users with role = 'guru'
        $this->db->query("DELETE g FROM guru g LEFT JOIN users u ON g.user_id = u.id WHERE u.role != 'guru' OR u.role IS NULL");
    }

    public function down()
    {
        // This migration cannot be rolled back
    }
}