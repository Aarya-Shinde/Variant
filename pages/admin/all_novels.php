<?php
session_start();
include '../../db/dbconnect.php';  // Include database connection file

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

// Pagination setup
$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

//Search novels query

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$novels = [];

if (!empty($search)) {
    // Count total results
    $countQuery = "SELECT COUNT(*) as total FROM Novels 
                   WHERE title LIKE '%$search%' OR author_name LIKE '%$search%'";
    $countResult = $conn->query($countQuery);
    $total = $countResult->fetch_assoc()['total'];

    // Fetch paginated results
    $query = "SELECT * FROM Novels 
              WHERE title LIKE '%$search%' OR author_name LIKE '%$search%' 
              LIMIT $limit OFFSET $offset";
    $result = $conn->query($query);

} else {
    // Count total
    $countQuery = "SELECT COUNT(*) as total FROM Novels";
    $countResult = $conn->query($countQuery);
    $total = $countResult->fetch_assoc()['total'];

    // Fetch paginated
    $query = "SELECT * FROM Novels LIMIT $limit OFFSET $offset";
    $result = $conn->query($query);
}

// Store results
while ($row = $result->fetch_assoc()) {
    $novels[] = $row;
}

// Total pages
$totalPages = ceil($total / $limit);


// Handle novel deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM Novels WHERE novel_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
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

    <link rel="stylesheet" href="style/admin_dash.css">

</head>
<body>

 <!-- Sidebar -->
<?php include './sidebar.php'; ?>

        <!-- Main Content -->
    <div class="main-content">

        <!-- Search & Display Novels -->
        <form method="GET" action="" class="search-box">
            <input type="text" name="search" placeholder="Search Novel..." class="form-control">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
         <!-- Working the search button -->
                <?php if (!empty($novels)): ?>
                    <!-- Display all the novels on the website -->
            <div class="novel-container">
                <?php foreach ($novels as $novel): ?>
                    <div class="novel-card">
                        <img src="<?php echo htmlspecialchars($novel['cover_image_url']); ?>" alt="Cover Image">
                        <h5><?php echo htmlspecialchars($novel['title']); ?></h5>
                        <p>By: <?php echo htmlspecialchars($novel['author_name']); ?></p>
                        <a href="admin/edit_novel.php?id=<?php echo $novel['novel_id']; ?>" class="btn btn-primary">Edit</a>
                        <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this novel?');">
                            <input type="hidden" name="delete_id" value="<?php echo $novel['novel_id']; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>

                        
                    </div> 
                 <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No novels found.</p>
        <?php endif; ?>

        <!-- Pagination -->
         <?php if ($totalPages > 1): ?>
        <div class="pagination justify-content-center mt-4">

            <!-- Prev -->
            <?php if ($page > 1): ?>
                <a class="btn btn-outline-primary mx-1"
                href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>">Prev</a>
            <?php endif; ?>

            <?php
            $range = 2; // pages around current
            $start = max(1, $page - $range);
            $end = min($totalPages, $page + $range);

            // Show first page
            if ($start > 1) {
                echo '<a class="btn btn-outline-primary mx-1" href="?page=1&search='.$search.'">1</a>';
                if ($start > 2) echo '<span class="mx-1">...</span>';
            }

            // Middle pages
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-outline-primary'; ?> mx-1"
                href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>">
                <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php
            // Show last page
            if ($end < $totalPages) {
                if ($end < $totalPages - 1) echo '<span class="mx-1">...</span>';
                echo '<a class="btn btn-outline-primary mx-1" href="?page='.$totalPages.'&search='.$search.'">'.$totalPages.'</a>';
            }
            ?>

            <!-- Next -->
            <?php if ($page < $totalPages): ?>
                <a class="btn btn-outline-primary mx-1"
                href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>">Next</a>
            <?php endif; ?>

        </div>
        <?php endif; ?>




    </div>
</body>
</html>