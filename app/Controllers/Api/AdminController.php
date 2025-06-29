<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\DocumentModel;
use App\Models\AuditLogModel;

class AdminController extends ResourceController
{
    protected $userModel;
    protected $documentModel;
    protected $auditLogModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->documentModel = new DocumentModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * Check if current user is admin
     */
    private function checkAdminAccess()
    {
        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return $this->fail('Access denied. Admin privileges required.', 403);
        }
        return true;
    }

    /**
     * ✅ FIXED: GET /api/admin/users - Get all users with proper status (excluding admin from count)
     */
    public function getUsers()
    {
        if ($this->checkAdminAccess() !== true) {
            return $this->checkAdminAccess();
        }

        try {
            // ✅ FIX: Get all users with proper boolean handling
            $users = $this->userModel->getAllUsersForAdmin();

            // Get document statistics for each user
            $userStats = $this->documentModel->getUserStats();

            // Merge user data with statistics
            foreach ($users as &$user) {
                // Find corresponding stats
                $userStat = array_filter($userStats, function ($stat) use ($user) {
                    return $stat['id'] == $user['id'];
                });

                if (!empty($userStat)) {
                    $userStat = array_values($userStat)[0];
                    $user['total_documents'] = (int) ($userStat['total_documents'] ?? 0);
                    $user['signed_documents'] = (int) ($userStat['signed_documents'] ?? 0);
                    $user['pending_documents'] = (int) ($userStat['pending_documents'] ?? 0);
                    $user['last_upload'] = $userStat['last_upload'] ?? null;
                } else {
                    // Default values for users without documents
                    $user['total_documents'] = 0;
                    $user['signed_documents'] = 0;
                    $user['pending_documents'] = 0;
                    $user['last_upload'] = null;
                }
            }

            // ✅ NEW: Calculate counts excluding admin
            $totalUsers = count(array_filter($users, function ($user) {
                return $user['role'] !== 'admin';
            }));

            return $this->respond([
                'status' => 'success',
                'data' => $users,
                'total_non_admin_users' => $totalUsers, // ✅ Add this for dashboard
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get users error: ' . $e->getMessage());
            return $this->fail('Failed to retrieve users: ' . $e->getMessage());
        }
    }

    /**
     * ✅ FIXED: GET /api/admin/users/{id} - Get specific user details
     */
    public function getUser($id = null)
    {
        if ($this->checkAdminAccess() !== true) {
            return $this->checkAdminAccess();
        }

        if (!$id) {
            return $this->fail('User ID is required');
        }

        try {
            $user = $this->userModel->find($id);

            if (!$user) {
                return $this->failNotFound('User not found');
            }

            // Remove password hash and ensure proper boolean conversion
            unset($user['password_hash']);
            $user['is_active'] = (int) $user['is_active'];

            // Get user's document statistics
            $userStats = $this->documentModel->getUserStats();
            $userStat = array_filter($userStats, function ($stat) use ($id) {
                return $stat['id'] == $id;
            });

            if (!empty($userStat)) {
                $userStat = array_values($userStat)[0];
                $user['total_documents'] = (int) ($userStat['total_documents'] ?? 0);
                $user['signed_documents'] = (int) ($userStat['signed_documents'] ?? 0);
                $user['pending_documents'] = (int) ($userStat['pending_documents'] ?? 0);
                $user['last_upload'] = $userStat['last_upload'] ?? null;
            } else {
                $user['total_documents'] = 0;
                $user['signed_documents'] = 0;
                $user['pending_documents'] = 0;
                $user['last_upload'] = null;
            }

            return $this->respond([
                'status' => 'success',
                'data' => $user,
                'message' => 'User retrieved successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Get user error: ' . $e->getMessage());
            return $this->fail('Failed to retrieve user: ' . $e->getMessage());
        }
    }

    /**
     * ✅ FIXED: POST /api/admin/users - Create new user with proper status
     */
    public function createUser()
    {
        if ($this->checkAdminAccess() !== true) {
            return $this->checkAdminAccess();
        }

        try {
            $json = $this->request->getJSON(true);

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'full_name' => 'required|max_length[100]',
                'username' => 'required|alpha_numeric|min_length[3]|max_length[50]|is_unique[users.username]',
                'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'role' => 'required|in_list[user,admin]',
                'is_active' => 'required|in_list[0,1]'
            ]);

            if (!$validation->run($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }

            // ✅ FIX: Ensure proper data types
            $userData = [
                'full_name' => $json['full_name'],
                'username' => $json['username'],
                'email' => $json['email'],
                'password' => $json['password'], // Will be hashed by model
                'role' => $json['role'],
                'is_active' => (int) $json['is_active'] // ✅ Ensure integer
            ];

            $result = $this->userModel->createUser($userData);

            if (!$result['success']) {
                return $this->fail([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null
                ]);
            }

            // Log activity
            $this->auditLogModel->logActivity(
                'user_create',
                session()->get('user_id'),
                null,
                'Admin created new user: ' . $json['username'],
                $this->request
            );

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => [
                    'user_id' => $result['user_id']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Create user error: ' . $e->getMessage());
            return $this->fail('Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * ✅ FIXED: PUT /api/admin/users/{id} - Update user
     */
    public function updateUser($id = null)
    {
        if ($this->checkAdminAccess() !== true) {
            return $this->checkAdminAccess();
        }

        if (!$id) {
            return $this->fail('User ID is required');
        }

        try {
            $json = $this->request->getJSON(true);

            // Check if user exists
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->failNotFound('User not found');
            }

            // Validation rules (username and email unique except current user)
            $validation = \Config\Services::validation();
            $validation->setRules([
                'full_name' => 'required|max_length[100]',
                'username' => "required|alpha_numeric|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
                'email' => "required|valid_email|max_length[100]|is_unique[users.email,id,{$id}]",
                'password' => 'permit_empty|min_length[6]',
                'role' => 'required|in_list[user,admin]',
                'is_active' => 'required|in_list[0,1]'
            ]);

            if (!$validation->run($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ]);
            }

            // ✅ FIX: Prepare update data with proper types
            $updateData = [
                'full_name' => $json['full_name'],
                'username' => $json['username'],
                'email' => $json['email'],
                'role' => $json['role'],
                'is_active' => (int) $json['is_active'] // ✅ Ensure integer
            ];

            // Only update password if provided
            if (!empty($json['password'])) {
                $updateData['password'] = $json['password']; // Will be hashed by model
            }

            $result = $this->userModel->updateProfile($id, $updateData);

            if (!$result['success']) {
                return $this->fail([
                    'status' => 'error',
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null
                ]);
            }

            // Log activity
            $this->auditLogModel->logActivity(
                'user_update',
                session()->get('user_id'),
                null,
                'Admin updated user: ' . $json['username'],
                $this->request
            );

            return $this->respond([
                'status' => 'success',
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update user error: ' . $e->getMessage());
            return $this->fail('Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * ✅ FIXED: PATCH /api/admin/users/{id}/status - Toggle user active status
     */
    public function updateUserStatus($id = null)
    {
        if ($this->checkAdminAccess() !== true) {
            return $this->checkAdminAccess();
        }

        if (!$id) {
            return $this->fail('User ID is required');
        }

        try {
            // Check if user exists
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->failNotFound('User not found');
            }

            // Prevent deactivating the last admin
            if ($user['role'] === 'admin' && $user['is_active'] == 1) {
                $activeAdmins = $this->userModel->where(['role' => 'admin', 'is_active' => 1])->countAllResults();
                if ($activeAdmins <= 1) {
                    return $this->fail('Cannot deactivate the last active admin');
                }
            }

            // ✅ FIX: Use model method for proper status toggle
            $result = $this->userModel->toggleUserStatus($id);

            if (!$result['success']) {
                return $this->fail($result['message']);
            }

            // Log activity
            $this->auditLogModel->logActivity(
                'user_status_change',
                session()->get('user_id'),
                null,
                'Admin ' . ($result['new_status'] ? 'activated' : 'deactivated') . ' user: ' . $user['username'],
                $this->request
            );

            return $this->respond([
                'status' => 'success',
                'message' => 'User status updated successfully',
                'data' => [
                    'new_status' => $result['new_status']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Update user status error: ' . $e->getMessage());
            return $this->fail('Failed to update user status: ' . $e->getMessage());
        }
    }

    /**
     * DELETE /api/admin/users/{id} - Delete user (only non-admin users)
     */
    public function deleteUser($id = null)
    {
        if ($this->checkAdminAccess() !== true) {
            return $this->checkAdminAccess();
        }

        if (!$id) {
            return $this->fail('User ID is required');
        }

        try {
            // Check if user exists
            $user = $this->userModel->find($id);
            if (!$user) {
                return $this->failNotFound('User not found');
            }

            // Prevent deleting admin users
            if ($user['role'] === 'admin') {
                return $this->fail('Cannot delete admin users for security reasons');
            }

            // Prevent deleting current user
            if ($id == session()->get('user_id')) {
                return $this->fail('Cannot delete your own account');
            }

            // Get user's documents for cleanup
            $userDocuments = $this->documentModel->where('user_id', $id)->findAll();

            // Delete user's documents and associated files
            foreach ($userDocuments as $doc) {
                // Delete files
                $this->deleteDocumentFiles($doc);

                // Delete from database (will cascade delete signatures)
                $this->documentModel->delete($doc['id']);
            }

            // Delete user
            $result = $this->userModel->delete($id);

            if (!$result) {
                return $this->fail('Failed to delete user');
            }

            // Log activity
            $this->auditLogModel->logActivity(
                'user_delete',
                session()->get('user_id'),
                null,
                'Admin deleted user: ' . $user['username'],
                $this->request
            );

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'User and associated data deleted successfully',
                'data' => [
                    'deleted_documents' => count($userDocuments)
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Delete user error: ' . $e->getMessage());
            return $this->fail('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to delete document files
     */
    private function deleteDocumentFiles($document)
    {
        try {
            // Delete original file
            if ($document['original_path']) {
                $originalPath = ROOTPATH . $document['original_path'];
                $originalPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $originalPath);
                if (file_exists($originalPath)) {
                    unlink($originalPath);
                }
            }

            // Delete signed file
            if ($document['signed_path']) {
                $signedPath = ROOTPATH . $document['signed_path'];
                $signedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $signedPath);
                if (file_exists($signedPath)) {
                    unlink($signedPath);
                }
            }

            // Delete QR code file if exists
            $signatureModel = new \App\Models\SignatureModel();
            $signature = $signatureModel->where('document_id', $document['id'])->first();
            if ($signature && $signature['qr_code_path']) {
                $qrPath = ROOTPATH . $signature['qr_code_path'];
                $qrPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $qrPath);
                if (file_exists($qrPath)) {
                    unlink($qrPath);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete files for document ID ' . $document['id'] . ': ' . $e->getMessage());
        }
    }
}
