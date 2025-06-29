<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Digital Signature System</title>
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
            overflow-x: hidden;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
            /* Kurangi dari 20px */
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ============ HEADER SECTION ============ */
        .dashboard-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px 30px;
            /* Kurangi dari 25px 40px */
            margin-bottom: 15px;
            /* Kurangi dari 20px */
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .header-left h1 {
            color: #2c3e50;
            font-size: 2.2em;
            margin-bottom: 8px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-left p {
            color: #6c757d;
            font-size: 1em;
        }

        .admin-controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
        }

        /* ============ MAIN CONTENT GRID ============ */
        .dashboard-main {
            flex: 1;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            min-height: 0;
        }

        /* ============ LEFT PANEL - STATS ============ */
        .stats-panel {
            display: flex;
            flex-direction: column;
            gap: 15px;
            /* Kurangi dari 20px */
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            /* Kurangi dari 15px */
            padding: 18px;
            /* Kurangi dari 25px */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            /* Kurangi shadow */
            cursor: pointer;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, var(--card-color), var(--card-color-dark));
        }

        .stat-card:hover {
            opacity: 0.9;
        }

        .stat-card .icon {
            font-size: 2em;
            /* Kurangi dari 2.5em */
            margin-bottom: 10px;
            /* Kurangi dari 15px */
            color: var(--card-color);
        }

        .stat-card .number {
            font-size: 2em;
            /* Kurangi dari 2.5em */
            font-weight: bold;
            margin-bottom: 6px;
            /* Kurangi dari 8px */
            color: var(--card-color);
        }

        .stat-card .label {
            color: #6c757d;
            font-size: 0.8em;
            /* Kurangi dari 0.9em */
            text-transform: uppercase;
            letter-spacing: 0.8px;
            /* Kurangi dari 1px */
            font-weight: 500;
        }

        /* Card Color Variations */
        .stat-card.users {
            --card-color: #007bff;
            --card-color-dark: #0056b3;
        }

        .stat-card.documents {
            --card-color: #28a745;
            --card-color-dark: #1e7e34;
        }

        .stat-card.signed {
            --card-color: #17a2b8;
            --card-color-dark: #117a8b;
        }

        .stat-card.pending {
            --card-color: #ffc107;
            --card-color-dark: #e0a800;
        }

        .stat-card.rate {
            --card-color: #6f42c1;
            --card-color-dark: #5a2d8a;
        }

        .stat-card.today {
            --card-color: #fd7e14;
            --card-color-dark: #e8690a;
        }

        /* ============ VERIFICATION SECTION ============ */
        .verification-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            /* Kurangi dari 15px */
            padding: 18px;
            /* Kurangi dari 25px */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.2em;
            /* Kurangi dari 1.4em */
            color: #2c3e50;
            margin-bottom: 15px;
            /* Kurangi dari 20px */
            display: flex;
            align-items: center;
            gap: 8px;
            /* Kurangi dari 10px */
        }

        .verification-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            /* Kurangi dari 15px */
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.9em;
        }

        .form-control {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1em;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        /* ============ RIGHT PANEL - ACTIVITY ============ */
        .activity-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .activity-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            /* Kurangi dari 15px */
            padding: 18px;
            /* Kurangi dari 25px */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            flex: 1;
            min-height: 0;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            /* Kurangi dari 20px */
        }

        .activity-feed {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 12px 0;
            /* Kurangi dari 15px */
            border-bottom: 1px solid #f8f9fa;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 35px;
            /* Kurangi dari 40px */
            height: 35px;
            /* Kurangi dari 40px */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            /* Kurangi dari 15px */
            font-size: 1.1em;
            /* Kurangi dari 1.2em */
            color: white;
            flex-shrink: 0;
        }

        .activity-icon.upload {
            background: #28a745;
        }

        .activity-icon.sign {
            background: #007bff;
        }

        .activity-icon.verify {
            background: #17a2b8;
        }

        .activity-icon.delete {
            background: #dc3545;
        }

        .activity-icon.login {
            background: #6f42c1;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-content h4 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-size: 0.95em;
        }

        #alertContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 300px;
            pointer-events: none;
        }

        .alert {
            padding: 8px 15px;
            /* Kurangi dari 12px 20px */
            border-radius: 6px;
            /* Kurangi dari 10px */
            margin-bottom: 8px;
            /* Kurangi dari 15px */
            font-weight: 500;
            font-size: 0.85em;
            /* Kurangi dari 0.9em */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            pointer-events: auto;
            transition: all 0.3s ease;
        }

        .activity-content p {
            margin: 0;
            color: #6c757d;
            font-size: 0.85em;
            line-height: 1.4;
        }

        .activity-time {
            color: #adb5bd;
            font-size: 0.75em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ============ VERIFICATION RESULT ============ */
        .verification-result {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            display: none;
        }

        .verification-result.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .verification-result.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        /* ============ ALERT SYSTEM ============ */
        .alert {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 0.9em;
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

        /* ============ RESPONSIVE DESIGN ============ */
        @media (max-width: 1200px) {
            .dashboard-main {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .activity-panel {
                grid-row: 2;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
                /* Kurangi dari 15px */
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
                height: auto;
            }

            .dashboard-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                padding: 20px;
            }

            .dashboard-main {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 20px;
            }

            .admin-controls {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                <p>Sistem Monitoring Tanda Tangan Digital</p>
            </div>
            <div class="admin-controls">
                <button class="btn" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-danger" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Main Dashboard Content -->
        <div class="dashboard-main">
            <!-- Left Panel: Statistics and Verification -->
            <div class="stats-panel">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card users" onclick="navigateToUserManagement()">
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <div class="number" id="totalUsers">-</div>
                        <div class="label">Total User</div>
                    </div>
                    <div class="stat-card documents" onclick="navigateToDocuments('all')">
                        <div class="icon"><i class="fas fa-file-alt"></i></div>
                        <div class="number" id="totalDocuments">-</div>
                        <div class="label">Total Dokumen</div>
                    </div>
                    <div class="stat-card signed" onclick="navigateToDocuments('signed')">
                        <div class="icon"><i class="fas fa-file-signature"></i></div>
                        <div class="number" id="signedDocuments">-</div>
                        <div class="label">Dokumen Ditandatangani</div>
                    </div>
                    <div class="stat-card pending" onclick="navigateToDocuments('pending')">
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <div class="number" id="pendingDocuments">-</div>
                        <div class="label">Dokumen Pending</div>
                    </div>
                    <div class="stat-card rate" onclick="showSignatureRateDetails()">
                        <div class="icon"><i class="fas fa-chart-pie"></i></div>
                        <div class="number" id="signatureRate">-</div>
                        <div class="label">% Dokumen Ditandatangani</div>
                    </div>
                    <div class="stat-card today" onclick="showTodayActivityDetails()">
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <div class="number" id="todayActivity">-</div>
                        <div class="label">Aktifitas Hari Ini</div>
                    </div>
                </div>

                <!-- Document Verification Section -->
                <div class="verification-section">
                    <h2 class="section-title">
                        <i class="fas fa-search"></i> Verifikasi Dokumen
                    </h2>
                    <div class="verification-form">
                        <div class="form-group">
                            <label for="adminVerificationCode">Kode Verifikasi:</label>
                            <input type="text"
                                id="adminVerificationCode"
                                class="form-control"
                                placeholder="Masukkan Kode Verifikasi"
                                onkeypress="if(event.key==='Enter') adminVerifyDocument()">
                        </div>
                        <button class="btn" onclick="adminVerifyDocument()">
                            <i class="fas fa-search"></i> Verifikasi Dokumen
                        </button>
                    </div>
                    <div id="adminVerificationResult" class="verification-result">
                        <!-- Verification result will be shown here -->
                    </div>
                </div>
            </div>

            <!-- Right Panel: Recent Activity -->
            <div class="activity-panel">
                <div class="activity-section">
                    <div class="activity-header">
                        <h2 class="section-title">
                            <i class="fas fa-history"></i> Aktifitas Terbaru
                        </h2>
                        <button class="btn" onclick="loadActivityLogs()" style="padding: 8px 15px; font-size: 0.8em;">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                    <div id="activityFeedContainer">
                        <div style="text-align: center; padding: 30px; color: #6c757d;">
                            Loading activity logs...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '<?= base_url('api/') ?>';
        let currentUser = null;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            checkAdminAccess();
            loadDashboardData();
        });

        // Check if user is admin
        async function checkAdminAccess() {
            try {
                const response = await fetch(API_BASE + 'auth/me');
                const result = await response.json();

                if (result.status === 'success') {
                    currentUser = result.data.user;

                    if (currentUser.role !== 'admin') {
                        showAlert('Access denied. This page is for administrators only.', 'error');
                        setTimeout(() => {
                            window.location.href = '<?= base_url() ?>';
                        }, 2000);
                        return;
                    }
                } else {
                    window.location.href = '<?= base_url('auth') ?>';
                }
            } catch (error) {
                console.error('Failed to check admin access:', error);
                window.location.href = '<?= base_url('auth') ?>';
            }
        }

        // Load all dashboard data
        async function loadDashboardData() {
            try {


                await Promise.all([
                    loadStatistics(),
                    loadActivityLogs()
                ]);

                clearAlerts();

            } catch (error) {
                console.error('Failed to load dashboard data:', error);
                showAlert('Failed to load dashboard data: ' + error.message, 'error');
            }
        }

        // ✅ FIXED: Load statistics with proper user count (excluding admin)
        async function loadStatistics() {
            try {
                const cards = ['totalUsers', 'totalDocuments', 'signedDocuments', 'pendingDocuments', 'todayActivity', 'signatureRate'];
                cards.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.textContent = '...';
                });
                const response = await fetch(API_BASE + 'admin/stats');
                const result = await response.json();

                if (result.status === 'success') {
                    const stats = result.data.system_stats;

                    // ✅ FIX: Use corrected user count (excluding admin)
                    document.getElementById('totalUsers').textContent = stats.total_users;
                    document.getElementById('totalDocuments').textContent = stats.total_documents;
                    document.getElementById('signedDocuments').textContent = stats.signed_documents;
                    document.getElementById('pendingDocuments').textContent = stats.pending_documents;
                    document.getElementById('todayActivity').textContent = stats.today_uploads + stats.today_signatures;
                    document.getElementById('signatureRate').textContent = stats.signature_rate + '%';

                    console.log('Dashboard Statistics Updated:', {
                        users: stats.total_users,
                        documents: stats.total_documents,
                        signed: stats.signed_documents,
                        pending: stats.pending_documents
                    });
                }
            } catch (error) {
                console.error('Failed to load statistics:', error);
                showAlert('Failed to load statistics', 'error');

            }
        }

        // ✅ FIXED: Navigation functions for cards
        function navigateToUserManagement() {
            window.location.href = '<?= base_url('admin/users') ?>';
        }

        function navigateToDocuments(filter) {
            window.location.href = '<?= base_url('admin/documents') ?>?filter=' + filter;
        }

        function showSignatureRateDetails() {
            showAlert('Signature rate details: Based on total signed vs total documents', 'info');
        }

        function showTodayActivityDetails() {
            showAlert('Today\'s activity includes uploads and signatures', 'info');
        }

        // Load activity logs
        async function loadActivityLogs() {
            const container = document.getElementById('activityFeedContainer');

            try {
                const response = await fetch(API_BASE + 'admin/activity-logs');
                const result = await response.json();

                if (result.status === 'success' && result.data.length > 0) {
                    displayActivityLogs(result.data);
                } else {
                    displaySampleActivityLogs();
                }
            } catch (error) {
                console.error('Failed to load activity logs:', error);
                displaySampleActivityLogs();
            }
        }

        // Display real activity logs
        function displayActivityLogs(logs) {
            const container = document.getElementById('activityFeedContainer');

            const activityHTML = `
                <div class="activity-feed">
                    ${logs.map(log => `
                        <div class="activity-item">
                            <div class="activity-icon ${log.action}">
                                <i class="fas fa-${getActivityIcon(log.action)}"></i>
                            </div>
                            <div class="activity-content">
                                <h4>${formatActivityTitle(log.action)}</h4>
                                <p>${log.description} ${log.username ? `by ${log.username}` : ''}</p>
                            </div>
                            <div class="activity-time">${formatTimeAgo(log.created_at)}</div>
                        </div>
                    `).join('')}
                </div>
            `;

            container.innerHTML = activityHTML;
        }

        // Display sample activity logs (fallback)
        function displaySampleActivityLogs() {
            const container = document.getElementById('activityFeedContainer');

            const activities = [{
                    type: 'sign',
                    title: 'Document Signed',
                    description: 'A document was digitally signed',
                    time: '2 hours ago'
                },
                {
                    type: 'upload',
                    title: 'Document Uploaded',
                    description: 'A new document was uploaded',
                    time: '4 hours ago'
                },
                {
                    type: 'verify',
                    title: 'Document Verified',
                    description: 'Public document verification',
                    time: '6 hours ago'
                },
                {
                    type: 'login',
                    title: 'User Login',
                    description: 'A user logged into the system',
                    time: '8 hours ago'
                }
            ];

            const activityHTML = `
                <div class="activity-feed">
                    <div style="text-align: center; padding: 20px; background: #fff3cd; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
                        <i class="fas fa-info-circle" style="color: #856404; margin-right: 8px;"></i>
                        <strong>Sample Data:</strong> No recent activities. Showing example data.
                    </div>
                    ${activities.map(activity => `
                        <div class="activity-item">
                            <div class="activity-icon ${activity.type}">
                                <i class="fas fa-${getActivityIcon(activity.type)}"></i>
                            </div>
                            <div class="activity-content">
                                <h4>${activity.title}</h4>
                                <p>${activity.description}</p>
                            </div>
                            <div class="activity-time">${activity.time}</div>
                        </div>
                    `).join('')}
                </div>
            `;

            container.innerHTML = activityHTML;
        }

        // Admin verification function
        async function adminVerifyDocument() {
            const code = document.getElementById('adminVerificationCode').value.trim();
            const resultContainer = document.getElementById('adminVerificationResult');

            if (!code) {
                showAlert('Please enter verification code!', 'error');
                return;
            }

            try {
                showAlert('Verifying document...', 'info');

                const response = await fetch(API_BASE + `documents/verify/${code}`);
                const result = await response.json();

                if (result.status === 'success') {
                    const data = result.data;

                    resultContainer.className = 'verification-result success';
                    resultContainer.innerHTML = `
                        <h4 style="margin-bottom: 15px;">
                            <i class="fas fa-check-circle"></i> Document Verified
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <p><strong>Document:</strong> ${data.original_name || data.filename}</p>
                                <p><strong>Signer:</strong> ${data.signer_name}</p>
                                <p><strong>Position:</strong> ${data.signer_position}</p>
                            </div>
                            <div>
                                <p><strong>Email:</strong> ${data.signer_email}</p>
                                <p><strong>Date:</strong> ${new Date(data.signature_date).toLocaleDateString('id-ID')}</p>
                                <p><strong>Code:</strong> ${data.verification_code}</p>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <button onclick="window.open('${API_BASE}documents/public-download/${code}', '_blank')" 
                                    class="btn" style="margin: 5px; padding: 8px 16px; font-size: 0.9em;">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button onclick="window.open('/verify/${code}', '_blank')" 
                                    class="btn" style="margin: 5px; padding: 8px 16px; font-size: 0.9em; background: #6c757d;">
                                <i class="fas fa-external-link-alt"></i> View Details
                            </button>
                        </div>
                    `;
                    resultContainer.style.display = 'block';
                    clearAlerts();
                    showAlert('Document verified successfully!', 'success');
                } else {
                    resultContainer.className = 'verification-result error';
                    resultContainer.innerHTML = `
                        <h4 style="margin-bottom: 10px;">
                            <i class="fas fa-exclamation-triangle"></i> Verification Failed
                        </h4>
                        <p>${result.message || 'Invalid verification code'}</p>
                    `;
                    resultContainer.style.display = 'block';
                    showAlert('Invalid verification code', 'error');
                }
            } catch (error) {
                showAlert('Error occurred: ' + error.message, 'error');
            }
        }

        // Helper functions
        function formatActivityTitle(action) {
            const titles = {
                upload: 'Document Uploaded',
                sign: 'Document Signed',
                verify: 'Document Verified',
                delete: 'Document Deleted',
                login: 'User Login',
                logout: 'User Logout',
                register: 'New Registration'
            };
            return titles[action] || 'Aktifitas Sistem ';
        }

        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);

            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + ' min ago';
            if (diff < 86400) return Math.floor(diff / 3600) + ' hrs ago';
            return Math.floor(diff / 86400) + ' days ago';
        }

        function getActivityIcon(type) {
            const icons = {
                upload: 'upload',
                sign: 'signature',
                verify: 'check-circle',
                delete: 'trash',
                login: 'sign-in-alt'
            };
            return icons[type] || 'info-circle';
        }

        // UBAH function showAlert() (sekitar baris 700)
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = `alert-${type}`;

            const alertId = 'alert-' + Date.now();
            const alertHTML = `
        <div class="alert ${alertClass}" id="${alertId}">
            ${message}
        </div>
    `;

            alertContainer.insertAdjacentHTML('beforeend', alertHTML);

            // Auto remove after 3 seconds
            setTimeout(() => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    alertElement.style.opacity = '0';
                    setTimeout(() => alertElement.remove(), 300);
                }
            }, 3000);
        }

        function clearAlerts() {
            document.getElementById('alertContainer').innerHTML = '';
        }

        function refreshDashboard() {
            loadDashboardData();
        }

        async function logout() {
            try {
                await fetch(API_BASE + 'auth/logout', {
                    method: 'POST'
                });
                showAlert('Logout successful!', 'success');
                setTimeout(() => {
                    window.location.href = '<?= base_url('auth') ?>';
                }, 1000);
            } catch (error) {
                window.location.href = '<?= base_url('auth') ?>';
            }
        }
    </script>
</body>

</html>