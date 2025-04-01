<?php 
session_start();
include '../../db/dbconnect.php';

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


// Check if session contains user_id or if there is a cookie containing the ID
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("User session not found! Please log in.");
}

$user_id = $_SESSION['user_id'];



// **Handle Adding Books to Library**
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['novel_id']) && !empty($_POST['title']) && !empty($_POST['author'])) {

    $novel_id = $_POST['novel_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];

    // Check if book is already in the user's library
    $check_query = $conn->prepare("SELECT * FROM Library WHERE novel_id = ? AND user_id = ?");
    $check_query->bind_param("ii", $novel_id, $user_id);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('This book is already in your library!');  window.location.href = '/Variant/template/novel/novel_info.php?novel_id=$novel_id';</script>";
    } else {
        // Insert book into the library
        $insert_query = $conn->prepare("INSERT INTO Library (user_id, novel_id, read_status) VALUES (?, ?, 'want to read')");

        $insert_query->bind_param("ii", $user_id, $novel_id);
        
        if ($insert_query->execute()) { 
            echo "<script>
                alert('Book added successfully!'); 
                window.location.href = '/Variant/template/novel/novel_info.php?novel_id=$novel_id';
            </script>";
        } else {
            echo "<script>
                alert('Failed to add book: " . $insert_query->error . "'); 
            </script>";
        }
        
        
    }
}


// **Fetch User’s Library**
$query = "SELECT Novels.novel_id, Novels.title, Novels.author_name,  Library.read_status 
          FROM Novels 
          JOIN Library ON Novels.novel_id = Library.novel_id 
          WHERE Library.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
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
</head>
<body>

    <!-- Navigation Bar -->
    <div class="nav">
        <a href="../../index.html">Variant</a>
        <a href="../../template/about.html">About</a>
        <a href="diary.html">Diary</a>
        <a href="../reader_dashboard.php">User</a>
    </div>

    <!-- Library added books -->
    <div class="content">
        <div class="bookshelf-container">
            <div class="bookshelf-title">
                <span> Owned Books </span>

                <a href="add_API_novel.html"><button class="add-book-btn">Add Book</button></a>
            </div>
            

            <div class="bookshelf">
                <div class="shelf-row">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <div class="book">
                            <a href="/Variant/template/novel/novel_info.php?novel_id=<?php echo $row['novel_id']; ?>">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
