<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username',
        'email',
        'password_hash',
        'full_name',
        'role',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // ✅ FIX: Remove static validation rules that conflict with profile updates
    // These rules will be set dynamically when needed
    protected $validationRules = [];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'alpha_numeric' => 'Username hanya boleh huruf dan angka',
            'min_length' => 'Username minimal 3 karakter',
            'max_length' => 'Username maksimal 50 karakter',
            'is_unique' => 'Username sudah digunakan'
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah terdaftar'
        ],
        'password_hash' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'full_name' => [
            'required' => 'Nama lengkap harus diisi',
            'max_length' => 'Nama lengkap maksimal 100 karakter'
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list' => 'Role harus user atau admin'
        ]
    ];

    /**
     * Hash password before insert/update
     */
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
        }
        return $data;
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Verify user login
     */
    public function verifyLogin($username, $password)
    {
        $user = $this->getUserByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Remove password hash from return data
            unset($user['password_hash']);
            return $user;
        }

        return false;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin($userId)
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'admin';
    }

    /**
     * Get active users
     */
    public function getActiveUsers()
    {
        return $this->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * ✅ FIXED: Create new user with proper validation rules
     */
    public function createUser($data)
    {
        // Set validation rules specifically for user creation
        $this->setValidationRules([
            'username' => 'required|alpha_numeric|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required|max_length[100]',
            'role' => 'permit_empty|in_list[user,admin]'
        ]);

        // Validate required fields
        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
            return [
                'success' => false,
                'message' => 'Semua field harus diisi'
            ];
        }

        // Set default role if not specified
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }

        // ✅ FIX: Ensure is_active is properly set as integer
        if (!isset($data['is_active'])) {
            $data['is_active'] = 1; // Default to active
        } else {
            $data['is_active'] = (int) $data['is_active'];
        }

        // Hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);

        try {
            $userId = $this->insert($data);

            if ($userId) {
                return [
                    'success' => true,
                    'user_id' => $userId,
                    'message' => 'User berhasil dibuat'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal membuat user',
                    'errors' => $this->errors()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ FIXED: Update user profile with NO automatic validation
     */
    public function updateProfile($userId, $data)
    {
        try {
            log_message('info', '=== UserModel updateProfile START ===');
            log_message('info', 'User ID: ' . $userId);
            log_message('info', 'Update data: ' . json_encode($data));

            // Validate user exists
            $existingUser = $this->find($userId);
            if (!$existingUser) {
                log_message('error', 'User not found for ID: ' . $userId);
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ];
            }

            log_message('info', 'Existing user found: ' . $existingUser['username']);

            // ✅ FIX: DISABLE validation for profile updates to avoid conflicts
            $originalValidationRules = $this->validationRules;
            $this->validationRules = []; // Temporarily disable validation

            // Remove password from data if empty
            if (isset($data['password']) && empty($data['password'])) {
                unset($data['password']);
                log_message('info', 'Empty password removed from update data');
            } else if (isset($data['password'])) {
                // Hash new password
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                unset($data['password']);
                log_message('info', 'Password hashed and replaced in update data');
            }

            // ✅ FIX: Ensure is_active is properly converted to integer
            if (isset($data['is_active'])) {
                $data['is_active'] = (int) $data['is_active'];
                log_message('info', 'is_active converted to integer: ' . $data['is_active']);
            }

            // ✅ MANUAL VALIDATION: Check email uniqueness
            if (!empty($data['email'])) {
                $emailExists = $this->where('email', $data['email'])
                    ->where('id !=', $userId)
                    ->first();

                if ($emailExists) {
                    log_message('error', 'Email already exists: ' . $data['email']);

                    // Restore validation rules
                    $this->validationRules = $originalValidationRules;

                    return [
                        'success' => false,
                        'message' => 'Email sudah digunakan oleh user lain',
                        'errors' => ['email' => 'Email sudah digunakan']
                    ];
                }
                log_message('info', 'Email uniqueness check passed');
            }

            // ✅ MANUAL VALIDATION: Check required fields
            if (isset($data['full_name']) && empty($data['full_name'])) {
                $this->validationRules = $originalValidationRules;
                return [
                    'success' => false,
                    'message' => 'Nama lengkap harus diisi',
                    'errors' => ['full_name' => 'Nama lengkap harus diisi']
                ];
            }

            if (isset($data['email']) && (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
                $this->validationRules = $originalValidationRules;
                return [
                    'success' => false,
                    'message' => 'Email tidak valid',
                    'errors' => ['email' => 'Email tidak valid']
                ];
            }

            log_message('info', 'Manual validation passed');

            // ✅ FIX: Use direct database update to bypass model validation completely
            $db = \Config\Database::connect();
            $result = $db->table($this->table)
                ->where('id', $userId)
                ->update($data);

            log_message('info', 'Database update result: ' . ($result ? 'success' : 'failed'));

            // Restore validation rules
            $this->validationRules = $originalValidationRules;

            if ($result) {
                log_message('info', '=== UserModel updateProfile SUCCESS ===');
                return [
                    'success' => true,
                    'message' => 'Profile berhasil diupdate'
                ];
            } else {
                log_message('error', 'Database update failed');

                return [
                    'success' => false,
                    'message' => 'Gagal update profile di database'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', '=== UserModel updateProfile EXCEPTION ===');
            log_message('error', 'Exception: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ':' . $e->getLine());

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug_file' => $e->getFile(),
                'debug_line' => $e->getLine()
            ];
        }
    }

    /**
     * ✅ NEW: Get all users with proper boolean conversion for admin
     */
    public function getAllUsersForAdmin()
    {
        $users = $this->orderBy('created_at', 'DESC')->findAll();

        // ✅ FIX: Ensure proper boolean conversion
        foreach ($users as &$user) {
            // Convert is_active to proper integer for consistency
            $user['is_active'] = (int) $user['is_active'];
            // Remove password hash for security
            unset($user['password_hash']);
        }

        return $users;
    }

    /**
     * ✅ NEW: Toggle user status
     */
    public function toggleUserStatus($userId)
    {
        $user = $this->find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User tidak ditemukan'
            ];
        }

        $newStatus = $user['is_active'] == 1 ? 0 : 1;

        // ✅ FIX: Use direct database update to avoid validation conflicts
        $db = \Config\Database::connect();
        $result = $db->table($this->table)
            ->where('id', $userId)
            ->update(['is_active' => $newStatus]);

        if ($result) {
            return [
                'success' => true,
                'new_status' => $newStatus,
                'message' => 'Status user berhasil diubah'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Gagal mengubah status user'
            ];
        }
    }
}
