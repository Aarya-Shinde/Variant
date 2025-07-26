<?php
session_start();
require_once '../../db/dbconnect.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

$author     = $_SESSION['user_id'];
$usernameQ  = $conn->prepare("SELECT username FROM Users WHERE user_id=?");
$usernameQ->bind_param("i",$author);
$usernameQ->execute();
$username   = $usernameQ->get_result()->fetch_assoc()['username'];

/* =========== fetch story =========== */
$story_id = (int)($_GET['story_id'] ?? 0);
$storyQ   = $conn->prepare("SELECT * FROM WriterStories WHERE story_id=? AND author_id=?");
$storyQ->bind_param("ii",$story_id,$author);
$storyQ->execute();
$story = $storyQ->get_result()->fetch_assoc();
if (!$story) die("Story not found or not yours");

/* =========== fetch chapters =========== */
$chapters = ['PUBLISHED'=>[], 'DRAFT'=>[], 'TRASH'=>[]];

$cq = $conn->prepare("
    SELECT chapter_id,title,word_count,status,created_at
    FROM WriterChapters
    WHERE story_id=?
    ORDER BY created_at DESC");
$cq->bind_param("i",$story_id);
$cq->execute();
$res = $cq->get_result();
while($row=$res->fetch_assoc()){
   $chapters[$row['status']][]=$row;
}

$totalWritten = count($chapters['PUBLISHED'])+count($chapters['DRAFT']);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($story['title']) ?> Explore</title>
  <link rel="stylesheet" href="css/writer.css"><!-- or keep inline -->
<style>
  :root {
    --light-bg: #f4f1de;
    --light-box: #fffaf0;
    --light-text: #3b2f23;
    --light-highlight: #7c4f28;
    --light-accent:  #2c1f14;

    --dark-bg: #1b150f;
    --dark-box: #2c1f14;
    --dark-text: #e7dbc3;
    --dark-highlight: #c3a269;
    --dark-accent: #f1c27d;

    --btn-radius: 6px;
  }

  body {
    font-family: 'Georgia', serif;
    background: var(--light-bg);
    color: var(--light-text);
    margin: 0;
    transition: background 0.3s ease, color 0.3s ease;
  }

  .dark-mode body {
    background: var(--dark-bg);
    color: var(--dark-text);
  }

  .explore-header {
    display: flex;
    gap: 20px;
    background: var(--light-box);
    color: var(--light-text);
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  }

  .dark-mode .explore-header {
    background: var(--dark-box);
    color: var(--dark-text);
    box-shadow: 0 2px 8px rgba(255,255,255,0.1);
  }

  .explore-header img {
    width: 120px;
    height: 160px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--light-highlight);
  }

  .dark-mode .explore-header img {
    border-color: var(--dark-highlight);
  }

  .meta h1 {
    font-size: 24px;
    margin-bottom: 10px;
    color: var(--light-highlight);
  }

  .dark-mode .meta h1 {
    color: var(--dark-highlight);
  }

  .chapter-tabs {
    padding: 20px 30px;
    background: none;
    border-top: 1px solid #ccc;
  }

  .chapter-tabs button {
    margin: 10px 10px 0 0;
    padding: 10px 18px;
    background: var(--light-accent);
    color: #fff;
    border: none;
    border-radius: var(--btn-radius);
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s ease;
  }

  .chapter-tabs button:hover {
    background: #0037d0;
  }

  .dark-mode .chapter-tabs button {
    background: var(--dark-accent);
    color: #1d1404;
  }

  .dark-mode .chapter-tabs button:hover {
    background: #d9a65f;
  }

  .chapter-list {
    padding: 0 30px 30px;
    animation: fadein 0.3s ease;
  }

  .chapter-item {
    background: var(--light-box);
    color: var(--light-text);
    border-radius: var(--btn-radius);
    margin: 10px 0;
    padding: 12px 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    position: relative;
  }

  .dark-mode .chapter-item {
    background: var(--dark-box);
    color: var(--dark-text);
    box-shadow: 0 1px 4px rgba(255,255,255,0.05);
  }

  .chapter-item span {
    font-size: 14px;
    font-weight: normal;
    opacity: 0.7;
  }

  .create-btn {
    display: inline-block;
    margin: 20px 30px;
    padding: 12px 24px;
    background: var(--light-highlight);
    color: #fff;
    border-radius: var(--btn-radius);
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    transition: background 0.3s ease;
  }

  .create-btn:hover {
    background: #5e3b1c;
  }

  .dark-mode .create-btn {
    background: var(--dark-highlight);
    color: #1b150f;
  }

  .dark-mode .create-btn:hover {
    background: #e4b673;
  }

  .hidden {
    display: none;
  }

  @keyframes fadein {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }


  /* For dashboard shortcut to avoid clash'' */

  
  /* ------- NEW / UPDATED  TOPBAR STYLE  ------- */
  .topbar{
    position: sticky;           /* stays visible when you scroll */
    top: 0;
    z-index: 20;                /* floats above everything else */
    background: var(--light-box);
    padding: 12px 24px;
    border-bottom: 1px solid var(--light-border, #d9c6a5);
    box-shadow: 0 2px 4px rgba(0,0,0,.08);
  }

  .dark-mode .topbar{
    background: var(--dark-box);
    border-color: var(--dark-border, #7a5e45);
    box-shadow: 0 2px 4px rgba(255,255,255,.05);
  }

  .topbar a{
    color: var(--light-highlight);
    font-weight: bold;
    text-decoration: none;
    font-size: 15px;
    letter-spacing: .4px;
  }

  .dark-mode .topbar a{
    color: var(--dark-highlight);
  }

  .topbar a:hover{
    text-decoration: underline;
  }

  /* ------- SMALL LAYOUT TWEAKS TO KEEP THINGS FROM TOUCHING ------- */
  .explore-header{             /* add breathing room below the sticky bar */
    margin-top: 12px;
  }

  .chapter-tabs{
    padding-top: 10px;         /* less gap now that topbar is separate */
  }

  .chapter-item {
  padding: 10px;
  border-bottom: 1px solid #ddd;
  font-family: Georgia, serif;
}
.chapter-item a:hover {
  text-decoration: underline;
}


</style>

</head>
<body>

<div class="topbar">
   <a href="/Variant/pages/writer_dashboard.php">Dashboard</a>
</div>

<div class="explore-header">
    <img src="/Variant/<?= htmlspecialchars($story['cover_url'] ?: 'images/placeholder.jpg') ?>" />
   <div class="meta">
       <h1><?= htmlspecialchars($story['title']) ?></h1>
       <p><strong>By:</strong> <?= htmlspecialchars($username) ?>
          &nbsp;|&nbsp;<strong>Genre:</strong> <?= htmlspecialchars($story['genre']) ?></p>
       <p><strong>Status:</strong> <?= ucfirst($story['status']) ?>
          &nbsp;|&nbsp;<strong>Total chapters:</strong> <?= $totalWritten ?></p>
       <p><?= nl2br(htmlspecialchars($story['summary'])) ?></p>
   </div>
</div>

<div class="chapter-tabs">
   <button onclick="showTab('PUBLISHED')">Published (<?= count($chapters['PUBLISHED']) ?>)</button>
   <button onclick="showTab('DRAFT')">Draft (<?= count($chapters['DRAFT']) ?>)</button>
   <button onclick="showTab('TRASH')">Trash (<?= count($chapters['TRASH']) ?>)</button>
</div>

<?php foreach (['PUBLISHED','DRAFT','TRASH'] as $tab): ?>
  <div class="chapter-list <?= $tab !== 'PUBLISHED' ? 'hidden' : '' ?>" id="<?= $tab ?>">
    <?php 
    $counter = 1;
    foreach ($chapters[$tab] as $c): ?>
      <div class="chapter-item">
        <strong>Chapter <?= $counter++ ?>:</strong> <?= htmlspecialchars($c['title']) ?>
        <span style="float:right"><?= $c['word_count'] ?> words</span>

        <div style="margin-top:5px;">
          <?php if ($tab !== 'TRASH'): ?>
            <a href="/Variant/pages/writer/chapter_editor.php?story_id=<?= $story_id ?>&chapter_id=<?= $c['chapter_id'] ?>"
               style="color:#c3a269;text-decoration:none;font-weight:bold;margin-right:15px">Edit</a>

            <a href="javascript:void(0);" 
               onclick="deleteChapter(<?= $story_id ?>, <?= $c['chapter_id'] ?>)" 
               style="color:#c3a269;text-decoration:none;font-weight:bold">Delete</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($chapters[$tab])) echo "<p style='color:#555'>No $tab chapters</p>"; ?>
  </div>
<?php endforeach; ?>



<a href="/Variant/pages/writer/chapter_editor.php?story_id=<?= $story_id ?>" class="create-btn">＋ Create Chapter</a>

<script>
function showTab(t){
   document.querySelectorAll('.chapter-list').forEach(el=>el.classList.add('hidden'));
   document.getElementById(t).classList.remove('hidden');
}
</script>

<script>
function showTab(t) {
  document.querySelectorAll('.chapter-list').forEach(el => el.classList.add('hidden'));
  document.getElementById(t).classList.remove('hidden');
}

function deleteChapter(storyId, chapterId) {
  if (!confirm("Are you sure you want to delete this chapter?")) return;

  fetch('/Variant/api/delete_chapter.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({ story_id: storyId, chapter_id: chapterId })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      alert('Chapter moved to Trash');
      location.reload();
    } else {
      alert('Error: ' + data.msg);
    }
  });
}
</script>

</body>
</html>
