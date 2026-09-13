<?php

namespace App\Models;

use CodeIgniter\Model;

class SupervisorModel extends Model
{
    protected $table = 'supervisor';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'tanggal_penugasan', 'status'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
}