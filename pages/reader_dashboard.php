<?php
session_start();
include '../db/dbconnect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$query = "SELECT * FROM Users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) { die("User not found."); }

$username = htmlspecialchars($user['username']);
$role = $user['is_admin'] ? 'Admin' : ($user['is_writer'] ? 'Writer' : 'Reader');
$created_at = htmlspecialchars($user['created_at']);

$query = "SELECT Novels.title, Novels.author_name, Novels.genre, User_Books.read_status 
          FROM Novels 
          JOIN User_Books ON Novels.novel_id = User_Books.novel_id 
          WHERE User_Books.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f4f0e6; font-family: 'Merriweather', serif; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background-color: #4b3f3f; padding-top: 20px; border-right: 5px solid #dbc1ac; color: #fff; }
        .sidebar a { padding: 10px; text-decoration: none; font-size: 18px; color: #ffffff; display: block; }
        .sidebar a:hover { background-color: #6b5f5f; }
        .content { margin-left: 260px; padding: 20px; }
        .card { border-radius: 8px; border: 2px solid #dbc1ac; background-color: #f9f5e3; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
        .card-header { background-color: #dbc1ac; color: #4b3f3f; font-size: 24px; }
        .dark-mode { background-color: #212529; color: white; }
        .dark-mode .sidebar, .dark-mode .card { background-color: #343a40; color: white; }
        .dark-mode .table thead { background-color: #555; color: white; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center text-white">Dashboard</h4>
    <a href="../index.html" ><i class="fa fa-home"></i> Home</a>
    <a href="reader_dashboard.php"><i class="fa fa-user"></i> Profile</a>
    <a href="#" onclick="loadPage('library'); return false;"><i class="fa fa-book"></i> My Library</a>
    <a href="#" onclick="loadPage('settings'); return false;"><i class="fa fa-cog"></i> Settings</a>
    <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    <a href="#" onclick="toggleDarkMode()"><i class="fa fa-moon-o"></i> Dark Mode</a>

</div>

<!-- Main Content -->
<div class="content" id="main-content">
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header">Welcome, <?php echo $username; ?>!</div>
            <div class="card-body">
                <p><strong>Email:</strong> <?php echo $email; ?></p>
                <p><strong>Role:</strong> <?php echo $role; ?></p>
                <p><strong>Registered On:</strong> <?php echo $created_at; ?></p>
            </div>
        </div>

        <!-- Books Read -->
        <div class="mt-4">
            <h4>Books Read</h4>
            <table class="table table-bordered mt-2">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['genre']); ?></td>
                            <td><?php echo ucfirst($row['read_status']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>        
        </div>
    </div>
</div>

<script>


    //to load settings page
function loadPage(page) {
    $("#main-content").load("reader/" + page + ".php");
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
}
</script>

</body>
</html>
