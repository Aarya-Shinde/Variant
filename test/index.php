<?php
include '../db/dbconnect.php';

// Fetch novels data
$sql = "SELECT 
            novel_id, 
            title, 
            author_name, 
            publication_date, 
            genre, 
            cover_image_url, 
            total_chapters, 
            series_name, 
            series_position, 
            description, 
            rating, 
            language 
        FROM Novels 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novels Library</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">MTLNovels</div>
        <nav>
            <a href="#">Library</a>
            <a href="#">New Novels</a>
            <a href="#">Upcoming</a>
            <a href="#">Ranking</a>
            <a href="#">Categories</a>
            <a href="#">Chat</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- New Novels Section -->
        <section class="new-novels">
            <h2>New Novels</h2>
            <div class="novels-grid">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <div class='novel-card'>
                            <img src='{$row['cover_image_url']}' alt='{$row['title']}' class='novel-cover'>
                            <h3>{$row['title']}</h3>
                            <p><strong>Author:</strong> {$row['author_name']}</p>
                            <p><strong>Genre:</strong> {$row['genre']}</p>
                            <p><strong>Chapters:</strong> {$row['total_chapters']}</p>
                            <p><strong>Series:</strong> {$row['series_name']} (Book {$row['series_position']})</p>
                            <p><strong>Rating:</strong> ⭐{$row['rating']}</p>
                            <p><strong>Language:</strong> {$row['language']}</p>
                            <p><strong>Description:</strong> {$row['description']}</p>
                            <p><strong>Published:</strong> {$row['publication_date']}</p>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>No novels available.</p>";
                }

                $conn->close();
                ?>
            </div>
        </section>
    </main>
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #0078d7;
    padding: 10px 20px;
    color: white;
}

.navbar a {
    color: white;
    text-decoration: none;
    margin: 0 10px;
}

.novels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px;
}

.novel-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.novel-cover {
    width: 100%;
    height: auto;
    border-radius: 5px;
    margin-bottom: 10px;
}

h2 {
    text-align: center;
    margin-top: 20px;
}

.novel-card h3 {
    margin: 10px 0 5px;
    font-size: 18px;
}

.novel-card p {
    margin: 5px 0;
    font-size: 14px;
    color: #555;
}
</style>
</body>
</html>
