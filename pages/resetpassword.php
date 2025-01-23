<?php
include '../db/dbconnect.php';

$message = "";
$token = $_GET['token'] ?? null;  // Get the token from the URL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password === $confirmPassword) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if the token exists and is valid
        $stmt = $conn->prepare("SELECT email FROM Users WHERE reset_token = ? AND reset_expiration > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];

            // Update the password and clear the reset token
            $stmt = $conn->prepare("UPDATE Users SET password_hash = ?, reset_token = NULL, reset_expiration = NULL WHERE email = ?");
            $stmt->bind_param("ss", $hashedPassword, $email);
            if ($stmt->execute()) {
                $message = "Password updated successfully";
            } else {
                $message = "Error updating password.";
            }
        } else {
            $message = "Invalid or expired token.";
        }

        $stmt->close();
    } else {
        $message = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Reset Password</title>
</head>
<body>
    <div class="container mt-5">
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form-control p-4">
            <h3>Reset Your Password</h3>
            <div class="mb-3">
                <label for="password">New Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
</body>
</html>
