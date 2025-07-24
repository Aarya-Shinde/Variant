<?php
session_start();
include '../db/dbconnect.php';

$user_id = $_SESSION['user_id'] ?? null;

/* ---------- Novels ---------- */
$sqlNovels = "
    SELECT novel_id   AS work_id,
           'novel'    AS work_type,
           title,
           author_name,
           publication_date,
           genre,
           cover_image_url,
           total_chapters,
           series_name,
           series_position,
           description,
           rating,
           language
    FROM   Novels
    ORDER  BY created_at DESC";
$novelRes = $conn->query($sqlNovels);

/* ---------- Fan-fics ---------- */
$sqlFanfics = "
    SELECT fanfic_id  AS work_id,
           'fanfic'   AS work_type,
           title,
           author      AS author_name,
           NOW()       AS publication_date,         -- or created_at column if you have one
           'Fan-fic'   AS genre,
           NULL        AS cover_image_url,
           chapter_count AS total_chapters,
           ''          AS series_name,
           NULL        AS series_position,
           summary     AS description,
           NULL        AS rating,
           language
    FROM   Fanfic
    ORDER  BY fanfic_id DESC";
$fanRes = $conn->query($sqlFanfics);

/* ---------- Merge ---------- */
$books = [];
foreach ([$novelRes,$fanRes] as $r){
    if ($r && $r->num_rows) while ($row=$r->fetch_assoc()) $books[] = $row;
}

/* ---------- User’s library (novels + fan-fics) ---------- */
$library = [];
if ($user_id){
  $lib = $conn->prepare("SELECT COALESCE(novel_id, fanfic_id) AS work_id,
                                IF(novel_id IS NULL,'fanfic','novel') AS type
                         FROM Library WHERE user_id=?");
  $lib->bind_param("i",$user_id); $lib->execute();
  $lRes=$lib->get_result();
  while($l=$lRes->fetch_assoc()) $library[$l['type'].'_'.$l['work_id']] = true;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>New Novels</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="../images/images.png" />

  <!-- CSS -->
  <link rel="stylesheet" href="../css/explore_new_dm.css" />

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <!-- JS -->
  <script src="../js/darkmode.js" defer></script>
  
  <style>
    /* Base + Light Mode Styles */
    body {
      background-color: #fdf6e3;
      color: #3b2f2f;
      font-family: 'Georgia', serif;
      transition: all 0.3s ease;
      margin: 0;
      padding: 0;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background-color: transparent;
      position: relative;
      background-color: #3b2f2f;
    }

    .navbar::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      height: 4px;
      width: 100%;
      background: linear-gradient(to right, #c8a96a, #b98e4b, #c8a96a);
      box-shadow: 0 0 8px rgba(185, 142, 75, 0.4);
      
    }

    .nav-links {
      list-style: none;
      display: flex;
      gap: 30px;
      font-family: Edu AU VIC WA NT Pre, cursive;
    }

    .nav-links li a {
      text-decoration: none;
      font-weight: bold;
      color: #fdf6e3;
      transition: color 0.3s ease;
    }

    .nav-links li a:hover {
      color: #b98e4b;
    }

    .search-container {
      display: flex;
      align-items: center;
    }

    .search-container input {
      background-color: #fff9ec;
      color: #3b2f2f;
      border: 1px solid #c8a96a;
      padding: 6px 12px;
      border-radius: 5px;
    }

    .search-btn {
      background-color: #b98e4b;
      color: #fffef8;
      border: none;
      padding: 6px 10px;
      margin-left: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .search-btn:hover {
      background-color: #a67635;
      box-shadow: 0 0 6px rgba(185, 142, 75, 0.4);
    }

    .dark-toggle button {
      background: none;
      border: none;
      font-size: 1.2rem;
      color: #3b2f2f;
      cursor: pointer;
    }

    main {
      padding: 2rem;
    }

    .new-novels h2 {
      text-align: center;
      color: #8b5e3c;
      font-size: 2rem;
      text-shadow: 1px 1px 3px #e9d9b2;
    }

    .novels-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .novel-card {
      background-color: #fffdf6;
      border: 1px solid #e0c193;
      border-radius: 10px;
      padding: 1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 2px 2px 6px rgba(150, 100, 50, 0.1);
    }

    .novel-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(150, 100, 50, 0.2);
    }

    .novel-card h3,
    .novel-card p {
      margin: 0.3rem 0;
    }

    .novel-cover {
      width: 100%;
      height: auto;
      border-radius: 8px;
      border: 1px solid #d1b07f;
    }

    .btn,
    .remove-btn {
      background-color: #b98e4b;
      color: #fffef8;
      border: none;
      padding: 0.5rem 1rem;
      margin-top: 0.5rem;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .btn:hover,
    .remove-btn:hover {
      background-color: #a67635;
      box-shadow: 0 0 8px rgba(185, 142, 75, 0.4);
    }

    /* Dark Mode Styling */
    body.dark-mode {
      background-color: #1c1208;
      color: #f5f0e1;
    }

    body.dark-mode .navbar {
      background-color: #3a2618;
      color: #d4af37;
      border-bottom: 2px solid #e5c07b;
    }

    body.dark-mode .navbar::after {
      background: linear-gradient(to right, #b9962d, #d4af37, #b9962d);
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.6);
    }

    body.dark-mode .nav-links li a {
      color: #f5f0e1;
    }

    body.dark-mode .nav-links li a:hover {
      color: #d4af37;
      text-shadow: 0 0 5px rgba(212, 175, 55, 0.6);
    }

    body.dark-mode .novel-card {
      background-color: #2d1f13;
      border-color: #6b4c2d;
      box-shadow: 2px 2px 8px rgba(212, 175, 55, 0.1);
    }

    body.dark-mode .search-container input {
      background-color: #3a2618;
      color: #f5f0e1;
      border: 1px solid #d4af37;
    }

    body.dark-mode .search-btn {
      background-color: #d4af37;
      color: #1c1208;
    }

    body.dark-mode .btn,
    body.dark-mode .remove-btn {
      background-color: #d4af37;
      color: #1c1208;
    }
  </style>
</head>
<!-- for search / filter -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const input  = document.querySelector(".search-input");
    const grid   = document.getElementById("novelsGrid");

    if(!input || !grid) return;

    input.addEventListener("input", () => {
        const term = input.value.toLowerCase();
        grid.querySelectorAll(".novel-card").forEach(card => {
            const hay = card.dataset.title + card.dataset.author + card.dataset.genre;
            card.style.display = hay.includes(term) ? "" : "none";
        });
    });
});
</script>


<body>
  <!-- Nav Bar -->
  <header>
    <nav class="navbar">
      <div class="logo">
        <a href="../index.php"><img src="../images/logowrite.png" alt="Logo" style="max-height: 40px;" /></a>
      </div>

      <div class="search-container">
        <input type="text" class="search-input" placeholder="Search . . ." required />
        <button class="search-btn"><i class="bx bx-search"></i></button>
      </div>

      <div class="nav">
        <ul class="nav-links">
          <li><a href="explore.php">Explore</a></li>
          <li><a href="../pages/reader_dashboard.php">Reader</a></li>
          <li><a href="../pages/writer_dashboard.php">Writer</a></li>
          <li><a href="../pages/reader/library.php">Library</a></li>
          <li><a href="../pages/reader_dashboard.php">User</a></li>
        </ul>
      </div>

      <div class="dark-toggle">
        <button class="dark-mode-btn" title="Toggle Dark Mode">
          <i class="fas fa-moon"></i>
        </button>
      </div>
    </nav>
  </header>

  <!-- Main Content -->
  <main>
    <section class="new-novels">
      <h2>New Novels</h2>
            <div class="novels-grid" id="novelsGrid">
      <?php foreach ($books as $b):
            $isFan = $b['work_type']==='fanfic';
            $key   = $b['work_type'].'_'.$b['work_id'];
            $inLib = isset($library[$key]);
            $cover = $b['cover_image_url'] ?: "../images/fanfic.jpg";
      ?>
        <div class="novel-card"
            data-title="<?= strtolower($b['title']) ?>"
            data-author="<?= strtolower($b['author_name']) ?>"
            data-genre="<?= strtolower($b['genre']) ?>"
            data-type="<?= $b['work_type'] ?>">
            <img src="<?= $cover ?>" alt="<?= htmlspecialchars($b['title']) ?>" class="novel-cover">
            <h3><?= htmlspecialchars($b['title']) ?></h3>
            <p><strong>Author:</strong> <?= htmlspecialchars($b['author_name']) ?></p>
            <p><strong>Genre:</strong> <?= htmlspecialchars($b['genre']) ?></p>
            <p><strong>Chapters:</strong> <?= $b['total_chapters'] ?? '—' ?></p>
            <?php if($b['series_name']): ?>
              <p><strong>Series:</strong> <?= htmlspecialchars($b['series_name']) ?>
                  (Book <?= $b['series_position'] ?>)</p>
            <?php endif; ?>
            <?php if(!$isFan): ?><p><strong>Rating:</strong> ⭐<?= $b['rating'] ?></p><?php endif; ?>
            <p><strong>Language:</strong> <?= htmlspecialchars($b['language'] ?: '—') ?></p>
            <p><strong>Published:</strong> <?= substr($b['publication_date'],0,10) ?></p>

            <?php if($user_id): ?>
              <form action="../pages/reader/library.php" method="post">
                <?php if($isFan): ?>
                  <input type="hidden" name="fanfic_id" value="<?= $b['work_id'] ?>">
                  <?php if($inLib): ?>
                    <input type="hidden" name="remove_fanfic_id" value="<?= $b['work_id'] ?>">
                    <button class="remove-btn">Remove</button>
                  <?php else: ?>
                    <button class="btn">Add to Library</button>
                  <?php endif; ?>
                <?php else: ?>   <!-- novel -->
                  <input type="hidden" name="novel_id" value="<?= $b['work_id'] ?>">
                  <?php if($inLib): ?>
                    <input type="hidden" name="remove_novel_id" value="<?= $b['work_id'] ?>">
                    <button class="remove-btn">Remove</button>
                  <?php else: ?>
                    <button class="btn">Add to Library</button>
                  <?php endif; ?>
                <?php endif; ?>
              </form>
            <?php else: ?>
              <p><a href="../pages/login.php">Login</a> to manage your library.</p>
            <?php endif; ?>
        </div>
      <?php endforeach; ?>
      </div>

    </section>
  </main>
</body>
</html>

