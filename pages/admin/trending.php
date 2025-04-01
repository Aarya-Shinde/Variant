<?php
session_start();
include '../../db/dbconnect.php';  // Database connection

// Ensure only admin can access
if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch most popular novels by genre (sorted by views)
$popularNovelsByGenre = [];

$query = "SELECT genre, title, author_name, cover_image_url, views
          FROM Novels
          ORDER BY genre, views DESC";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $popularNovelsByGenre[$row['genre']][] = $row;
}

// Fetch most popular authors (authors with the highest total views across all books)
$popularAuthors = [];

$authorQuery = "SELECT author_name, SUM(views) AS total_views
                FROM Novels
                GROUP BY author_name
                ORDER BY total_views DESC
                LIMIT 10";

$authorResult = $conn->query($authorQuery);

while ($row = $authorResult->fetch_assoc()) {
    $popularAuthors[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Books & Authors</title>
    <link rel="stylesheet" href="dashboard_style/admin_dash.css">
</head>
<body>

    <div class="main-content">
        <h2>Most Popular Books by Genre</h2>

        <?php if (!empty($popularNovelsByGenre)): ?>
            <?php foreach ($popularNovelsByGenre as $genre => $novels): ?>
                <h3><?php echo htmlspecialchars($genre); ?></h3>
                <div class="novel-container">
                    <?php foreach ($novels as $novel): ?>
                        <div class="novel-card">
                            <img src="<?php echo htmlspecialchars($novel['cover_image_url']); ?>" alt="Cover Image">
                            <h5><?php echo htmlspecialchars($novel['title']); ?></h5>
                            <p>By: <?php echo htmlspecialchars($novel['author_name']); ?></p>
                            <p>Views: <?php echo htmlspecialchars($novel['views']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No trending books found.</p>
        <?php endif; ?>

        <h2>Top 10 Trending Authors</h2>

        <?php if (!empty($popularAuthors)): ?>
            <ul class="list-group">
                <?php foreach ($popularAuthors as $author): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($author['author_name']); ?> - <?php echo $author['total_views']; ?> total views
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No trending authors found.</p>
        <?php endif; ?>

    </div>

</body>
</html>
