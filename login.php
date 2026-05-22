<?php
session_start();
require_once 'koneksi.php';

$message = "";
$message_type = "";

// Redirect to dashboard if already logged in
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
        $message_type = "error";
    } else if (!isset($db_connection_error)) {
        $sql = "SELECT id, username, password, first_name FROM tb_interstudent WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row['password'])) {
                    // Password correct, set session variables
                    $_SESSION['student_id'] = $row['id'];
                    $_SESSION['student_username'] = $row['username'];
                    $_SESSION['student_name'] = $row['first_name'];
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid username or password.";
                    $message_type = "error";
                }
            } else {
                $message = "Invalid username or password.";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Login failed due to database error.";
            $message_type = "error";
        }
    } else {
        $message = $db_connection_error;
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | Poltekkes Kemenkes Bengkulu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #008080;
            --primary-dark: #006666;
            --white: #ffffff;
            --text-dark: #2d3748;
            --text-light: #718096;
            --bg-color: #f4fbfb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body {
            background-color: var(--bg-color);
            background-image: linear-gradient(135deg, #e6f2f2 0%, #ffffff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .logo {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        h2 {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        p.subtitle {
            color: var(--text-light);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            font-size: 1rem;
            background: #f7fafc;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.15);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .alert-error {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h2>Student Portal</h2>
        <p class="subtitle">Log in to upload your documents and print your registration card.</p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fa-solid fa-circle-xmark"></i>
                <div><?php echo htmlspecialchars($message); ?></div>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-submit">Login securely <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i></button>
        </form>

        <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Home / Registration</a>
    </div>

</body>
</html>
