
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Novel</title>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&family=Merriweather&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="novel.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <!-- Website Logo -->
    <div class="logo"><a href="#">Variant</a> </div>
    <div class="nav-links">
      <a href="#home">Home</a>
      <a href="#novels">Novels</a>
      <a href="#reviews">Reviews</a>
      <a href="#contact">Contact</a>
    </div>
    <button onclick="toggleDarkMode()">🌙</button>
  </nav>

  <!-- Main Content -->
<div class="container" id="home">

    <!-- Book Details Section -->
    <section class="book-section">
      <h1 class="book-title" id="bookTitle"></h1>
      <img src="" alt="Book Cover" class="book-cover" id="bookCover">
      <p class="summary" id="bookSummary"></p>

      <!-- Publishing Date -->
      <p class="publish-date"><strong>Published on:</strong> <span id="publishDate"></span></p>

      <!-- Tags -->
      <div class="tags" id="tagsContainer"></div>

      <!-- Chapters -->
      <h2>📜 Chapters</h2>
      <ul class="chapters" id="chapterList"></ul>

      <!-- Chapter Navigation Buttons -->
      <div class="chapter-nav">
        <button onclick="prevChapters()">⬅ Previous</button>
        <button onclick="nextChapters()">Next ➡</button>
      </div>

      <p class="author">Written by <strong id="bookAuthor"></strong></p>
    </section>


<script>
  let chapters = [];
  let startIndex = 0;
  const chaptersPerPage = 15;

  // Fetch novel details from PHP
  fetch("fetch_novel.php")
  .then(response => response.text()) // First, get raw response
  .then(text => {
    try {
      return JSON.parse(text); // Try parsing JSON
    } catch (error) {
      throw new Error("Invalid JSON: " + text); // Show actual response in console
    }
  })
  .then(data => {
    console.log("Fetched data:", data);
    if (!data.novel) throw new Error("Novel data missing");

    document.getElementById("bookTitle").textContent = data.novel.title || "Unknown Title";
    document.getElementById("bookCover").src = data.novel.cover_image_url || "placeholder.jpg";
    document.getElementById("bookSummary").textContent = data.novel.description || "No description available.";
    document.getElementById("publishDate").textContent = data.novel.publication_date || "Unknown Date";
    document.getElementById("bookAuthor").textContent = data.novel.author_name || "Unknown Author";

    let tagsContainer = document.getElementById("tagsContainer");
    tagsContainer.innerHTML = "";
    if (data.tags && data.tags.length > 0) {
      data.tags.forEach(tag => {
        let span = document.createElement("span");
        span.className = "tag";
        span.textContent = tag;
        tagsContainer.appendChild(span);
      });
    } else {
      tagsContainer.textContent = "No tags available.";
    }

    chapters = data.chapters || [];
    displayChapters();
  })
  .catch(error => console.error("Error loading novel:", error));


  function displayChapters() {
    const chapterList = document.getElementById("chapterList");
    chapterList.innerHTML = "";

    for (let i = startIndex; i < startIndex + chaptersPerPage && i < chapters.length; i++) {
      const li = document.createElement("li");
      li.textContent = `Chapter ${chapters[i].chapter_number}: ${chapters[i].title}`;
      chapterList.appendChild(li);
    }
  }

  function nextChapters() {
    if (startIndex + chaptersPerPage < chapters.length) {
      startIndex += chaptersPerPage;
      displayChapters();
    }
  }

  function prevChapters() {
    if (startIndex - chaptersPerPage >= 0) {
      startIndex -= chaptersPerPage;
      displayChapters();
    }
  }

  // Initial display
  displayChapters();
</script>



    <!-- Reviews Section------------- -->

 <!-- Reviews Section -->
 <section class="reviews-section" id="reviews">
    <h2>Reviews</h2>
    <!-- Predefined Reviews -->
    <div class="review">
      <strong>Jane Doe:</strong> "Absolutely magical! A must-read for fantasy lovers."
      <div class="stars">★★★★★</div>
    </div>
    <div class="review">
      <strong>John Smith:</strong> "Loved the depth of the characters and the immersive storytelling."
      <div class="stars">★★★★☆</div>
    </div>
    
    <!-- Review Submission Form -->
    <div class="review-form">
      <textarea id="reviewText" placeholder="Share your thoughts on the narrative..."></textarea>
      <div class="star-rating">
        <input type="radio" id="star5" name="rating" value="5" />
        <label for="star5" title="5 stars">&#9733;</label>
        <input type="radio" id="star4" name="rating" value="4" />
        <label for="star4" title="4 stars">&#9733;</label>
        <input type="radio" id="star3" name="rating" value="3" />
        <label for="star3" title="3 stars">&#9733;</label>
        <input type="radio" id="star2" name="rating" value="2" />
        <label for="star2" title="2 stars">&#9733;</label>
        <input type="radio" id="star1" name="rating" value="1" />
        <label for="star1" title="1 star">&#9733;</label>
      </div>
      <br>
      <button class="submit-review" onclick="addReview()">Submit Review</button>
    </div>
  </section>
  
  <script>
    // Fetch reviews from PHP and display them
        document.addEventListener("DOMContentLoaded", fetchReviews); // Ensure reviews load when page loads

    function fetchReviews() {
      fetch("fetch_reviews.php")
        .then(response => response.json())
        .then(data => {
          const reviewsContainer = document.getElementById("reviews");
          reviewsContainer.innerHTML = ""; // Clear old reviews before adding new ones

          data.reviews.forEach(review => {
            let reviewDiv = document.createElement("div");
            reviewDiv.className = "review";

            let starsHtml = "";
            for (let i = 1; i <= 5; i++) {
              starsHtml += i <= review.rating ? "&#9733;" : "&#9734;";
            }

            reviewDiv.innerHTML = `<strong>${review.reviewer_name}:</strong> "${review.review_text}"
                                  <div class="stars">${starsHtml}</div>
                                  <small>Reviewed on ${new Date(review.created_at).toLocaleDateString()}</small>`;
            reviewsContainer.appendChild(reviewDiv);
          });
        })
        .catch(error => console.error("Error fetching reviews:", error));
    }

    function addReview() {
      var reviewText = document.getElementById("reviewText").value;
      var ratingInput = document.querySelector('input[name="rating"]:checked');

      if (!reviewText.trim()) {
        alert("Please share your thoughts before submitting.");
        return;
      }
      if (!ratingInput) {
        alert("Please select a star rating.");
        return;
      }

      var rating = parseInt(ratingInput.value);
      var formData = new URLSearchParams();
      formData.append("review_text", reviewText);
      formData.append("rating", rating);

      fetch("submit_review.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString()
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert("Review submitted successfully!");
            document.getElementById("reviewText").value = "";
            document.querySelectorAll('input[name="rating"]').forEach(input => (input.checked = false));
            fetchReviews(); // Reload reviews dynamically after submission
          } else {
            alert("Failed to submit review.");
          }
        })
        .catch(error => console.error("Error submitting review:", error));
    }

  </script>
     
    <!-- Comment Section ---------------->

 <!-- Bookish Comment Section -->
 <section class="comments">
    <h2>Comments</h2>
    <textarea id="commentText" placeholder="Share your thoughts on the tale..."></textarea>
    <button class="read-more" onclick="addComment()">Submit Reflection</button>
    <div id="commentSection"></div>
  </section>

<script>
    // Optional: Toggle additional novel information using a bookish reveal
    function toggleReadMore() {
      var moreInfo = document.getElementById('moreInfo');
      moreInfo.style.display = (moreInfo.style.display === 'none' || moreInfo.style.display === '') ? 'block' : 'none';
    }

    // Add Comment Functionality with a literary twist
    function addComment() {
      var commentText = document.getElementById("commentText").value;
      if (commentText.trim() === "") {
        alert("Please pen down your thoughts before submitting.");
        return;
      }
      var commentSection = document.getElementById("commentSection");
      var newComment = document.createElement("div");
      newComment.className = "comment";
      newComment.innerHTML = `<strong>Anonymous:</strong><br><p>${commentText}</p>`;
      commentSection.appendChild(newComment);
      document.getElementById("commentText").value = "";
    }
</script>


<!-- Comment section ends here -->



  </div>

  <footer>
    &copy; 2025 Novel Hub | Crafted for Book Lovers 📚
  </footer>

  <script>
    function toggleDarkMode() {
      document.body.classList.toggle('dark-mode');
    }
  </script>

</body>
</html>
