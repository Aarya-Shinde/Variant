<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "@pokemon1", "variant");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully";

// Initialize variables
$message = '';
$toastClass = '';

// Assuming form data is passed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("INSERT INTO Users (username, email, password_hash) VALUES (?, ?, ?)");

    // Check if prepare() succeeded
    if (!$stmt) {
        $message = "Database error: " . $mysqli->error;
        $toastClass = 'red'; // Bootstrap red background for error
    } else {
        // Bind parameters
        $stmt->bind_param("sss", $username, $email, $password_hash);

        // Execute the query
        if ($stmt->execute()) {
            $message = "Registration successful!";
            $toastClass = 'green'; // Bootstrap green background for success
        } else {
            $message = "Error: " . $stmt->error;
            $toastClass = 'red';
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the connection
$mysqli->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Registration</title>
</head>

<body class="bg-light">
    <div class="container p-5 d-flex flex-column align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0"
            role="alert" aria-live="assertive" aria-atomic="true"
            style="background-color: <?php echo $toastClass; ?>;">

                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <form method="post" class="form-control mt-5 p-4"
            style="width:380px; box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px;">
            <div class="text-center">
                <i class="fa fa-user-circle-o fa-3x mt-1 mb-2" style="color: green;"></i>
                <h5>Create Your Account</h5>
            </div>
            <div class="mb-3">
                <label for="username">User Name</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success">Create Account</button>
            </div>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="./login.php">Login</a></p>
            </div>
        </form>
    </div>
    <script>
        let toastElList = [].slice.call(document.querySelectorAll('.toast'));
        let toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, { delay: 3000 });
        });
        toastList.forEach(toast => toast.show());
    </script>
</body>

</html>
