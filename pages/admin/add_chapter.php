<?php
session_start();
include '../../db/dbconnect.php';  // Include database connection file

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch existing novels
$novelQuery = "SELECT novel_id, title FROM Novels ORDER BY title ASC";
$novelResult = $conn->query($novelQuery);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novel_id = intval($_POST['novel_id']);
    $chapter_number = intval($_POST['chapter_number']);
    $title = $conn->real_escape_string($_POST['title']);

    // Trim and sanitize content
    $content = trim($_POST['content']); 
    $content = preg_replace("/\n{2,}/", "\n\n", $content); // Remove excessive blank lines
    $content = $conn->real_escape_string($content);

    // Check if chapter already exists
    $checkQuery = "SELECT chapter_id FROM Chapters WHERE novel_id = $novel_id AND chapter_number = $chapter_number";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        $errorMsg = "Chapter number already exists for this novel.";
    } else {
        // Insert chapter if it does not exist
        $insertQuery = "INSERT INTO Chapters (novel_id, chapter_number, title, content) 
                        VALUES ('$novel_id', '$chapter_number', '$title', '$content')";

        if ($conn->query($insertQuery) === TRUE) {
            $successMsg = "Chapter added successfully!";
        } else {
            $errorMsg = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Chapter</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f2e6d9;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
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

        h2 {
            text-align: center;
            color: #8b4513;
            border-bottom: 2px solid #8b4513;
            padding-bottom: 10px;
            font-family: 'Merriweather', serif;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        label {
            margin-top: 15px;
            font-weight: bold;
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

        .success {
            color: #2e8b57;
            font-weight: bold;
        }

        .error {
            color: #b22222;
            font-weight: bold;
        }
        

        /* Navbar Styling */
        .navbar {
            background-color: #8b4513;
            color: #fff;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
        .navbar-nav .nav-item {
            margin-right: 20px;
        }
        .navbar-nav.ms-auto {
            margin-left: auto;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Variant</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../../index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_novels.php">Manage Novels</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_novel.php">Add Novels</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Add New Chapter</h2>

    <?php if (isset($successMsg)) echo "<p class='success'>$successMsg</p>"; ?>
    <?php if (isset($errorMsg)) echo "<p class='error'>$errorMsg</p>"; ?>

    <form action="" method="post">
        <label for="novel_id">Select Novel:</label>
        <select name="novel_id" required>
            <option value="">-- Select a Novel --</option>
            <?php while ($row = $novelResult->fetch_assoc()) { ?>
                <option value="<?php echo $row['novel_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
            <?php } ?>
        </select>

        <label for="chapter_number">Chapter Number:</label>
        <input type="number" name="chapter_number" min="1" required>

        <label for="title">Chapter Title:</label>
        <input type="text" name="title" required>

        <label for="content">Chapter Content:</label>
        <textarea name="content" rows="10" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>

        <button type="submit">Add Chapter</button>
    </form>
</div>

</body>
</html>
