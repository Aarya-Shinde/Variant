<?php
session_start();

include '../../db/dbconnect.php';
// Check if the user is an admin
if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch novels from database
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $searchQuery = "WHERE title LIKE '%$search%'";
}

$novelQuery = "SELECT * FROM Novels $searchQuery ORDER BY created_at DESC";
$novelResult = $conn->query($novelQuery);

// Handle delete novel request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM Novels WHERE novel_id = $delete_id");
    header("Location: manage_novels.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
<!-- <style>

    .container {
        background: rgba(255, 255, 255, 0.9);
        margin-top: 20px;
        padding: 20px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
        border: 2px solid #d4c0a1;
    }
    .novel-table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        font-family: 'Georgia', serif;
    }
    .novel-table th, .novel-table td {
        padding: 10px;
        border-bottom: 1px solid #d4c0a1;
        text-align: left;
    }
    .novel-table th {
        background: rgba(210, 180, 140, 0.2);
        color: #8b4513;
    }
    .btn-danger {
        background: #8b4513;  /* Saddle Brown */
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        color: #fff;
        border-radius: 5px;
        font-family: 'Georgia', serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .btn-warning {
        background: #cd853f;  /* Peru */
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        color: #fff;
        border-radius: 5px;
        font-family: 'Georgia', serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .btn-success {
        background: #6b8e23;  /* Olive Drab */
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        color: #fff;
        border-radius: 5px;
        font-family: 'Georgia', serif;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .btn-danger:hover, .btn-warning:hover, .btn-success:hover {
        transform: scale(1.05);
        opacity: 0.8;
    }
</style> -->

</head>

<body>

 <!-- Sidebar -->
<?php include './sidebar.php'; ?>

<div class="container">
    <h2>Manage Novels</h2>

    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <form method="GET" action="" style="display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Search by title..." class="form-control" style="width: 300px;">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <a href="add_novel.php" class="btn btn-success" style="margin-left: 600px;">Add Novel</a>
    </div>

<!-- Manage Novels Table -->
    <table class="novel-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($novel = $novelResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $novel['novel_id']; ?></td>
                    <td><?php echo htmlspecialchars($novel['title']); ?></td>
                    <td><?php echo htmlspecialchars($novel['author_name']); ?></td>
                    <td><?php echo htmlspecialchars($novel['genre']); ?></td>
                    <td><?php echo $novel['created_at']; ?></td>
                    <td>
                        <a href="edit_novel.php?id=<?php echo $novel['novel_id']; ?>" class="btn btn-warning">Edit</a>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $novel['novel_id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
