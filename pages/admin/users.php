<?php
session_start();
include '../../db/dbconnect.php';

// Ensure only admin can access
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Handle Role Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $userId = intval($_POST['user_id']);
    $isReader = isset($_POST['is_reader']) ? 1 : 0;
    $isWriter = isset($_POST['is_writer']) ? 1 : 0;

    // Ensure at least one role is assigned (Reader is default)
    if (!$isReader && !$isWriter) {
        $isReader = 1; // Prevents a user from having no role
    }

    $updateQuery = "UPDATE Users SET is_reader = ?, is_writer = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("iii", $isReader, $isWriter, $userId);
    $stmt->execute();
}

// Handle User Deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $userId = intval($_POST['user_id']);
    $deleteQuery = "DELETE FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

// Fetch Users List
$users = [];
$query = "SELECT user_id, username, email, is_reader, is_writer FROM Users ORDER BY user_id DESC";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
</head>
<body>


<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand text-light" href="#">Variant</a>
        <div class="ml-auto">
            <a href="../admin_dashboard.php">Dashboard</a>
            <a href="../../index.html">Home</a>
            <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

    <div class="container">
        <h2 class="mt-3">Manage Users</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                
                                <input type="checkbox" name="is_reader" value="1" <?php echo $user['is_reader'] ? 'checked' : ''; ?>>
                                <label>Reader</label>

                                <input type="checkbox" name="is_writer" value="1" <?php echo $user['is_writer'] ? 'checked' : ''; ?>>
                                <label>Writer</label>

                                <button type="submit" name="update_role" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
