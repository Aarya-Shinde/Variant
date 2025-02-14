<?php
session_start();
include '../db/dbconnect.php';  // Include database connection file

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
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
$username = $user ? htmlspecialchars($user['username']) : "User";
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
    <title>User Dashboard</title>

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm navbar-light">
        <div class="container">
            <a class="navbar-brand text-white" href="#" style="font-weight: bold;">User Dashboard</a>
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
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-color: #A2A79E;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 0px;
    }

    .navbar {
        background-color: #0B0A07;
    }
    .btn-logout {
        color: #007bff;
        font-weight: bold;
    }

    .dashboard {
      width: 100%;
      max-width: 1400px;
      background-color: #0B0A07;
      border-radius: 5px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }

    .header {
      background: linear-gradient(90deg, #88292F, #A77464);
      color: #ffffff;
      padding: 20px;
      text-align: center;
      font-size: 1.8rem;
      font-weight: bold;
    }

    .dashboard-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      padding: 24px;
    }

    .card {
      background-color: #fafafa;
      border: 1px solid #d7ccc8;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card h2 {
      font-size: 1.4rem;
      color: #88292F;
      margin-bottom: 12px;
    }

    .graph-section {
      display: flex;
      flex-wrap: wrap;
      gap: 24px;
      padding: 24px;
      background-color: #A77464;
      border-radius: 12px;
      margin: 24px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .graph-container {
      flex: 1;
      min-width: 300px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .graph-container h3 {
      font-size: 1.8rem;
      color: #ffffff;
      margin-bottom: 16px;
    }

    .graph {
      width: 100%;
      max-width: 500px;
      height: 300px;
      background: linear-gradient(135deg, #A77464, #88292F);
      border-radius: 12px;
      overflow: hidden;
      position: relative;
    }

    .graph canvas {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }

    .image-section {
      flex: 1;
      min-width: 300px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .image-section img {
      width: 100%;
      max-width: 300px;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .footer {
      background: #4e342e;
      color: #ffffff;
      text-align: center;
      padding: 16px;
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .header {
        font-size: 1.5rem;
      }

      .dashboard-content {
        grid-template-columns: 1fr;
      }

      .graph-section {
        flex-direction: column;
      }
    }
  </style>
 
 
  <div class="dashboard">
    <div class="header">User Dashboard</div>
    <div class="dashboard-content">
      <div class="card">
        <h2>About Me</h2>
        <p>Name: Kajal Mane</p>
        <p>Email: kajal515@gmail.com</p>
        <p>Role: User</p>
      </div>
      <div class="card">
        <h2>User Books History</h2>
        <p>Books Read: 10</p>
        <p>Favorites: 3</p>
      </div>
      <div class="card">
        <h2>Registration Date</h2>
        <p>01 January 2023</p>
      </div>
      <div class="card">
        <h2>User Activity Data</h2>
        <p>Active Users: 200</p>
        <p>Inactive Users: 50</p>
      </div>
      <div class="card">
        <h2>Management Section</h2>
        <p>Total Users: 1500</p>
      </div>
      <div class="card">
        <h2>Notifications and Alerts</h2>
        <p>Unread Messages: 12</p>
      </div>
      <div class="card">
        <h2>Content Management</h2>
        <p>Pending Approvals: 20</p>
      </div>
      <div class="card">
        <h2>Customizable Widgets</h2>
        <p>Widgets Available: 5</p>
      </div>
      <div class="card">
        <h2>Dark Mode Toggle</h2>
        <p>Switch between light and dark modes.</p>
      </div>
    </div>
    <div class="graph-section">
      <div class="graph-container">
        <h3>User Engagement</h3>
        <div class="graph">
          <canvas id="userGraph"></canvas>
        </div>
      </div>
      <div class="image-section">
        <img src="https://via.placeholder.com/300" alt="Placeholder Image">
      </div>
    </div>
    <br>
    <br>
    <div class="footer">&copy; 2025 Admin Dashboard | All Rights Reserved</div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('userGraph').getContext('2d');
    const userGraph = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Screen Time', 'Books Read', 'Interests'],
        datasets: [{
          label: 'User Data',
          data: [8, 5, 10],
          backgroundColor: ['#A77464', '#88292F', '#A2A79E'],
          borderColor: '#0B0A07',
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            ticks: { color: '#ffffff' },
            grid: { display: false }
          },
          y: {
            ticks: { color: '#ffffff' },
            grid: { color: 'rgba(255, 255, 255, 0.2)' }
          }
        }
      }
    });
  </script>
</body>
 

</html>
