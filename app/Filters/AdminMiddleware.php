<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminMiddleware implements FilterInterface
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

        // Check if user is admin
        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Forbidden. Admin access required.',
                'code' => 403
            ])->setStatusCode(403);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
