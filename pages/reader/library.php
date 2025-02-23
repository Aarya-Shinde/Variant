<?php
session_start();
include '../../db/dbconnect.php';

// ✅ Check if session contains user_id or if there is a cookie containing the ID
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

$user_id = $_SESSION['user_id']; // ✅ Use the stored session variable

$query = "SELECT Novels.novel_id, Novels.title, Novels.author_name, Novels.genre, Library.read_status 
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
    
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f1de;
        color: #4b2e2e;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    /************* Navigation Bar *************/
    .nav {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: #a0522d;
        color: white;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 0;
        z-index: 1000;
    }

    .nav a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        padding: 10px 15px;
        border-radius: 5px;
    }

    .nav a:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Library Content */
    .content {
        padding-top: 80px;
    }

    .bookshelf-container {
        width: 75%;
        border: 2px solid #8c6d45;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        background-color: #fff8dc;
        position: relative;
        margin: 0 auto;
    }

    h1 {
        text-align: center;
        font-family: 'Georgia', serif;
        color: #a0522d;
    }

    @import url('https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap');

    @import url('https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap');

    .bookshelf {
        background: #8b4513; /* Dark Brown (Wood Shelf) */
        padding: 20px;
        border-radius: 10px;
        box-shadow: inset 0px -5px 0px #5a3212;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
        max-width: 100%;
    }

    .bookshelf-title {
        font-family: 'Merriweather', serif;
        font-size: 2.5em;
        color: #333;
        text-align: center;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
        padding: 10px 20px;
        background-color: #f4f4f4;
        border-bottom: 5px solid #c49a6c;
        border-top: 5px solid #c49a6c;
        margin: 20px auto;
        width: 80%;
        letter-spacing: 1px;
    }

    .bookshelf-title::before {
        content: '📜';
        margin-right: 10px;
    }

    .bookshelf-title::after {
        content: '🖋️';
        margin-left: 10px;
    }

    /* Shelf Rows - 10 Books Per Row */
    .shelf-row {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        gap: 5px;
        flex-wrap: wrap;
        width: 100%;
        max-width: 1100px;
        padding: 10px;
        position: relative;
    }

    /* ✅ **Clear Divider Between Rows (Simulates Bookshelf)** */
    .shelf-row:not(:last-child)::after {
        content: "";
        position: absolute;
        bottom: -12px;
        width: 100%;
        height: 16px;
        background: linear-gradient(to bottom, #5a3212, #3e2310);
        border-radius: 5px;
        box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.4); /* More Realistic */
    }

    /* ✅ **Book Styling - Text Wraps Properly** */
    .book {
        background: linear-gradient(to right, #a0522d 10%, #8b4513 40%, #6e3b15 90%);
        color: white;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.3);
        font-size: 14px;
        font-weight: bold;
        font-family: 'EB Garamond', serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        
        /* ✅ **Fixed Book Size** */
        width: 65px;  /* ✅ Wider */
        height: 220px; /* ✅ Shorter */
        
        /* ✅ **Text Wrapping** */
        word-wrap: break-word;
        white-space: normal;
        overflow: hidden;
        line-height: 1.2;
        max-height: 90%;
        
        /* ✅ **Smooth Transition on Hover** */
        transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }

    /* ✅ **Hover Effect - Darkens Slightly + 3D Lift** */
    .book:hover {
        transform: scale(1.05);
        box-shadow: 8px 8px 14px rgba(0, 0, 0, 0.5);
        background: linear-gradient(to right, #8b4513 10%, #6e3b15 40%, #4e240d 90%);
    }



    /* Ensures Only 10 Books Per Row */
    .shelf-row:nth-child(n+2) {
        margin-top: 15px;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .shelf-row {
            max-width: 900px;
        }
    }

    @media (max-width: 768px) {
        .book {
            width: 50px;
            height: 220px;
            font-size: 16px;
        }
    }

</style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="nav">
        <a href="../../index.html">Variant</a>
        <a href="../../template/about.html">About</a>
        <a href="diary.html">Diary</a>
        <a href="../reader_dashboard.php">User</a>
    </div>

    <div class="content">
        <div class="bookshelf-container">
            <div class="bookshelf-title">Owned Books</div>
            <div class="bookshelf">
                <div class="shelf-row">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <div class="book" onclick="viewBook('<?php echo htmlspecialchars($row['title']); ?>')">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewBook(bookName) {
            alert('You clicked on ' + bookName);
        }
    </script>
</body>
</html>
