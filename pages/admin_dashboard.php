<?php
session_start();
include '../db/dbconnect.php';  // Include database connection file

// Check if the user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$email = $_SESSION['email'];
$query = "SELECT * FROM Users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle cases where no user is found
$username = $user ? htmlspecialchars($user['username']) : "Admin";

// Fetch total counts
$totalBooks = $conn->query("SELECT COUNT(*) AS count FROM Novels")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM Users")->fetch_assoc()['count'];
$totalFanfics = $conn->query("SELECT COUNT(*) AS count FROM Fanfic")->fetch_assoc()['count'];
// $totalReports = $conn->query("SELECT COUNT(*) AS count FROM Reports")->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

<link rel="stylesheet" href="style/admin_dash.css">

</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
                <!-- variant logo image here -->
                <div class="logo"> 
                <a href="../index.php"><img src="../images/logowrite.png" alt="Variant Logo" style="max-height: 40px;">
                </a> </div>

        <ul>
            <li><a href="admin_dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="admin/manage_novels.php"><i class="fa fa-book"></i> Manage Novels</a></li>
            <li><a href="admin/all_novels.php"><i class="fa fa-fire"></i> All Novels</a></li>
            <li><a href="admin/users.php"><i class="fa fa-users"></i> Users</a></li>
            <li><a href="admin/reports.php"><i class="fa fa-flag"></i> Reports</a></li>
            <li><a href="logout.php" class="btn btn-light"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">Welcome, <?php echo $username; ?>!</div>

        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-card">Total Books: <strong><?php echo $totalBooks; ?></strong></div>
            <div class="stat-card">Total Fanfics: <strong><?php echo $totalFanfics; ?></strong></div>
            <div class="stat-card">Total Users: <strong><?php echo $totalUsers; ?></strong></div>
            <!-- <div class="stat-card">Reports: <strong></strong></div> -->
        </div>



    </div>

</body>
</html>
