<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'activity_type', 'description', 'ip_address', 'user_agent', 'created_at'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';
    
    /**
     * Get audit logs with user information
     */
    public function getAuditLogsWithUsers($filters = [])
    {
        $builder = $this->select('audit_logs.*, users.username, users.email')
                        ->join('users', 'users.id = audit_logs.user_id', 'left');
        
        // Apply filters if provided
        if (!empty($filters['user_id'])) {
            $builder->where('audit_logs.user_id', $filters['user_id']);
        }
        
        if (!empty($filters['activity_type'])) {
            $builder->where('audit_logs.activity_type', $filters['activity_type']);
        }
        
        if (!empty($filters['start_date'])) {
            $builder->where('audit_logs.created_at >=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $builder->where('audit_logs.created_at <=', $filters['end_date'] . ' 23:59:59');
        }
        
        return $builder->orderBy('audit_logs.created_at', 'DESC');
    }
    
    /**
     * Log an activity
     */
    public function logActivity($userId, $activityType, $description)
    {
        return $this->insert([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => \Config\Services::request()->getIPAddress(),
            'user_agent' => \Config\Services::request()->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}