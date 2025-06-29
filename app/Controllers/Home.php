<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('is_logged_in')) {
            // Redirect to login page if not logged in
            return redirect()->to('/auth');
        }

        // ✅ Auto redirect admin to dashboard
        $userRole = session()->get('role');
        if ($userRole === 'admin') {
            return redirect()->to('/admin');
        }

        return view('home');
    }

    public function auth()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to('/');
        }

        return view('auth');
    }

    public function verify($code = null)
    {
        if (!$code) {
            return redirect()->to('/')->with('error', 'Kode verifikasi tidak valid');
        }

        $signatureModel = new \App\Models\SignatureModel();
        $signature = $signatureModel->getByVerificationCode($code);

        if (!$signature) {
            return view('verify_error', ['code' => $code]);
        }

        return view('verify_success', ['signature' => $signature]);
    }

    // ✅ NEW: User Profile Page
    public function profile()
    {
        // Check if user is logged in
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $userRole = session()->get('role');

        // Redirect admin to admin panel (admin should not access user profile)
        if ($userRole === 'admin') {
            return redirect()->to('/admin')->with('error', 'Admin tidak memiliki akses ke halaman profil user.');
        }

        return view('user_profile');
    }

    public function adminDashboard()
    {
        // Check if user is logged in and is admin
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        return view('admin_dashboard');
    }

    // ✅ NEW: User Management Page
    public function userManagement()
    {
        // Check if user is logged in and is admin
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        return view('user_management');
    }

    // ✅ NEW: Document Management Page
    public function documentManagement()
    {
        // Check if user is logged in and is admin
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $userRole = session()->get('role');
        if ($userRole !== 'admin') {
            return redirect()->to('/admin')->with('error', 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        return view('document_management');
    }
}
