<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run()
    {
        $this->call('AuditLogsSeeder');
    }
}
