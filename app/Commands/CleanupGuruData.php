<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\GuruModel;
use App\Models\UserModel;

class CleanupGuruData extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'app:cleanup-guru-data';
    protected $description = 'Clean up guru data to remove records for non-guru users';

    public function run(array $params)
    {
        $guruModel = new GuruModel();
        $userModel = new UserModel();
        
        // Get all users
        $users = $userModel->findAll();
        
        // Create array of valid user_ids (only users with role 'guru')
        $validUserIds = [];
        foreach ($users as $user) {
            if ($user['role'] == 'guru') {
                $validUserIds[] = $user['id'];
            }
        }
        
        // Get all guru records
        $gurus = $guruModel->findAll();
        
        $removedCount = 0;
        foreach ($gurus as $guru) {
            // If guru record has null user_id or user_id not in valid list, remove it
            if (is_null($guru['user_id']) || !in_array($guru['user_id'], $validUserIds)) {
                $guruModel->delete($guru['id']);
                $removedCount++;
                CLI::write('Removed guru record with id: ' . $guru['id'], 'yellow');
            }
        }
        
        CLI::write('Cleanup completed. Removed ' . $removedCount . ' invalid guru records.', 'green');
    }
}
