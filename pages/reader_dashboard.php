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

$query = "SELECT Novels.title, Novels.author_name, Novels.genre, Library.read_status 
          FROM Novels 
          JOIN Library ON Novels.novel_id = Library.novel_id 
          WHERE Library.user_id = ?";
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
    body {
        font-family: 'Arial', sans-serif;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        height: 100vh;
        margin: 0;
        background-color: #e6ccb2;
        color: #4b3f3f;
    }

    .sidebar {
        height: 100vh;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #4b3f3f;
        padding-top: 20px;
        border-right: 5px solid #dbc1ac;
        color: #fff;
    }

    .sidebar a {
        padding: 10px;
        text-decoration: none;
        font-size: 18px;
        color: #ffffff;
        display: block;
    }

    .sidebar a:hover {
        background-color: #6b5f5f;
    }

    .content {
        margin-left: 260px;
        padding: 20px;
    }

    .card {
        border-radius: 8px;
        border: 2px solid #dbc1ac;
        background-color: #f5e6ca;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        color: #4b3f3f;
    } 

    .card-header {
        background-color: #4b3f3f; /* Warm brown */
        color: white;
        font-size: 25px;
    }

    .table thead {
        background-color: #4b3f3f;
        color: #f5e6ca;
    }
    .table tbody {
        background-color: #f5e6ca;
        color: #4b3f3f;
    }
    .dark-mode {
    background-color: #212529 !important; /* Dark mode background */
    color: #e0e0e0 !important; /* Light text */
    }

        .dark-mode .table {
            background-color: #343a40; /* Dark table background */
            color: #e0e0e0; /* Light text */
        }

        .dark-mode .sidebar {
            background-color: #343a40;
            border-right: 5px solid #555;
        }

        .dark-mode .sidebar a {
            color: #e0e0e0;
        }

        .dark-mode .sidebar a:hover {
            background-color: #495057;
        }

        .dark-mode .content {
            background-color: #212529;
            color: #e0e0e0;
        }

        .dark-mode .card {
            background-color: #343a40;
            color: #e0e0e0;
        }
        .dark-mode .card-header {
            background-color: #495057;
            color: #e0e0e0;
        }

        .dark-mode .table tbody {
        background-color: #343a40; /* Dark table background */
        color: #e0e0e0 !important; /* Light text */
        }

        .dark-mode .table td {
            border-color: #555; /* Ensure visible borders */
        }

        .dark-mode .table thead {
            background-color: #495057; /* Slightly lighter for contrast */
            color: #e0e0e0;
        }



</style>
</head>
<body>
    <!-- -----sidebar------>

    <div class="sidebar">
    <div class="logo"> <a href="index.php"><img src="../images/logowrite.png" alt="Variant Logo" style="max-height: 70px;"></a> </div>

    <a href="../index.php"><i class="fa fa-home"></i> Home</a>
    <a href="reader_dashboard.php"><i class="fa fa-user"></i> Profile</a>
    <a href="#" onclick="loadPage('reader_space')"><i class="fa fa-book"></i> Reader's Space</a>
    <a href="#" onclick="loadPage('settings')"><i class="fa fa-cog"></i> Settings</a>
    <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
    <a href="#" onclick="toggleDarkMode()"><i class="fa fa-moon-o"></i> Dark Mode</a>
</div>

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

        <div class="mt-4">
            <h4>Books Read</h4>
            <table class="table table-bordered mt-2">
                <thead>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function loadPage(page) {
    let pagePath = "reader/" + page + ".php";

    $("#main-content").load(pagePath, function(response, status, xhr) {
        if (status == "error") {
            console.error("Error loading page:", xhr.status, xhr.statusText);
        } else {
            if (document.body.classList.contains("dark-mode")) {
                document.getElementById("main-content").classList.add("dark-mode");
            }
        }
    });
}

    // Function to toggle dark mode
    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
    }


</script>

</body>
</html>
