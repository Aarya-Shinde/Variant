<?php

    include '../../db/dbconnect.php';

    $novel_id = isset($_GET['novel_id']) ? intval($_GET['novel_id']) : 0;
    $chapter_number = isset($_GET['chapter_number']) ? intval($_GET['chapter_number']) : 0;

    if ($novel_id === 0 || $chapter_number === 0) {
        die("Invalid request.");
    }

    // Fetch novel details
    $novelQuery = "SELECT title, author_name, cover_image_url FROM Novels WHERE novel_id = $novel_id";
    $novelResult = $conn->query($novelQuery);
    $novel = $novelResult->fetch_assoc();

    // Fetch selected chapter
    $chapterQuery = "SELECT * FROM Chapters WHERE novel_id = $novel_id AND chapter_number = $chapter_number";
    $chapterResult = $conn->query($chapterQuery);
    $chapter = $chapterResult->fetch_assoc();

    // Fetch total chapters
    $totalChaptersQuery = "SELECT COUNT(*) as total FROM Chapters WHERE novel_id = $novel_id";
    $totalChaptersResult = $conn->query($totalChaptersQuery);
    $totalChapters = $totalChaptersResult->fetch_assoc()['total'];

    // Fetch all chapters for dropdown
    $chaptersQuery = "SELECT chapter_number, title FROM Chapters WHERE novel_id = $novel_id ORDER BY chapter_number ASC";
    $chaptersResult = $conn->query($chaptersQuery);

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($chapter['title'] . ' - ' . $novel['title']); ?></title>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <link rel="stylesheet" href="css/chapter.css">

    <!-- To load the dark toogle icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>

<button class="dark-mode-btn" onclick="toggleDarkMode()">
    <i class="fas fa-moon"></i> 
</button>


<div class="container">
    <div class="novel-title"><?php echo htmlspecialchars($novel['title']); ?></div>
    <div class="author">by <?php echo htmlspecialchars($novel['author_name']); ?></div>

    <div class="toc">
        <h3>Jump to Chapter</h3>
        <select onchange="location = this.value;">
            <?php while ($row = $chaptersResult->fetch_assoc()) { ?>
                <option value="?novel_id=<?php echo $novel_id; ?>&chapter_number=<?php echo $row['chapter_number']; ?>"
                    <?php echo ($row['chapter_number'] == $chapter_number) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['title']); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <?php if ($chapter): ?>
    <div class="chapter-title"><?php echo htmlspecialchars($chapter['title']); ?></div>

    <!-- previous and next buttons at the start of the page -->
    <div class="nav-buttons">
        <a href="?novel_id=<?php echo $novel_id; ?>&chapter_number=<?php echo $chapter_number - 1; ?>" 
            class="btn <?php echo ($chapter_number <= 1) ? 'disabled-link' : ''; ?>">← Previous</a>

        <a href="?novel_id=<?php echo $novel_id; ?>&chapter_number=<?php echo $chapter_number + 1; ?>" 
            class="btn <?php echo ($chapter_number >= $totalChapters) ? 'disabled-link' : ''; ?>">Next →</a>
    </div>

    <div class="chapter-content">
        <?php
        function cleanText($text) {
            // Ensure paragraph breaks are properly formatted
            $text = preg_replace('/<\/p>\s*<p>/', "</p>\n<p>", $text); // Ensure correct paragraph separation
            $text = preg_replace('/<p>\s*<\/p>/', '', $text); // Remove empty <p> tags

            // Wrap plain text in <p> tags if it's not already HTML
            if (strip_tags($text) === $text) {
                $paragraphs = explode("\n", trim($text)); // Split by line breaks
                $text = "";
                foreach ($paragraphs as $para) {
                    if (!empty(trim($para))) {
                        $text .= "<p>" . htmlspecialchars($para) . "</p>\n"; // Wrap each paragraph
                    }
                }
            }

            return $text;
        }

        echo cleanText($chapter['content']);
        ?>
    </div>

    <!-- previous and next buttons at the end of the page -->
    <div class="nav-buttons">
        <a href="?novel_id=<?php echo $novel_id; ?>&chapter_number=<?php echo $chapter_number - 1; ?>" 
            class="btn <?php echo ($chapter_number <= 1) ? 'disabled-link' : ''; ?>">← Previous</a>

        <a href="?novel_id=<?php echo $novel_id; ?>&chapter_number=<?php echo $chapter_number + 1; ?>" 
            class="btn <?php echo ($chapter_number >= $totalChapters) ? 'disabled-link' : ''; ?>">Next →</a>
    </div>

<?php else: ?>
    <p style="text-align: center; color: red;">Chapter not found.</p>
<?php endif; ?>
</div>


<script>

document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    const darkModeBtn = document.querySelector(".dark-mode-btn");
    const icon = darkModeBtn.querySelector("i");

    // Check local storage for dark mode preference
    if (localStorage.getItem("theme") === "dark") {
        body.classList.add("dark-mode");
        icon.classList.replace("fa-moon", "fa-sun"); // Change icon to sun
    }

    darkModeBtn.addEventListener("click", function () {
        body.classList.toggle("dark-mode");

        if (body.classList.contains("dark-mode")) {
            localStorage.setItem("theme", "dark");
            icon.classList.replace("fa-moon", "fa-sun"); // Change icon to sun
        } else {
            localStorage.setItem("theme", "light");
            icon.classList.replace("fa-sun", "fa-moon"); // Change back to moon
        }
    });
});


</script>

</body>
</html>
