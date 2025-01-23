<?php
include '../db/dbconnect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a secure reset token
        $token = bin2hex(random_bytes(32));
        $resetLink = "http://yourwebsite.com/resetpassword.php?token=$token";

        // Store the token in the database with an expiration time
        $stmt = $conn->prepare("UPDATE Users SET reset_token = ?, reset_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Send the reset link via email
        mail($email, "Password Reset", "Click this link to reset your password: $resetLink", "From: no-reply@yourwebsite.com");

        $message = "A password reset link has been sent to your email.";
    } else {
        $message = "This email is not registered.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container mt-5">
        <form action="" method="post" class="form-control p-4">
            <h3>Forgot Password</h3>
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="email">Enter your email address:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
