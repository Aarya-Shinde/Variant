<?php 
include '../db/dbconnect.php'; // Ensure $conn is correctly initialized in this file

$message = "";
$toastClass = "";

// Start session at the beginning
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL); // Validate and sanitize email
    $password = trim($_POST['password']); // Trim password input

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepared statement to check if email exists
        $stmt = $conn->prepare("SELECT password_hash FROM Users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($db_password_hash);
                $stmt->fetch();

                // Validate password
                if (password_verify($password, $db_password_hash)) {
                    // Regenerate session ID to prevent fixation
                    session_regenerate_id(true);
                    $_SESSION['email'] = $email;

                    //// Redirect to the dashboard page
                    header("Location: ../pages/dashboard.php");
                    exit();
                } else {
                    $message = "Incorrect password";
                    $toastClass = "bg-danger";
                }
            } else {
                $message = "Email not found";
                $toastClass = "bg-warning";
            }
            $stmt->close();
        } else {
            die("SQL prepare failed: " . $conn->error);
        }
    } else {
        $message = "Invalid email format";
        $toastClass = "bg-warning";
    }
}

$conn->close();
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
        }

        .form-wrapper {
            max-width: 450px;
            margin: 50px auto;
            padding: 30px;
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
            display: flex;
            justify-content: center;
            background: rgba(210, 180, 140, 0.9);
            padding: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
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
            margin-top: 30px;
            color: #5a4231;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="nav-bar">
        <a href="../index.html">Home</a>
        <a href="../library.html">Library</a>
        <a href="../reader.html">Reader</a>
        <a href="../writer.html">Writer</a>
    </div>

    <!-- Login Form -->
    <div class="form-wrapper">
        <h2>Login</h2>
        <?php if ($message): ?>
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

        <!-- Add Forgot Password Link -->
    <div class="mt-3">
        <a href="forgotpassword.php" style="color: #007bff; text-decoration: none;">Forgot Password?</a>
        or 
        <br>Don't have an account? 
        <a href="./register.php" style="color: #d4a373; text-decoration: none;">Register</a>
    </div>


    <footer>
        &copy; 2025 Unified System for Author and Audience. <br>
        All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
