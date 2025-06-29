<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'filename',
        'original_name',
        'original_path',
        'signed_path',
        'file_size',
        'is_signed',
        'upload_date'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'filename' => 'required|max_length[255]',
        'original_name' => 'required|max_length[255]',
        'original_path' => 'required|max_length[500]',
        'file_size' => 'required|integer',
    ];

    protected $validationMessages = [
        'filename' => [
            'required' => 'Nama file harus diisi',
            'max_length' => 'Nama file terlalu panjang'
        ]
    ];

    public function getWithSignature($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('documents.*, 
                         signatures.signer_name, 
                         signatures.signer_position, 
                         signatures.signer_email, 
                         signatures.signature_date, 
                         signatures.verification_code,
                         users.username,
                         users.full_name as owner_name,
                         users.email as owner_email');
        $builder->join('signatures', 'signatures.document_id = documents.id', 'left');
        $builder->join('users', 'users.id = documents.user_id', 'left');

        if ($id !== null) {
            $builder->where('documents.id', $id);
            return $builder->get()->getRowArray();
        }

        $builder->orderBy('documents.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }
    /**
     * Get documents with owner info for admin view
     */
    public function getWithOwnerInfo($userId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('documents.*, 
                         signatures.signer_name, 
                         signatures.signer_position, 
                         signatures.signer_email, 
                         signatures.signature_date, 
                         signatures.verification_code,
                         users.username,
                         users.full_name as owner_name,
                         users.email as owner_email,
                         users.role as owner_role');
        $builder->join('signatures', 'signatures.document_id = documents.id', 'left');
        $builder->join('users', 'users.id = documents.user_id', 'left');

        // If userId provided, filter for specific user (regular users)
        if ($userId !== null) {
            $builder->where('documents.user_id', $userId);
        }

        $builder->orderBy('documents.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get user statistics for admin dashboard
     */
    public function getUserStats()
    {
        $builder = $this->db->table('users');
        $builder->select('users.id, 
                         users.username, 
                         users.full_name, 
                         users.email,
                         users.role,
                         users.created_at,
                         COUNT(documents.id) as total_documents,
                         SUM(CASE WHEN documents.is_signed = 1 THEN 1 ELSE 0 END) as signed_documents,
                         SUM(CASE WHEN documents.is_signed = 0 THEN 1 ELSE 0 END) as pending_documents,
                         MAX(documents.created_at) as last_upload');
        $builder->join('documents', 'documents.user_id = users.id', 'left');
        $builder->where('users.role', 'user'); // Only show regular users
        $builder->groupBy('users.id');
        $builder->orderBy('total_documents', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get system statistics for admin dashboard
     */
    public function getSystemStats()
    {
        $totalUsers = $this->db->table('users')->where('role', 'user')->countAllResults();
        $totalDocuments = $this->db->table('documents')->countAllResults();
        $signedDocuments = $this->db->table('documents')->where('is_signed', 1)->countAllResults();
        $pendingDocuments = $this->db->table('documents')->where('is_signed', 0)->countAllResults();

        // Today's activity
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $todayUploads = $this->db->table('documents')
            ->where('created_at >=', $todayStart)
            ->where('created_at <=', $todayEnd)
            ->countAllResults();

        $todaySignatures = $this->db->table('signatures')
            ->where('created_at >=', $todayStart)
            ->where('created_at <=', $todayEnd)
            ->countAllResults();

        return [
            'total_users' => $totalUsers,
            'total_documents' => $totalDocuments,
            'signed_documents' => $signedDocuments,
            'pending_documents' => $pendingDocuments,
            'today_uploads' => $todayUploads,
            'today_signatures' => $todaySignatures,
            'signature_rate' => $totalDocuments > 0 ? round(($signedDocuments / $totalDocuments) * 100, 1) : 0
        ];
    }

    public function getSignedDocuments()
    {
        return $this->where('is_signed', true)->orderBy('created_at', 'DESC')->findAll();
    }

    public function getUnsignedDocuments()
    {
        return $this->where('is_signed', false)->orderBy('created_at', 'DESC')->findAll();
    }
}
