<?php
session_start();
include '../../db/dbconnect.php';  

// Ensure only admin can access
if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Get user details
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$userId = intval($_GET['id']);
$query = "SELECT * FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update User Details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $updateQuery = "UPDATE Users SET username = ?, email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $username, $email, $userId);
    $stmt->execute();

    header("Location: users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>

    <div class="container">
        <h2>Edit User</h2>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

</body>
</html>
