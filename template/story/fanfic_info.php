<?php
/**********************************************************************
 * fanfic_info.php  —  Single-fanfic view with reviews & comments
 *********************************************************************/
session_start();
require '../../db/dbconnect.php';   // adjust if necessary

echo "<!-- Served by: " . __FILE__ . " -->";

/* ---------- 1. validate ID ---------- */

$fanfic_id = $_GET['fanfic_id'] ?? null;
if (!$fanfic_id || !ctype_digit($fanfic_id)) {
    die("Invalid or missing fan-fic ID.");
}

/* ---------- 2. fetch fan-fic ---------- */
$q = $conn->prepare(
    "SELECT fanfic_id, title, url, author, language,
            word_count, chapter_count, summary, tags
     FROM   Fanfic
     WHERE  fanfic_id = ?"
);
$q->bind_param("i", $fanfic_id);
$q->execute();
$fanfic = $q->get_result()->fetch_assoc() ?: die("Fan-fic not found.");
$q->close();

/* ---------- 3. library status ---------- */
$user_id       = $_SESSION['user_id'] ?? null;
$is_in_library = false;

if ($user_id) {
    $lib = $conn->prepare(
        "SELECT 1 FROM Library WHERE user_id = ? AND fanfic_id = ?"
    );
    $lib->bind_param("ii", $user_id, $fanfic_id);
    $lib->execute();
    $is_in_library = $lib->get_result()->num_rows > 0;
    $lib->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($fanfic['title']) ?> | Variant</title>

  <!-- styles reused from novel page -->
  <link rel="stylesheet" href="../novel/css/novel.css">
  <link rel="stylesheet" href="../novel/css/darkmode.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php /* ---------- NAVBAR ---------- */ ?>
<nav class="navbar">
  <div class="logo"><a href="../../index.php"><img src="../../images/logowrite.png" alt="Variant" style="max-height:40px;"></a></div>
  <div class="nav-links">
    <a href="../../index.php">Home</a>
    <a href="../explore.php">Novels</a>
    <a href="../../pages/reader/library.php">Library</a>
    <a href="../../pages/reader_dashboard.php">User</a>
  </div>
  <button class="dark-mode-btn"><i class="fas fa-moon"></i></button>
</nav>

<div class="container">
  <section class="book-section">
    <h1 class="book-title"><?= htmlspecialchars($fanfic['title']) ?></h1>

    <!-- single default cover for all fan-fic -->
    <img src="../../images/fanfic.jpg" alt="Cover" class="book-cover">

    <p><strong>Author:</strong> <?= htmlspecialchars($fanfic['author']) ?></p>
    <p>
      <strong>Language:</strong> <?= htmlspecialchars($fanfic['language'] ?: 'Unknown') ?> |
      <strong>Words:</strong> <?= number_format($fanfic['word_count'] ?? 0) ?> |
      <strong>Chapters:</strong> <?= $fanfic['chapter_count'] ?? '—' ?>
    </p>
    <p><strong>Original site:</strong>
       <a href="<?= htmlspecialchars($fanfic['url']) ?>" target="_blank">Source Link</a>
    </p>

    <p class="summary"><?= nl2br(htmlspecialchars($fanfic['summary'] ?: 'No summary available.')) ?></p>

    <?php if ($fanfic['tags']): ?>
      <div class="tags">
        <?php foreach (explode(',', $fanfic['tags']) as $tag): ?>
            <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Library button -->
    <?php if ($user_id): ?>
      <form action="../../pages/reader/library.php" method="post">
         <input type="hidden" name="fanfic_id" value="<?= $fanfic_id ?>">
         <?php if ($is_in_library): ?>
             <input type="hidden" name="remove_fanfic_id" value="<?= $fanfic_id ?>">
             <button type="submit" class="remove-btn">In Library</button>
         <?php else: ?>
             <button type="submit" class="btn">Add to Library</button>
         <?php endif; ?>
      </form>
    <?php else: ?>
      <p><a href="../../pages/login.php">Login</a> to manage your library.</p>
    <?php endif; ?>
  </section>

  <!-- ---------- REVIEWS (fanfic) ---------- -->
  <section class="reviews">
      <h2>Reviews</h2>
      <div id="reviews-list"></div>

      <?php if ($user_id): ?>
      <div class="review-form">
          <textarea id="reviewText" placeholder="Share your thoughts…"></textarea>
          <div class="star-rating">
              <?php for ($i=5; $i>=1; $i--): ?>
                 <input type="radio" id="star<?= $i ?>"  name="rating" value="<?= $i ?>">
                 <label for="star<?= $i ?>">&#9733;</label>
              <?php endfor; ?>
          </div>
          <button onclick="addReview()">Submit Review</button>
      </div>
      <?php endif; ?>
  </section>

  <!-- ---------- COMMENTS (fanfic) ---------- -->
  <section class="comments-list">
     <h2>Reader Comments</h2>
     <div id="comments-container"></div>
     <?php if ($user_id): ?>
       <textarea id="commentText" placeholder="Leave a comment…"></textarea>
       <button onclick="addComment()">Submit Comment</button>
     <?php endif; ?>
  </section>
</div>

<footer>&copy; 2025 Variant | Crafted for people like ME</footer>

<script>
    const fanficId = <?= $fanfic_id ?>;
</script>

<!-- ---------- SCRIPTS ---------- -->
<script src="js/fanfic_info.js" defer></script>

<script>
  /* ------- dark-mode toggle (unchanged) ------- */
  document.addEventListener("DOMContentLoaded", () => {
    const btn = document.querySelector(".dark-mode-btn"),
          icon = btn.querySelector("i"),
          body = document.body;
    if (localStorage.getItem("dark-mode")==="enabled"){
          body.classList.add("dark-mode"); icon.classList.replace("fa-moon","fa-sun");
    }
    btn.onclick = () => {
        body.classList.toggle("dark-mode");
        const on = body.classList.contains("dark-mode");
        icon.classList.replace(on?"fa-moon":"fa-sun",on?"fa-sun":"fa-moon");
        localStorage.setItem("dark-mode", on?"enabled":"disabled");
    };
  });
</script>



</body>
</html>
