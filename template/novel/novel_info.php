
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Novel</title>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&family=Merriweather&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/novel.css">

</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <!-- Website Logo -->
    <div class="logo"><a href="#">Variant</a> </div>
    <div class="nav-links">
      <a href="../../index.html">Home</a>
      <a href="../explore.php">Novels</a>
      <a href="#reviews">Reviews</a>
      <a href="../../pages/reader_dashboard.php">User</a>
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
      <h2>Chapters</h2>
      <ul class="chapters" id="chapterList"></ul>

      <!-- Chapter Navigation Buttons -->
      <div class="chapter-nav">
        <button onclick="prevChapters()"> Previous</button>
        <button onclick="nextChapters()">Next </button>
      </div>

      <p class="author">Written by <strong id="bookAuthor"></strong></p>
    </section>


    <!-- Reviews Section------------- -->

 <!-- Reviews Section -->
 <section class="reviews-section" id="reviews">
    <h2>Reviews</h2>
    <!-- Predefined Reviews -->


    
    <!-- Review Submission Form -->
  <div class="review-form">
    <input type="hidden" id="novelId" value="1"> <!-- Ensure this ID exists -->
    <input type="hidden" id="userId" value="2"> <!-- Ensure this ID exists -->

    <textarea id="reviewText" placeholder="Share your thoughts on the narrative..."></textarea>
    <div class="star-rating">
        <input type="radio" id="star5" name="rating" value="5">
        <label for="star5" title="5 stars">&#9733;</label>
        <input type="radio" id="star4" name="rating" value="4">
        <label for="star4" title="4 stars">&#9733;</label>
        <input type="radio" id="star3" name="rating" value="3">
        <label for="star3" title="3 stars">&#9733;</label>
        <input type="radio" id="star2" name="rating" value="2">
        <label for="star2" title="2 stars">&#9733;</label>
        <input type="radio" id="star1" name="rating" value="1">
        <label for="star1" title="1 star">&#9733;</label>
    </div>
    <br>
    <button class="submit-review" onclick="addReview()">Submit Review</button>
  </div>
</section>
  
 

 
     
    <!-- Comment Section ---------------->

 
    <!-- Bookish Comment Section -->

<section class="comments">
    <h2>Comments</h2>
    <textarea id="commentText" placeholder="Share your thoughts on the tale..."></textarea>
    <button class="read-more" onclick="addComment()">Submit Reflection</button>
    <div id="commentSection"></div>
</section>


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

<script src="js/novel_info.js" defer></script>


</body>
