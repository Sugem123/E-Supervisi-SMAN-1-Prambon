<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['setting_key', 'setting_value', 'description'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }
    
    /**
     * Set or update a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $description
     * @return bool
     */
    public function setSetting(string $key, $value, string $description = null): bool
    {
        $existing = $this->where('setting_key', $key)->first();
        
        if ($existing) {
            // Update existing setting
            return $this->update($existing['id'], [
                'setting_value' => $value,
                'description' => $description,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Create new setting
            return $this->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Delete a setting by key
     *
     * @param string $key
     * @return bool
     */
    public function deleteSetting(string $key): bool
    {
        $setting = $this->where('setting_key', $key)->first();
        if ($setting) {
            return $this->delete($setting['id']);
        }
        return false;
    }
    
    /**
     * Get all settings as key-value pairs
     *
     * @return array
     */
    public function getAllSettings(): array
    {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }
}