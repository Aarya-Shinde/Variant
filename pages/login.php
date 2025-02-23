<?php  
include '../db/dbconnect.php';
session_start();

$message = "";  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Error: Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, password_hash, is_admin, is_writer FROM Users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $db_password_hash, $is_admin, $is_writer);
            $stmt->fetch();

            if (password_verify($password, $db_password_hash)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['is_admin'] = $is_admin;
                $_SESSION['is_writer'] = $is_writer;

                setcookie("user_id", $user_id, time() + (86400 * 10), "/");

                // Redirect based on roles
                if ($is_admin || $is_writer) {
                    $_SESSION['role_choice'] = true;
                    header("Location: choose_dashboard.php");
                } else {
                    header("Location: reader_dashboard.php");
                }
                exit();
            } else {
                $message = "Error: Incorrect password.";
            }
        } else {
            $message = "Error: Email not found.";
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        body {
            background-size: cover;
            font-family: 'Cormorant Garamond', serif;
            color: #4a3c31;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            justify-content: center;
        }
        .form-wrapper {
    max-width: 450px;
    margin: 80px auto 50px; /* Adjusted margin */
    padding: 40px;
    padding-left: 90px;
    padding-right: 90px;
    background: rgba(255, 248, 235, 0.9);
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    border: 2px solid #d4a373;
}


        .form-wrapper h2 {
            text-align: center;
            font-weight: bold;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #5a4231;
            border-bottom: 2px solid #d4a373;
            padding-bottom: 10px;
        }
        .form-wrapper label {
            font-weight: bold;
        }
        .form-wrapper input {
            background: #faf3eb;
            border: 1px solid #d4a373;
            border-radius: 5px;
        }
        .form-wrapper input:focus {
            border-color: #bf8450;
            box-shadow: 0 0 5px #bf8450;
            background: #f8f1e9;
        }
        .form-wrapper .btn {
            background-color: #d4a373;
            color: #fff;
            border: none;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        .form-wrapper .btn:hover {
            background-color: #bf8450;
            transform: scale(1.05);
        }
        .nav-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: rgba(210, 180, 140, 0.9);
    padding: 15px 0;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    z-index: 1000;
}

.nav-bar a {
    color: #fff;
    margin: 0 15px;
    text-decoration: none;
    font-size: 1.2rem;
    font-weight: bold;
    transition: color 0.3s;
}

.nav-bar a:hover {
    color: #d4a373;
}

        .toast {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 235, 205, 0.9);
            color: #4a3c31;
            border: 1px solid #d4a373;
        }
        footer {
            text-align: center;
            margin-top: auto; /* Pushes footer to the bottom */
            padding-bottom: 20px;
            color: #5a4231;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="nav-bar">
        <a href="../index.html">Home</a>
        <a href="writer_dashboard.php">Writer</a>
        <a href="reader_dashboard.php">Reader</a>
        <a href="admin_dashboard.php">Admin</a>
    </div>

    <!-- Login Form -->
    <div class="form-wrapper">
        <h2>Login</h2>
        <?php if (!empty($message)): ?>
            <div class="toast align-items-center text-white border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <form method="POST" action="#">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn w-100">Login</button>
        </form>
        <div class="text-center mt-3">
            <div class="mt-3">
                <a href="forgotpassword.php" style="color: #007bff; text-decoration: none;">Forgot Password?</a>
                or 
                <br>Don't have an account? 
                <a href="./register.php" style="color: #d4a373; text-decoration: none;">Register</a>
            </div>
        </div>
    </div>

    <footer>
        &copy; 2025 Unified System for Author and Audience. <br>
        All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
