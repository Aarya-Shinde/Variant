<?php
session_start();
include '../db/dbconnect.php'; // Ensure this file correctly establishes $conn

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$query = "SELECT * FROM Users WHERE email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found in database.");
}

$username = htmlspecialchars($user['username']);
$email = htmlspecialchars($user['email']);
$role = $user['is_admin'] ? 'Admin' : ($user['is_writer'] ? 'Writer' : 'Reader');
$created_at = htmlspecialchars($user['created_at']);

// Fetch user's book history
$historyQuery = "SELECT Novels.title, Novels.author_name, Novels.genre, Novels.publication_date 
                 FROM Novels 
                 JOIN Chapters ON Novels.novel_id = Chapters.novel_id 
                 WHERE Novels.novel_id IN (SELECT novel_id FROM Chapters) 
                 ORDER BY Novels.publication_date DESC";

$historyResult = $conn->query($historyQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            color: #ffffff;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .card {
            border-radius: 8px;
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h4 class="text-center text-white">Dashboard</h4>
    <a href="#"><i class="fa fa-user"></i> Profile</a>
    <a href="#"><i class="fa fa-book"></i> My Library</a>
    <a href="#"><i class="fa fa-cog"></i> Settings</a>
    <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="container mt-4">
        <!-- User Info -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Welcome, <?php echo $username; ?>!</h4>
            </div>
            <div class="card-body">
                <p><strong>Email:</strong> <?php echo $email; ?></p>
                <p><strong>Role:</strong> <?php echo $role; ?></p>
                <p><strong>Registered On:</strong> <?php echo $created_at; ?></p>
            </div>
        </div>

        <!-- User Library History -->
        <div class="mt-4">
            <h4>Library History</h4>
            <table class="table table-bordered mt-2">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Publication Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($historyResult->num_rows > 0) { 
                        while ($row = $historyResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['genre']); ?></td>
                            <td><?php echo htmlspecialchars($row['publication_date']); ?></td>
                        </tr>
                    <?php }} else { ?>
                        <tr><td colspan="4" class="text-center">No books found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
