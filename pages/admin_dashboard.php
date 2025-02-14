<?php
session_start();
include '../db/dbconnect.php';  // Include database connection file

// Check if the user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php"); // Redirect to login page if not an admin
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

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm navbar-light">
        <div class="container">
            <a class="navbar-brand text-white" href="#" style="font-weight: bold;"></a>
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav m-auto mt-2 mt-lg-0"></ul>
                <form class="d-flex my-2 my-lg-0">
                    <a href="logout.php" class="btn btn-light btn-logout my-2 my-sm-0">Logout</a>
                </form>
            </div>
        </div>
    </nav>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #f3f4f6, #e5e7eb);
            color: #333;
        }
        nav {
            background-color: #0b0a07;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .btn-logout {
            color: brown;
            font-weight: bold;
            background-color: black;
        }
        .sidebar {
            width: 250px;
            background-color: #0b0a07;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding: 0.5rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar .logo {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 2.5rem;
            color: #a2a79e;
            text-align: center;
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
            transition: background-color 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: #a77464;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        .header {
            background: linear-gradient(to right, #88292f, #a77464);
            color: white;
            padding: 1.5rem;
            text-align: center;
            font-size: 2.2rem;
            font-weight: bold;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #a2a79e 60%, #88292f 30%, #0b0a07 10%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
         
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 1rem;
            text-align: left;
        }
        table th {
            background-color: #a77464;
            color: white;
        }
        table td {
            background-color: #f9fafb;
        }
        .add-button {
            padding: 0.8rem 1.5rem;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .add-button:hover {
            background-color: #2563eb;
        }
    </style>

    <div class="sidebar">
        <div class="logo">Admin Panel</div>
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="add_book.php">Manage Books</a></li>
            <li><a href="#">Manage Categories</a></li>
            <li><a href="#">User Management</a></li>
            <li><a href="#">Novels</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">Welcome to Admin Dashboard</div>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Books</h3>
                <p>1,234</p>
            </div>
            <div class="stat-card">
                <h3>Total Users</h3>
                <p>567</p>
            </div>
            <div class="stat-card">
                <h3>Categories</h3>
                <p>12</p>
            </div>
            <div class="stat-card">
                <h3>Reports</h3>
                <p>34</p>
            </div>
        </div>
 
    </div>
 
</body>
</html>
