<?php

namespace App\Models;

use CodeIgniter\Model;

class SignatureModel extends Model
{
    protected $table = 'signatures';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'document_id',
        'signer_name',
        'signer_position',
        'signer_email',
        'verification_code',
        'qr_code_path',
        'signature_date',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'document_id' => 'required|integer',
        'signer_name' => 'required|max_length[255]',
        'signer_position' => 'required|max_length[255]',
        'signer_email' => 'required|valid_email|max_length[255]',
        'verification_code' => 'required|max_length[100]|is_unique[signatures.verification_code]',
    ];

    public function getByVerificationCode($code)
    {
        $builder = $this->db->table($this->table);
        $builder->select('signatures.*, documents.filename, documents.original_name, 
                         documents.signed_path, documents.original_path');
        $builder->join('documents', 'documents.id = signatures.document_id');
        $builder->where('signatures.verification_code', $code);

        return $builder->get()->getRowArray();
    }

    public function generateVerificationCode()
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(8)));
        } while ($this->where('verification_code', $code)->first());

        return $code;
    }
}
