<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register - Digital Signature System</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-tabs {
            display: flex;
            margin-bottom: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }

        .auth-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            background: transparent;
            font-size: 1em;
            font-weight: 500;
        }

        .auth-tab.active {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .auth-form {
            display: none;
        }

        .auth-form.active {
            display: block;
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

        .btn {
            width: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease;
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

        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-home a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-to-home a:hover {
            color: #764ba2;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
            }

            .auth-header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1><i class="fas fa-user-shield"></i></h1>
            <h2>Digital Signature System</h2>
            <p>Masuk atau daftar untuk melanjutkan</p>
        </div>

        <div class="auth-tabs">
            <button class="auth-tab active" onclick="showAuthForm('login')">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
            <button class="auth-tab" onclick="showAuthForm('register')">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </div>

        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Login Form -->
        <div id="loginForm" class="auth-form active">
            <form onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label for="loginUsername">Username</label>
                    <input type="text" class="form-control" id="loginUsername" required>
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" class="form-control" id="loginPassword" required>
                </div>

                <button type="submit" class="btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
        </div>

        <!-- Register Form -->
        <div id="registerForm" class="auth-form">
            <form onsubmit="handleRegister(event)">
                <div class="form-group">
                    <label for="registerFullName">Nama Lengkap</label>
                    <input type="text" class="form-control" id="registerFullName" required>
                </div>

                <div class="form-group">
                    <label for="registerUsername">Username</label>
                    <input type="text" class="form-control" id="registerUsername" required>
                </div>

                <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input type="email" class="form-control" id="registerEmail" required>
                </div>

                <div class="form-group">
                    <label for="registerPassword">Password</label>
                    <input type="password" class="form-control" id="registerPassword" required>
                </div>

                <div class="form-group">
                    <label for="registerConfirmPassword">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="registerConfirmPassword" required>
                </div>

                <button type="submit" class="btn" id="registerBtn">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>
        </div>

        <div class="back-to-home">
            <a href="<?= base_url() ?>">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        const API_BASE = '<?= base_url('api/') ?>';

        // Tab switching
        function showAuthForm(formType) {
            const tabs = document.querySelectorAll('.auth-tab');
            const forms = document.querySelectorAll('.auth-form');

            tabs.forEach(tab => tab.classList.remove('active'));
            forms.forEach(form => form.classList.remove('active'));

            event.target.classList.add('active');
            document.getElementById(formType + 'Form').classList.add('active');

            // Clear alerts
            document.getElementById('alertContainer').innerHTML = '';
        }

        // Handle login
        async function handleLogin(event) {
            event.preventDefault();

            const username = document.getElementById('loginUsername').value.trim();
            const password = document.getElementById('loginPassword').value;
            const loginBtn = document.getElementById('loginBtn');

            if (!username || !password) {
                showAlert('Harap isi semua field!', 'error');
                return;
            }

            try {
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<span class="spinner"></span> Memproses...';

                const response = await fetch(API_BASE + 'auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('Login berhasil! Mengarahkan...', 'success');

                    setTimeout(() => {
                        window.location.href = '<?= base_url() ?>';
                    }, 1500);
                } else {
                    showAlert(result.message || 'Login gagal', 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, 'error');
            } finally {
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Masuk';
            }
        }

        // Handle register
        async function handleRegister(event) {
            event.preventDefault();

            const fullName = document.getElementById('registerFullName').value.trim();
            const username = document.getElementById('registerUsername').value.trim();
            const email = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('registerConfirmPassword').value;
            const registerBtn = document.getElementById('registerBtn');

            if (!fullName || !username || !email || !password || !confirmPassword) {
                showAlert('Harap isi semua field!', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Password dan konfirmasi password tidak sama!', 'error');
                return;
            }

            if (password.length < 6) {
                showAlert('Password minimal 6 karakter!', 'error');
                return;
            }

            try {
                registerBtn.disabled = true;
                registerBtn.innerHTML = '<span class="spinner"></span> Memproses...';

                const response = await fetch(API_BASE + 'auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        full_name: fullName,
                        username: username,
                        email: email,
                        password: password,
                        confirm_password: confirmPassword
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('Registrasi berhasil! Silakan login.', 'success');

                    // Clear form
                    document.getElementById('registerForm').querySelector('form').reset();

                    // Switch to login tab
                    setTimeout(() => {
                        showAuthForm('login');
                    }, 2000);
                } else {
                    let errorMessage = result.message || 'Registrasi gagal';

                    if (result.errors) {
                        errorMessage = Object.values(result.errors).join('<br>');
                    }

                    showAlert(errorMessage, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, 'error');
            } finally {
                registerBtn.disabled = false;
                registerBtn.innerHTML = '<i class="fas fa-user-plus"></i> Daftar';
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

            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        // Check if already logged in
        async function checkAuthStatus() {
            try {
                const response = await fetch(API_BASE + 'auth/check');
                const result = await response.json();

                if (result.data && result.data.is_logged_in) {
                    showAlert('Anda sudah login. Mengarahkan...', 'info');
                    setTimeout(() => {
                        window.location.href = '<?= base_url() ?>';
                    }, 2000);
                }
            } catch (error) {
                // Ignore error, user is not logged in
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            checkAuthStatus();
        });
    </script>
</body>

</html>