<?php
session_start();
require '../../db/dbconnect.php'; // Adjust path as needed

$user_id = $_SESSION['user_id'] ?? null;
$novel_id = $_GET['novel_id'] ?? null;

if (!$novel_id) {
    die("Invalid novel ID.");
}

// Fetch novel details
$stmt = $conn->prepare("SELECT * FROM Novels WHERE novel_id = ?");
$stmt->bind_param("i", $novel_id);
$stmt->execute();
$novel = $stmt->get_result()->fetch_assoc();

if (!$novel) {
    die("Novel not found.");
}

// Check if the novel is in the user's library
$is_in_library = false;
if ($user_id) {
    $check_stmt = $conn->prepare("SELECT * FROM Library WHERE user_id = ? AND novel_id = ?");
    $check_stmt->bind_param("ii", $user_id, $novel_id);
    $check_stmt->execute();
    $is_in_library = $check_stmt->get_result()->num_rows > 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variant</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&family=Merriweather&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/novel.css">
    <link rel="stylesheet" href="css/darkmode.css">

    <!-- To load the dark toogle icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
 
    <!-- variant logo image here -->
    <div class="logo"> 
    <a href="../../index.php">
    <img src="../../images/logowrite.png" alt="Variant Logo" style="max-height: 40px;">
    </a>

</div>

    <div class="nav-links">
      <a href="../../index.php">Home</a>
      <a href="../explore.php">Novels</a>
      <a href="../../pages/reader/library.php">Library</a>
      <a href="../../pages/reader_dashboard.php">User</a>
    </div>
    <button class="dark-mode-btn">
    <i class="fas fa-moon"></i> 
</button>


  </nav>

  <!-- Main Content -->
  <div class="container" id="home">

<!-- Book Details Section -->
<section class="book-section">
    <h1 class="book-title" id="bookTitle"></h1>
    <img src="" alt="Book Cover" class="book-cover" id="bookCover">
    <p class="summary" id="bookSummary"></p>
    <p class="publish-date"><strong>Published on:</strong> <span id="publishDate"></span></p>
    <div class="tags" id="tagsContainer"></div>

    <?php if ($user_id): ?>
        <form action="../../pages/reader/library.php" method="POST">
            <input type="hidden" name="novel_id" value="<?= $novel_id ?>">
            <input type="hidden" name="title" value="<?= htmlspecialchars($novel['title']) ?>">
            <input type="hidden" name="author" value="<?= htmlspecialchars($novel['author_name']) ?>">

            <?php if ($is_in_library): ?>
                <input type="hidden" name="remove_novel_id" value="<?= $novel_id ?>">
                <button type="submit" name="remove" class="remove-btn">In Library</button>
            <?php else: ?>
                <button type="submit" name="add" class="btn">Add to Library</button>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <p><a href="../../pages/login.php">Login</a> to manage your library.</p>
    <?php endif; ?>
</section>




      <h2>Chapters</h2>
      <ul class="chapters" id="chapterList">
          <!-- Chapters will be dynamically inserted here -->
           
      </ul>

     <div id="pagination"></div>

      <p class="author">Written by <strong id="bookAuthor"></strong></p>
    </section>

    <!-- Reviews Section -->
     <section class = "reviews">
    <div class="review-form">

        <div id="reviews-list"></div>
        <input type="hidden" id="novelId">
        <textarea id="reviewText" placeholder="Share your thoughts on the narrative..."></textarea>
        <div class="star-rating">
            <input type="radio" id="star5" name="rating" value="5"><label for="star5">&#9733;</label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4">&#9733;</label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3">&#9733;</label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2">&#9733;</label>
            <input type="radio" id="star1" name="rating" value="1"><label for="star1">&#9733;</label>
        </div>
        <button onclick="addReview()">Submit Review</button>
    </div>
    </section>


    <!-- <div id="reviews-list"></div> -->

    <!-- Comments Section -->
    <section class="comments-list">
        <h2>Reader Comments</h2>
        <div id="comments-container"></div>
        <textarea id="commentText" placeholder="Share your thoughts on the tale..."></textarea>
        <button onclick="addComment()">Submit Comment</button>
    </section>


  </div>

  <footer>
    &copy; 2025 Variant | Crafted for for people like ME
  </footer>

  <script src="js/novel_info.js" defer></script>


<script>
// Dark Mode Toggle

    document.addEventListener("DOMContentLoaded", function () {
        const body = document.body;
        const darkModeBtn = document.querySelector(".dark-mode-btn");
        const icon = darkModeBtn.querySelector("i");

        // Check local storage for dark mode preference
        if (localStorage.getItem("dark-mode") === "enabled") {
            body.classList.add("dark-mode");
            icon.classList.replace("fa-moon", "fa-sun"); // Set correct icon
        }

        // Toggle Dark Mode
        darkModeBtn.addEventListener("click", function () {
            toggleDarkMode();
        });
    });

    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");

        const btn = document.querySelector(".dark-mode-btn");
        const icon = btn.querySelector("i");

        if (document.body.classList.contains("dark-mode")) {
            icon.classList.replace("fa-moon", "fa-sun"); // Change to sun
            localStorage.setItem("dark-mode", "enabled");
        } else {
            icon.classList.replace("fa-sun", "fa-moon"); // Change back to moon
            localStorage.setItem("dark-mode", "disabled");
        }
    }


</script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let urlParams = new URLSearchParams(window.location.search);
        let novelId = urlParams.get("novel_id"); // Get novel_id from URL

        if (!novelId) {
            console.error("Novel ID missing from URL.");
            return;
        }

        fetchNovelDetails(novelId);
        fetchReviews(novelId);
        fetchComments(novelId);
    });

    function fetchNovelDetails(novelId) {
        fetch(`/variant/template/novel/fetch_data/fetch_novel.php?novel_id=${novelId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.novel) {
                console.error("Invalid novel data received.");
                return;
            }
            displayNovelDetails(data.novel);
            console.log("Fetching chapters now...");
            fetchChapters(novelId); // Call here
        })
        .catch(error => console.error("Error fetching novel details:", error));

    }

// Fetch and display chapter list
    function fetchChapters(novelId) {
        fetch(`/variant/template/novel/fetch_data/fetch_chapters.php?novel_id=${novelId}`)
            .then(response => response.json())
            .then(data => {
                let chapterList = document.getElementById("chapterList");
                if (!chapterList) {
                    console.error("Error: #chapterList element not found!");
                    return;
                }

                chapterList.innerHTML = ""; // Clear previous list

                if (data.success && data.chapters.length > 0) {
                    data.chapters.forEach((chapter) => {
                        let li = document.createElement("li");
                        li.innerHTML = `<a href="chapter.php?novel_id=${novelId}&chapter_number=${chapter.chapter_number}">${chapter.title}</a>`;
                        chapterList.appendChild(li);
                    });
                } else {
                    chapterList.innerHTML = "<p>No chapters available.</p>";
                }
            })
            .catch(error => console.error("Error fetching chapters:", error));
    }

    function loadChapters(novel_id, page = 1) {
        fetch(`/variant/template/novel/fetch_data/fetch_chapters.php?novel_id=${novel_id}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            let chapterList = document.getElementById("chapterList"); // FIXED ID
            if (!chapterList) {
                console.error("Error: #chapterList element not found!");
                return;
            }

            chapterList.innerHTML = ""; // Clear previous list

            if (!data.chapters || data.chapters.length === 0) { // FIXED EMPTY CHECK
                chapterList.innerHTML = "<p>No chapters available.</p>";
                return;
            }

            data.chapters.forEach(chapter => {
                let li = document.createElement("li");
                li.innerHTML = `<a href="chapter.php?novel_id=${novel_id}&chapter_number=${chapter.chapter_number}">${chapter.title}</a>`;
                chapterList.appendChild(li);
            });

            // Pagination controls
            let pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            if (data.total_pages > 1) {
                // Previous button
                if (page > 1) {
                    pagination.innerHTML += `<button onclick="loadChapters(${novel_id}, ${page - 1})">Prev</button> `;
                }

                // Page numbers
                for (let i = 1; i <= data.total_pages; i++) {
                    pagination.innerHTML += `<button onclick="loadChapters(${novel_id}, ${i})" ${i === page ? 'style="font-weight:bold;"' : ''}>${i}</button> `;
                }

                // Next button
                if (page < data.total_pages) {
                    pagination.innerHTML += `<button onclick="loadChapters(${novel_id}, ${page + 1})">Next</button>`;
                }
            }
        })
        .catch(error => console.error("Error loading chapters:", error));
    }



    function displayNovelDetails(novel) {
        document.getElementById("bookTitle").innerText = novel.title;
        document.getElementById("bookCover").src = novel.cover_image_url;
        document.getElementById("bookCover").alt = novel.title + " Cover";
        document.getElementById("bookSummary").innerText = novel.description;
        document.getElementById("publishDate").innerText = novel.publication_date;
        document.getElementById("bookAuthor").innerText = novel.author_name;
    }

    // Fetch and Display Reviews

    function fetchReviews(novelId) {
        fetch(`/variant/template/novel/fetch_data/fetch_reviews.php?novel_id=${novelId}`)
            .then(response => response.json())
            .then(data => {
                let reviewContainer = document.getElementById("reviews-list");
                if (!reviewContainer) {
                    console.error("Review container not found.");
                    return;
                }

                reviewContainer.innerHTML = "<h2>Reviews</h2>";
                if (data.success && data.reviews.length > 0) {
                    data.reviews.forEach(review => {
                        reviewContainer.innerHTML += `
                            <div class="review">
                                <p><strong>Rating:</strong> ${review.rating} ⭐</p>
                                <p>${review.review_text}</p>
                                <hr>
                            </div>
                        `;
                    });
                } else {
                    reviewContainer.innerHTML += "<p>No reviews yet.</p>";
                }
            })
            .catch(error => console.error("Error fetching reviews:", error));
    }

    // Add a New Review
    function addReview() {
        let novelId = new URLSearchParams(window.location.search).get("novel_id");
        let reviewTextElement = document.getElementById("reviewText");
        let ratingElement = document.querySelector('input[name="rating"]:checked');

        if (!novelId || !reviewTextElement || !reviewTextElement.value.trim() || !ratingElement) {
            alert("Please fill out all fields.");
            return;
        }

        let requestBody = {
            novel_id: parseInt(novelId),
            review_text: reviewTextElement.value.trim(),
            rating: parseInt(ratingElement.value)
        };

        fetch(`/variant/template/novel/submit_data/submit_review.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestBody)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                reviewTextElement.value = ""; // Clear input
                document.querySelectorAll('input[name="rating"]').forEach(radio => radio.checked = false);
                fetchReviews(novelId); // Refresh reviews immediately
            } else {
                alert(`Error submitting review: ${result.error || "Unknown error"}`);
            }
        })
        .catch(error => console.error("Error submitting review:", error));
    }

    // Load reviews on page load
    document.addEventListener("DOMContentLoaded", () => {
        let novelId = new URLSearchParams(window.location.search).get("novel_id");
        if (novelId) fetchReviews(novelId);
    });


    // Fetch and Display Comments
    function fetchComments(novelId) {
        fetch(`/variant/template/novel/fetch_data/fetch_comments.php?novel_id=${novelId}`)
            .then(response => response.json())
            .then(data => {
                let commentsContainer = document.getElementById("comments-container");
                if (!commentsContainer) {
                    console.error("Comment container not found.");
                    return;
                }

                commentsContainer.innerHTML = "<h3>Comments</h3>";
                if (data.success && data.comments.length > 0) {
                    data.comments.forEach(comment => {
                        commentsContainer.innerHTML += `
                            <div class="comment">
                                <p>${comment.comment_text}</p>
                                <small>Posted on: ${new Date(comment.created_at).toLocaleString()}</small>
                                <hr>
                            </div>
                        `;
                    });
                } else {
                    commentsContainer.innerHTML += "<p>No comments yet.</p>";
                }
            })
            .catch(error => console.error("Error fetching comments:", error));
    }

    // Add a New Comment
    function addComment() {
        let novelId = new URLSearchParams(window.location.search).get("novel_id");
        let commentText = document.getElementById("commentText");

        if (!commentText || !commentText.value.trim()) {
            alert("Please enter a comment.");
            return;
        }

        fetch(`/variant/template/novel/submit_data/submit_comment.php`, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `novel_id=${encodeURIComponent(novelId)}&comment_text=${encodeURIComponent(commentText.value)}`
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                commentText.value = ""; // Clear input
                fetchComments(novelId); // Refresh comments
            } else {
                alert(`Error: ${result.error || "Failed to submit comment."}`);
            }
        })
        .catch(error => console.error("Error submitting comment:", error));
    }

    // Load comments on page load
    document.addEventListener("DOMContentLoaded", () => {
        let novelId = new URLSearchParams(window.location.search).get("novel_id");
        if (novelId) fetchComments(novelId);
    });

</script>

</body>
</html>