<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class AuthController extends ResourceController
{
    protected $userModel;
    protected $auditLogModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
        helper(['cookie']);
    }

    // POST /api/auth/login
    public function login()
    {
        try {
            $json = $this->request->getJSON(true);

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => 'required|min_length[3]',
                'password' => 'required|min_length[6]'
            ]);

            if (!$validation->run($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'errors' => $validation->getErrors()
                ]);
            }

            // Verify login
            $user = $this->userModel->verifyLogin($json['username'], $json['password']);

            if (!$user) {
                // Log failed login attempt
                $this->auditLogModel->logActivity(
                    'login_failed',
                    null,
                    null,
                    'Failed login attempt for username: ' . $json['username'],
                    $this->request
                );

                return $this->fail([
                    'status' => 'error',
                    'message' => 'Username atau password salah'
                ]);
            }

            // Create session
            $sessionData = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'is_logged_in' => true
            ];

            session()->set($sessionData);

            // Log successful login
            $this->auditLogModel->logActivity(
                'login',
                $user['id'],
                null,
                'User logged in successfully',
                $this->request
            );

            return $this->respond([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'full_name' => $user['full_name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            return $this->fail('Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // POST /api/auth/register
    public function register()
    {
        try {
            $json = $this->request->getJSON(true);

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'username' => 'required|alpha_numeric|min_length[3]|max_length[50]|is_unique[users.username]',
                'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'confirm_password' => 'required|matches[password]',
                'full_name' => 'required|max_length[100]'
            ]);

            if (!$validation->run($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'errors' => $validation->getErrors()
                ]);
            }

            // Create user
            $userData = [
                'username' => $json['username'],
                'email' => $json['email'],
                'password' => $json['password'], // Will be hashed by model
                'full_name' => $json['full_name'],
                'role' => 'user', // Default role
                'is_active' => 1
            ];

            $result = $this->userModel->createUser($userData);

            if (!$result['success']) {
                return $this->fail([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null
                ]);
            }

            // Log user registration
            $this->auditLogModel->logActivity(
                'register',
                $result['user_id'],
                null,
                'New user registered: ' . $json['username'],
                $this->request
            );

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Registrasi berhasil. Silakan login.',
                'data' => [
                    'user_id' => $result['user_id']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Register error: ' . $e->getMessage());
            return $this->fail('Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // POST /api/auth/logout
    public function logout()
    {
        try {
            $userId = session()->get('user_id');

            if ($userId) {
                // Log logout
                $this->auditLogModel->logActivity(
                    'logout',
                    $userId,
                    null,
                    'User logged out',
                    $this->request
                );
            }

            // Destroy session
            session()->destroy();

            return $this->respond([
                'status' => 'success',
                'message' => 'Logout berhasil'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Logout error: ' . $e->getMessage());
            return $this->fail('Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // GET /api/auth/me
    public function me()
    {
        try {
            $userId = session()->get('user_id');

            if (!$userId) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Tidak ada sesi aktif'
                ]);
            }

            $user = $this->userModel->find($userId);

            if (!$user) {
                session()->destroy();
                return $this->fail([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ]);
            }

            // Remove sensitive data
            unset($user['password_hash']);

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'user' => $user,
                    'session' => [
                        'is_logged_in' => true,
                        'role' => $user['role']
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get user info error: ' . $e->getMessage());
            return $this->fail('Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // GET /api/auth/check
    public function check()
    {
        try {
            $isLoggedIn = session()->get('is_logged_in');
            $userId = session()->get('user_id');
            $role = session()->get('role');

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'is_logged_in' => (bool)$isLoggedIn,
                    'user_id' => $userId,
                    'role' => $role,
                    'username' => session()->get('username'),
                    'full_name' => session()->get('full_name')
                ]
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'is_logged_in' => false
                ]
            ]);
        }
    }

    // ✅ NEW: GET /api/auth/profile - Get current user profile
    public function getProfile()
    {
        try {
            $userId = session()->get('user_id');
            $userRole = session()->get('role');

            // Only regular users can access profile (not admin)
            if ($userRole === 'admin') {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Admin tidak memiliki akses ke profil user'
                ], 403);
            }

            if (!$userId) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'User tidak teridentifikasi'
                ]);
            }

            $user = $this->userModel->find($userId);

            if (!$user) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ]);
            }

            // Remove sensitive data
            unset($user['password_hash']);

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'user' => $user
                ],
                'message' => 'Data profil berhasil diambil'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get profile error: ' . $e->getMessage());
            return $this->fail('Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // ✅ FIXED: PUT /api/auth/profile - Update current user profile
    public function updateProfile()
    {
        try {
            $userId = session()->get('user_id');
            $userRole = session()->get('role');

            // Debug logging
            log_message('info', '=== PROFILE UPDATE START ===');
            log_message('info', 'User ID: ' . $userId);
            log_message('info', 'User Role: ' . $userRole);

            // Only regular users can update profile (not admin)
            if ($userRole === 'admin') {
                log_message('error', 'Admin tried to access user profile');
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Admin tidak memiliki akses ke profil user'
                ], 403);
            }

            if (!$userId) {
                log_message('error', 'No user ID in session');
                return $this->fail([
                    'status' => 'error',
                    'message' => 'User tidak teridentifikasi'
                ]);
            }

            $json = $this->request->getJSON(true);
            log_message('info', 'Request data: ' . json_encode($json));

            // ✅ FIX: Simple manual validation to avoid model conflicts
            $errors = [];

            // Check required fields
            if (empty($json['full_name'])) {
                $errors['full_name'] = 'Nama lengkap harus diisi';
            }

            if (empty($json['email'])) {
                $errors['email'] = 'Email harus diisi';
            } elseif (!filter_var($json['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Format email tidak valid';
            }

            if (empty($json['current_password'])) {
                $errors['current_password'] = 'Password saat ini harus diisi';
            }

            // Check password match if new password provided
            if (!empty($json['new_password'])) {
                if (strlen($json['new_password']) < 6) {
                    $errors['new_password'] = 'Password baru minimal 6 karakter';
                }

                if ($json['new_password'] !== ($json['confirm_password'] ?? '')) {
                    $errors['confirm_password'] = 'Konfirmasi password tidak sesuai';
                }
            }

            if (!empty($errors)) {
                log_message('error', 'Manual validation failed: ' . json_encode($errors));
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'errors' => $errors
                ]);
            }

            // Get current user data
            $currentUser = $this->userModel->find($userId);
            if (!$currentUser) {
                log_message('error', 'User not found: ' . $userId);
                return $this->fail([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ]);
            }

            log_message('info', 'Current user found: ' . $currentUser['username']);

            // Verify current password
            if (!password_verify($json['current_password'], $currentUser['password_hash'])) {
                log_message('error', 'Invalid current password for user: ' . $userId);
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Password saat ini tidak benar'
                ]);
            }

            log_message('info', 'Current password verified');

            // ✅ FIX: Check email uniqueness manually
            if ($json['email'] !== $currentUser['email']) {
                $emailExists = $this->userModel->where('email', $json['email'])
                    ->where('id !=', $userId)
                    ->first();
                if ($emailExists) {
                    log_message('error', 'Email already exists: ' . $json['email']);
                    return $this->fail([
                        'status' => 'error',
                        'message' => 'Email sudah digunakan oleh user lain',
                        'errors' => ['email' => 'Email sudah digunakan']
                    ]);
                }
                log_message('info', 'Email uniqueness check passed');
            }

            // Prepare update data
            $updateData = [
                'full_name' => $json['full_name'],
                'email' => $json['email']
            ];

            // Only update password if new password provided
            if (!empty($json['new_password'])) {
                $updateData['password'] = $json['new_password']; // Will be hashed by model
                log_message('info', 'Including new password in update');
            }

            log_message('info', 'Update data prepared: ' . json_encode(array_keys($updateData)));

            // ✅ FIX: Use updateProfile method that bypasses model validation
            $result = $this->userModel->updateProfile($userId, $updateData);

            log_message('info', 'UserModel updateProfile result: ' . json_encode($result));

            if (!$result['success']) {
                log_message('error', 'UserModel updateProfile failed: ' . $result['message']);

                return $this->fail([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null
                ]);
            }

            // Update session data if email or full_name changed
            if ($json['email'] !== $currentUser['email']) {
                session()->set('email', $json['email']);
                log_message('info', 'Session email updated');
            }
            if ($json['full_name'] !== $currentUser['full_name']) {
                session()->set('full_name', $json['full_name']);
                log_message('info', 'Session full_name updated');
            }

            // Log profile update activity
            $this->auditLogModel->logActivity(
                'profile_update',
                $userId,
                null,
                'User updated profile: ' . $json['full_name'],
                $this->request
            );

            log_message('info', '=== PROFILE UPDATE SUCCESS ===');

            return $this->respond([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'updated_fields' => array_keys($updateData),
                    'user_id' => $userId
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', '=== PROFILE UPDATE EXCEPTION ===');
            log_message('error', 'Exception: ' . $e->getMessage());
            log_message('error', 'File: ' . $e->getFile() . ':' . $e->getLine());
            log_message('error', 'Trace: ' . $e->getTraceAsString());

            return $this->fail([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }
}
