<?php
include '../../db/dbconnect.php';

// Get novel ID and chapter number from URL (defaults to first chapter)
$novel_id = isset($_GET['novel_id']) ? intval($_GET['novel_id']) : 14;
$chapter_number = isset($_GET['chapter']) ? intval($_GET['chapter']) : 1;

// Fetch novel details
$novelQuery = "SELECT title, author_name, cover_image_url FROM Novels WHERE novel_id = $novel_id";
$novelResult = $conn->query($novelQuery);
$novel = $novelResult->fetch_assoc();

// Fetch the current chapter
$chapterQuery = "SELECT * FROM Chapters WHERE novel_id = $novel_id AND chapter_number = $chapter_number";
$chapterResult = $conn->query($chapterQuery);
$chapter = $chapterResult->fetch_assoc();

// Fetch all chapters for TOC navigation
$chaptersQuery = "SELECT chapter_number, title FROM Chapters WHERE novel_id = $novel_id ORDER BY chapter_number";
$chaptersResult = $conn->query($chaptersQuery);

// Fetch total chapters
$totalChaptersQuery = "SELECT COUNT(*) AS total FROM Chapters WHERE novel_id = $novel_id";
$totalChaptersResult = $conn->query($totalChaptersQuery);
$totalChapters = $totalChaptersResult->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read: <?php echo htmlspecialchars($novel['title']); ?></title>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        :root {
            --background-light: #fffaf0;
            --text-light: #3e2723;
            --border-light: #d4c0a1;
            --background-dark: #121212;
            --text-dark: #f5f5f5;
            --border-dark: #333;
        }
        body {
            font-family: 'Georgia', serif;
            background: var(--background-light);
            color: var(--text-light);
            margin: 0;
            padding: 20px;
            transition: background 0.3s, color 0.3s;
        }
        .container {
            max-width: 900px;
            width: 100%;
            margin: auto;
            background: var(--background-light);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--border-light);
        }

        .novel-title {
            font-size: 2rem;
            font-weight: bold;
            color: #8b4513;
            text-align: center;
        }
        .author {
            text-align: center;
            font-size: 1.2rem;
            color: #5d4037;
        }
        .chapter-title {
            font-size: 1.8rem;
            margin-top: 20px;
            color: #8b4513;
            text-align: center;
        }
        .chapter-content {
            text-align: justify;
            line-height: 1.8;
            font-family: 'Georgia', serif;
            white-space: pre-wrap;
            max-width: 90%;
            margin: auto;
        }

        .dark-mode .chapter-content {
            text-align: justify;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            font-size: 1.2rem;
            background: #8b4513;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:disabled {
            background: #d4c0a1;
            cursor: not-allowed;
        }
        .disabled-link {
        pointer-events: none;
        opacity: 0.5;
        }

        /* Table of Contents */
        .toc {
            background: #fdf8f2;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid var(--border-light);
        }
        .toc h3 {
            margin: 0;
            text-align: center;
            font-size: 1.4rem;
            color: #8b4513;
        }
        .toc select {
            width: 100%;
            padding: 8px;
            font-size: 1.1rem;
            margin-top: 10px;
        }
        /* Dark Mode */
        .dark-mode {
            background: var(--background-dark);
            color: var(--text-dark);
        }
        .dark-mode .container {
            background: var(--background-dark);
            border: 2px solid var(--border-dark);
        }
        .dark-mode .toc {
            background: #1e1e1e;
            border: 1px solid var(--border-dark);
        }
        .toggle-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 15px;
            background: #8b4513;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Dark Mode Toggle -->
<button class="toggle-btn" onclick="toggleDarkMode()">🌙 Dark Mode</button>

<div class="container">
    <div class="novel-title"><?php echo htmlspecialchars($novel['title']); ?></div>
    <div class="author">by <?php echo htmlspecialchars($novel['author_name']); ?></div>

    <!-- Table of Contents -->
    <div class="toc">
        <h3>Jump to Chapter</h3>
        <select onchange="location = this.value;">
            <?php while ($row = $chaptersResult->fetch_assoc()) { ?>
                <option value="?novel_id=<?php echo $novel_id; ?>&chapter=<?php echo $row['chapter_number']; ?>"
                    <?php echo ($row['chapter_number'] == $chapter_number) ? 'selected' : ''; ?>>
                    Chapter <?php echo $row['chapter_number']; ?>: <?php echo htmlspecialchars($row['title']); ?>
                </option>
            <?php } ?>
        </select>
    </div>

        <?php if ($chapter): ?>
        <div class="chapter-title">
            Chapter <?php echo $chapter['chapter_number']; ?>: <?php echo htmlspecialchars($chapter['title']); ?>
        </div>

        <div class="chapter-content">
            <?php echo nl2br(htmlspecialchars($chapter['content'])); ?>
        </div>

        <div class="nav-buttons">
        <a href="?novel_id=<?php echo $novel_id; ?>&chapter=<?php echo $chapter_number - 1; ?>" 
        class="btn <?php echo ($chapter_number <= 1) ? 'disabled-link' : ''; ?>">← Previous</a>

        <a href="?novel_id=<?php echo $novel_id; ?>&chapter=<?php echo $chapter_number + 1; ?>" 
        class="btn <?php echo ($chapter_number >= $totalChapters) ? 'disabled-link' : ''; ?>">Next →</a>

        </div>
    <?php else: ?>
        <p style="text-align: center; color: red;">Chapter not found.</p>
    <?php endif; ?>


    <div class="nav-buttons">
    <a href="?novel_id=<?php echo $novel_id; ?>&chapter=<?php echo $chapter_number - 1; ?>" 
    class="btn <?php echo ($chapter_number <= 1) ? 'disabled-link' : ''; ?>">← Previous</a>

    <a href="?novel_id=<?php echo $novel_id; ?>&chapter=<?php echo $chapter_number + 1; ?>" 
    class="btn <?php echo ($chapter_number >= $totalChapters) ? 'disabled-link' : ''; ?>">Next →</a>

    </div>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        let mode = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        localStorage.setItem('theme', mode);
    }
    // Load stored theme
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>

</body>
</html>
