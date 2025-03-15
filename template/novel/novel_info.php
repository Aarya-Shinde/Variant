<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variant</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&family=Merriweather&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/novel.css">

    <!-- To load the dark toogle icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
 
    <!-- variant logo image here -->
    <div class="logo"> 
        <a href="../../index.html"><img src="../../images/logowrite.png" alt="Variant Logo" style="max-height: 40px;">
    </a> </div>

    <div class="nav-links">
      <a href="../../index.html">Home</a>
      <a href="../explore.php">Novels</a>
      <a href="../../pages/writer_dashboard.php">Writer</a>
      <a href="../../pages/reader_dashboard.php">User</a>
    </div>
    <button class="dark-mode-btn" onclick="toggleDarkMode()">
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

      <h2>Chapters</h2>
      <ul class="chapters" id="chapterList"></ul>

      <div class="chapter-nav">
        <button onclick="prevChapters()">Previous</button>
        <button onclick="nextChapters()">Next</button>
      </div>

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

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");

    // Change button icon
    const btn = document.querySelector(".dark-mode-btn");
    const icon = btn.querySelector("i");

    if (document.body.classList.contains("dark-mode")) {
        icon.classList.replace("fa-moon", "fa-sun"); // Change to sun
    } else {
        icon.classList.replace("fa-sun", "fa-moon"); // Change back to moon
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

    // Fetch Novel Details
    function fetchNovelDetails(novelId) {
        fetch(`/variant/template/novel/fetch_data/fetch_novel.php?novel_id=${novelId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.novel) {
                    console.error("Invalid novel data received.");
                    return;
                }
                displayNovelDetails(data.novel);
            })
            .catch(error => console.error("Error fetching novel details:", error));
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

