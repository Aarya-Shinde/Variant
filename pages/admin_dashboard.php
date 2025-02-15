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
// $totalCategories = $conn->query("SELECT COUNT(*) AS count FROM Categories")->fetch_assoc()['count'];
// $totalReports = $conn->query("SELECT COUNT(*) AS count FROM Reports")->fetch_assoc()['count'];

//Search novels query

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$novels = [];
if (!empty($search)) {
    $searchQuery = "SELECT * FROM Novels WHERE title LIKE '%$search%' OR author_name LIKE '%$search%'";
    $searchResult = $conn->query($searchQuery);

    while ($row = $searchResult->fetch_assoc()) {
        $novels[] = $row;
    }
} else {
    $novelQuery = "SELECT * FROM Novels";
    $novelResult = $conn->query($novelQuery);

    while ($row = $novelResult->fetch_assoc()) {
        $novels[] = $row;
    }
}


// Handle novel deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $conn->query("DELETE FROM Novels WHERE id = '$delete_id'");
    header("Location: dashboard.php");
    exit();
}
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

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #a67c52, #8b6f4e);
            color: #3e2723;
        }

        .sidebar {
            width: 250px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            position: fixed;
            height: 100%;
            padding: 1rem;
            border-radius: 0px 0 0 10px;
            backdrop-filter: blur(10px);
        }

        .sidebar .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2rem;
            color: #8b4513;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 1.5rem 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-size: 1.2rem;
            display: block;
            padding: 0.7rem 1rem;
            border-radius: 8px;
            transition: 0.3s;
            background-color: rgba(0, 0, 0, 0);
        }

        .sidebar ul li a:hover {
            background-color: #8b4513;
            transform: scale(1.05);
        }

        .main-content {
            margin-left: 270px;
            padding: 2rem;
        }

        .header {
            background: linear-gradient(to right, #DABAA5, #A37C70);
            color: white;
            padding: 1.5rem;
            text-align: center;
            font-size: 2rem;
            border-radius: 10px;
            font-weight: bold;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            color: white;
            backdrop-filter: blur(10px);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 5px 15px rgba(255, 255, 255, 0.2);
        }

        .novel-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .novel-card {
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 10px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .novel-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.2);
        }

        .novel-card img {
            width: 100%;
            border-radius: 5px;
        }

        .btn-primary {
            background: #8b4513;
            border: none;
        }

        .btn-primary:hover {
            background: #a0522d;
        }

        .btn-danger {
            background: #8b4513;
            border: none;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .search-box {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }



    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Admin Panel</div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li><a href="admin/manage_novels.php"><i class="fa fa-book"></i> Manage Novels</a></li>
            <li><a href="categories.php"><i class="fa fa-list"></i> Categories</a></li>
            <li><a href="users.php"><i class="fa fa-users"></i> Users</a></li>
            <li><a href="reports.php"><i class="fa fa-flag"></i> Reports</a></li>
            <li><a href="logout.php" class="btn btn-light"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">Welcome, <?php echo $username; ?>!</div>

        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-card">Total Books: <strong><?php echo $totalBooks; ?></strong></div>
            <div class="stat-card">Total Users: <strong><?php echo $totalUsers; ?></strong></div>
            <div class="stat-card">Categories: <strong><?php echo $totalCategories; ?></strong></div>
            <div class="stat-card">Reports: <strong><?php echo $totalReports; ?></strong></div>
        </div>

        <!-- Search & Display Novels -->
        <form method="GET" action="" class="search-box">
            <input type="text" name="search" placeholder="Search Novel..." class="form-control">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
         <!-- Working the search button -->
                <?php if (!empty($novels)): ?>
            <div class="novel-container">
                <?php foreach ($novels as $novel): ?>
                    <div class="novel-card">
                        <img src="<?php echo htmlspecialchars($novel['cover_image_url']); ?>" alt="Cover Image">
                        <h5><?php echo htmlspecialchars($novel['title']); ?></h5>
                        <p>By: <?php echo htmlspecialchars($novel['author_name']); ?></p>
                        <a href="edit_novel.php?id=<?php echo $novel['novel_id']; ?>" class="btn btn-primary">Edit</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No novels found.</p>
        <?php endif; ?>

    </div>

</body>
</html>
