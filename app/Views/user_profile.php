<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Digital Signature System</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h1 {
            color: #2c3e50;
            font-size: 2.2em;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .profile-header p {
            color: #6c757d;
            font-size: 1em;
        }

        .user-avatar-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .user-info {
            color: #6c757d;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.9em;
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

        .form-control:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .password-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
        }

        .password-section h3 {
            color: #495057;
            font-size: 1.1em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-hint {
            color: #6c757d;
            font-size: 0.85em;
            margin-bottom: 15px;
            font-style: italic;
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
            width: 100%;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #495057);
            margin-right: 10px;
            width: auto;
            padding: 12px 25px;
        }

        .btn-secondary:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
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

        .loading {
            text-align: center;
            padding: 20px;
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

        .back-link {
            position: absolute;
            top: 30px;
            left: 30px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .profile-header h1 {
                font-size: 1.8em;
            }

            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5em;
            }

            .back-link {
                top: 15px;
                left: 15px;
                font-size: 1em;
                padding: 8px 12px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-secondary {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Back to Dashboard Link -->
    <a href="<?= base_url() ?>" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>

    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-edit"></i> Edit Profil</h1>
            <p>Perbarui informasi profil Anda</p>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- User Avatar Section -->
        <div class="user-avatar-section">
            <div class="user-avatar" id="userAvatar">
                <!-- User initial will be inserted here -->
            </div>
            <div class="user-info">
                <p id="currentUsername">Loading...</p>
                <p style="font-size: 0.8em; color: #adb5bd;">ID: <span id="currentUserId">-</span></p>
            </div>
        </div>

        <!-- Profile Form -->
        <form id="profileForm" onsubmit="updateProfile(event)">
            <div class="form-group">
                <label for="fullName">Nama Lengkap *</label>
                <input type="text" class="form-control" id="fullName" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" class="form-control" id="email" required>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" disabled>
                <small style="color: #6c757d; font-size: 0.8em;">Username tidak dapat diubah</small>
            </div>

            <!-- Password Change Section -->
            <div class="password-section">
                <h3>
                    <i class="fas fa-lock"></i> Ubah Password
                </h3>
                <p class="password-hint">
                    Kosongkan jika tidak ingin mengubah password
                </p>

                <div class="form-group">
                    <label for="currentPassword">Password Saat Ini *</label>
                    <input type="password" class="form-control" id="currentPassword" required>
                </div>

                <div class="form-group">
                    <label for="newPassword">Password Baru</label>
                    <input type="password" class="form-control" id="newPassword" minlength="6">
                    <small style="color: #6c757d; font-size: 0.8em;">Minimal 6 karakter</small>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="confirmPassword">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?= base_url() ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn" id="updateBtn">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        const API_BASE = '<?= base_url('api/') ?>';
        let currentUser = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadUserProfile();
        });

        // Load current user profile
        async function loadUserProfile() {
            try {
                showAlert('Memuat data profil...', 'info');

                const response = await fetch(API_BASE + 'auth/profile');
                const result = await response.json();

                if (result.status === 'success') {
                    currentUser = result.data.user;
                    populateForm(currentUser);
                    clearAlerts();
                } else {
                    showAlert('Gagal memuat profil: ' + result.message, 'error');
                    setTimeout(() => {
                        window.location.href = '<?= base_url() ?>';
                    }, 2000);
                }
            } catch (error) {
                console.error('Load profile error:', error);
                showAlert('Terjadi kesalahan saat memuat profil', 'error');
                setTimeout(() => {
                    window.location.href = '<?= base_url() ?>';
                }, 2000);
            }
        }

        // Populate form with current user data
        function populateForm(user) {
            // Update avatar and info
            document.getElementById('userAvatar').textContent = user.full_name.charAt(0).toUpperCase();
            document.getElementById('currentUsername').textContent = '@' + user.username;
            document.getElementById('currentUserId').textContent = user.id;

            // Fill form fields
            document.getElementById('fullName').value = user.full_name;
            document.getElementById('email').value = user.email;
            document.getElementById('username').value = user.username;
        }

        // Update profile
        async function updateProfile(event) {
            event.preventDefault();

            const fullName = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const updateBtn = document.getElementById('updateBtn');

            // Basic validation
            if (!fullName || !email || !currentPassword) {
                showAlert('Harap isi semua field yang wajib!', 'error');
                return;
            }

            // Password confirmation validation
            if (newPassword && newPassword !== confirmPassword) {
                showAlert('Konfirmasi password tidak sesuai!', 'error');
                return;
            }

            // Password length validation
            if (newPassword && newPassword.length < 6) {
                showAlert('Password baru minimal 6 karakter!', 'error');
                return;
            }

            try {
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<span class="spinner"></span> Menyimpan...';

                const requestData = {
                    full_name: fullName,
                    email: email,
                    current_password: currentPassword
                };

                // Only include new password if provided
                if (newPassword) {
                    requestData.new_password = newPassword;
                    requestData.confirm_password = confirmPassword;
                }

                const response = await fetch(API_BASE + 'auth/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('Profil berhasil diperbarui!', 'success');

                    // Update current user data
                    currentUser.full_name = fullName;
                    currentUser.email = email;

                    // Update avatar if name changed
                    document.getElementById('userAvatar').textContent = fullName.charAt(0).toUpperCase();

                    // Clear password fields
                    document.getElementById('currentPassword').value = '';
                    document.getElementById('newPassword').value = '';
                    document.getElementById('confirmPassword').value = '';

                    // Redirect after success
                    setTimeout(() => {
                        window.location.href = '<?= base_url() ?>';
                    }, 2000);
                } else {
                    let errorMessage = result.message || 'Gagal memperbarui profil';

                    if (result.errors) {
                        const errorList = Object.values(result.errors).join('<br>');
                        errorMessage = errorList;
                    }

                    showAlert(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Update profile error:', error);
                showAlert('Terjadi kesalahan: ' + error.message, 'error');
            } finally {
                updateBtn.disabled = false;
                updateBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
            }
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

            // Auto clear info and success alerts
            if (type === 'info' || type === 'success') {
                setTimeout(() => {
                    clearAlerts();
                }, 5000);
            }
        }

        // Clear alerts
        function clearAlerts() {
            document.getElementById('alertContainer').innerHTML = '';
        }

        // Password field synchronization
        document.getElementById('newPassword').addEventListener('input', function() {
            const newPassword = this.value;
            const confirmField = document.getElementById('confirmPassword');

            if (newPassword) {
                confirmField.required = true;
            } else {
                confirmField.required = false;
                confirmField.value = '';
            }
        });

        // Real-time password confirmation validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = this.value;

            if (confirmPassword && newPassword !== confirmPassword) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e9ecef';
            }
        });
    </script>
</body>

</html>