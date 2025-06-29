<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Management - Admin Dashboard</title>
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
            /* Kurangi top margin */
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

        .btn-info {
            background: linear-gradient(45deg, #17a2b8, #138496);
        }

        /* ✅ PERBAIKAN 1: Statistics Cards - Layout Horizontal dengan Icon dan Number bersebelahan */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            /* Kurangi min-width dari 200px ke 180px */
            gap: 15px;
            /* Kurangi dari 20px ke 15px */
            margin-bottom: 20px;
            /* Kurangi dari 30px ke 20px */
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            /* Kurangi dari 15px ke 12px */
            padding: 15px 20px;
            /* Kurangi dari 25px ke 15px 20px */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            /* Kurangi shadow */
            display: flex;
            /* ✅ UBAH: dari text-align center ke flex */
            align-items: center;
            /* ✅ Icon dan number sejajar horizontal */
            gap: 12px;
            /* ✅ Jarak antara icon dan content */
            min-height: 70px;
            /* ✅ Height yang lebih tipis */
        }

        .stat-card .icon {
            font-size: 2em;
            /* Kurangi dari 2.5em ke 2em */
            margin-bottom: 0;
            /* ✅ Hilangkan margin-bottom */
            opacity: 0.8;
            flex-shrink: 0;
            /* ✅ Icon tidak mengecil */
        }

        .stat-card .content {
            /* ✅ BARU: Container untuk number dan label */
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .stat-card .number {
            font-size: 1.8em;
            /* Kurangi dari 2.2em ke 1.8em */
            font-weight: bold;
            margin-bottom: 0;
            /* ✅ Hilangkan margin-bottom */
            line-height: 1;
        }

        .stat-card .label {
            color: #6c757d;
            font-size: 0.8em;
            /* Kurangi dari 0.9em ke 0.8em */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            /* Kurangi dari 1px ke 0.5px */
            line-height: 1.2;
            margin: 0;
        }

        .stat-card.total {
            color: #007bff;
        }

        .stat-card.signed {
            color: #28a745;
        }

        .stat-card.pending {
            color: #ffc107;
        }

        .stat-card.today {
            color: #17a2b8;
        }

        /* ✅ PERBAIKAN 2: Main Content - Kurangi Padding dan Spacing */
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
            /* Kurangi ukuran heading */
            margin: 0;
            color: #2c3e50;
        }

        /* ✅ PERBAIKAN 4: Filter Controls - Kurangi Spacing */
        .filter-controls {
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
            /* Kurangi dari 250px ke 200px */
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

        /* ✅ PERBAIKAN 5: Documents Table - Kurangi Padding */
        .documents-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            /* Kurangi dari 20px ke 10px */
        }

        .documents-table th,
        .documents-table td {
            padding: 10px 12px;
            /* Kurangi dari 15px ke 10px 12px */
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .documents-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 0.85em;
            /* Kurangi dari 0.9em ke 0.85em */
            text-transform: uppercase;
            letter-spacing: 0.3px;
            /* Kurangi dari 0.5px ke 0.3px */
        }

        .documents-table tr:hover {
            background: #f8f9fa;
        }

        .document-info h4 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .document-info p {
            margin: 2px 0;
            color: #6c757d;
            font-size: 0.9em;
        }

        .owner-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .owner-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9em;
        }

        .owner-details h5 {
            margin: 0;
            color: #2c3e50;
            font-size: 0.9em;
        }

        .owner-details p {
            margin: 0;
            color: #6c757d;
            font-size: 0.8em;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .status-signed {
            background: #d4edda;
            color: #155724;
        }

        .status-unsigned {
            background: #f8d7da;
            color: #721c24;
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
            max-width: 700px;
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

        /* ✅ PERBAIKAN 3: HILANGKAN Verification Panel Completely */
        .verification-panel {
            display: none;
            /* ✅ Sembunyikan seluruh verification panel */
        }

        .verification-input {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 15px;
        }

        .verification-input input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
        }

        .verification-result {
            display: none;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                padding: 18px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .stat-card {
                padding: 12px 15px;
                min-height: 60px;
            }

            .stat-card .icon {
                font-size: 1.6em;
            }

            .stat-card .number {
                font-size: 1.5em;
            }

            .filter-controls {
                flex-direction: column;
                gap: 10px;
            }

            .search-box,
            .filter-select {
                min-width: auto;
                width: 100%;
            }

            .documents-table {
                font-size: 0.85em;
            }

            .documents-table th,
            .documents-table td {
                padding: 8px 6px;
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
                <h1><i class="fas fa-file-alt"></i> Document Management</h1>
                <p>Monitor dan kelola semua dokumen sistem</p>
            </div>
            <div class="nav-buttons">
                <a href="<?= base_url('admin') ?>" class="btn btn-secondary">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                    <i class="fas fa-users"></i> Users
                </a>
                <button class="btn btn-danger" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- ✅ REPLACE: Statistics Cards HTML Structure -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="icon"><i class="fas fa-file-alt"></i></div>
                <div class="content">
                    <div class="number" id="totalDocuments">-</div>
                    <div class="label">Total Documents</div>
                </div>
            </div>
            <div class="stat-card signed">
                <div class="icon"><i class="fas fa-file-signature"></i></div>
                <div class="content">
                    <div class="number" id="signedDocuments">-</div>
                    <div class="label">Signed Documents</div>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="content">
                    <div class="number" id="pendingDocuments">-</div>
                    <div class="label">Pending Signature</div>
                </div>
            </div>
            <div class="stat-card today">
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
                <div class="content">
                    <div class="number" id="todayDocuments">-</div>
                    <div class="label">Today's Documents</div>
                </div>
            </div>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h2><i class="fas fa-folder-open"></i> All Documents</h2>
                <div>
                    <button class="btn" onclick="exportDocuments()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                    <button class="btn btn-secondary" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Document Verification Panel -->
            <div class="verification-panel">
                <h4><i class="fas fa-search"></i> Quick Document Verification</h4>
                <div class="verification-input">
                    <input type="text" id="verificationCode" placeholder="Enter verification code..." maxlength="20">
                    <button class="btn" onclick="quickVerifyDocument()">
                        <i class="fas fa-search"></i> Verify
                    </button>
                </div>
                <div id="verificationResult" class="verification-result">
                    <!-- Verification result will be shown here -->
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="filter-controls">
                <input type="text" class="search-box" id="searchInput" placeholder="Search documents by name, owner, or verification code..." onkeyup="filterDocuments()">
                <select class="filter-select" id="statusFilter" onchange="filterDocuments()">
                    <option value="">All Status</option>
                    <option value="1">Signed</option>
                    <option value="0">Unsigned</option>
                </select>
                <select class="filter-select" id="ownerFilter" onchange="filterDocuments()">
                    <option value="">All Owners</option>
                    <!-- Will be populated dynamically -->
                </select>
                <input type="date" class="filter-select" id="dateFilter" onchange="filterDocuments()">
            </div>

            <!-- Documents Table -->
            <div id="documentsTableContainer">
                <div class="loading">
                    <div class="spinner"></div> Loading documents...
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">QR Code Verification</h3>
                <button class="close-btn" onclick="closeQrModal()">&times;</button>
            </div>
            <div id="qrCodeContent">
                <!-- QR Code content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // ✅ FIXED: Use PHP to generate correct API base URL  
        const API_BASE = <?= json_encode(base_url('api/')) ?>;
        let allDocuments = [];
        let filteredDocuments = [];
        let allOwners = [];

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadDocumentData();
            loadStatistics();
        });

        // Load document statistics
        async function loadStatistics() {
            try {
                const response = await fetch(API_BASE + 'admin/stats');
                const result = await response.json();

                if (result.status === 'success') {
                    const stats = result.data.system_stats;

                    document.getElementById('totalDocuments').textContent = stats.total_documents;
                    document.getElementById('signedDocuments').textContent = stats.signed_documents;
                    document.getElementById('pendingDocuments').textContent = stats.pending_documents;
                    document.getElementById('todayDocuments').textContent = stats.today_uploads;
                }
            } catch (error) {
                console.error('Failed to load statistics:', error);
            }
        }

        // Load document data
        async function loadDocumentData() {
            try {
                const response = await fetch(API_BASE + 'documents');
                const result = await response.json();

                if (result.status === 'success') {
                    allDocuments = result.data;
                    filteredDocuments = [...allDocuments];

                    // Extract unique owners for filter
                    allOwners = [...new Set(allDocuments.map(doc => doc.owner_name).filter(Boolean))];
                    populateOwnerFilter();

                    displayDocuments(filteredDocuments);
                } else {
                    showAlert('Failed to load documents: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Failed to load documents:', error);
                showAlert('Failed to load documents: ' + error.message, 'error');
            }
        }

        // Populate owner filter dropdown
        function populateOwnerFilter() {
            const ownerFilter = document.getElementById('ownerFilter');
            ownerFilter.innerHTML = '<option value="">All Owners</option>';

            allOwners.forEach(owner => {
                const option = document.createElement('option');
                option.value = owner;
                option.textContent = owner;
                ownerFilter.appendChild(option);
            });
        }

        // ✅ PERBAIKAN: Function displayDocuments dengan logic status yang benar
        function displayDocuments(documents) {
            const container = document.getElementById('documentsTableContainer');

            if (documents.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 40px;">No documents found.</p>';
                return;
            }

            const tableHTML = `
        <table class="documents-table">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Upload Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${documents.map(doc => {
                    // ✅ PERBAIKAN: Logic status yang benar untuk handle semua format
                    const isSigned = doc.is_signed === true || doc.is_signed === 1 || doc.is_signed === '1' || 
                                    (doc.verification_code && doc.verification_code.trim() !== '');
                    
                    return `
                        <tr>
                            <td>
                                <div class="document-info">
                                    <h4>${doc.original_name || doc.filename}</h4>
                                    <p><i class="fas fa-file-pdf"></i> ${formatFileSize(doc.file_size)}</p>
                                    ${doc.signer_name ? `<p><i class="fas fa-user"></i> Signed by: ${doc.signer_name}</p>` : ''}
                                    ${doc.signer_position ? `<p><i class="fas fa-briefcase"></i> ${doc.signer_position}</p>` : ''}
                                </div>
                            </td>
                            <td>
                                <div class="owner-info">
                                    <div class="owner-avatar">
                                        ${doc.owner_name ? doc.owner_name.charAt(0).toUpperCase() : 'U'}
                                    </div>
                                    <div class="owner-details">
                                        <h5>${doc.owner_name || 'Unknown'}</h5>
                                        <p>@${doc.username || 'unknown'}</p>
                                        ${doc.owner_email ? `<p style="font-size: 0.75em; color: #999;">${doc.owner_email}</p>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge ${isSigned ? 'status-signed' : 'status-unsigned'}">
                                    ${isSigned ? 'Signed' : 'Unsigned'}
                                </span>
                                ${doc.signature_date ? `<br><small>${new Date(doc.signature_date).toLocaleDateString('id-ID')}</small>` : ''}
                            </td>
                            <td>
                                ${new Date(doc.upload_date || doc.created_at).toLocaleDateString('id-ID')}
                                <br><small>${new Date(doc.upload_date || doc.created_at).toLocaleTimeString('id-ID')}</small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    ${isSigned && doc.verification_code ? `
                                        <button class="btn btn-sm btn-info" onclick="showQRCode('${doc.verification_code}')" title="Show QR Code & Verification Details">
                                            <i class="fas fa-qrcode"></i> QR
                                        </button>
                                        <button class="btn btn-sm" onclick="window.open('${window.location.origin}/verify/${doc.verification_code}', '_blank')" title="Open Public Verification Page">
                                            <i class="fas fa-external-link-alt"></i> Verify
                                        </button>
                                    ` : ''}
                                    <button class="btn btn-sm btn-danger" onclick="deleteDocument(${doc.id}, '${doc.original_name || doc.filename}')" title="Delete Document">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
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

        // ✅ PERBAIKAN 2: Filter Logic - Fix masalah status terbalik
        function filterDocuments() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const ownerFilter = document.getElementById('ownerFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;

            filteredDocuments = allDocuments.filter(doc => {
                const matchesSearch = !searchTerm ||
                    (doc.original_name || doc.filename).toLowerCase().includes(searchTerm) ||
                    (doc.owner_name || '').toLowerCase().includes(searchTerm) ||
                    (doc.verification_code || '').toLowerCase().includes(searchTerm);

                // ✅ PERBAIKAN: Logic status yang benar
                let matchesStatus = true;
                if (statusFilter !== '') {
                    if (statusFilter === '1') {
                        // Filter "Signed" - harus is_signed = true/1/'1'
                        matchesStatus = doc.is_signed === true || doc.is_signed === 1 || doc.is_signed === '1';
                    } else if (statusFilter === '0') {
                        // Filter "Unsigned" - harus is_signed = false/0/'0'/null
                        matchesStatus = doc.is_signed === false || doc.is_signed === 0 || doc.is_signed === '0' ||
                            doc.is_signed === null || doc.is_signed === undefined;
                    }
                }

                const matchesOwner = !ownerFilter || doc.owner_name === ownerFilter;

                let matchesDate = true;
                if (dateFilter) {
                    const docDate = new Date(doc.upload_date || doc.created_at).toISOString().split('T')[0];
                    matchesDate = docDate === dateFilter;
                }

                return matchesSearch && matchesStatus && matchesOwner && matchesDate;
            });

            displayDocuments(filteredDocuments);
        }

        // Quick document verification
        async function quickVerifyDocument() {
            const code = document.getElementById('verificationCode').value.trim();
            const resultDiv = document.getElementById('verificationResult');

            if (!code) {
                showAlert('Please enter a verification code!', 'error');
                return;
            }

            try {
                const response = await fetch(API_BASE + `documents/verify/${code}`);
                const result = await response.json();

                if (result.status === 'success') {
                    const data = result.data;
                    resultDiv.innerHTML = `
                        <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 15px;">
                            <h5 style="color: #155724; margin-bottom: 10px;">
                                <i class="fas fa-check-circle"></i> Document Verified
                            </h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <p><strong>Document:</strong> ${data.original_name || data.filename}</p>
                                    <p><strong>Signer:</strong> ${data.signer_name}</p>
                                </div>
                                <div>
                                    <p><strong>Position:</strong> ${data.signer_position}</p>
                                    <p><strong>Date:</strong> ${new Date(data.signature_date).toLocaleString('id-ID')}</p>
                                </div>
                            </div>
                            <div style="margin-top: 10px; text-align: center;">
                                <button onclick="window.open('${API_BASE}documents/public-download/${code}', '_blank')" 
                                        class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </button>
                                <button onclick="window.open('${window.location.origin}/verify/${code}', '_blank')" 
                                        class="btn btn-sm btn-info">
                                    <i class="fas fa-external-link-alt"></i> View Details
                                </button>
                            </div>
                        </div>
                    `;
                    resultDiv.style.display = 'block';
                    showAlert('Document verified successfully!', 'success');
                } else {
                    resultDiv.innerHTML = `
                        <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 15px;">
                            <h5 style="color: #721c24; margin-bottom: 10px;">
                                <i class="fas fa-exclamation-triangle"></i> Verification Failed
                            </h5>
                            <p style="color: #721c24;">${result.message || 'Invalid verification code'}</p>
                        </div>
                    `;
                    resultDiv.style.display = 'block';
                    showAlert('Verification failed', 'error');
                }
            } catch (error) {
                showAlert('Error: ' + error.message, 'error');
            }
        }

        // Show QR Code with enhanced information
        function showQRCode(verificationCode) {
            const verifyUrl = `${window.location.origin}/verify/${verificationCode}`;
            const qrCodeUrl = `${API_BASE}documents/qr/${verificationCode}`;
            const publicDownloadUrl = `${API_BASE}documents/public-download/${verificationCode}`;

            // Find document with this verification code for additional context
            const doc = allDocuments.find(d => d.verification_code === verificationCode);

            const content = `
                <div style="text-align: center;">
                    ${doc ? `
                        <div style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            <h5 style="color: #004085; margin-bottom: 10px;">
                                <i class="fas fa-file-signature"></i> ${doc.original_name || doc.filename}
                            </h5>
                            <p style="color: #004085; margin: 5px 0;">
                                <strong>Signed by:</strong> ${doc.signer_name} (${doc.signer_position})
                            </p>
                            <p style="color: #004085; margin: 5px 0;">
                                <strong>Date:</strong> ${new Date(doc.signature_date).toLocaleString('id-ID')}
                            </p>
                        </div>
                    ` : ''}
                    
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
                        <img src="${qrCodeUrl}" 
                             alt="QR Code" 
                             style="max-width: 200px; max-height: 200px; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 15px;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        
                        <div style="display: none; color: #dc3545; margin-bottom: 15px;">
                            <i class="fas fa-exclamation-triangle"></i> QR Code not available
                        </div>
                        
                        <div>
                            <p style="font-family: monospace; font-size: 18px; font-weight: bold; color: #dc3545; margin: 10px 0;">
                                ${verificationCode}
                            </p>
                            <p style="font-size: 14px; color: #6c757d; word-break: break-all;">
                                Verification URL:<br>
                                <a href="${verifyUrl}" target="_blank" style="color: #007bff;">
                                    ${verifyUrl}
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button onclick="copyToClipboard('${verificationCode}')" 
                                class="btn" style="margin: 5px;">
                            <i class="fas fa-copy"></i> Copy Code
                        </button>
                        <button onclick="copyToClipboard('${verifyUrl}')" 
                                class="btn btn-success" style="margin: 5px;">
                            <i class="fas fa-link"></i> Copy URL
                        </button>
                        <button onclick="window.open('${verifyUrl}', '_blank')" 
                                class="btn btn-info" style="margin: 5px;">
                            <i class="fas fa-external-link-alt"></i> Open Verify
                        </button>
                        <button onclick="window.open('${publicDownloadUrl}', '_blank')" 
                                class="btn btn-secondary" style="margin: 5px;">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('qrCodeContent').innerHTML = content;
            document.getElementById('qrModal').classList.add('show');
        }

        // Download document
        function downloadDocument(docId) {
            window.open(`${API_BASE}documents/download/${docId}`, '_blank');
        }

        // Delete document
        function deleteDocument(docId, docName) {
            if (confirm(`Are you sure you want to delete document "${docName}"? This action cannot be undone.`)) {
                performDeleteDocument(docId);
            }
        }

        async function performDeleteDocument(docId) {
            try {
                const response = await fetch(`${API_BASE}documents/${docId}`, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('Document deleted successfully!', 'success');
                    await loadDocumentData();
                    await loadStatistics();
                } else {
                    showAlert(result.message || 'Failed to delete document', 'error');
                }
            } catch (error) {
                showAlert('Error: ' + error.message, 'error');
            }
        }

        // Export documents to CSV
        function exportDocuments() {
            const csvContent = "data:text/csv;charset=utf-8," +
                "Document Name,Owner,Status,Upload Date,Signer,Signature Date,Verification Code\n" +
                filteredDocuments.map(doc =>
                    `"${doc.original_name || doc.filename}","${doc.owner_name || 'Unknown'}","${doc.is_signed ? 'Signed' : 'Unsigned'}","${new Date(doc.upload_date || doc.created_at).toLocaleDateString('id-ID')}","${doc.signer_name || ''}","${doc.signature_date ? new Date(doc.signature_date).toLocaleDateString('id-ID') : ''}","${doc.verification_code || ''}"`
                ).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `documents_export_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showAlert('Documents exported successfully!', 'success');
        }

        // Refresh data
        async function refreshData() {
            showAlert('Refreshing data...', 'info');
            await loadDocumentData();
            await loadStatistics();
            showAlert('Data refreshed successfully!', 'success');
        }

        // Utility functions
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showAlert('Copied to clipboard!', 'success');
            }).catch(err => {
                showAlert('Failed to copy', 'error');
            });
        }

        // Close QR modal
        function closeQrModal() {
            document.getElementById('qrModal').classList.remove('show');
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
                window.location.href = <?= json_encode(base_url('auth')) ?>;
            } catch (error) {
                window.location.href = <?= json_encode(base_url('auth')) ?>;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const qrModal = document.getElementById('qrModal');
            if (event.target === qrModal) {
                qrModal.classList.remove('show');
            }
        }
    </script>
</body>

</html>