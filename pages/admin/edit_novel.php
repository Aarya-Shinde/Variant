<?php
session_start();
include '../../db/dbconnect.php';

// Ensure user is an admin
if (!isset($_SESSION['email']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Validate & sanitize novel_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_novels.php");
    exit();
}

$novel_id = intval($_GET['id']);

// Fetch novel details using prepared statements
$stmt = $conn->prepare("SELECT * FROM Novels WHERE novel_id = ?");
$stmt->bind_param("i", $novel_id);
$stmt->execute();
$novelResult = $stmt->get_result();

if ($novelResult->num_rows == 0) {
    die("Error: Novel not found.");
}

$novel = $novelResult->fetch_assoc();
$stmt->close();

// Fetch all published chapters
$stmt = $conn->prepare("SELECT * FROM Chapters WHERE novel_id = ? ORDER BY chapter_number ASC");
$stmt->bind_param("i", $novel_id);
$stmt->execute();
$chapterResult = $stmt->get_result();
$stmt->close();

// Handle form submission for novel update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['title']) && !empty($_POST['author_name']) && !empty($_POST['genre']) && !empty($_POST['cover_image']) && !empty($_POST['description'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $author_name = $conn->real_escape_string($_POST['author_name']);
        $genre = $conn->real_escape_string($_POST['genre']);
        $cover_image = $conn->real_escape_string($_POST['cover_image']);
        $description = $conn->real_escape_string($_POST['description']);

        $stmt = $conn->prepare("UPDATE Novels SET title = ?, author_name = ?, genre = ?, cover_image_url = ?, description = ? WHERE novel_id = ?");
        $stmt->bind_param("sssssi", $title, $author_name, $genre, $cover_image, $description, $novel_id);

        if ($stmt->execute()) {
            echo "<p class='success'>Novel updated successfully!</p>";
            // Refresh novel data
            $stmt = $conn->prepare("SELECT * FROM Novels WHERE novel_id = ?");
            $stmt->bind_param("i", $novel_id);
            $stmt->execute();
            $novelResult = $stmt->get_result();
            $novel = $novelResult->fetch_assoc();
            $stmt->close();
        } else {
            echo "<p class='error'>Error updating novel: " . $conn->error . "</p>";
        }
    } else {
        echo "<p class='error'>Please fill in all fields before submitting.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Novel</title>

<style>
    body {
        font-family: 'Georgia', serif;
        background-color: #f2e6d9;
        margin: 0;
        padding: 0px;
        line-height: 1.6;
        color: #333;
    }
    /* -----------------navbar styling------------------------- */
    .navbar {
            background-color: #8b4513;
            color: #fff;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .navbar-nav .nav-item {
            margin-right: 20px; /* Add spacing between items */
        }
        .navbar-nav.ml-auto {
            margin-left: auto;
        }

    .container {
        width: 60%;
        margin: auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: 2px solid #d4c0a1;
    }

    h2, h4 {
        color: #8b4513;
        border-bottom: 2px solid #8b4513;
        padding-bottom: 10px;
        font-family: 'Merriweather', serif;
    }

    label {
        margin-top: 15px;
        font-weight: bold;
        color: #8b4513;
    }

    input, select, textarea {
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #b19d8c;
        border-radius: 5px;
        font-family: 'Georgia', serif;
        background-color: #fdf8f2;
    }

    button {
        margin-top: 20px;
        background-color: #8b4513;
        color: #fff;
        padding: 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-family: 'Merriweather', serif;
    }

    button:hover {
        background-color: #a0522d;
    }

    .list-group-item {
        background-color: #fffaf0;
        border: 1px solid #d4c0a1;
    }

    .success {
        color: #2e8b57;
        font-weight: bold;
    }

    .error {
        color: #b22222;
        font-weight: bold;
    }

    /* <!-- Modal Styling --> */

    .modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
        display: none;
        z-index: 1000;
    }

</style>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Variant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../../index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_novels.php">Manage Novels</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_chapter.php">Add Chapters</a></li>
                </ul>
            </div>
        </div>
    </nav>
</head>
<body>

<div class="container mt-5">
    <!-- Novel Details -->
    <div class="d-flex align-items-center">
        <img src="<?php echo htmlspecialchars($novel['cover_image_url']); ?>" alt="Cover Image" class="img-thumbnail" style="width: 150px; height: 200px; object-fit: cover; margin-right: 20px;">
        <div>
            <h2><?php echo htmlspecialchars($novel['title']); ?></h2>
            <p><strong>By:</strong> <?php echo htmlspecialchars($novel['author_name']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($novel['genre']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($novel['description'])); ?></p>
            <a href="add_chapter.php?novel_id=<?php echo $novel_id; ?>" class="btn btn-primary">Create Chapter</a>

            <!-- Upload Novel Button -->
            <button class="btn btn-secondary mt-3" onclick="showUploadOptions()">Upload Novel File</button>

            <!-- File Upload Modal -->
            <div id="uploadModal" class="modal" style="display: none;">
                <div class="modal-content p-3">
                    <h4>Select File Format</h4>
                    <form action="upload_handler.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="novel_id" value="<?php echo $novel_id; ?>">

                        <label for="file_format">Choose Format:</label>
                        <select name="file_format" id="file_format" class="form-control">
                            <option value="epub">EPUB</option>
                            <option value="pdf">PDF</option>
                            <option value="txt">TXT</option>
                        </select>

                        <label for="file">Upload File:</label>
                        <input type="file" name="file" id="file" class="form-control" required>

                        <button type="submit" class="btn btn-primary mt-3">Upload</button>
                        <button type="button" class="btn btn-danger mt-3" onclick="closeModal()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Edit Novel Form -->
    <h4>Edit Novel Details</h4>
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($novel['title']); ?>" class="form-control" required>

        <label>Author Name:</label>
        <input type="text" name="author_name" value="<?php echo htmlspecialchars($novel['author_name']); ?>" class="form-control" required>

        <label>Genre:</label>
        <input type="text" name="genre" value="<?php echo htmlspecialchars($novel['genre']); ?>" class="form-control" required>

        <label>Description:</label>
        <textarea name="description" class="form-control" required><?php echo htmlspecialchars($novel['description']); ?></textarea>

        <label>Cover Image URL:</label>
        <input type="text" name="cover_image" value="<?php echo htmlspecialchars($novel['cover_image_url']); ?>" class="form-control" required>

        <button type="submit" class="btn btn-success mt-3">Update</button>
    </form>

    <hr>

    <!-- Published Chapters -->
    <h4>Published Chapters</h4>
    <?php if ($chapterResult->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($chapter = $chapterResult->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?php echo htmlspecialchars($chapter['chapter_number']) . ". " . htmlspecialchars($chapter['title']); ?></span>
                    <span class="text-muted"><?php echo date("H:i d M Y", strtotime($chapter['published_at'])); ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No published chapters yet.</p>
    <?php endif; ?>
</div>

<script>
    function showUploadOptions() { document.getElementById('uploadModal').style.display = 'block'; }
    function closeModal() { document.getElementById('uploadModal').style.display = 'none'; }
</script>


</body>
</html>



