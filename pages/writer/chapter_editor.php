<?php
// ───────── Session + DB ───────────────────────────────────────────────
session_start();
require_once dirname(__DIR__).'../../db/dbconnect.php';
if (!isset($_SESSION['user_id'])) { header("Location: /Variant/login.php"); exit; }

$author = $_SESSION['user_id'];
$story  = (int)($_GET['story_id']  ?? 0);   // required
$chapId = (int)($_GET['chapter_id'] ?? 0);  // 0 = new

/* ────────── verify ownership ────────── */
$chk = $conn->prepare("SELECT title FROM WriterStories WHERE story_id=? AND author_id=?");
$chk->bind_param("ii",$story,$author);
$chk->execute();
$storyRow = $chk->get_result()->fetch_assoc() or die("Story not found.");

/* ────────── load existing chapter (if editing) ────────── */
$chapter = ['title'=>'','content'=>'','status'=>'DRAFT'];
if ($chapId){
  $c = $conn->prepare("SELECT * FROM WriterChapters WHERE chapter_id=? AND story_id=?");
  $c->bind_param("ii",$chapId,$story); $c->execute();
  $chapter = $c->get_result()->fetch_assoc() ?: $chapter;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($storyRow['title']) ?> – <?= $chapId? 'Edit':'New' ?> Chapter</title>

<!-- Quill editor -->
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

<style>
  :root {
    --light-bg: #f4f1de;
    --light-box: #fffaf0;
    --light-text: #3b2f23;
    --light-accent: #7c4f28;
    --light-border: #c6b89e;

    --dark-bg: #1b150f;
    --dark-box: #2c1f14;
    --dark-text: #e7dbc3;
    --dark-accent: #d9a65f;
    --dark-border: #8b6f54;

    --btn-radius: 6px;
  }

  body {
    margin: 0;
    background: var(--light-bg);
    font-family: 'Libre Baskerville', serif;
    color: var(--light-text);
    transition: background 0.3s, color 0.3s;
  }

  .dark-mode body {
    background: var(--dark-bg);
    color: var(--dark-text);
  }

  .topbar {
    background: var(--light-accent);
    color: #fff;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    font-weight: bold;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }

  .dark-mode .topbar {
    background: var(--dark-accent);
    color: #1b150f;
    box-shadow: 0 2px 6px rgba(255,255,255,0.05);
  }

  .topbar a {
    color: inherit;
    text-decoration: none;
    font-weight: bold;
    font-size: 15px;
  }

  .topbar a:hover {
    text-decoration: underline;
  }

  .wrapper {
    max-width: 900px;
    margin: 40px auto;
    background: var(--light-box);
    padding: 24px 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }

  .dark-mode .wrapper {
    background: var(--dark-box);
    box-shadow: 0 2px 10px rgba(255,255,255,0.03);
  }

  .title {
    width: 100%;
    font-size: 20px;
    padding: 10px 12px;
    border: 1px solid var(--light-border);
    border-radius: var(--btn-radius);
    margin-bottom: 20px;
    background: #fff;
    color: var(--light-text);
  }

  .dark-mode .title {
    background: #2a1f18;
    color: var(--dark-text);
    border-color: var(--dark-border);
  }

  #editor {
    height: 480px;
    padding: 16px;
    background: #fff;
    border: 1px solid var(--light-border);
    border-radius: var(--btn-radius);
    overflow-y: auto;
  }

  .dark-mode #editor {
    background: #2b211a;
    border-color: var(--dark-border);
    color: var(--dark-text);
  }

  .btnbar {
    margin: 24px 0 0;
    display: flex;
    gap: 16px;
    align-items: center;
  }

  .btn {
    padding: 10px 22px;
    border: none;
    border-radius: var(--btn-radius);
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .save {
    background:rgb(95, 163, 143);
    color: #fff;
  }

  .save:hover {
    background: #57524c;
  }

  .publish {
    background:rgb(110, 12, 12);
    color: #fff;
  }

  .publish:hover {
    background:rgb(48, 11, 5);
  }

  .dark-mode .save {
    background: #a49788;
    color: #1b150f;
  }

  .dark-mode .publish {
    background: #c3a269;
    color: #1b150f;
  }

  #msg {
    font-style: italic;
    font-size: 14px;
    color: #555;
  }

  .dark-mode #msg {
    color: #d9c9aa;
  }

  /* 🪶 Quill Editor Styling */
.ql-toolbar.ql-snow {
  background: #fdfaf4;
  border: 1px solid var(--light-border);
  border-radius: 6px 6px 0 0;
  font-family: 'Georgia', serif;
  padding: 8px;
}

.ql-container.ql-snow {
  border: 1px solid var(--light-border);
  border-top: none;
  background: #fff;
  color: var(--light-text);
  border-radius: 0 0 6px 6px;
  font-family: 'Libre Baskerville', serif;
  font-size: 16px;
  line-height: 1.6;
}

.ql-editor {
  min-height: 400px;
  padding: 20px;
}

/* Dark Mode Quill */
.dark-mode .ql-toolbar.ql-snow {
  background: #32261c;
  border-color: var(--dark-border);
}

.dark-mode .ql-container.ql-snow {
  background: #2b211a;
  border-color: var(--dark-border);
  color: var(--dark-text);
}

.dark-mode .ql-editor {
  color: var(--dark-text);
}

</style>

</head>
<body>

<!-- ────────── Top Navigation ────────── -->
<div class="topbar">
   <a href="/Variant/pages/writer_dashboard.php">← Dashboard</a>
   <span><?= htmlspecialchars($storyRow['title']) ?></span>
</div>

<!-- ────────── Editor Form ────────── -->
<div class="wrapper">
  <input id="title" class="title" placeholder="Chapter title" value="<?= htmlspecialchars($chapter['title']) ?>">
  <div id="editor"><?= $chapter['content'] ?></div>

  <div class="btnbar">
      <button class="btn save"    onclick="saveDraft()">Save Draft</button>
      <button class="btn publish" onclick="publish()">Publish</button>
      <span id="msg"></span>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
const quill   = new Quill('#editor',{theme:'snow'});
const story   = <?= $story ?>;
let   chapId  = <?= $chapId ?>;
const msgBox  = document.getElementById('msg');

/* autosave every 30 s */
setInterval(()=>{ if(chapId) saveDraft(true); },30_000);

function qs(obj){return new URLSearchParams(obj).toString();}

//* ───────── Save Draft ───────── */
function saveDraft(silent = false){
  const payload = qs({
     story: story,
     chapter: chapId,
     title: document.getElementById('title').value,
     content: quill.root.innerHTML
  });

  if (!silent) msgBox.textContent = 'Saving…';

  /* return the fetch promise so callers can await it */
  return fetch('/Variant/pages/writer/api/save_chapter.php', {
           method: 'POST',
           headers: {'Content-Type':'application/x-www-form-urlencoded'},
           body: payload
         })
         .then(r => r.json())
         .then(data => {
            if (data.status === 'ok') {
               if (!chapId) chapId = data.id;   // id of new draft
               if (!silent) msgBox.textContent = 'Draft saved ✔';
            } else {
               msgBox.textContent = 'Error: ' + data.msg;
            }
            return data;                         // propagate result
         });
}

/* ───────── Publish ───────── */
async function publish(){
  if (!chapId) {                    // brand-new chapter
      const d = await saveDraft();  // wait for real save
      if (d.status !== 'ok') return;  // abort on error
  }
  if (!confirm('Publish this chapter now?')) return;

  msgBox.textContent = 'Publishing…';
  const res  = await fetch('/Variant/pages/writer/api/publish_chapter.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: qs({story:story, chapter:chapId})
  });
  const data = await res.json();

  if (data.status === 'ok') {
      msgBox.textContent = 'Published ✔  redirecting…';
      setTimeout(()=>location.href='/Variant/pages/writer/story_view.php?story_id='+story, 900);
  } else {
      msgBox.textContent = 'Error: ' + data.msg;
  }
}

</script>
</body>
</html>
