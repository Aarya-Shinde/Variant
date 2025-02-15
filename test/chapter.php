<?php
include '../db/dbconnect.php';  // Include database connection file

$novel_id = isset($_GET['novel_id']) ? intval($_GET['novel_id']) : 14; // Default to novel ID 1
$sql = "SELECT n.title AS novel_title, n.author_name, n.cover_image_url, 
               c.chapter_number, c.title AS chapter_title, c.content 
        FROM Novels n 
        JOIN Chapters c ON n.novel_id = c.novel_id 
        WHERE n.novel_id = $novel_id 
        ORDER BY c.chapter_number ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novel Reader</title>
    <link rel="stylesheet" href="styles.css"> 
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <?php if ($result->num_rows > 0) {
        $firstRow = $result->fetch_assoc(); ?>
        <div class="info">Reading: <?php echo htmlspecialchars($firstRow['novel_title']); ?></div>
        <div class="laptop-layout">
            <div class="notification-header">
                <div class="time">9:41</div>
                <div class="necessities">
                    <i class="fas fa-signal"></i>
                    <i class="fas fa-wifi"></i>
                    <i class="fas fa-battery-full"></i>
                </div>
            </div>
            <div class="actions">
                <i class="fas fa-chevron-left"></i>
                <i class="fas fa-bookmark"></i>
            </div>  
            <div class="book-cover">
                <img class="book-top" src="<?php echo htmlspecialchars($firstRow['cover_image_url']); ?>" alt="Book Cover" />
            </div>
            <div class="preface">
                <div class="content">
                    <div class="header">
                        <div class="title"><?php echo htmlspecialchars($firstRow['novel_title']); ?></div>
                        <div class="icon"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="author">by <?php echo htmlspecialchars($firstRow['author_name']); ?></div>
                    <div class="body">
                        <?php do { ?>
                            <h3>Chapter <?php echo $firstRow['chapter_number']; ?>: <?php echo htmlspecialchars($firstRow['chapter_title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($firstRow['content'])); ?></p>
                        <?php } while ($firstRow = $result->fetch_assoc()); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="info">No Chapters Found</div>
    <?php } ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
