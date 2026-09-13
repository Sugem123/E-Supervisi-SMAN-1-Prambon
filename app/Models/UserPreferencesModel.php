<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPreferencesModel extends Model
{
    protected $table = 'user_preferences';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'preference_key', 'preference_value'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get user preferences by user ID
     *
     * @param int $userId
     * @return array
     */
    public function getPreferencesByUser(int $userId)
    {
        $preferences = $this->where('user_id', $userId)->findAll();
        $result = [];
        
        foreach ($preferences as $pref) {
            $result[$pref['preference_key']] = $pref['preference_value'];
        }
        
        return $result;
    }
    
    /**
     * Save or update a user preference
     *
     * @param int $userId
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function savePreference(int $userId, string $key, string $value)
    {
        $existing = $this->where('user_id', $userId)
                         ->where('preference_key', $key)
                         ->first();
        
        if ($existing) {
            return $this->update($existing['id'], [
                'preference_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->insert([
                'user_id' => $userId,
                'preference_key' => $key,
                'preference_value' => $value
            ]);
        }
    }
}