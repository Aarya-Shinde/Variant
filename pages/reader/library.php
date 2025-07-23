<?php
session_start();
require '../../db/dbconnect.php'; // Ensure database connection is included

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle ADD action for novels
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novel_id']) && !isset($_POST['remove_novel_id'])) {
    $novel_id = $_POST['novel_id'];

    // Check if novel already exists
    $check_stmt = $conn->prepare("SELECT 1 FROM Library WHERE user_id = ? AND novel_id = ?");
    if (!$check_stmt) die("Check query failed: " . $conn->error);
    
    $check_stmt->bind_param("ii", $user_id, $novel_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        // Insert without title/author
        $stmt = $conn->prepare("INSERT INTO Library (user_id, novel_id) VALUES (?, ?)");
        if (!$stmt) die("Insert query failed: " . $conn->error);
        
        $stmt->bind_param("ii", $user_id, $novel_id);
        $stmt->execute();
        $stmt->close();
    }

    $check_stmt->close();
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}


/* ---------- ADD FAN-FICs into library  ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['fanfic_id'])
    && !isset($_POST['remove_fanfic_id'])) {

    $fanfic_id = $_POST['fanfic_id'];

    /* 1.  Already in library? */
    $check_stmt = $conn->prepare(
        "SELECT 1 FROM Library WHERE user_id = ? AND fanfic_id = ?"
    ) or die("Check query failed: " . $conn->error);

    $check_stmt->bind_param("ii", $user_id, $fanfic_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    /* 2.  Insert only if absent */
    if ($result->num_rows === 0) {
        $stmt = $conn->prepare(
            "INSERT INTO Library (user_id, fanfic_id) VALUES (?, ?)"
        ) or die("Insert query failed: " . $conn->error);

        $stmt->bind_param("ii", $user_id, $fanfic_id);
        $stmt->execute();
        $stmt->close();
    }

    $check_stmt->close();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


// Handle REMOVE action for novels
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_novel_id'])) {
    $remove_novel_id = $_POST['remove_novel_id'];

    // DELETE from Library
    $stmt = $conn->prepare("DELETE FROM Library WHERE user_id = ? AND novel_id = ?");
    $stmt->bind_param("ii", $user_id, $remove_novel_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Book removed successfully.";
    } else {
        $_SESSION['error'] = "Failed to remove book.";
    }

    $stmt->close();
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}

/* ---------- REMOVE action for novels FAN-FIC ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_fanfic_id'])) {
    $remove_fanfic_id = $_POST['remove_fanfic_id'];

    $stmt = $conn->prepare(
        "DELETE FROM Library WHERE user_id = ? AND fanfic_id = ?"
    );
    $stmt->bind_param("ii", $user_id, $remove_fanfic_id);

    $_SESSION[ $stmt->execute() ? 'message' : 'error' ]
        = $stmt->execute()
        ? "Fan-fic removed successfully."
        : "Failed to remove fan-fic.";

    $stmt->close();
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit();
}




// Fetch User’s Library both novels and fanfic

$query = "
    SELECT
        'novel'                       AS item_type,
        Novels.novel_id              AS item_id,
        Novels.title                 AS title,
        Novels.author_name           AS author,
        Library.read_status
    FROM Library
    JOIN Novels ON Novels.novel_id = Library.novel_id
    WHERE Library.user_id = ?

    UNION ALL

    SELECT
        'fanfic'                     AS item_type,
        Fanfic.fanfic_id            AS item_id,
        Fanfic.title                AS title,
        Fanfic.author               AS author,
        Library.read_status
    FROM Library
    JOIN Fanfic ON Fanfic.fanfic_id = Library.fanfic_id
    WHERE Library.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);  // bind twice (one per SELECT)
$stmt->execute();
$result = $stmt->get_result();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>
    <link rel="stylesheet" href="../style/library.css">

    <!-- Icon for deleting the library book -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

    <!-- Navigation Bar -->
    <div class="nav">
        <a href="../../index.php">Variant</a>
        <a href="../../template/about.html">About</a>
        <a href="./diary/diary.html">Diary</a>
        <a href="../reader_dashboard.php">User</a>
    </div>

    <!-- Library added books -->
    <div class="content">
        <div class="bookshelf-container">
            <div class="bookshelf-title">
                <span> Library </span>

                <a href="./book scraper\book upload.php"><button class="add-book-btn">Add Novels</button></a>
            </div>
            

            <div class="bookshelf">
                <div class="shelf-row">
                    <?php while ($row = $result->fetch_assoc()):
                        $isNovel = $row['item_type'] === 'novel';
                        $infoPage = $isNovel
                            ? "/Variant/template/novel/novel_info.php?novel_id={$row['item_id']}"
                            : "/Variant/template/fanfic/fanfic_info.php?fanfic_id={$row['item_id']}";
                        $hiddenField = $isNovel ? 'remove_novel_id' : 'remove_fanfic_id';
                    ?>
                    <div class="book">
                        <a href="<?= $infoPage ?>">
                            <?= htmlspecialchars($row['title']) ?>
                        </a>

                        <!-- delete tiny-form -->
                        <form method="post"
                                onsubmit="return confirm('Remove this from your library?');"
                                class="delete-form">
                            <input type="hidden"
                                    name="<?= $hiddenField ?>"
                                    value="<?= $row['item_id'] ?>">
                                    <button type="submit" class="delete-btn" aria-label="Delete">
                                    <i class="fas fa-times"></i>
                                    </button>
                        </form>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
