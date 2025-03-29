<?php
session_start();
require '../db/dbconnect.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT is_writer, username FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$is_writer = $user['is_writer'];
$username = $user['username'];

// Fetch user's novels if they are a writer
$novels = [];
if ($is_writer) {
    $sql = "SELECT * FROM Novels WHERE author_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $novels = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch analytics data
// $sql = "SELECT SUM(views) as total_views, SUM(collections) as total_collections, COUNT(DISTINCT review_id) as total_reviews, COUNT(DISTINCT comment_id) as total_comments FROM Analytics WHERE author_id = ?";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $result = $stmt->get_result();
// $analytics = $result->fetch_assoc();

// $total_views = $analytics['total_views'] ?? 0;
// $total_collections = $analytics['total_collections'] ?? 0;
// $total_reviews = $analytics['total_reviews'] ?? 0;
// $total_comments = $analytics['total_comments'] ?? 0;


// Handle becoming a writer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['become_writer']) && !$is_writer) {
    $sql = "UPDATE Users SET is_writer = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        header("Location: writer_dashboard.php"); // Reload page
        exit();
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./writer/writer_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
                <!-- variant logo image here -->
            <div class="logo"> 
                <a href="../../index.html"><img src="../images/logowrite.png" alt="Variant Logo" style="max-height: 40px;">
            </a> </div>

            <ul>
                <li class="menu-item active" data-section="dashboard"> Dashboard</li>
                <li class="menu-item" data-section="stories">Stories</li>
                <li class="menu-item" data-section="income"> Income</li>
                <li class="menu-item" data-section="inbox">Inbox</li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">



        <section id="dashboard" class="section active">
                <h2>Welcome, <?= htmlspecialchars($username) ?></h2>
                <p>Overview of your writing journey.</p>
            </section>

            <section id="analytics" class="section">
                <h2>Story Analytics</h2>
                <p>Track your novel's performance.</p>
                <div class="stats">
                    <p><strong>Total Views:</strong> <?= $total_views ?></p>
                    <p><strong>Total Collections:</strong> <?= $total_collections ?></p>
                    <p><strong>Total Reviews:</strong> <?= $total_reviews ?></p>
                    <p><strong>Total Comments:</strong> <?= $total_comments ?></p>
                </div>
            </section>



            <section id="stories" class="section">
    <div class="top-bar">
        <h2>My Stories</h2>
        <button id="createStoryBtn">+ Create a Story</button>
    </div>

    <?php if ($is_writer): ?>
        <table>
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Publication Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($novels as $novel): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($novel['cover_image_url']) ?>" alt="Cover" width="50"></td>
                        <td><?= htmlspecialchars($novel['title']) ?></td>
                        <td><?= htmlspecialchars($novel['genre']) ?></td>
                        <td><?= htmlspecialchars($novel['publication_date']) ?></td>
                        <td>
                            <a href="writer/edit_story.html?novel_id=<?= $novel['novel_id'] ?>" class="edit-btn">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h2>Become a Writer</h2>
        <form method="post">
            <button type="submit" name="become_writer">Become a Writer</button>
        </form>
    <?php endif; ?>
</section>


            <section id="income" class="section">
                <h2> Income</h2>
                <p>Your earnings from published stories.</p>
            </section>

            <section id="inbox" class="section">
                <h2> Inbox</h2>
                <p>Messages from readers and publishers.</p>
            </section>
        </main>
    </div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll(".menu-item");
    const sections = document.querySelectorAll(".section");

    // Sidebar click event
    menuItems.forEach((item) => {
        item.addEventListener("click", function () {
            // Remove active class from all menu items
            menuItems.forEach((el) => el.classList.remove("active"));
            this.classList.add("active");

            // Show the selected section
            const sectionId = this.getAttribute("data-section");
            sections.forEach((section) => {
                section.classList.remove("active");
                if (section.id === sectionId) {
                    section.classList.add("active");
                }
            });
        });
    });

    // "Create Story" button
    document.getElementById("createStoryBtn").addEventListener("click", function () {
        alert("Redirect to Story Creation Page (Implement as Needed)");
    });
});


</script>
</body>
</html>

