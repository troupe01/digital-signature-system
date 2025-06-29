<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// Main routes
$routes->get('/', 'Home::index');
$routes->get('/auth', 'Home::auth');
$routes->get('/verify/(:any)', 'Home::verify/$1');

// ✅ NEW: User Profile Route
$routes->get('/profile', 'Home::profile');

// ✅ UPDATED: Admin routes
$routes->get('/admin', 'Home::adminDashboard');
$routes->get('/admin/users', 'Home::userManagement');        // ← NEW
$routes->get('/admin/documents', 'Home::documentManagement'); // ← NEW

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->options('(:any)', 'Options::index');

    // Authentication routes
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/register', 'AuthController::register');
    $routes->get('auth/check', 'AuthController::check');
    $routes->get('auth/me', 'AuthController::me');
    $routes->post('auth/logout', 'AuthController::logout');


    // ✅ NEW: Profile API routes
    $routes->get('auth/profile', 'AuthController::getProfile');
    $routes->put('auth/profile', 'AuthController::updateProfile');

    // Public verification
    $routes->get('documents/verify/(:any)', 'DocumentController::verify/$1');
    $routes->get('documents/qr/(:any)', 'DocumentController::getQRCode/$1');
    $routes->get('documents/public-download/(:any)', 'DocumentController::publicDownload/$1');

    // Document management 
    $routes->get('documents', 'DocumentController::index');
    $routes->post('documents/upload', 'DocumentController::upload');
    $routes->post('documents/sign', 'DocumentController::sign');
    $routes->get('documents/download/(:num)', 'DocumentController::download/$1');
    $routes->delete('documents/(:num)', 'DocumentController::delete/$1');

    // Admin statistics and activity routes
    $routes->get('admin/stats', 'DocumentController::getAdminStats');
    $routes->get('admin/activity-logs', 'DocumentController::getActivityLogs');

    // ✅ USER MANAGEMENT API ROUTES - NEW CRUD OPERATIONS
    $routes->get('admin/users', 'AdminController::getUsers');
    $routes->post('admin/users', 'AdminController::createUser');
    $routes->get('admin/users/(:num)', 'AdminController::getUser/$1');
    $routes->put('admin/users/(:num)', 'AdminController::updateUser/$1');
    $routes->patch('admin/users/(:num)/status', 'AdminController::updateUserStatus/$1');
    $routes->delete('admin/users/(:num)', 'AdminController::deleteUser/$1');
});
