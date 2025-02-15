<?php
session_start();
include '../../db/dbconnect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_novels.php");
    exit();
}

$novel_id = intval($_GET['id']);
$novelQuery = "SELECT * FROM Novels WHERE novel_id = $novel_id";
$novelResult = $conn->query($novelQuery);
$novel = $novelResult->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $author_name = $conn->real_escape_string($_POST['author_name']);
    $genre = $conn->real_escape_string($_POST['genre']);

    $updateQuery = "UPDATE Novels SET title = '$title', author_name = '$author_name', genre = '$genre' WHERE novel_id = $novel_id";
    $conn->query($updateQuery);

    header("Location: manage_novels.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Novel</title>
</head>
<body>

<div class="container mt-5">
    <h2>Edit Novel</h2>
    <form method="POST" action="">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($novel['title']); ?>" class="form-control" required>

        <label>Author Name:</label>
        <input type="text" name="author_name" value="<?php echo htmlspecialchars($novel['author_name']); ?>" class="form-control" required>

        <label>Genre:</label>
        <input type="text" name="genre" value="<?php echo htmlspecialchars($novel['genre']); ?>" class="form-control" required>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>

</body>
</html>
