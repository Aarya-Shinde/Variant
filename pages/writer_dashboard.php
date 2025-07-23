<?php
session_start();
require '../db/dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id  = $_SESSION['user_id'];
$usernameStmt = $conn->prepare("SELECT username FROM Users WHERE user_id=?");
$usernameStmt->bind_param("i", $user_id);
$usernameStmt->execute();
$username = $usernameStmt->get_result()->fetch_assoc()['username'];


/* ---------- STORY COUNTS ---------- */
$cStory = $conn->prepare("
     SELECT 
       SUM(status='serializing' OR status='hiatus') AS in_progress,
       SUM(status='completed')                     AS published
     FROM WriterStories WHERE author_id=?");
$cStory->bind_param("i",$user_id); $cStory->execute();
$countStories = $cStory->get_result()->fetch_assoc() + ['in_progress'=>0,'published'=>0];

/* ---------- CHAPTER & WORD COUNTS ---------- */
$cChap = $conn->prepare("
    SELECT COUNT(*) AS total_chapters,
           COALESCE(SUM(word_count),0) AS total_words
    FROM WriterChapters c
    JOIN WriterStories s ON s.story_id=c.story_id
    WHERE s.author_id=?");
$cChap->bind_param("i",$user_id); $cChap->execute();
$countCh   = $cChap->get_result()->fetch_assoc();

/* ----------  Story list (with chapter + word stats) ---------- */
$listStmt = $conn->prepare("
    SELECT s.story_id, s.title, s.genre, s.status,
           s.cover_url,
           COUNT(c.chapter_id) AS chapters,
           COALESCE(SUM(c.word_count),0) AS words,
           s.created_at
    FROM WriterStories s
    LEFT JOIN WriterChapters c ON c.story_id=s.story_id
    WHERE s.author_id=?
    GROUP BY s.story_id
    ORDER BY s.created_at DESC");
$listStmt->bind_param("i",$user_id);
$listStmt->execute();
$stories = $listStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer</title>

    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/writer_dashboard.css">
</head>
<body>

<style>
      .explore-btn{padding:6px 12px;
            background:#2c1f14;
        color:#fff;
        border-radius:4px;
        text-decoration:none}
</style>


    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
                <!-- variant logo image here -->
            <div class="logo"> 
                <a href="../index.php"><img src="../images/logowrite.png" alt="Variant Logo" style="max-height: 80px;">
            </a> </div>

            <ul>
                <li class="menu-item active" data-section="dashboard"> Dashboard</li>
                <li class="menu-item" data-section="stories">Stories</li>
                <li class="menu-item" data-section="income"> Income</li>
                <li class="menu-item" data-section="inbox">Inbox</li>
            </ul>
        </aside>

        <!-- Main Content -->
       <!-- Main -->
  <main class="main-content">
      <!-- Dashboard -->
      <section id="dashboard" class="section active">
          <h2>Welcome, <?= htmlspecialchars($username) ?></h2>
          <p>Quick overview of your writing journey.</p>
          <div class="stats-container">
              <div class="stat-card">
                  <h3>Stories</h3>
                  <p>Published: <?= $countStories['published'] ?></p>
                  <p>In&nbsp;Progress: <?= $countStories['in_progress'] ?></p>
              </div>
              <div class="stat-card">
                  <h3>Writing</h3>
                  <p>Chapters: <?= $countCh['total_chapters'] ?></p>
                  <p>Words: <?= number_format($countCh['total_words']) ?></p>
              </div>
              <div class="stat-card">
                  <h3>Coming Soon</h3>
                  <p>Earnings/Subscriptions</p>
              </div>
          </div>
      </section>

      <!-- Stories -->
      <section id="stories" class="section">
          <div class="top-bar">
              <h2>My Stories</h2>
              <a href="/Variant/pages/writer/create_story.php" class="create-btn" id="createStoryBtn">
                    Create Story</a>

          </div>
          <table class="story-table">
              <thead><tr><th>Cover</th><th>Title</th><th>Status</th><th>Ch / Words</th><th>Created</th><th></th></tr></thead>
              <tbody>
                <?php while($s = $stories->fetch_assoc()): ?>
                  <tr>
                      <td><img src="/Variant/<?= $s['cover_url'] ?: 'images/placeholder.jpg' ?>" alt="cover" width="60"></td>
                      <td><?= htmlspecialchars($s['title']) ?><br><small><?= htmlspecialchars($s['genre']) ?></small></td>
                      <td><?= ucfirst($s['status']) ?></td>
                      <td><?= $s['chapters'].' / '.$s['words'] ?></td>
                      <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                      <td><a class="explore-btn" href="/Variant/pages/writer/story_view.php?story_id=<?= $s['story_id'] ?>">Explore</a>
                      </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
          </table>
      </section>

      <!-- Income -->
      <section id="income" class="section">
          <h2>Income</h2><p>(Coming soon)</p>
      </section>
      <!-- Inbox -->
      <section id="inbox" class="section">
          <h2>Inbox</h2><p>(Coming soon)</p>
      </section>
  </main>
</div>

<script>
document.querySelectorAll(".menu-item").forEach(item=>{
  item.onclick=()=> {
     document.querySelectorAll(".menu-item").forEach(i=>i.classList.remove("active"));
     item.classList.add("active");
     const section = item.dataset.section;
     document.querySelectorAll(".section").forEach(sec=>{
       sec.classList.toggle("active", sec.id === section);
     });
  };
});
</script>

</body>
</html>

