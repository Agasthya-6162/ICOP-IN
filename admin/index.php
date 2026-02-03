<?php
require_once 'config.php';

$error = '';
$show2FA = false;
$requires2FA = isset($_SESSION['pending_2fa_user_id']);

// Handle 2FA verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_2fa'])) {
    $code = $_POST['2fa_code'] ?? '';
    $rememberMe = isset($_POST['remember_me']) ? '1' : '0';

    if (empty($code)) {
        $error = 'Please enter your 2FA code!';
        $show2FA = true;
    } else {
        // Verify 2FA via API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, SITE_URL . '/backend/api/auth_api.php?action=verify-2fa');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code' => $code,
            'remember_me' => $rememberMe
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($result && $result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'] ?? 'Invalid 2FA code!';
            $show2FA = true;
        }
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['verify_2fa'])) {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password!';
    } else {
        // Verify credentials from database
        $result = verifyAdminCredentials($username, $password);

        if ($result['success']) {
            // Create session using SessionManager
            $sessionManager = new SessionManager();
            $rememberMe = isset($_POST['remember_me']);
            $sessionResult = $sessionManager->createSession($result['user']['id'], $rememberMe);

            if ($sessionResult['success']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_user_id'] = $result['user']['id'];
                $_SESSION['admin_full_name'] = $result['user']['full_name'];

                // Log the login
                logActivity('Admin Login', 'admin_users', $result['user']['id']);

                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Failed to create session. Please try again.';
            }
        } elseif (isset($result['requires_2fa']) && $result['requires_2fa']) {
            // Show 2FA form
            $show2FA = true;
        } elseif (isset($result['password_expired']) && $result['password_expired']) {
            $error = $result['message'] . ' Please contact administrator.';
        } else {
            $error = $result['message'] ?? 'Invalid credentials!';
        }
    }
}

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ICOP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Roboto:wght@300;400;500;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: linear-gradient(to bottom, #003366 0%, #003366 50%, #f0f2f5 50%, #f0f2f5 100%);
        }

        .login-container {
            background: #ffffff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            color: #333;
            border-top: 5px solid #c0a16b;
            /* Gold Accent */
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }

        .login-header h1 {
            font-family: 'Merriweather', serif;
            color: #003366;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .login-header p {
            color: #666;
            font-size: 15px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #444;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            color: #333;
            transition: all 0.3s;
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .form-group input:focus {
            outline: none;
            background: #fff;
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #003366;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background: #002244;
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #003366;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .forgot-password a:hover {
            color: #c0a16b;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-link a:hover {
            color: #003366;
        }

        /* Loading state */
        .btn-login.loading {
            position: relative;
            color: transparent;
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Accessibility improvements */
        *:focus {
            outline: 2px solid #c0a16b;
            outline-offset: 2px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
            }

            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../images/sanstha-logo.png" alt="Sahayog Sevabhavi Sanstha" class="login-logo">
            <h1>Admin Login</h1>
            <p>Indira College of Pharmacy</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($show2FA || $requires2FA): ?>
            <!-- 2FA Verification Form -->
            <form method="POST" id="twoFactorForm">
                <input type="hidden" name="verify_2fa" value="1">

                <div class="form-group">
                    <label for="2fa_code"><i class="fas fa-shield-alt"></i> Two-Factor Authentication Code</label>
                    <input type="text" id="2fa_code" name="2fa_code" placeholder="Enter 6-digit code" maxlength="9"
                        pattern="[0-9]{4}-?[0-9]{4}|[0-9]{6}" required autofocus aria-label="2FA Code"
                        style="text-align: center; font-size: 20px; letter-spacing: 5px;">
                    <small style="display: block; margin-top: 8px; color: #666; text-align: center;">
                        Enter the 6-digit code from your authenticator app<br>
                        or use a backup code (XXXX-XXXX format)
                    </small>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                        <input type="checkbox" name="remember_me" value="1" style="width: auto;">
                        <span>Remember this device for 30 days</span>
                    </label>
                </div>

                <button type="submit" class="btn-login" id="verify2FABtn">
                    <i class="fas fa-check-circle"></i> Verify Code
                </button>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="index.php" style="color: #666; text-decoration: none; font-size: 14px;">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </form>
        <?php else: ?>
            <!-- Regular Login Form -->
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus aria-label="Username">
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required
                        aria-label="Password">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                        <input type="checkbox" name="remember_me" value="1" style="width: auto;">
                        <span>Remember me for 30 days</span>
                    </label>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
            </form>
        <?php endif; ?>

        <div class="forgot-password">
            <a href="forgot-password.php">
                <i class="fas fa-key"></i> Forgot Password?
            </a>
        </div>

        <div class="back-link">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Website</a>
        </div>
    </div>

    <script>
        // Add loading state to login button on submit
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
        });

        // Clear any stored admin session on page load (optional)
        if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
            window.location.reload();
        }
    </script>
</body>

</html>