<!-- page lets users pick Reader, Writer, or Admin dashboard. -->

<!-- 
If the user is only a reader, they go directly to reader_dashboard.php.
If they are a writer or an admin, they are redirected to choose_dashboard.php to pick a role.
The choose_dashboard.php page dynamically shows buttons based on user roles.
The redirect_dashboard.php file handles their selection. 
-->

<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_choice'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f4f0e6; 
            font-family: 'Merriweather', serif; 
            color: #4b3f3f; 
        }
        .container { 
            background-color: #f9f5e3; 
            border: 2px solid #dbc1ac; 
            border-radius: 8px; 
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); 
            padding: 40px; 
            max-width: 600px; 
        }
        h2 { 
            font-family: 'Caveat', cursive; 
            margin-bottom: 30px; 
        }
        .btn-primary { 
            background-color: #4b3f3f; 
            border-color: #4b3f3f; 
        }
        .btn-primary:hover { 
            background-color: #6b5f5f; 
        }
        .btn-success { 
            background-color: #6c757d; 
            border-color: #6c757d; 
        }
        .btn-success:hover { 
            background-color: #5a6268; 
        }
        .btn-danger { 
            background-color: #343a40; 
            border-color: #343a40; 
        }
        .btn-danger:hover { 
            background-color: #23272b; 
        }
    </style>
</head>
<body>
    <div class="container text-center mt-5">
        <h2>Choose Your Dashboard</h2>
        <form method="POST" action="redirect_dashboard.php">
            <button type="submit" name="dashboard" value="reader" class="btn btn-primary m-2">Reader Dashboard</button>

            <?php if ($_SESSION['is_writer']): ?>
                <button type="submit" name="dashboard" value="writer" class="btn btn-success m-2">Writer Dashboard</button>
            <?php endif; ?>

            <?php if ($_SESSION['is_admin']): ?>
                <button type="submit" name="dashboard" value="admin" class="btn btn-danger m-2">Admin Dashboard</button>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
