<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
            /* Kurangi dari 20px ke 15px */
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 20px 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2em;
            /* Kurangi dari 2.5em ke 2em */
            background: linear-gradient(45deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .header p {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 0.9em;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #495057);
        }

        .btn-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800);
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 15px 20px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 70px;
        }

        .stat-card .icon {
            font-size: 2em;
            margin-bottom: 0;
            opacity: 0.8;
            flex-shrink: 0;
        }

        .stat-card .number {
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 0;
            line-height: 1;
        }

        .stat-card .label {
            color: #6c757d;
            font-size: 0.8em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
            margin: 0;
        }

        .stat-card .content {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .stat-card.users {
            color: #007bff;
        }

        .stat-card.active {
            color: #28a745;
        }

        /* Main Content */
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            /* Kurangi dari 15px ke 12px */
            padding: 20px 25px;
            /* Kurangi dari 30px ke 20px 25px */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            /* Kurangi shadow */
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            /* Kurangi dari 25px ke 15px */
            border-bottom: 1px solid #f8f9fa;
            /* Kurangi dari 2px ke 1px */
            padding-bottom: 10px;
            /* Kurangi dari 15px ke 10px */
        }

        .content-header h2 {
            font-size: 1.4em;
            margin: 0;
            color: #2c3e50;
        }

        .search-controls {
            display: flex;
            gap: 12px;
            /* Kurangi dari 15px ke 12px */
            margin-bottom: 15px;
            /* Kurangi dari 20px ke 15px */
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 200px;
            /* Tambahkan min-width */
            padding: 10px 12px;
            /* Kurangi dari 12px 15px ke 10px 12px */
            border: 1px solid #e9ecef;
            /* Kurangi dari 2px ke 1px */
            border-radius: 8px;
            /* Kurangi dari 10px ke 8px */
            font-size: 0.9em;
            /* Kurangi dari 1em ke 0.9em */
        }

        .search-box:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .filter-select {
            padding: 10px 12px;
            /* Kurangi dari 12px 15px ke 10px 12px */
            border: 1px solid #e9ecef;
            /* Kurangi dari 2px ke 1px */
            border-radius: 8px;
            /* Kurangi dari 10px ke 8px */
            font-size: 0.9em;
            /* Kurangi dari 1em ke 0.9em */
            min-width: 130px;
            /* Kurangi dari 150px ke 130px */
        }

        /* Users Table */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            /* Kurangi dari 20px ke 10px */
        }

        .users-table th,
        .users-table td {
            padding: 10px 12px;
            /* Kurangi dari 15px ke 10px 12px */
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .users-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 0.85em;
            /* Kurangi dari 0.9em ke 0.85em */
            text-transform: uppercase;
            letter-spacing: 0.3px;
            /* Kurangi dari 0.5px ke 0.3px */
        }

        .users-table tr:hover {
            background: #f8f9fa;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-details h4 {
            margin: 0;
            color: #2c3e50;
        }

        .user-details p {
            margin: 2px 0 0 0;
            color: #6c757d;
            font-size: 0.9em;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .role-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .role-admin {
            background: #ffd700;
            color: #856404;
        }

        .role-user {
            background: #d4edda;
            color: #155724;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8em;
            border-radius: 15px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 15px;
        }

        .modal-title {
            font-size: 1.5em;
            color: #2c3e50;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            color: #6c757d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #cce7ff;
            color: #004085;
            border: 1px solid #b3d9ff;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 12px;
                /* Kurangi dari 15px ke 12px */
                padding: 20px 25px;
                /* Kurangi dari 30px ke 20px 25px */
                margin-bottom: 20px;
                /* Kurangi dari 30px ke 20px */
                box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
                /* Kurangi shadow */
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .search-controls {
                flex-direction: column;
            }

            .users-table {
                font-size: 0.9em;
            }

            .users-table th,
            .users-table td {
                padding: 8px 10px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1><i class="fas fa-users-cog"></i> User Management</h1>
                <p>Kelola user dan akses sistem</p>
            </div>
            <div class="nav-buttons">
                <a href="<?= base_url('admin') ?>" class="btn btn-secondary">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="<?= base_url('admin/documents') ?>" class="btn btn-secondary">
                    <i class="fas fa-file-alt"></i> Dokumen
                </a>
                <button class="btn btn-danger" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card users">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div class="content">
                    <div class="number" id="totalUsers">-</div>
                    <div class="label">Total User</div>
                </div>
            </div>
            <div class="stat-card active">
                <div class="icon"><i class="fas fa-user-check"></i></div>
                <div class="content">
                    <div class="number" id="activeUsers">-</div>
                    <div class="label">User Aktif</div>
                </div>
            </div>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h2><i class="fas fa-users"></i> Daftar User</h2>
                <div>
                    <button class="btn btn-success" onclick="showAddUserModal()">
                        <i class="fas fa-user-plus"></i> Tambah User Baru
                    </button>
                    <button class="btn" onclick="exportUsers()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    <button class="btn btn-secondary" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Search & Filter Controls -->
            <div class="search-controls">
                <input type="text" class="search-box" id="searchInput" placeholder="Cari User Berdasarkan Nama, Email dan Username" onkeyup="filterUsers()">
                <select class="filter-select" id="roleFilter" onchange="filterUsers()">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <select class="filter-select" id="statusFilter" onchange="filterUsers()">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <!-- Users Table -->
            <div id="usersTableContainer">
                <div class="loading">
                    <div class="spinner"></div> Loading users...
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Add New User</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form id="userForm" onsubmit="saveUser(event)">
                <input type="hidden" id="userId" value="">

                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" class="form-control" id="fullName" required>
                </div>

                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" class="form-control" id="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" class="form-control" id="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" class="form-control" id="password">
                    <small id="passwordHint" style="color: #6c757d;">Leave blank to keep current password</small>
                </div>

                <div class="form-group">
                    <label for="role">Role *</label>
                    <select class="form-control" id="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="isActive">Status *</label>
                    <select class="form-control" id="isActive" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div style="text-align: right; margin-top: 25px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <i class="fas fa-save"></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Detail Modal -->
    <div id="userDetailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">User Details</h3>
                <button class="close-btn" onclick="closeDetailModal()">&times;</button>
            </div>
            <div id="userDetailContent">
                <!-- User details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // ✅ FIXED: Use PHP to generate correct API base URL
        const API_BASE = <?= json_encode(base_url('api/')) ?>;
        let allUsers = [];
        let filteredUsers = [];


        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing...');
            loadUserData();
        });

        // ✅ ENHANCED: Load user data with better error handling
        async function loadUserData() {
            try {
                console.log('Loading user data...');
                //showAlert('Loading user data...', 'info');

                const response = await fetch(API_BASE + 'admin/users');
                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                console.log('API Response:', result);

                if (result.status === 'success') {
                    allUsers = result.data;
                    console.log('Users loaded:', allUsers.length);

                    if (allUsers.length > 0) {
                        console.log('Sample user:', allUsers[0]);
                    }

                    // ✅ FIX: Ensure is_active is properly converted
                    allUsers.forEach(user => {
                        user.is_active = parseInt(user.is_active) || 0;
                    });

                    filteredUsers = [...allUsers];
                    displayUsers(filteredUsers);
                    loadStatistics();
                    //showAlert('Users loaded successfully!', 'success');
                } else {
                    console.error('API Error:', result.message);
                    showAlert('Failed to load users: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Failed to load users:', error);
                showAlert('Failed to load users. Click Debug API for details.', 'error');
            }
        }

        async function loadStatistics() {
            try {
                if (allUsers.length === 0) return;

                const nonAdminUsers = allUsers.filter(u => u.role !== 'admin');
                const totalUsers = nonAdminUsers.length;
                const activeUsers = nonAdminUsers.filter(u => u.is_active == 1).length;

                // Update UI - hanya 2 cards
                document.getElementById('totalUsers').textContent = totalUsers;
                document.getElementById('activeUsers').textContent = activeUsers;

                console.log('Statistics updated:', {
                    total: totalUsers,
                    active: activeUsers
                });
            } catch (error) {
                console.error('Failed to load statistics:', error);
            }
        }

        // ✅ ENHANCED: Display users table with proper status handling
        function displayUsers(users) {
            const container = document.getElementById('usersTableContainer');

            if (users.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 40px;">User Tidak Ditemukan.</p>';
                return;
            }

            const tableHTML = `
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Dokumen</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${users.map(user => {
                            // ✅ FIX: Proper boolean conversion for each user
                            const isActive = user.is_active == 1 || user.is_active === true || user.is_active === 'true';
                            
                            return `
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            ${user.full_name.charAt(0).toUpperCase()}
                                        </div>
                                        <div class="user-details">
                                            <h4>${user.full_name}</h4>
                                            <p>@${user.username}</p>
                                            <p>${user.email}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge role-${user.role}">
                                        ${user.role === 'admin' ? 'Administrator' : 'User'}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge ${isActive ? 'status-active' : 'status-inactive'}">
                                        ${isActive ? 'Aktif' : 'Nonaktif'}
                                        
                                    </span>
                                </td>
                                <td>
                                    <strong>${user.total_documents || 0}</strong> Dokumen
                                    ${user.signed_documents ? `<br><small>${user.signed_documents} Ditandatangani</small>` : ''}
                                </td>
                                <td>${new Date(user.created_at).toLocaleDateString('id-ID')}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm" onclick="viewUser(${user.id})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="editUser(${user.id})" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        ${user.role !== 'admin' ? `
                                            <button class="btn btn-sm ${isActive ? 'btn-secondary' : 'btn-success'}" 
                                                onclick="toggleUserStatus(${user.id})" 
                                                title="${isActive ? 'Deactivate' : 'Activate'} User">
                                            <i class="fas fa-${isActive ? 'user-slash' : 'user-check'}"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id}, '${user.full_name}')" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                        }).join('')}
                    </tbody>
                </table>
            `;

            container.innerHTML = tableHTML;
        }

        // ✅ FIXED: View user function with enhanced debugging
        function viewUser(userId) {
            console.log('=== VIEW USER DEBUG ===');
            console.log('userId:', userId, typeof userId);
            console.log('allUsers length:', allUsers.length);

            if (allUsers.length > 0) {
                console.log('allUsers sample:', allUsers[0]);
            }

            const user = allUsers.find(u => {
                console.log(`Checking user ${u.id} (${typeof u.id}) === ${userId} (${typeof userId})`);
                return u.id == userId; // Use == for type coercion
            });

            console.log('Found user:', user);

            if (!user) {
                console.error('User not found!');
                showAlert('User not found', 'error');
                return;
            }

            // ✅ FIX: Ensure proper boolean conversion for display
            const isActive = user.is_active == 1 || user.is_active === true || user.is_active === 'true';
            console.log('User is_active:', user.is_active, 'converted to:', isActive);

            const content = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <p><strong>ID:</strong> ${user.id}</p>
                        <p><strong>Full Name:</strong> ${user.full_name}</p>
                        <p><strong>Username:</strong> ${user.username}</p>
                        <p><strong>Email:</strong> ${user.email}</p>
                        <p><strong>Role:</strong> 
                            <span class="role-badge role-${user.role}">
                                ${user.role === 'admin' ? 'Administrator' : 'User'}
                            </span>
                        </p>
                        <p><strong>Status:</strong> 
                            <span class="status-badge ${isActive ? 'status-active' : 'status-inactive'}">
                                ${isActive ? 'Aktif' : 'Nonaktif'}
                            </span>
                        </p>
                        <p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString('id-ID')}</p>
                    </div>
                    
                    <div>
                        <h4 style="color: #495057; margin-bottom: 10px;">Activity Statistics</h4>
                        <p><strong>Total Documents:</strong> ${user.total_documents || 0}</p>
                        <p><strong>Signed Documents:</strong> ${user.signed_documents || 0}</p>
                        <p><strong>Pending Documents:</strong> ${(user.total_documents || 0) - (user.signed_documents || 0)}</p>
                        <p><strong>Last Upload:</strong> ${user.last_upload ? new Date(user.last_upload).toLocaleDateString('id-ID') : 'Never'}</p>
                    </div>
                </div>
                
            `;

            document.getElementById('userDetailContent').innerHTML = content;
            document.getElementById('userDetailModal').classList.add('show');
        }

        // Filter users
        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;

            filteredUsers = allUsers.filter(user => {
                const matchesSearch = !searchTerm ||
                    user.full_name.toLowerCase().includes(searchTerm) ||
                    user.username.toLowerCase().includes(searchTerm) ||
                    user.email.toLowerCase().includes(searchTerm);

                const matchesRole = !roleFilter || user.role === roleFilter;
                const matchesStatus = statusFilter === '' || user.is_active == statusFilter;

                return matchesSearch && matchesRole && matchesStatus;
            });

            displayUsers(filteredUsers);
        }

        // Show add user modal
        function showAddUserModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userId').value = '';
            document.getElementById('userForm').reset();
            document.getElementById('passwordHint').style.display = 'none';
            document.getElementById('password').required = true;
            document.getElementById('userModal').classList.add('show');
        }

        // ✅ FIXED: Edit user function with proper debugging
        function editUser(userId) {
            console.log('=== EDIT USER DEBUG ===');
            console.log('userId:', userId, typeof userId);
            console.log('allUsers length:', allUsers.length);

            if (allUsers.length > 0) {
                console.log('allUsers sample:', allUsers[0]);
            }

            const user = allUsers.find(u => {
                console.log(`Checking user ${u.id} (${typeof u.id}) === ${userId} (${typeof userId})`);
                return u.id == userId; // Use == for type coercion
            });

            console.log('Found user:', user);

            if (!user) {
                console.error('User not found!');
                showAlert('User not found', 'error');
                return;
            }

            // ✅ FIX: Set modal title and form values
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = user.id;
            document.getElementById('fullName').value = user.full_name || '';
            document.getElementById('username').value = user.username || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('password').value = ''; // Always empty for security
            document.getElementById('role').value = user.role || 'user';

            // ✅ FIX: Ensure proper boolean conversion for is_active
            const isActive = user.is_active == 1 || user.is_active === true || user.is_active === 'true';
            document.getElementById('isActive').value = isActive ? '1' : '0';

            console.log('User is_active:', user.is_active, 'converted to:', isActive ? '1' : '0');

            // Show password hint and make password optional
            const passwordHint = document.getElementById('passwordHint');
            if (passwordHint) {
                passwordHint.style.display = 'block';
            }
            const passwordField = document.getElementById('password');
            if (passwordField) {
                passwordField.required = false;
            }

            // Show modal
            document.getElementById('userModal').classList.add('show');

            console.log('Modal opened for editing user:', user.username);
        }

        // ✅ ENHANCED: Save user with better debugging and error handling
        async function saveUser(event) {
            event.preventDefault();

            const userId = document.getElementById('userId').value;
            const isEdit = userId !== '';

            console.log('=== SAVE USER DEBUG ===');
            console.log('userId:', userId);
            console.log('isEdit:', isEdit);

            const userData = {
                full_name: document.getElementById('fullName').value.trim(),
                username: document.getElementById('username').value.trim(),
                email: document.getElementById('email').value.trim(),
                role: document.getElementById('role').value,
                is_active: parseInt(document.getElementById('isActive').value)
            };

            const password = document.getElementById('password').value.trim();
            if (password) {
                userData.password = password;
            }

            console.log('User data to save:', userData);

            // Client-side validation
            if (!userData.full_name || !userData.username || !userData.email) {
                showAlert('Please fill in all required fields', 'error');
                return;
            }

            if (!isEdit && !password) {
                showAlert('Password is required for new users', 'error');
                return;
            }

            const saveBtn = document.getElementById('saveBtn');
            const originalText = saveBtn.innerHTML;

            try {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

                const url = isEdit ? `${API_BASE}admin/users/${userId}` : `${API_BASE}admin/users`;
                const method = isEdit ? 'PUT' : 'POST';

                console.log('API Request:', method, url);
                console.log('Request body:', JSON.stringify(userData));

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('HTTP Error:', response.status, errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }

                const result = await response.json();
                console.log('Save result:', result);

                if (result.status === 'success') {
                    showAlert(isEdit ? 'User updated successfully!' : 'User created successfully!', 'success');
                    closeModal();
                    await loadUserData(); // Reload the user list
                } else {
                    console.error('Save failed:', result);
                    let errorMessage = result.message || 'Failed to save user';

                    // Handle validation errors
                    if (result.errors) {
                        errorMessage += ':\n' + Object.values(result.errors).join('\n');
                    }

                    showAlert(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Save error:', error);
                showAlert('Error: ' + error.message, 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        }

        // ✅ ENHANCED: Toggle user status with better debugging
        async function toggleUserStatus(userId) {
            console.log('Toggling status for user:', userId);

            try {
                const response = await fetch(`${API_BASE}admin/users/${userId}/status`, {
                    method: 'PATCH'
                });

                console.log('Toggle response status:', response.status);
                const result = await response.json();
                console.log('Toggle result:', result);

                if (result.status === 'success') {
                    showAlert('User status updated!', 'success');
                    await loadUserData();
                } else {
                    console.error('Toggle failed:', result);
                    showAlert(result.message || 'Failed to update status', 'error');
                }
            } catch (error) {
                console.error('Toggle error:', error);
                showAlert('Error: ' + error.message, 'error');
            }
        }

        // Delete user
        function deleteUser(userId, userName) {
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                performDeleteUser(userId);
            }
        }

        async function performDeleteUser(userId) {
            try {
                const response = await fetch(`${API_BASE}admin/users/${userId}`, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('User deleted successfully!', 'success');
                    await loadUserData();
                } else {
                    showAlert(result.message || 'Failed to delete user', 'error');
                }
            } catch (error) {
                showAlert('Error: ' + error.message, 'error');
            }
        }

        // Export users to CSV
        function exportUsers() {
            const csvContent = "data:text/csv;charset=utf-8," +
                "Full Name,Username,Email,Role,Status,Documents,Joined\n" +
                filteredUsers.map(user =>
                    `"${user.full_name}","${user.username}","${user.email}","${user.role}","${user.is_active == 1 ? 'Active' : 'Inactive'}","${user.total_documents || 0}","${new Date(user.created_at).toLocaleDateString('id-ID')}"`
                ).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `users_export_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showAlert('Users exported successfully!', 'success');
        }

        // Refresh data
        async function refreshData() {
            //showAlert('Refreshing data...', 'info');
            await loadUserData();
            //showAlert('Data refreshed successfully!', 'success');
        }

        // Close modals
        function closeModal() {
            document.getElementById('userModal').classList.remove('show');
        }

        function closeDetailModal() {
            document.getElementById('userDetailModal').classList.remove('show');
        }

        // Show alert
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = `alert-${type}`;
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    ${message}
                </div>
            `;

            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        // Logout
        async function logout() {
            try {
                await fetch(API_BASE + 'auth/logout', {
                    method: 'POST'
                });
                window.location.href = '<?= base_url('auth') ?>';
            } catch (error) {
                window.location.href = '<?= base_url('auth') ?>';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.classList.remove('show');
                }
            });
        }
    </script>
</body>

</html>