<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Tanda Tangan Digital - CodeIgniter 4</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 20px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            color: #2c3e50;
            font-size: 2em;
            margin-bottom: 5px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .header-left p {
            color: #6c757d;
            font-size: 0.9em;
            /* Kurangi sedikit ukuran font deskripsi */
            margin: 0;
            /* Remove default margin */
        }

        /* ✅ PERBAIKAN: User Info yang lebih compact */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.7);
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .user-avatar {
            width: 42px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3em;
            font-weight: bold;
            flex-shrink: 0;
        }

        .user-details h3 {
            color: #2c3e50;
            margin: 0;
            font-size: 1em;
            font-weight: 600;
            line-height: 1.2;
        }

        .user-details p {
            color: #6c757d;
            font-size: 0.85em;
            /* Kurangi dari 0.9em ke 0.85em */
            margin: 0;
            line-height: 1.2;
        }

        /* ✅ PERBAIKAN: User Details Layout */
        .user-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 3px;
            min-width: 200px;
        }


        .user-role {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 500;
            margin-top: 2px;
            align-self: flex-start;
        }

        .role-admin {
            background: #ffd700;
            color: #856404;
        }

        .role-user {
            background: #d4edda;
            color: #155724;
        }

        .user-actions {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-left: 12px;
        }

        .profile-btn,
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        /* ✅ PERBAIKAN: Nav tabs yang lebih compact */
        .nav-tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            /* Kurangi dari 15px ke 12px */
            padding: 8px;
            /* Kurangi dari 10px ke 8px */
            margin-bottom: 20px;
            /* Kurangi dari 30px ke 20px */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            /* Kurangi shadow */
            gap: 4px;
            /* Kurangi dari 5px ke 4px */
        }

        .nav-tab {
            flex: 1;
            background: transparent;
            border: none;
            padding: 12px 16px;
            /* Kurangi dari 15px 20px ke 12px 16px */
            border-radius: 8px;
            /* Kurangi dari 10px ke 8px */
            cursor: pointer;
            font-size: 0.9em;
            /* Kurangi dari 1em ke 0.9em */
            color: #6c757d;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            /* Kurangi dari 8px ke 6px */
        }

        .nav-tab.active {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .nav-tab:hover:not(.active) {
            background: rgba(102, 126, 234, 0.1);
        }

        /* ✅ PERBAIKAN: Tab content spacing */
        .tab-content {
            display: none;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            /* Kurangi dari 15px ke 12px */
            padding: 25px;
            /* Kurangi dari 30px ke 25px */
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            /* Kurangi shadow */
            backdrop-filter: blur(10px);
        }

        .tab-content h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            /* Kurangi dari 25px ke 20px */
            display: flex;
            align-items: center;
            gap: 8px;
            /* Kurangi dari 10px ke 8px */
            font-size: 1.4em;
            /* Kurangi sedikit ukuran heading */
        }

        .tab-content.active {
            display: block;
        }

        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            background: rgba(102, 126, 234, 0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .upload-area:hover {
            border-color: #764ba2;
            background: rgba(118, 75, 162, 0.05);
        }

        .upload-area.dragover {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 15px;
        }


        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 30px;
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


        .document-info h4 {
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .document-info p {
            color: #6c757d;
            margin: 4px 0;
            font-size: 14px;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
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

        /* ✅ SIMPLE SEARCH CONTROLS */
        .document-search-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .search-header {
            margin-bottom: 15px;
        }

        .search-header h3 {
            color: #2c3e50;
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        .search-header p {
            color: #6c757d;
            font-size: 0.9em;
        }

        .search-controls {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .search-input-group {
            display: flex;
            background: white;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .search-input {
            flex: 1;
            border: none;
            outline: none;
            padding: 10px 15px;
            font-size: 0.9em;
            border-radius: 6px 0 0 6px;
        }

        .search-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 0;
        }

        .clear-search-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 12px;
            cursor: pointer;
            border-radius: 0 6px 6px 0;
        }

        .filter-controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 0.9em;
            background: white;
        }

        .filter-select:focus {
            outline: none;
            border-color: #007bff;
        }

        .custom-date-range {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .search-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            margin-top: 10px;
            border-top: 1px solid #e9ecef;
            font-size: 0.85em;
            color: #6c757d;
        }

        .btn-download {
            background: linear-gradient(45deg, #28a745, #20c997);
        }

        .btn-verify {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .btn-delete {
            background: linear-gradient(45deg, #dc3545, #c82333);
        }

        .owner-info {
            font-size: 0.85em;
            color: #6c757d;
            font-style: italic;
        }


        /* ✅ PERBAIKAN: Document Card Compact Layout */
        .document-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .document-info {
            flex: 1;
        }

        .document-info h3 {
            color: #2c3e50;
            font-size: 1.1em;
            margin-bottom: 10px;
            font-weight: 600;
        }

        /* ✅ PERBAIKAN: Meta info dengan status di samping */
        .document-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 8px;
        }

        .document-details {
            flex: 1;
        }

        .document-details p {
            color: #6c757d;
            font-size: 0.9em;
            margin: 2px 0;
            line-height: 1.4;
        }

        .document-status-wrapper {
            flex-shrink: 0;
        }

        .document-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
            white-space: nowrap;
        }

        .status-signed {
            background: #d4edda;
            color: #155724;
        }

        .status-unsigned {
            background: #f8d7da;
            color: #721c24;
        }

        /* ✅ PERBAIKAN: Signature info dalam satu baris */
        .document-signature-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 5px;
        }

        .document-signature-info span {
            color: #6c757d;
            font-size: 0.85em;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .document-signature-info i {
            color: #007bff;
        }

        /* ✅ PERBAIKAN: Actions di Sisi Kanan */
        /* ✅ Actions di Sisi Kanan */
        .document-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 120px;
            flex-shrink: 0;
            align-self: flex-start;
        }

        .document-actions .btn {
            padding: 8px 12px;
            font-size: 0.85em;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            white-space: nowrap;
            text-decoration: none;
            color: white;
        }

        .btn-download {
            background: #28a745;
        }

        .btn-verify,
        .btn-info {
            background: #007bff;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-secondary {
            background: #6c757d;
        }


        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .nav-tabs {
                flex-direction: column;
            }

            .document-item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .document-actions {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                min-width: auto;
            }

            .no-results {
                text-align: center;
                padding: 60px 20px;
                color: #6c757d;
            }

            .no-results-icon {
                font-size: 4em;
                color: #dee2e6;
                margin-bottom: 20px;
            }

            .no-results h3 {
                color: #495057;
                margin-bottom: 10px;
            }

            .no-results p {
                margin-bottom: 20px;
            }

            /* Search highlight */
            .search-highlight {
                background: #fff3cd;
                padding: 2px 4px;
                border-radius: 3px;
                font-weight: bold;
            }

            /* Filter active indicator */
            .filter-active {
                border-color: #28a745 !important;
                background: #f8fff8 !important;
            }

            /* Mobile responsive */
            @media (max-width: 768px) {
                .container {
                    padding: 10px;
                }

                .document-card {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 15px;
                }

                .document-meta {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 8px;
                }

                .document-status-wrapper {
                    align-self: flex-start;
                }

                .document-signature-info {
                    flex-direction: column;
                    gap: 5px;
                }

                .document-actions {
                    flex-direction: row;
                    min-width: auto;
                    justify-content: center;
                    align-self: stretch;
                }


                .document-search-section {
                    padding: 20px 15px;
                }

                .search-input-group {
                    flex-direction: column;
                }

                .search-input {
                    border-radius: 6px 6px 0 0;
                }

                .search-btn {
                    border-radius: 0;
                }

                .clear-search-btn {
                    border-radius: 0 0 6px 6px;
                }

                .filter-controls {
                    grid-template-columns: 1fr;
                }

                .search-stats {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 5px;
                }

                .header {
                    flex-direction: column;
                    gap: 15px;
                    text-align: center;
                    padding: 18px;
                    /* Kurangi padding mobile */

                }

                .header-left h1 {
                    font-size: 1.8em;
                    /* Kurangi untuk mobile */
                }

                .user-info {
                    flex-direction: column;
                    text-align: center;
                    gap: 8px;
                    /* Kurangi gap mobile */
                    padding: 15px;
                    /* Kurangi padding mobile */
                }

                .user-details {
                    align-items: center;
                    min-width: auto;
                }

                .user-role {
                    align-self: center;
                }

                .user-actions {
                    flex-direction: row;
                    justify-content: center;
                    margin-left: 0;
                }

                .nav-tabs {
                    flex-direction: column;
                    gap: 3px;
                    padding: 6px;
                }

                .nav-tab {
                    justify-content: flex-start;
                    padding: 10px 12px;
                    /* Kurangi padding mobile */
                }

                .tab-content {
                    padding: 20px;
                    /* Kurangi padding content mobile */
                }
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-file-signature"></i> Sistem Tanda Tangan Digital</h1>
                <p>Tanda tangan verifikasi dokumen</p>
            </div>
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">
                    <!-- User initial will be inserted here -->
                </div>
                <div class="user-details">
                    <h3 id="userName">Loading...</h3>
                    <p id="userEmail">Loading...</p>
                    <span class="user-role" id="userRole">Loading...</span>
                </div>
                <!-- ✅ NEW: Profile Edit Button -->
                <a href="<?= base_url('profile') ?>" class="profile-btn" title="Edit Profil" style="
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.9em;
        margin-left: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
    " onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i class="fas fa-user-edit"></i> Profil
                </a>
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('upload')">
                <i class="fas fa-upload"></i> Upload Dokumen
            </button>
            <button class="nav-tab" onclick="showTab('sign')">
                <i class="fas fa-signature"></i> Tanda Tangan
            </button>
            <button class="nav-tab" onclick="showTab('verify')">
                <i class="fas fa-search"></i> Verifikasi
            </button>
            <button class="nav-tab" onclick="showTab('history')">
                <i class="fas fa-history"></i> Dokumen
            </button>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Tab Upload Dokumen -->
        <div id="upload" class="tab-content active">
            <h2><i class="fas fa-upload"></i> Upload Dokumen PDF</h2>
            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Klik atau Drag & Drop PDF di sini</h3>
                <p>Maksimal ukuran file: 10MB</p>
                <input type="file" id="pdfFile" accept=".pdf" style="display: none;">
            </div>

            <div id="uploadedFile" style="display: none;">
                <div class="alert alert-success">
                    <strong><i class="fas fa-check"></i> File berhasil diupload!</strong>
                    <p id="fileName"></p>
                </div>
                <button class="btn btn-success" onclick="proceedToSign()">
                    <i class="fas fa-arrow-right"></i> Lanjut ke Tanda Tangan
                </button>
            </div>
        </div>

        <!-- Tab Tanda Tangan -->
        <div id="sign" class="tab-content">
            <h2><i class="fas fa-signature"></i> Tanda Tangan Dokumen</h2>
            <div class="form-group">
                <label>Pilih Dokumen untuk Ditandatangani:</label>
                <select class="form-control" id="documentSelect">
                    <option value="">-- Pilih Dokumen --</option>
                </select>
            </div>

            <div id="signatureForm" style="display: none;">
                <div class="form-group">
                    <label>Nama Penandatangan:</label>
                    <input type="text" class="form-control" id="signerName" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label>Posisi/Jabatan:</label>
                    <input type="text" class="form-control" id="signerPosition" placeholder="Masukkan posisi/jabatan" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" class="form-control" id="signerEmail" placeholder="Masukkan email" required>
                </div>

                <button class="btn btn-success" onclick="signDocument()" id="signBtn">
                    <i class="fas fa-signature"></i> Tandatangani Dokumen
                </button>
            </div>

            <div id="signatureResult" style="display: none;">
                <div class="alert alert-success">
                    <strong><i class="fas fa-check"></i> Dokumen berhasil ditandatangani!</strong>
                </div>
                <p><strong>Kode Verifikasi:</strong> <span id="resultCode"></span></p>
                <button class="btn btn-success" onclick="downloadSignedPDF()">
                    <i class="fas fa-download"></i> Download PDF Bertanda Tangan
                </button>
            </div>
        </div>

        <!-- Tab Verifikasi -->
        <div id="verify" class="tab-content">
            <h2><i class="fas fa-search"></i> Verifikasi Dokumen</h2>

            <div class="form-group">
                <label>Kode Verifikasi:</label>
                <input type="text" class="form-control" id="verificationCode" placeholder="Masukkan kode verifikasi">
            </div>

            <button class="btn" onclick="verifyDocument()">
                <i class="fas fa-search"></i> Verifikasi Dokumen
            </button>

            <div id="verificationResult" style="display: none;">
                <div class="alert alert-success">
                    <strong><i class="fas fa-check-circle"></i> Dokumen Terverifikasi!</strong>
                    <p><strong>Penandatangan:</strong> <span id="verifySignerName"></span></p>
                    <p><strong>Email:</strong> <span id="verifySignerEmail"></span></p>
                    <p><strong>Waktu:</strong> <span id="verifySignTime"></span></p>
                </div>
            </div>
        </div>

        <!-- ✅ SEARCH FEATURE: Add to home.php in the history tab section -->

        <!-- Update Tab Riwayat section in home.php -->
        <div id="history" class="tab-content">
            <!-- ✅ NEW: Document Search Section -->
            <div class="document-search-section">
                <div class="search-header">
                    <h3><i class="fas fa-search"></i> Cari Dokumen</h3>
                </div>

                <div class="search-controls">
                    <!-- Search Input -->
                    <div class="search-input-group">
                        <input type="text"
                            id="documentSearchInput"
                            class="search-input"
                            placeholder="Cari berdasarkan nama dokumen,  atau kode verifikasi..."
                            onkeyup="searchDocuments()"
                            onkeypress="handleSearchEnter(event)">
                        <button class="search-btn" onclick="searchDocuments()">
                            <i class="fas fa-search"></i>
                        </button>
                        <button class="clear-search-btn" onclick="clearSearch()" title="Clear Search">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Filter Controls -->
                    <div class="filter-controls">
                        <select id="statusFilter" class="filter-select">
                            <option value="">Semua Status</option>
                            <option value="signed">Sudah Ditandatangani</option>
                            <option value="unsigned">Belum Ditandatangani</option>
                        </select>

                        <select id="dateFilter" class="filter-select">
                            <option value="">Semua Waktu</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="custom">Custom Range</option>
                        </select>

                        <select id="sortFilter" class="filter-select" onchange="sortDocuments()">
                            <option value="newest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="name_asc">Nama A-Z</option>
                            <option value="name_desc">Nama Z-A</option>
                            <option value="size_desc">Ukuran Terbesar</option>
                            <option value="size_asc">Ukuran Terkecil</option>
                        </select>
                    </div>

                    <!-- Custom Date Range (Hidden by default) -->
                    <div id="customDateRange" class="custom-date-range" style="display: none;">
                        <input type="date" id="startDate" class="date-input" ">
                        <span>sampai</span>
                        <input type=" date" id="endDate" class="date-input" ">
                    </div>
                </div>

                <!-- Search Stats -->
                <div class=" search-stats">
                        <span id="searchResultsCount">0 dokumen ditemukan</span>
                        <span id="searchActiveFilters"></span>
                    </div>
                </div>

                <!-- Document List Container -->
                <div id="documentList">
                    <!-- Documents will be loaded here -->
                </div>

                <!-- No Results Message -->
                <div id="noResultsMessage" class="no-results" style="display: none;">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Tidak ada dokumen ditemukan</h3>
                    <p>Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                    <button class="btn btn-secondary" onclick="clearAllFilters()">
                        <i class="fas fa-refresh"></i> Reset Pencarian
                    </button>
                </div>
            </div>

            <script>
                // Global variables
                let currentDocument = null;
                let currentUser = null;
                const API_BASE = '<?= base_url('api/') ?>';
                let allDocuments = [];
                let filteredDocuments = [];
                let searchQuery = '';
                let activeFilters = {
                    status: '',
                    date: '',
                    sort: 'newest'
                };

                // Initialize drag and drop functionality
                function initializeDragAndDrop() {
                    const uploadArea = document.getElementById('uploadArea');
                    const fileInput = document.getElementById('pdfFile');

                    uploadArea.addEventListener('click', () => {
                        fileInput.click();
                    });

                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        uploadArea.addEventListener(eventName, preventDefaults, false);
                        document.body.addEventListener(eventName, preventDefaults, false);
                    });

                    ['dragenter', 'dragover'].forEach(eventName => {
                        uploadArea.addEventListener(eventName, highlight, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        uploadArea.addEventListener(eventName, unhighlight, false);
                    });

                    uploadArea.addEventListener('drop', handleDrop, false);

                    function preventDefaults(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    function highlight(e) {
                        uploadArea.classList.add('dragover');
                    }

                    function unhighlight(e) {
                        uploadArea.classList.remove('dragover');
                    }

                    function handleDrop(e) {
                        const dt = e.dataTransfer;
                        const files = dt.files;

                        if (files.length > 0) {
                            const file = files[0];

                            if (file.type !== 'application/pdf') {
                                showAlert('Harap pilih file PDF yang valid!', 'error');
                                return;
                            }

                            if (file.size > 10 * 1024 * 1024) {
                                showAlert('Ukuran file maksimal 10MB!', 'error');
                                return;
                            }

                            fileInput.files = files;
                            handleFileUpload({
                                target: {
                                    files: [file]
                                }
                            });
                        }
                    }
                }

                // Load user info
                async function loadUserInfo() {
                    try {
                        const response = await fetch(API_BASE + 'auth/me');
                        const result = await response.json();

                        if (result.status === 'success') {
                            currentUser = result.data.user;

                            // Update UI
                            document.getElementById('userName').textContent = currentUser.full_name;
                            document.getElementById('userEmail').textContent = currentUser.email;
                            document.getElementById('userAvatar').textContent = currentUser.full_name.charAt(0).toUpperCase();

                            const roleElement = document.getElementById('userRole');
                            roleElement.textContent = currentUser.role === 'admin' ? 'Administrator' : 'User';
                            roleElement.className = `user-role role-${currentUser.role}`;

                            // ✅ ADMIN UI RESTRICTIONS - FIXED
                            if (currentUser.role === 'admin') {
                                // Hide upload and sign tabs for admin
                                hideTabsForAdmin();
                                showAlert('Mode Administrator: Anda dapat memantau semua dokumen sistem', 'info');
                            } else {
                                // Auto-fill signer info for regular users
                                if (document.getElementById('signerName')) {
                                    document.getElementById('signerName').value = currentUser.full_name;
                                }
                                if (document.getElementById('signerEmail')) {
                                    document.getElementById('signerEmail').value = currentUser.email;
                                }
                            }
                        } else {
                            // Redirect to login if session invalid
                            window.location.href = '/auth';
                        }
                    } catch (error) {
                        console.error('Failed to load user info:', error);
                        window.location.href = '/auth';
                    }
                }

                // ✅ FIXED: Hide upload/sign tabs for admin with better error handling
                function hideTabsForAdmin() {
                    try {
                        const uploadTab = document.querySelector('button[onclick="showTab(\'upload\')"]');
                        const signTab = document.querySelector('button[onclick="showTab(\'sign\')"]');

                        if (uploadTab) {
                            uploadTab.style.display = 'none';
                            console.log('Upload tab hidden for admin');
                        }
                        if (signTab) {
                            signTab.style.display = 'none';
                            console.log('Sign tab hidden for admin');
                        }

                        // Update history tab text for admin
                        const historyTab = document.querySelector('button[onclick="showTab(\'history\')"]');
                        if (historyTab) {
                            historyTab.innerHTML = '<i class="fas fa-eye"></i> Monitor Semua Dokumen';
                        }

                        // ✅ ADD: Admin Dashboard Link
                        addAdminDashboardLink();

                        // Ensure history tab is active for admin
                        const activeTab = document.querySelector('.nav-tab.active');
                        if (!activeTab || activeTab.onclick.toString().includes('upload') || activeTab.onclick.toString().includes('sign')) {
                            // Only switch to history if current tab is upload/sign
                            showTabSilent('history');
                        }

                        console.log('Admin UI setup completed');
                    } catch (error) {
                        console.error('Error setting up admin UI:', error);
                    }
                }

                // ✅ NEW: Add Admin Dashboard Link
                function addAdminDashboardLink() {
                    const userInfo = document.querySelector('.user-info');
                    if (userInfo && !document.getElementById('adminDashboardLink')) {
                        const dashboardLink = document.createElement('a');
                        dashboardLink.id = 'adminDashboardLink';
                        dashboardLink.href = '/admin';
                        dashboardLink.className = 'btn';
                        dashboardLink.style.cssText = 'margin-left: 15px; padding: 8px 16px; font-size: 0.9em;';
                        dashboardLink.innerHTML = '<i class="fas fa-tachometer-alt"></i> Dashboard';
                        dashboardLink.title = 'Buka Admin Dashboard';

                        userInfo.appendChild(dashboardLink);
                    }
                }

                // ✅ NEW: Silent tab switching (no event trigger)
                function showTabSilent(tabName) {
                    try {
                        const tabs = document.querySelectorAll('.tab-content');
                        tabs.forEach(tab => tab.classList.remove('active'));

                        const navTabs = document.querySelectorAll('.nav-tab');
                        navTabs.forEach(tab => tab.classList.remove('active'));

                        const targetTab = document.getElementById(tabName);
                        if (targetTab) {
                            targetTab.classList.add('active');
                        }

                        const targetNavTab = document.querySelector(`button[onclick="showTab('${tabName}')"]`);
                        if (targetNavTab) {
                            targetNavTab.classList.add('active');
                        }

                        if (tabName === 'history') {
                            loadDocuments();
                        }

                        console.log(`Silently switched to ${tabName} tab`);
                    } catch (error) {
                        console.error('Error in showTabSilent:', error);
                    }
                }


                // Logout function
                async function logout() {
                    try {
                        await fetch(API_BASE + 'auth/logout', {
                            method: 'POST'
                        });
                        showAlert('Logout berhasil!', 'success');
                        setTimeout(() => {
                            window.location.href = '/auth';
                        }, 1000);
                    } catch (error) {
                        window.location.href = '/auth';
                    }
                }

                // ✅ UPDATE: Replace your existing showTab function with this
                function showTab(tabName) {
                    try {
                        if (currentUser && currentUser.role === 'admin' && (tabName === 'upload' || tabName === 'sign')) {
                            showAlert('Admin tidak memiliki akses ke fitur ini', 'error');
                            showTabSilent('history');
                            return;
                        }

                        const tabs = document.querySelectorAll('.tab-content');
                        tabs.forEach(tab => tab.classList.remove('active'));

                        const navTabs = document.querySelectorAll('.nav-tab');
                        navTabs.forEach(tab => tab.classList.remove('active'));

                        const targetTab = document.getElementById(tabName);
                        if (targetTab) {
                            targetTab.classList.add('active');
                        }

                        if (event && event.target) {
                            event.target.classList.add('active');
                        }

                        if (tabName === 'history') {
                            loadDocuments().then(() => {
                                // ✅ NEW: Initialize search after loading
                                setTimeout(() => {
                                    updateSearchStats();
                                }, 100);
                            });
                        } else if (tabName === 'sign') {
                            loadDocuments();
                        }
                    } catch (error) {
                        console.error('Error in showTab:', error);
                    }
                }

                // File upload handling
                document.getElementById('pdfFile').addEventListener('change', handleFileUpload);
                // ✅ IMPROVED: Handle successful upload with proper dropdown update
                async function handleFileUpload(event) {
                    const file = event.target.files[0];

                    if (!file) return;

                    if (file.type !== 'application/pdf') {
                        showAlert('Harap pilih file PDF yang valid!', 'error');
                        return;
                    }

                    if (file.size > 10 * 1024 * 1024) {
                        showAlert('Ukuran file maksimal 10MB!', 'error');
                        return;
                    }

                    await uploadFile(file);
                }

                async function uploadFile(file) {
                    const formData = new FormData();
                    formData.append('pdf_file', file);

                    try {
                        showAlert('Mengupload file...', 'info');

                        const response = await fetch(API_BASE + 'documents/upload', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();
                        console.log('Upload result:', result); // ✅ DEBUG LOG

                        if (result.status === 'success') {
                            currentDocument = result.data;

                            document.getElementById('fileName').textContent = result.data.filename;
                            document.getElementById('uploadedFile').style.display = 'block';

                            // ✅ FIX: Refresh document list to show new upload
                            const documents = await loadDocuments();
                            console.log('Documents after upload:', documents); // ✅ DEBUG LOG

                            showAlert('File berhasil diupload!', 'success');
                        } else {
                            console.error('Upload failed:', result); // ✅ DEBUG LOG
                            showAlert(result.message || 'Gagal upload file', 'error');
                        }
                    } catch (error) {
                        console.error('Upload error:', error); // ✅ DEBUG LOG
                        showAlert('Terjadi kesalahan: ' + error.message, 'error');
                    }
                }
                // Document selection
                document.getElementById('documentSelect').addEventListener('change', function() {
                    const signatureForm = document.getElementById('signatureForm');
                    if (this.value) {
                        signatureForm.style.display = 'block';
                    } else {
                        signatureForm.style.display = 'none';
                    }
                });

                // ✅ IMPROVED: Better document signing with status update
                async function signDocument() {
                    const documentId = document.getElementById('documentSelect').value;
                    const signerName = document.getElementById('signerName').value.trim();
                    const signerPosition = document.getElementById('signerPosition').value.trim();
                    const signerEmail = document.getElementById('signerEmail').value.trim();

                    if (!documentId || !signerName || !signerPosition || !signerEmail) {
                        showAlert('Harap lengkapi semua field!', 'error');
                        return;
                    }

                    try {
                        showAlert('Menandatangani dokumen...', 'info');

                        const response = await fetch(API_BASE + 'documents/sign', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                document_id: parseInt(documentId),
                                signer_name: signerName,
                                signer_position: signerPosition,
                                signer_email: signerEmail
                            })
                        });

                        const result = await response.json();

                        if (result.status === 'success') {
                            document.getElementById('resultCode').textContent = result.data.verification_code;
                            document.getElementById('signatureForm').style.display = 'none';
                            document.getElementById('signatureResult').style.display = 'block';

                            // ✅ FIX: Clear the signed document from dropdown and refresh data
                            await loadDocuments();

                            // Reset form
                            document.getElementById('documentSelect').value = '';

                            showAlert('Dokumen berhasil ditandatangani!', 'success');
                        } else {
                            showAlert(result.message || 'Gagal menandatangani dokumen', 'error');
                        }
                    } catch (error) {
                        showAlert('Terjadi kesalahan: ' + error.message, 'error');
                    }
                }

                // Verify document
                async function verifyDocument() {
                    const code = document.getElementById('verificationCode').value.trim();

                    if (!code) {
                        showAlert('Harap masukkan kode verifikasi!', 'error');
                        return;
                    }

                    try {
                        const response = await fetch(API_BASE + `documents/verify/${code}`);
                        const result = await response.json();

                        if (result.status === 'success') {
                            const data = result.data;

                            document.getElementById('verifySignerName').textContent = data.signer_name;
                            document.getElementById('verifySignerEmail').textContent = data.signer_email;
                            document.getElementById('verifySignTime').textContent = new Date(data.signature_date).toLocaleString('id-ID');

                            document.getElementById('verificationResult').style.display = 'block';
                            showAlert('Dokumen berhasil diverifikasi!', 'success');
                        } else {
                            showAlert(result.message || 'Kode verifikasi tidak valid', 'error');
                        }
                    } catch (error) {
                        showAlert('Terjadi kesalahan: ' + error.message, 'error');
                    }
                }

                // ✅ MASALAH 7: Fix loadDocuments function to call initialization
                async function loadDocuments() {
                    try {
                        const response = await fetch(API_BASE + 'documents');
                        const result = await response.json();

                        if (result.status === 'success') {
                            // Store documents globally for search
                            allDocuments = result.data || [];
                            filteredDocuments = [...allDocuments];

                            // Apply current filters if any
                            if (searchQuery || activeFilters.status || activeFilters.date) {
                                applyAllFilters();
                            } else {
                                displayDocuments(filteredDocuments);
                                updateSearchStats();
                            }

                            // Only update document select for non-admin users
                            if (currentUser && currentUser.role !== 'admin') {
                                updateDocumentSelect(result.data || []);
                            }

                            // ✅ CRITICAL: Initialize filter listeners after DOM is populated
                            setTimeout(() => {
                                initializeFilterEventListeners();
                            }, 100);

                            return result.data || [];
                        } else {
                            console.error('Failed to load documents:', result.message);
                            showAlert('Gagal memuat dokumen: ' + result.message, 'error');
                            return [];
                        }
                    } catch (error) {
                        console.error('Error loading documents:', error);
                        showAlert('Gagal memuat dokumen: ' + error.message, 'error');
                        return [];
                    }
                }

                // ✅ NEW: Search and Filter Functions - Add these to your existing script
                // Search documents function
                function searchDocuments() {
                    const searchInput = document.getElementById('documentSearchInput');
                    searchQuery = searchInput.value.toLowerCase().trim();

                    applyAllFilters();
                    updateSearchStats();
                }

                // Handle Enter key in search
                function handleSearchEnter(event) {
                    if (event.key === 'Enter') {
                        searchDocuments();
                    }
                }
                // Filter documents by status/date
                function filterDocuments() {
                    const statusFilter = document.getElementById('statusFilter');
                    const dateFilter = document.getElementById('dateFilter');

                    if (statusFilter) activeFilters.status = statusFilter.value;
                    if (dateFilter) activeFilters.date = dateFilter.value;

                    // Show/hide custom date range
                    const customDateRange = document.getElementById('customDateRange');
                    if (customDateRange && activeFilters.date === 'custom') {
                        customDateRange.style.display = 'flex';
                    } else if (customDateRange) {
                        customDateRange.style.display = 'none';
                    }

                    applyAllFilters();
                    updateSearchStats();
                }


                // Sort documents
                function sortDocuments() {
                    const sortFilter = document.getElementById('sortFilter');
                    if (sortFilter) {
                        activeFilters.sort = sortFilter.value;
                        applyAllFilters();
                        updateSearchStats();
                    }
                }

                // ✅ PERBAIKAN: Filter function yang benar untuk status signed/unsigned
                function applyAllFilters() {
                    let filtered = [...allDocuments];

                    // Apply search query
                    if (searchQuery) {
                        filtered = filtered.filter(doc => {
                            const searchableText = [
                                doc.original_name || doc.filename || '',
                                doc.signer_name || '',
                                doc.verification_code || '',
                                doc.signer_position || '',
                                doc.signer_email || ''
                            ].join(' ').toLowerCase();

                            return searchableText.includes(searchQuery);
                        });
                    }

                    // ✅ PERBAIKAN: Status filter logic yang benar
                    if (activeFilters.status) {
                        if (activeFilters.status === 'signed') {
                            // Filter untuk dokumen yang sudah ditandatangani
                            filtered = filtered.filter(doc => {
                                return doc.is_signed === true ||
                                    doc.is_signed === 1 ||
                                    doc.is_signed === '1' ||
                                    (doc.verification_code && doc.verification_code.trim() !== '');
                            });
                        } else if (activeFilters.status === 'unsigned') {
                            // Filter untuk dokumen yang belum ditandatangani
                            filtered = filtered.filter(doc => {
                                return doc.is_signed === false ||
                                    doc.is_signed === 0 ||
                                    doc.is_signed === '0' ||
                                    doc.is_signed === null ||
                                    doc.is_signed === undefined ||
                                    (!doc.verification_code || doc.verification_code.trim() === '');
                            });
                        }
                    }

                    // Apply date filter
                    if (activeFilters.date) {
                        filtered = filterByDate(filtered, activeFilters.date);
                    }

                    // Apply sorting
                    filtered = sortDocumentsList(filtered, activeFilters.sort);

                    filteredDocuments = filtered;
                    displayDocuments(filteredDocuments);
                    updateSearchStats();
                }
                // ✅ MASALAH 1: Missing sortDocumentsList function
                function sortDocumentsList(documents, sortType) {
                    const sorted = [...documents];

                    switch (sortType) {
                        case 'newest':
                            return sorted.sort((a, b) => new Date(b.created_at || b.upload_date) - new Date(a.created_at || a.upload_date));
                        case 'oldest':
                            return sorted.sort((a, b) => new Date(a.created_at || a.upload_date) - new Date(b.created_at || b.upload_date));
                        case 'name_asc':
                            return sorted.sort((a, b) => (a.original_name || a.filename || '').localeCompare(b.original_name || b.filename || ''));
                        case 'name_desc':
                            return sorted.sort((a, b) => (b.original_name || b.filename || '').localeCompare(a.original_name || a.filename || ''));
                        case 'size_desc':
                            return sorted.sort((a, b) => (b.file_size || 0) - (a.file_size || 0));
                        case 'size_asc':
                            return sorted.sort((a, b) => (a.file_size || 0) - (b.file_size || 0));
                        default:
                            return sorted;
                    }
                }
                // ✅ PERBAIKAN 1: Function displayDocuments dengan tombol Sign yang benar
                function displayDocuments(documents) {
                    const container = document.getElementById('documentList');
                    if (!container) {
                        console.error('Document list container not found');
                        return;
                    }

                    if (documents.length === 0) {
                        container.innerHTML = `
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Tidak ada dokumen ditemukan</h3>
                <p>Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                <button class="btn btn-secondary" onclick="clearAllFilters()">
                    <i class="fas fa-refresh"></i> Reset Pencarian
                </button>
            </div>
        `;
                        return;
                    }

                    container.innerHTML = documents.map(doc => {
                        const isSigned = doc.is_signed === true || doc.is_signed === 1 || doc.is_signed === '1' ||
                            (doc.verification_code && doc.verification_code.trim() !== '');

                        const formatDate = (dateStr) => {
                            return dateStr ? new Date(dateStr).toLocaleDateString('id-ID', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) : 'Unknown';
                        };

                        return `
            <div class="document-card">
                <div class="document-info">
                    <h3 class="document-title">${doc.original_name || doc.filename || 'Unknown Document'}</h3>
                    
                    <div class="document-meta">
                        <div class="document-details">
                            <p><i class="fas fa-calendar"></i> ${formatDate(doc.created_at || doc.upload_date)}</p>
                            ${doc.file_size ? `<p><i class="fas fa-file"></i> ${formatFileSize(doc.file_size)}</p>` : ''}
                        </div>
                        <div class="document-status-wrapper">
                            <span class="document-status ${isSigned ? 'status-signed' : 'status-unsigned'}">
                                ${isSigned ? 'Sudah Ditandatangani' : 'Belum Ditandatangani'}
                            </span>
                        </div>
                    </div>
                    
                    ${isSigned && (doc.signer_name || doc.signature_date || doc.verification_code) ? `
                        <div class="document-signature-info">
                            ${doc.signer_name ? `<span><i class="fas fa-user"></i> ${doc.signer_name}</span>` : ''}
                            ${doc.signature_date ? `<span><i class="fas fa-clock"></i> ${formatDate(doc.signature_date)}</span>` : ''}
                            ${doc.verification_code ? `<span><i class="fas fa-qrcode"></i> ${doc.verification_code}</span>` : ''}
                        </div>
                    ` : ''}
                </div>
                
                <div class="document-actions">
                    <button class="btn btn-download" onclick="downloadDocument(${doc.id})">
                        <i class="fas fa-download"></i> Download
                    </button>
                    
                    ${!isSigned ? `
                        <button class="btn btn-sign" onclick="goToSignDocument(${doc.id}, '${(doc.original_name || doc.filename || '').replace(/'/g, "\\'")}')">
                            <i class="fas fa-signature"></i> Tanda Tangan
                        </button>
                    ` : `
                        ${doc.verification_code ? `
                            <button class="btn btn-info" onclick="showQRCodeModal('${doc.verification_code}')">
                                <i class="fas fa-qrcode"></i> QR Code
                            </button>
                        ` : ''}
                    `}
                    
                    <button class="btn btn-delete" onclick="deleteDocument(${doc.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
                    }).join('');
                }

                // ✅ PERBAIKAN 2: Function goToSignDocument
                function goToSignDocument(docId, docName) {
                    // Pindah ke tab tanda tangan
                    showTab('sign');

                    // Set dokumen terpilih di dropdown
                    setTimeout(() => {
                        const documentSelect = document.getElementById('documentSelect');
                        if (documentSelect) {
                            documentSelect.value = docId;

                            // Trigger change event
                            const event = new Event('change', {
                                bubbles: true
                            });
                            documentSelect.dispatchEvent(event);
                        }

                        // Focus ke nama penandatangan
                        const signerNameInput = document.getElementById('signerName');
                        if (signerNameInput) {
                            signerNameInput.focus();
                        }
                    }, 100);
                }
                // ✅ PERBAIKAN: Function showQRCodeModal dengan close button yang benar
                function showQRCodeModal(verificationCode) {
                    // Create modal overlay dengan ID unik
                    const modal = document.createElement('div');
                    modal.id = 'qrCodeModal';
                    modal.style.cssText = `
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); display: flex; align-items: center;
        justify-content: center; z-index: 1000;
    `;

                    const verifyUrl = `${window.location.origin}/verify/${verificationCode}`;
                    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${verificationCode}`;

                    modal.innerHTML = `
        <div style="background: white; padding: 30px; border-radius: 15px; max-width: 500px; text-align: center;">
            <h3 style="margin-bottom: 20px; color: #2c3e50;">
                <i class="fas fa-qrcode"></i> QR Code Verifikasi
            </h3>
            
            <div style="margin: 20px 0;">
                <div style="display: inline-block; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <img src="${qrCodeUrl}" 
                         alt="QR Code" style="width: 200px; height: 200px;">
                </div>
            </div>
            
            <div style="background: #e7f3ff; padding: 15px; border-radius: 10px; margin: 15px 0;">
                <p style="margin: 0; color: #2c3e50; font-weight: 500;">Kode Verifikasi:</p>
                <p style="margin: 5px 0 0 0; font-family: monospace; font-size: 1.2em; color: #007bff; font-weight: bold;">
                    ${verificationCode}
                </p>
            </div>
            
            <div style="margin-top: 20px;">
                <button onclick="copyToClipboard('${verificationCode}')" 
                        style="background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; margin: 5px;">
                    <i class="fas fa-copy"></i> Copy Code
                </button>
                <button onclick="window.open('${verifyUrl}', '_blank')" 
                        style="background: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; margin: 5px;">
                    <i class="fas fa-external-link-alt"></i> Buka Verifikasi
                </button>
                <button onclick="closeQRModal()" 
                        style="background: #6c757d; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; margin: 5px;">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    `;

                    // Close modal when clicking overlay
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeQRModal();
                        }
                    });

                    document.body.appendChild(modal);
                }
                // ✅ FUNCTION BARU: Close QR Modal dengan selector yang tepat
                function closeQRModal() {
                    const modal = document.getElementById('qrCodeModal');
                    if (modal) {
                        modal.remove();
                    }
                }


                // ✅ MASALAH 3: Missing utility functions
                function formatFileSize(bytes) {
                    if (!bytes) return 'Unknown size';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
                }


                function showVerificationDetails(code) {
                    showAlert(`Kode Verifikasi: ${code}`, 'info');
                    // You can expand this to show QR code modal
                }
                // ✅ PERBAIKAN: Event handler untuk status filter
                function handleStatusFilterChange() {
                    const statusFilter = document.getElementById('statusFilter');
                    if (statusFilter) {
                        activeFilters.status = statusFilter.value;
                        applyAllFilters();
                    }
                }
                // ✅ MASALAH 4: Fix updateSearchStats function placement
                function updateSearchStats() {
                    const statsElement = document.getElementById('searchResultsCount');
                    if (statsElement) {
                        const total = filteredDocuments.length;
                        const allTotal = allDocuments.length;

                        if (searchQuery || activeFilters.status || activeFilters.date) {
                            statsElement.textContent = `${total} dari ${allTotal} dokumen ditemukan`;
                        } else {
                            statsElement.textContent = `${total} total dokumen`;
                        }
                    }

                    // Update active filters display
                    const activeFiltersElement = document.getElementById('searchActiveFilters');
                    if (activeFiltersElement) {
                        let filterTexts = [];

                        if (searchQuery) {
                            filterTexts.push(`Pencarian: "${searchQuery}"`);
                        }

                        if (activeFilters.status) {
                            const statusText = activeFilters.status === 'signed' ? 'Sudah Ditandatangani' : 'Belum Ditandatangani';
                            filterTexts.push(`Status: ${statusText}`);
                        }

                        if (activeFilters.date && activeFilters.date !== 'custom') {
                            const dateTexts = {
                                'today': 'Hari Ini',
                                'week': 'Minggu Ini',
                                'month': 'Bulan Ini'
                            };
                            filterTexts.push(`Waktu: ${dateTexts[activeFilters.date]}`);
                        }

                        activeFiltersElement.textContent = filterTexts.length > 0 ? ` • ${filterTexts.join(' • ')}` : '';
                    }
                }


                // ✅ MASALAH 5: Fix initializeFilterEventListeners function structure
                function initializeFilterEventListeners() {
                    // Status filter
                    const statusFilter = document.getElementById('statusFilter');
                    if (statusFilter) {
                        // Remove existing listeners to prevent duplicates
                        statusFilter.removeEventListener('change', handleStatusFilterChange);
                        statusFilter.addEventListener('change', handleStatusFilterChange);
                    }

                    // Search input
                    const searchInput = document.getElementById('documentSearchInput');
                    if (searchInput) {
                        searchInput.removeEventListener('input', handleSearchInput);
                        searchInput.addEventListener('input', handleSearchInput);
                    }

                    // Date filter
                    const dateFilter = document.getElementById('dateFilter');
                    if (dateFilter) {
                        dateFilter.removeEventListener('change', handleDateFilterChange);
                        dateFilter.addEventListener('change', handleDateFilterChange);
                    }

                    // Sort filter
                    const sortFilter = document.getElementById('sortFilter');
                    if (sortFilter) {
                        sortFilter.removeEventListener('change', handleSortFilterChange);
                        sortFilter.addEventListener('change', handleSortFilterChange);
                    }
                }

                // ✅ MASALAH 6: Separate event handlers
                function handleSearchInput() {
                    searchQuery = this.value.toLowerCase();
                    applyAllFilters();
                }

                function handleDateFilterChange() {
                    activeFilters.date = this.value;

                    // Show/hide custom date range
                    const customRange = document.getElementById('customDateRange');
                    if (customRange) {
                        customRange.style.display = this.value === 'custom' ? 'flex' : 'none';
                    }

                    applyAllFilters();
                }

                function handleSortFilterChange() {
                    activeFilters.sort = this.value;
                    applyAllFilters();
                }


                // Filter by date range
                function filterByDate(documents, dateFilter) {
                    const now = new Date();
                    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

                    return documents.filter(doc => {
                        const docDate = new Date(doc.upload_date || doc.created_at);

                        switch (dateFilter) {
                            case 'today':
                                return docDate >= today;

                            case 'week':
                                const weekAgo = new Date(today);
                                weekAgo.setDate(today.getDate() - 7);
                                return docDate >= weekAgo;

                            case 'month':
                                const monthAgo = new Date(today);
                                monthAgo.setMonth(today.getMonth() - 1);
                                return docDate >= monthAgo;

                            case 'custom':
                                const startDateEl = document.getElementById('startDate');
                                const endDateEl = document.getElementById('endDate');

                                if (startDateEl && endDateEl && startDateEl.value && endDateEl.value) {
                                    const start = new Date(startDateEl.value);
                                    const end = new Date(endDateEl.value);
                                    end.setHours(23, 59, 59, 999);
                                    return docDate >= start && docDate <= end;
                                }
                                return true;

                            default:
                                return true;
                        }
                    });
                }

                // Sort documents list
                function sortDocumentsList(documents, sortType) {
                    return documents.sort((a, b) => {
                        switch (sortType) {
                            case 'newest':
                                return new Date(b.upload_date || b.created_at) - new Date(a.upload_date || a.created_at);

                            case 'oldest':
                                return new Date(a.upload_date || a.created_at) - new Date(b.upload_date || b.created_at);

                            case 'name_asc':
                                return (a.original_name || a.filename).localeCompare(b.original_name || b.filename);

                            case 'name_desc':
                                return (b.original_name || b.filename).localeCompare(a.original_name || a.filename);

                            case 'size_desc':
                                return (b.file_size || 0) - (a.file_size || 0);

                            case 'size_asc':
                                return (a.file_size || 0) - (b.file_size || 0);

                            default:
                                return 0;
                        }
                    });
                }



                // Get status text for display
                function getStatusText(status) {
                    switch (status) {
                        case 'signed':
                            return 'Sudah Ditandatangani';
                        case 'unsigned':
                            return 'Belum Ditandatangani';
                        default:
                            return status;
                    }
                }

                // ✅ NEW: Get date text for display
                function getDateText(date) {
                    switch (date) {
                        case 'today':
                            return 'Hari Ini';
                        case 'week':
                            return 'Minggu Ini';
                        case 'month':
                            return 'Bulan Ini';
                        case 'custom':
                            return 'Custom Range';
                        default:
                            return date;
                    }
                }

                // Update filter visual indicators
                function updateFilterIndicators() {
                    const statusFilter = document.getElementById('statusFilter');
                    const dateFilter = document.getElementById('dateFilter');
                    const searchInput = document.getElementById('documentSearchInput');

                    if (statusFilter) statusFilter.classList.toggle('filter-active', activeFilters.status !== '');
                    if (dateFilter) dateFilter.classList.toggle('filter-active', activeFilters.date !== '');
                    if (searchInput) searchInput.classList.toggle('filter-active', searchQuery !== '');
                }
                // Clear search
                function clearSearch() {
                    const searchInput = document.getElementById('documentSearchInput');
                    if (searchInput) {
                        searchInput.value = '';
                        searchQuery = '';
                        applyAllFilters();
                        updateSearchStats();
                    }
                }

                // Clear all filters
                function clearAllFilters() {
                    const searchInput = document.getElementById('documentSearchInput');
                    const statusFilter = document.getElementById('statusFilter');
                    const dateFilter = document.getElementById('dateFilter');
                    const sortFilter = document.getElementById('sortFilter');
                    const startDate = document.getElementById('startDate');
                    const endDate = document.getElementById('endDate');
                    const customDateRange = document.getElementById('customDateRange');

                    if (searchInput) searchInput.value = '';
                    if (statusFilter) statusFilter.value = '';
                    if (dateFilter) dateFilter.value = '';
                    if (sortFilter) sortFilter.value = 'newest';
                    if (startDate) startDate.value = '';
                    if (endDate) endDate.value = '';
                    if (customDateRange) customDateRange.style.display = 'none';

                    searchQuery = '';
                    activeFilters = {
                        status: '',
                        date: '',
                        sort: 'newest'
                    };

                    applyAllFilters();
                    updateSearchStats();
                }

                // Highlight search terms in text
                function highlightSearchTerm(text) {
                    if (!searchQuery || !text) return text;

                    const regex = new RegExp(`(${escapeRegExp(searchQuery)})`, 'gi');
                    return text.replace(regex, '<span class="search-highlight">$1</span>');
                }
                // ✅ NEW: Escape special regex characters
                function escapeRegExp(string) {
                    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }

                // ✅ Generate document actions based on user role
                function generateDocumentActions(doc) {
                    if (!currentUser) return '';

                    const isAdmin = currentUser.role === 'admin';
                    const isOwner = doc.user_id == currentUser.id;

                    let actions = [];

                    // Download button - admin can download all, users can download own
                    if (isAdmin || isOwner) {
                        actions.push(`
                    <button class="btn btn-download" onclick="downloadDocument(${doc.id})" title="Download dokumen">
                        <i class="fas fa-download"></i> Download
                    </button>
                `);
                    }

                    // QR Code button - available for all signed documents
                    if (doc.is_signed && doc.verification_code) {
                        actions.push(`
                    <button class="btn btn-verify" onclick="showVerificationCode('${doc.verification_code}')" title="Lihat kode verifikasi">
                        <i class="fas fa-qrcode"></i> QR Code
                    </button>
                `);
                    }

                    // Delete button - only for owners (not admin)
                    if (!isAdmin && isOwner) {
                        actions.push(`
                    <button class="btn btn-delete" onclick="confirmDeleteDocument(${doc.id}, '${doc.original_name || doc.filename}')" title="Hapus dokumen">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                `);
                    }

                    // Admin-only actions
                    if (isAdmin) {
                        actions.push(`
                    <button class="btn btn-verify" onclick="viewDocumentDetails(${doc.id})" title="Lihat detail lengkap">
                        <i class="fas fa-info-circle"></i> Detail
                    </button>
                `);
                    }

                    return actions.join('');
                }

                // ✅ View document details for admin
                function viewDocumentDetails(docId) {
                    try {
                        const documents = window.currentDocuments || [];
                        const doc = documents.find(d => d.id == docId);

                        if (!doc) {
                            showAlert('Dokumen tidak ditemukan', 'error');
                            return;
                        }

                        const modal = document.createElement('div');
                        modal.style.cssText = `
                    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                    background: rgba(0,0,0,0.5); display: flex; align-items: center;
                    justify-content: center; z-index: 1000;
                `;

                        modal.innerHTML = `
                    <div style="background: white; padding: 30px; border-radius: 15px; max-width: 700px; max-height: 80vh; overflow-y: auto;">
                        <h3 style="margin-bottom: 20px; color: #2c3e50;">
                            <i class="fas fa-file-alt"></i> Detail Dokumen
                        </h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <h4 style="color: #495057; margin-bottom: 10px;">Informasi Dokumen</h4>
                                <p><strong>Nama File:</strong> ${doc.original_name || doc.filename}</p>
                                <p><strong>Ukuran:</strong> ${formatFileSize(doc.file_size)}</p>
                                <p><strong>Status:</strong> 
                                    <span class="status-badge ${doc.is_signed ? 'status-signed' : 'status-unsigned'}">
                                        ${doc.is_signed ? 'Sudah Ditandatangani' : 'Belum Ditandatangani'}
                                    </span>
                                </p>
                                <p><strong>Upload:</strong> ${new Date(doc.upload_date || doc.created_at).toLocaleString('id-ID')}</p>
                            </div>
                            
                            <div>
                                <h4 style="color: #495057; margin-bottom: 10px;">Informasi Pemilik</h4>
                                <p><strong>Nama:</strong> ${doc.owner_name || 'N/A'}</p>
                                <p><strong>Username:</strong> ${doc.username || 'N/A'}</p>
                                <p><strong>Email:</strong> ${doc.owner_email || 'N/A'}</p>
                            </div>
                        </div>
                        
                        ${doc.is_signed ? `
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                                <h4 style="color: #495057; margin-bottom: 10px;">Informasi Tanda Tangan</h4>
                                <p><strong>Penandatangan:</strong> ${doc.signer_name}</p>
                                <p><strong>Posisi:</strong> ${doc.signer_position}</p>
                                <p><strong>Email:</strong> ${doc.signer_email}</p>
                                <p><strong>Waktu:</strong> ${new Date(doc.signature_date).toLocaleString('id-ID')}</p>
                                <p><strong>Kode Verifikasi:</strong> 
                                    <span style="font-family: monospace; background: #e9ecef; padding: 2px 6px; border-radius: 4px;">
                                        ${doc.verification_code}
                                    </span>
                                </p>
                            </div>
                        ` : ''}
                        
                        <div style="text-align: center; margin-top: 20px;">
                            <button onclick="downloadDocument(${doc.id})" 
                                    style="margin: 5px; padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-download"></i> Download
                            </button>
                            ${doc.is_signed ? `
                                <button onclick="showVerificationCode('${doc.verification_code}'); this.closest('.modal').remove();" 
                                        style="margin: 5px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    <i class="fas fa-qrcode"></i> QR Code
                                </button>
                            ` : ''}
                            <button onclick="this.closest('.modal').remove()" 
                                    style="margin: 5px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-times"></i> Tutup
                            </button>
                        </div>
                    </div>
                `;

                        modal.className = 'modal';
                        modal.onclick = (e) => {
                            if (e.target === modal) modal.remove();
                        };
                        document.body.appendChild(modal);
                    } catch (error) {
                        console.error('Error viewing document details:', error);
                        showAlert('Gagal membuka detail dokumen', 'error');
                    }
                }

                // Format file size helper
                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }

                // Show verification code modal with QR code image
                function showVerificationCode(code) {
                    const verifyUrl = `${window.location.origin}/verify/${code}`;
                    const qrCodeUrl = `${API_BASE}documents/qr/${code}`;
                    const publicDownloadUrl = `${API_BASE}documents/public-download/${code}`; // ✅ PUBLIC DOWNLOAD URL

                    const modal = document.createElement('div');
                    modal.style.cssText = `
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); display: flex; align-items: center;
        justify-content: center; z-index: 1000;
    `;

                    modal.innerHTML = `
        <div style="background: white; padding: 30px; border-radius: 15px; max-width: 600px; text-align: center;">
            <h3 style="margin-bottom: 20px; color: #2c3e50;">
                <i class="fas fa-qrcode"></i> QR Code Verifikasi
            </h3>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; display: flex; flex-direction: column; align-items: center;">
                <img id="qrCodeImage" 
                     src="${qrCodeUrl}" 
                     alt="QR Code Verifikasi" 
                     style="max-width: 200px; max-height: 200px; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 15px;"
                     onerror="this.style.display='none'; document.getElementById('qrError').style.display='block';">
                
                <div id="qrError" style="display: none; color: #dc3545; margin-bottom: 15px;">
                    <i class="fas fa-exclamation-triangle"></i> QR Code tidak tersedia
                </div>
                
                <div style="margin-top: 10px;">
                    <p style="font-family: monospace; font-size: 18px; font-weight: bold; color: #dc3545; margin: 10px 0;">
                        ${code}
                    </p>
                    <p style="font-size: 14px; color: #6c757d;">
                        URL Verifikasi: <br>
                        <a href="${verifyUrl}" target="_blank" style="color: #007bff; word-break: break-all;">
                            ${verifyUrl}
                        </a>
                    </p>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <button onclick="copyToClipboard('${code}')" 
                        style="margin: 5px; padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-copy"></i> Copy Code
                </button>
                <button onclick="copyToClipboard('${verifyUrl}')" 
                        style="margin: 5px; padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-link"></i> Copy URL
                </button>
                <button onclick="window.open('${verifyUrl}', '_blank')" 
                        style="margin: 5px; padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-external-link-alt"></i> Open Verify
                </button>
                <button onclick="window.open('${publicDownloadUrl}', '_blank')" 
                        style="margin: 5px; padding: 10px 20px; background: #fd7e14; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-download"></i> Public Download
                </button>
                <button onclick="downloadQRCode('${qrCodeUrl}', 'qr_${code}.png')" 
                        style="margin: 5px; padding: 10px 20px; background: #6f42c1; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-download"></i> Download QR
                </button>
                <button onclick="this.closest('.modal').remove()" 
                        style="margin: 5px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    `;

                    modal.className = 'modal';
                    modal.onclick = (e) => {
                        if (e.target === modal) modal.remove();
                    };
                    document.body.appendChild(modal);
                }

                // Download QR Code function
                function downloadQRCode(qrUrl, filename) {
                    const link = document.createElement('a');
                    link.href = qrUrl;
                    link.download = filename;
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                // ✅ PERBAIKAN: Copy to clipboard function
                function copyToClipboard(text) {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(() => {
                            showAlert('Berhasil dicopy ke clipboard!', 'success');
                        }).catch(() => {
                            // Fallback method
                            fallbackCopyToClipboard(text);
                        });
                    } else {
                        // Fallback method for older browsers
                        fallbackCopyToClipboard(text);
                    }
                }
                // ✅ FALLBACK: Copy method untuk browser lama
                function fallbackCopyToClipboard(text) {
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    textArea.style.top = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();

                    try {
                        document.execCommand('copy');
                        showAlert('Berhasil dicopy ke clipboard!', 'success');
                    } catch (err) {
                        showAlert('Gagal copy ke clipboard', 'error');
                    }

                    document.body.removeChild(textArea);
                }

                // Confirm delete document
                function confirmDeleteDocument(docId, docName) {
                    const modal = document.createElement('div');
                    modal.style.cssText = `
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); display: flex; align-items: center;
        justify-content: center; z-index: 1000;
    `;

                    modal.innerHTML = `
        <div style="background: white; padding: 30px; border-radius: 15px; max-width: 500px; text-align: center;">
            <div style="color: #dc3545; font-size: 48px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="margin-bottom: 15px; color: #2c3e50;">Konfirmasi Hapus Dokumen</h3>
            <p style="margin-bottom: 20px; color: #6c757d;">
                Apakah Anda yakin ingin menghapus dokumen:<br>
                <strong>"${docName}"</strong>
            </p>
            <p style="margin-bottom: 25px; color: #dc3545; font-size: 14px;">
                <i class="fas fa-warning"></i> Tindakan ini tidak dapat dibatalkan!
            </p>
            <div style="margin-top: 20px;">
                <button onclick="deleteDocument(${docId}); this.closest('.modal').remove();" 
                        style="margin: 5px; padding: 12px 24px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
                <button onclick="this.closest('.modal').remove()" 
                        style="margin: 5px; padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </div>
    `;

                    modal.className = 'modal';
                    modal.onclick = (e) => {
                        if (e.target === modal) modal.remove();
                    };
                    document.body.appendChild(modal);
                }

                // ✅ PERBAIKAN: Function deleteDocument dengan route yang benar
                async function deleteDocument(documentId) {
                    if (!confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                        return;
                    }

                    try {
                        // ✅ PERBAIKAN: Route yang benar sesuai Routes.php
                        const response = await fetch(API_BASE + `documents/${documentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (response.ok && result.status === 'success') {
                            showAlert('Dokumen berhasil dihapus', 'success');

                            // ✅ Auto refresh documents list
                            await loadDocuments();

                            // Also update document select dropdown for signing
                            if (typeof updateDocumentSelect === 'function') {
                                updateDocumentSelect(allDocuments);
                            }

                        } else {
                            showAlert(result.message || 'Gagal menghapus dokumen', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting document:', error);
                        showAlert('Terjadi kesalahan: ' + error.message, 'error');
                    }
                }

                function updateDocumentSelect(documents) {
                    const select = document.getElementById('documentSelect');
                    if (!select) {
                        console.log('Document select element not found'); // ✅ DEBUG
                        return;
                    }

                    // Store current selection
                    const currentValue = select.value;

                    // Clear and rebuild options
                    select.innerHTML = '<option value="">-- Pilih Dokumen --</option>';

                    // ✅ FIX: Handle both boolean and string values for is_signed
                    const unsignedDocs = documents.filter(doc => {
                        // Convert to boolean: handle '0', 0, false, null, undefined
                        const isSigned = doc.is_signed === true || doc.is_signed === 1 || doc.is_signed === '1';
                        return !isSigned;
                    });

                    console.log('All documents:', documents.length); // ✅ DEBUG
                    console.log('Unsigned documents:', unsignedDocs.length); // ✅ DEBUG
                    console.log('Documents status:', documents.map(d => ({
                        id: d.id,
                        name: d.original_name,
                        signed: d.is_signed,
                        type: typeof d.is_signed
                    }))); // ✅ DEBUG

                    unsignedDocs.forEach(doc => {
                        const option = document.createElement('option');
                        option.value = doc.id;
                        option.textContent = doc.original_name || doc.filename;
                        select.appendChild(option);
                    });

                    // ✅ FIX: Restore selection if document still available
                    if (currentValue && unsignedDocs.find(d => d.id == currentValue)) {
                        select.value = currentValue;
                        // Show signature form if document was selected
                        const signatureForm = document.getElementById('signatureForm');
                        if (signatureForm) {
                            signatureForm.style.display = 'block';
                        }
                    }

                    console.log(`Updated document select: ${unsignedDocs.length} unsigned documents available`);
                }

                // Utility functions
                function showAlert(message, type) {
                    const alertContainer = document.getElementById('alertContainer');
                    const alertClass = type === 'info' ? 'alert-info' : `alert-${type}`;
                    const alertHtml = `
                <div class="alert ${alertClass}">
                    ${message}
                </div>
            `;
                    alertContainer.innerHTML = alertHtml;

                    setTimeout(() => {
                        alertContainer.innerHTML = '';
                    }, 5000);
                }

                // ✅ NEW: Enhanced proceedToSign with dropdown refresh
                function proceedToSign() {
                    if (currentUser && currentUser.role === 'admin') {
                        showAlert('Admin tidak dapat menandatangani dokumen', 'error');
                        return;
                    }

                    // Switch to sign tab
                    showTabSilent('sign');

                    // ✅ FIX: Refresh documents and then set selection
                    loadDocuments().then(() => {
                        if (currentDocument) {
                            setTimeout(() => {
                                const docSelect = document.getElementById('documentSelect');
                                if (docSelect) {
                                    docSelect.value = currentDocument.document_id;
                                    const signForm = document.getElementById('signatureForm');
                                    if (signForm) {
                                        signForm.style.display = 'block';
                                    }
                                }
                            }, 500); // Small delay to ensure dropdown is populated
                        }
                    });
                }

                async function downloadDocument(docId) {
                    window.open(API_BASE + `documents/download/${docId}`, '_blank');
                }

                function downloadSignedPDF() {
                    if (currentDocument) {
                        downloadDocument(currentDocument.document_id);
                    }
                }

                // ✅ NEW: Clear document filter
                function clearDocumentFilter() {
                    displayDocuments(window.currentDocuments || []);
                    showAlert('Filter dihapus', 'success');
                }
                // ✅ NEW: Handle dashboard filter on page load
                function handleDashboardFilter() {
                    const filter = sessionStorage.getItem('documentFilter');
                    if (filter) {
                        sessionStorage.removeItem('documentFilter'); // Clear after use

                        // Show history tab and apply filter
                        showTabSilent('history');

                        setTimeout(() => {
                            if (filter === 'all') {
                                showAlert('Menampilkan semua dokumen', 'info');
                            } else if (filter === 'signed') {
                                filterDocuments('signed');
                                showAlert('Menampilkan dokumen yang sudah ditandatangani', 'info');
                            } else if (filter === 'pending') {
                                filterDocuments('pending');
                                showAlert('Menampilkan dokumen yang belum ditandatangani', 'info');
                            }
                        }, 1000);
                    }
                }

                // ✅ NEW: Filter documents function
                function filterDocuments(filterType) {
                    const documents = window.currentDocuments || [];
                    let filteredDocs = documents;

                    if (filterType === 'signed') {
                        filteredDocs = documents.filter(doc => doc.is_signed);
                    } else if (filterType === 'pending') {
                        filteredDocs = documents.filter(doc => !doc.is_signed);
                    }

                    displayDocuments(filteredDocs);

                    // Add filter info
                    const container = document.getElementById('documentList');
                    if (container && filteredDocs.length < documents.length) {
                        const filterInfo = document.createElement('div');
                        filterInfo.style.cssText = 'background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: center;';
                        filterInfo.innerHTML = `
                    <i class="fas fa-filter" style="margin-right: 8px;"></i>
                    <strong>Filter:</strong> Menampilkan ${filteredDocs.length} dari ${documents.length} dokumen
                    <button onclick="clearDocumentFilter()" style="margin-left: 15px; background: #007bff; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
                        <i class="fas fa-times"></i> Clear Filter
                    </button>
                `;
                        container.insertBefore(filterInfo, container.firstChild);
                    }
                }

                // Initialize with better error handling
                document.addEventListener('DOMContentLoaded', function() {
                    try {
                        initializeDragAndDrop();
                        loadUserInfo();
                        // Load documents after a short delay to ensure user info is loaded
                        setTimeout(() => {
                            loadDocuments().then(() => {
                                // ✅ NEW: Handle dashboard filter after documents loaded
                                handleDashboardFilter();
                            });
                        }, 500);
                    } catch (error) {
                        console.error('Initialization error:', error);
                        showAlert('Terjadi kesalahan saat inisialisasi sistem', 'error');
                    }
                });
            </script>
</body>

</html>