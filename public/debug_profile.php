<?php
// File: public/debug_profile.php
// Script untuk debug profile update issue

require_once '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/bootstrap.php';

$app = \Config\Services::codeigniter();
$app->initialize();

echo "<h2>🔍 Profile Update Debug Tool</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f4f4f4; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .section { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
</style>";

// Simulasi session user ID
$testUserId = 1; // Ganti dengan user ID yang valid

echo "<div class='section'>";
echo "<h3>1. 🔍 Test Database Connection</h3>";
try {
    $db = \Config\Database::connect();
    echo "<div class='success'>✅ Database connection successful</div>";

    // Test basic query
    $userCount = $db->table('users')->countAllResults();
    echo "<div class='info'>📊 Total users in database: {$userCount}</div>";
} catch (\Exception $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>2. 🔍 Test UserModel Loading</h3>";
try {
    $userModel = new \App\Models\UserModel();
    echo "<div class='success'>✅ UserModel loaded successfully</div>";

    // Test find user
    $user = $userModel->find($testUserId);
    if ($user) {
        echo "<div class='success'>✅ Test user found (ID: {$testUserId})</div>";
        echo "<div class='info'>👤 User: {$user['username']} ({$user['full_name']})</div>";
        echo "<div class='info'>📧 Email: {$user['email']}</div>";
    } else {
        echo "<div class='error'>❌ Test user not found (ID: {$testUserId})</div>";
        echo "<div class='info'>Available user IDs:</div>";
        $allUsers = $userModel->findAll();
        foreach ($allUsers as $u) {
            echo "<div class='info'>- ID: {$u['id']}, Username: {$u['username']}</div>";
        }
    }
} catch (\Exception $e) {
    echo "<div class='error'>❌ UserModel error: " . $e->getMessage() . "</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>3. 🔍 Test Validation Rules</h3>";
try {
    $validation = \Config\Services::validation();

    // Test validation rules yang digunakan di profile update
    $testData = [
        'full_name' => 'Test User Updated',
        'email' => 'test@email.com',
        'current_password' => 'password'
    ];

    $rules = [
        'full_name' => 'required|max_length[100]',
        'email' => "required|valid_email|max_length[100]|is_unique[users.email,id,{$testUserId}]",
        'current_password' => 'required|min_length[6]'
    ];

    echo "<div class='info'>📝 Testing validation rules:</div>";
    echo "<div class='code'>" . json_encode($rules, JSON_PRETTY_PRINT) . "</div>";

    $validation->setRules($rules);
    $isValid = $validation->run($testData);

    if ($isValid) {
        echo "<div class='success'>✅ Validation passed</div>";
    } else {
        echo "<div class='error'>❌ Validation failed</div>";
        $errors = $validation->getErrors();
        echo "<div class='error'>Validation errors:</div>";
        echo "<div class='code'>" . json_encode($errors, JSON_PRETTY_PRINT) . "</div>";
    }
} catch (\Exception $e) {
    echo "<div class='error'>❌ Validation error: " . $e->getMessage() . "</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>4. 🔍 Test UserModel updateProfile Method</h3>";
if (isset($user) && $user) {
    try {
        echo "<div class='info'>🧪 Testing updateProfile method...</div>";

        $updateData = [
            'full_name' => 'Debug Test Name',
            'email' => $user['email'] // Use same email to avoid unique constraint
        ];

        echo "<div class='info'>📝 Update data:</div>";
        echo "<div class='code'>" . json_encode($updateData, JSON_PRETTY_PRINT) . "</div>";

        $result = $userModel->updateProfile($testUserId, $updateData);

        echo "<div class='info'>📤 updateProfile result:</div>";
        echo "<div class='code'>" . json_encode($result, JSON_PRETTY_PRINT) . "</div>";

        if ($result['success']) {
            echo "<div class='success'>✅ updateProfile method works correctly</div>";

            // Revert changes
            $revertData = [
                'full_name' => $user['full_name'],
                'email' => $user['email']
            ];
            $userModel->updateProfile($testUserId, $revertData);
            echo "<div class='info'>🔄 Changes reverted</div>";
        } else {
            echo "<div class='error'>❌ updateProfile failed: " . $result['message'] . "</div>";
            if (isset($result['errors'])) {
                echo "<div class='error'>Errors:</div>";
                echo "<div class='code'>" . json_encode($result['errors'], JSON_PRETTY_PRINT) . "</div>";
            }
        }
    } catch (\Exception $e) {
        echo "<div class='error'>❌ updateProfile error: " . $e->getMessage() . "</div>";
        echo "<div class='error'>Stack trace:</div>";
        echo "<div class='code'>" . $e->getTraceAsString() . "</div>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>5. 🔍 Test Password Verification</h3>";
if (isset($user) && $user) {
    try {
        $testPassword = 'password'; // Ganti dengan password yang benar

        echo "<div class='info'>🔐 Testing password verification...</div>";
        echo "<div class='info'>Stored hash: " . substr($user['password_hash'], 0, 20) . "...</div>";

        $isValidPassword = password_verify($testPassword, $user['password_hash']);

        if ($isValidPassword) {
            echo "<div class='success'>✅ Password verification works</div>";
        } else {
            echo "<div class='error'>❌ Password verification failed</div>";
            echo "<div class='info'>💡 Try these common passwords: password, 123456, admin</div>";
        }
    } catch (\Exception $e) {
        echo "<div class='error'>❌ Password verification error: " . $e->getMessage() . "</div>";
    }
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>6. 🔍 Test API Endpoint Simulation</h3>";
try {
    echo "<div class='info'>🌐 Simulating API call...</div>";

    // Simulate the exact API call
    $authController = new \App\Controllers\Api\AuthController();

    // Create a mock request
    $request = \Config\Services::request();

    echo "<div class='info'>📡 AuthController loaded successfully</div>";
    echo "<div class='success'>✅ API endpoint accessible</div>";
} catch (\Exception $e) {
    echo "<div class='error'>❌ API simulation error: " . $e->getMessage() . "</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>7. 🔍 Check Log Files</h3>";
$logPath = WRITEPATH . 'logs/';
echo "<div class='info'>📁 Log directory: {$logPath}</div>";

if (is_dir($logPath)) {
    $logFiles = glob($logPath . '*.log');
    if (!empty($logFiles)) {
        echo "<div class='info'>📋 Recent log files:</div>";
        foreach (array_slice($logFiles, -3) as $logFile) {
            $fileName = basename($logFile);
            $fileSize = filesize($logFile);
            $lastModified = date('Y-m-d H:i:s', filemtime($logFile));
            echo "<div class='info'>- {$fileName} ({$fileSize} bytes, modified: {$lastModified})</div>";
        }

        // Show last few lines of most recent log
        $latestLog = end($logFiles);
        if ($latestLog && filesize($latestLog) > 0) {
            echo "<div class='info'>📖 Last 5 lines from latest log:</div>";
            $lines = file($latestLog);
            $lastLines = array_slice($lines, -5);
            echo "<div class='code'>" . implode('', $lastLines) . "</div>";
        }
    } else {
        echo "<div class='info'>📝 No log files found</div>";
    }
} else {
    echo "<div class='error'>❌ Log directory not found</div>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>8. 🔍 Environment Check</h3>";
echo "<div class='info'>🏠 Environment: " . ENVIRONMENT . "</div>";
echo "<div class='info'>🐘 PHP Version: " . PHP_VERSION . "</div>";
echo "<div class='info'>🎯 CodeIgniter Version: " . \CodeIgniter\CodeIgniter::CI_VERSION . "</div>";
echo "<div class='info'>🗃️ Database Driver: " . $db->getDatabase() . "</div>";
echo "</div>";

echo "<hr>";
echo "<h3>🚀 Quick Fix Suggestions</h3>";
echo "<div class='info'>";
echo "1. Check if the user ID {$testUserId} exists in your database<br>";
echo "2. Verify that the password 'password' is correct for test user<br>";
echo "3. Check validation rules for unique email constraint<br>";
echo "4. Look at log files for detailed error messages<br>";
echo "5. Ensure UserModel updateProfile method handles errors properly<br>";
echo "</div>";

echo "<br><b>🔧 To run this debug:</b><br>";
echo "1. Save this file as <code>public/debug_profile.php</code><br>";
echo "2. Access: <code>http://localhost:8080/debug_profile.php</code><br>";
echo "3. Update \$testUserId variable with valid user ID<br>";
