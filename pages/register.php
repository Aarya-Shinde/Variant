<?php
// Include database connection
include '../db/dbconnect.php';

// Initialize variables
$message = '';
$toastClass = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password']; 

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
        $toastClass = 'alert-danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $toastClass = 'alert-danger';
    } else {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");

        if (!$check_stmt) {
            die("Prepare failed (Check email query): " . $conn->error);
        }

        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Email already registered!";
            $toastClass = 'alert-warning';
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash) VALUES (?, ?, ?)");

            if (!$stmt) {
                die("Prepare failed (Insert query): " . $conn->error);
            }

            $stmt->bind_param("sss", $username, $email, $password_hash);

            if ($stmt->execute()) {
                $message = "Registration successful!";
                $toastClass = 'alert-success';
            } else {
                $message = "Error: " . $stmt->error;
                $toastClass = 'alert-danger';
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// No need to close the connection, as it persists in `dbconnect.php`
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        body {
            /* background: url('') no-repeat center center fixed; */
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
        <a href="../index.php">Home</a>
        <a href="../library.html">Library</a>
        <a href="../reader.html">Reader</a>
        <a href="../writer.html">Writer</a>
    </div>

    <!-- Registration Form -->
    <div class="form-wrapper">
        <h2>Register</h2>
        <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <form method="POST" action="#">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn w-100">Register</button>
        </form>
        <div class="text-center mt-3">
            Already have an account? <a href="./login.php" style="color: #d4a373;">Login</a>
        </div>
    </div>

    <footer>
        &copy; 2025 Unified System for Author and Audience. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





