<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'document_id',
        'action',
        'description',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'action' => 'required|max_length[50]',
        'ip_address' => 'required|max_length[45]',
        'user_agent' => 'required'
    ];

    /**
     * Log user activity - FIXED PARAMETER ORDER
     */
    public function logActivity($action, $userId = null, $documentId = null, $description = null, $request = null)
    {
        try {
            $data = [
                'user_id' => $userId,
                'document_id' => $documentId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $request ? $request->getIPAddress() : '127.0.0.1',
                'user_agent' => $request ? $request->getUserAgent()->getAgentString() : 'Unknown'
            ];

            $this->insert($data);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get audit logs with user and document details
     */
    public function getAuditLogs($limit = 100, $offset = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            audit_logs.*,
            users.username,
            users.full_name,
            documents.original_name as document_name
        ');
        $builder->join('users', 'users.id = audit_logs.user_id', 'left');
        $builder->join('documents', 'documents.id = audit_logs.document_id', 'left');
        $builder->orderBy('audit_logs.created_at', 'DESC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResultArray();
    }

    /**
     * Get audit logs by user
     */
    public function getLogsByUser($userId, $limit = 50)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            audit_logs.*,
            documents.original_name as document_name
        ');
        $builder->join('documents', 'documents.id = audit_logs.document_id', 'left');
        $builder->where('audit_logs.user_id', $userId);
        $builder->orderBy('audit_logs.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }

    /**
     * Get audit logs by document
     */
    public function getLogsByDocument($documentId, $limit = 20)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            audit_logs.*,
            users.username,
            users.full_name
        ');
        $builder->join('users', 'users.id = audit_logs.user_id', 'left');
        $builder->where('audit_logs.document_id', $documentId);
        $builder->orderBy('audit_logs.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }

    /**
     * Get audit logs by action
     */
    public function getLogsByAction($action, $limit = 100)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            audit_logs.*,
            users.username,
            users.full_name,
            documents.original_name as document_name
        ');
        $builder->join('users', 'users.id = audit_logs.user_id', 'left');
        $builder->join('documents', 'documents.id = audit_logs.document_id', 'left');
        $builder->where('audit_logs.action', $action);
        $builder->orderBy('audit_logs.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }

    /**
     * Get audit logs statistics
     */
    public function getStatistics($days = 30)
    {
        $builder = $this->db->table($this->table);

        // Total activities in last X days
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $totalActivities = $builder->countAllResults();

        // Activities by action
        $builder = $this->db->table($this->table);
        $builder->select('action, COUNT(*) as count');
        $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $builder->groupBy('action');
        $actionStats = $builder->get()->getResultArray();

        // Most active users
        $builder = $this->db->table($this->table);
        $builder->select('users.username, users.full_name, COUNT(*) as activity_count');
        $builder->join('users', 'users.id = audit_logs.user_id', 'left');
        $builder->where('audit_logs.created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $builder->groupBy('audit_logs.user_id');
        $builder->orderBy('activity_count', 'DESC');
        $builder->limit(10);
        $activeUsers = $builder->get()->getResultArray();

        return [
            'total_activities' => $totalActivities,
            'action_statistics' => $actionStats,
            'most_active_users' => $activeUsers,
            'period_days' => $days
        ];
    }

    /**
     * Clean old audit logs (older than specified days)
     */
    public function cleanOldLogs($days = 365)
    {
        try {
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $deletedCount = $this->where('created_at <', $cutoffDate)->delete();

            log_message('info', "Cleaned {$deletedCount} old audit logs older than {$days} days");
            return $deletedCount;
        } catch (\Exception $e) {
            log_message('error', 'Failed to clean old audit logs: ' . $e->getMessage());
            return false;
        }
    }
}
