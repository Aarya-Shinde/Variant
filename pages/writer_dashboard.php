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

// // Fetch dashboard data
// $query = "SELECT 
//             (SELECT COUNT(*) FROM books WHERE status='published') AS published_books,
//             (SELECT COUNT(*) FROM books WHERE status='in-progress') AS in_progress_books,
//             (SELECT COUNT(*) FROM chapters) AS total_chapters,
//             (SELECT COUNT(*) FROM subscriptions WHERE MONTH(created_at) = MONTH(CURRENT_DATE())) AS monthly_subscribers,
//             (SELECT COUNT(*) FROM subscriptions) AS total_subscribers,
//             (SELECT title, downloads, ratings FROM books ORDER BY downloads DESC LIMIT 1) AS top_book,
//             (SELECT COUNT(*) FROM readers) AS total_readers,
//             (SELECT COUNT(*) FROM comments) AS total_comments";
// $stmt = $pdo->query($query);
// $data = $stmt->fetch(PDO::FETCH_ASSOC);
// 


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
    <link rel="stylesheet" href="style/writer_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
                <!-- variant logo image here -->
            <div class="logo"> 
                <a href="../index.php"><img src="../images/logowrite.png" alt="Variant Logo" style="max-height: 80px;">
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
                <div class="stats-container">
        <div class="stat-card">
            <h3>Library</h3>
            <p>Published Books: <?= $data['published_books'] ?></p>
            <p>Books in Progress: <?= $data['in_progress_books'] ?></p>
            <p>Chapters Written: <?= $data['total_chapters'] ?></p>
        </div>

        <div class="stat-card">
            <h3>Subscriptions</h3>
            <p>New This Month: <?= $data['monthly_subscribers'] ?></p>
            <p>Total Subscribers: <?= $data['total_subscribers'] ?></p>
        </div>

        <div class="stat-card">
            <h3>Top Performing Book</h3>
            <p>Title: <?= $data['top_book']['title'] ?? 'N/A' ?></p>
            <p>Downloads: <?= $data['top_book']['downloads'] ?? 0 ?></p>
            <p>Ratings: <?= $data['top_book']['ratings'] ?? 'N/A' ?></p>
        </div>

        <div class="stat-card">
            <h3>Reader Engagement</h3>
            <p>Total Readers: <?= $data['total_readers'] ?></p>
            <p>Comments Received: <?= $data['total_comments'] ?></p>
        </div>
    </div>
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

