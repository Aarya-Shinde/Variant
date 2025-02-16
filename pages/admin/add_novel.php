<?php
session_start();
include '../../db/dbconnect.php';  // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $author_name = $_POST['author_name'];
    $publication_date = $_POST['publication_date'];
    $genre = $_POST['genre'];
    $cover_image_url = $_POST['cover_image_url'];
    $total_chapters = $_POST['total_chapters'];
    $series_name = $_POST['series_name'];
    $series_position = $_POST['series_position'];
    $description = $_POST['description'];
    $isbn = $_POST['isbn'];
    $rating = $_POST['rating'];
    $language = $_POST['language'];

    // Prepare and execute the insert query
    $query = "INSERT INTO Novels (title, author_id, author_name, publication_date, genre, cover_image_url, 
              total_chapters, series_name, series_position, description, isbn, rating, language) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sissssissssds", $title, $author_id, $author_name, $publication_date, $genre, $cover_image_url, 
                                      $total_chapters, $series_name, $series_position, $description, $isbn, $rating, $language);

    if ($stmt->execute()) {
        echo "<script>alert('Book added successfully!'); window.location.href='add_novel.php';</script>";
    } else {
        echo "<script>alert('Error adding book!');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
</head>
<body>

<nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Variant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
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
                        <a class="nav-link" href="add_chapter.php">Add Chapters</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Form for adding book information -->
    <div class="container mt-5">
        <h2 class="text-center">Add a New Book</h2>
        <form method="POST" action="add_novel.php">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Author ID</label>
                <input type="number" class="form-control" name="author_id" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Author Name</label>
                <input type="text" class="form-control" name="author_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Publication Date</label>
                <input type="date" class="form-control" name="publication_date">
            </div>
            <div class="mb-3">
                <label class="form-label">Genre</label>
                <input type="text" class="form-control" name="genre">
            </div>
            <div class="mb-3">
                <label class="form-label">Cover Image URL</label>
                <input type="text" class="form-control" name="cover_image_url">
            </div>
            <div class="mb-3">
                <label class="form-label">Total Chapters</label>
                <input type="number" class="form-control" name="total_chapters">
            </div>
            <div class="mb-3">
                <label class="form-label">Series Name</label>
                <input type="text" class="form-control" name="series_name">
            </div>
            <div class="mb-3">
                <label class="form-label">Series Position</label>
                <input type="number" class="form-control" name="series_position">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">ISBN</label>
                <input type="text" class="form-control" name="isbn">
            </div>
            <div class="mb-3">
                <label class="form-label">Rating</label>
                <input type="number" step="0.1" class="form-control" name="rating">
            </div>
            <div class="mb-3">
                <label class="form-label">Language</label>
                <input type="text" class="form-control" name="language">
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>

    <style>
        body {
            background-color: #f4e7da;
            font-family: 'Georgia', serif;
        }
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
            background-color: #fffaf0;
            border: 2px solid #8b4513;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #8b4513;
        }
        .form-label {
            color: #8b4513;
        }
        .btn-primary {
            background-color: #8b4513;
            border: none;
        }
        .btn-primary:hover {
            background-color: #a0522d;
        }
    </style>
</body>
</html>
