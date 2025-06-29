<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        
        // Check if user is logged in
        if (!session()->get('is_logged_in')) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized. Please login first.',
                'code' => 401
            ])->setStatusCode(401);
        }

        // Check if user still exists and is active
        $userId = session()->get('user_id');
        if ($userId) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($userId);
            
            if (!$user || !$user['is_active']) {
                session()->destroy();
                return $response->setJSON([
                    'status' => 'error',
                    'message' => 'User account is inactive or not found.',
                    'code' => 401
                ])->setStatusCode(401);
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
