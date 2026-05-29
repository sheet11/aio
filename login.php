<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

// Load database connection
require_once 'koneksi.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Check if database connection is available
        if (isset($db_connection_error)) {
            $error_message = "Database connection failed. Please try again later.";
        } else {
            // Query admin from database
            $escaped_username = mysqli_real_escape_string($conn, $username);
            $sql = "SELECT id, username, password FROM `tb_admin` WHERE username = '$escaped_username' LIMIT 1";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) === 1) {
                $admin = mysqli_fetch_assoc($result);
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_id']       = $admin['id'];
                    header("Location: admin_dashboard.php");
                    exit;
                } else {
                    $error_message = "Invalid username or password.";
                }
            } else {
                $error_message = "Invalid username or password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Poltekkes Kemenkes Bengkulu</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #008080;
            --primary-dark: #006666;
            --primary-light: #e6f2f2;
            --text-dark: #1a202c;
            --white: #ffffff;
            --error: #e53e3e;
            --error-light: #fff5f5;
            --transition-all: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius-md: 12px;
            --border-radius-lg: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #004d4d 0%, #008080 50%, #e6f2f2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Decorative background elements */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            z-index: 0;
            pointer-events: none;
        }

        body::before {
            top: -50px;
            left: -50px;
            animation: float-slow 15s infinite alternate;
        }

        body::after {
            bottom: -50px;
            right: -50px;
            animation: float-slow 18s infinite alternate-reverse;
        }

        @keyframes float-slow {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(30px) scale(1.1); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: var(--border-radius-lg);
            padding: 3.5rem 3rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 40px rgba(0, 77, 77, 0.25);
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header .logo {
            font-size: 2.2rem;
            color: var(--primary-dark);
            margin-bottom: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }

        .login-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #4a5568;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.05rem;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.8rem;
            border: 1px solid rgba(0, 128, 128, 0.2);
            border-radius: var(--border-radius-md);
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.6);
            color: var(--text-dark);
            transition: var(--transition-all);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.15);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--border-radius-md);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition-all);
            box-shadow: 0 4px 15px rgba(0, 77, 77, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 77, 77, 0.35);
        }

        /* Alert Styling */
        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            border-radius: var(--border-radius-md);
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
            background-color: var(--error-light);
            border: 1px solid #fed7d7;
            color: #742a2a;
        }

        .back-to-site {
            display: block;
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary-dark);
            text-decoration: none;
            transition: var(--transition-all);
        }

        .back-to-site:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="logo">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h2>Poltekkes Bengkulu</h2>
            <p>Admin Portal - International Admissions</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert">
                <i class="fa-solid fa-circle-exclaim"></i>
                <div><?php echo htmlspecialchars($error_message); ?></div>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Sign In Securely</span>
                <i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </form>

        <a href="index.php" class="back-to-site"><i class="fa-solid fa-arrow-left" style="margin-right: 5px;"></i> Back to Main Website</a>
    </div>

    <!-- Login form loading feedback -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('loginForm');
            if (form) {
                const submitBtn = form.querySelector('.btn-submit');
                form.addEventListener('submit', () => {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Signing in... <i class="fa-solid fa-spinner fa-spin" style="margin-left: 8px;"></i>';
                    submitBtn.style.opacity = '0.8';
                    submitBtn.style.cursor = 'not-allowed';
                });
            }
        });
    </script>
</body>
</html>
