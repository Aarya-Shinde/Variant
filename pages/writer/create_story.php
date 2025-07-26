<!-- To create new story for writer form-->

<?php
session_start();
require '../../db/dbconnect.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }
$author = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $coverPath = '';
    if (!empty($_FILES['cover']['tmp_name'])) {
        $ext   = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $fname = 'cover_'.time().'.'.$ext;
        $dest = '../../images/covers/'.$fname; // filesystem path
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
          $coverPath = 'images/covers/'.$fname;  // stored path
        }
    }

    $title   = $_POST['title']   ?? '';
    $genre   = $_POST['genre']   ?? '';
    $status  = $_POST['status']  ?? 'serializing';
    $summary = $_POST['summary'] ?? '';

    $ins = $conn->prepare(
        "INSERT INTO WriterStories (author_id,title,genre,status,summary,cover_url)
         VALUES (?,?,?,?,?,?)"
    );
    $ins->bind_param("isssss",$author,$title,$genre,$status,$summary,$coverPath);
    if ($ins->execute()) {
        header("Location: ../writer_dashboard.php");
        exit;
    }
    $error = $ins->error;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Create Story</title>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f4f1de;--box:#fff8e1;--txt:#3e2f1c;--border:#d9c6a5;--accent:#8a5a2b;
      --gold:#bfa75d;--gold-txt:#1d1404;
    }
    body{font-family:'Libre Baskerville',serif;background:var(--bg);color:var(--txt);padding:30px}
    .form-box{max-width:600px;margin:auto;background:var(--box);padding:25px 30px;
              border:1px solid var(--border);border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,.15);}
    input,textarea,select{width:100%;padding:10px;margin:12px 0;border-radius:5px;
                          border:1px solid var(--border);font-size:16px}
    label{font-weight:700;color:var(--accent)}
    .btn{background:var(--gold);color:var(--gold-txt);padding:10px 20px;margin-top:8px;
         border:none;border-radius:6px;font-weight:600;cursor:pointer}
    .btn:hover{background:#a68e49}
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Create a New Story</h2>
    <?php if(!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post" enctype="multipart/form-data">
      <label>Title</label>
      <input name="title" required>

      <label>Genre</label>
      <input name="genre" placeholder="e.g. Fantasy">

      <label>Status</label>
      <select name="status">
        <option value="serializing">Serializing</option>
        <option value="completed">Completed</option>
        <option value="hiatus">Hiatus</option>
      </select>

      <label>Summary</label>
      <textarea name="summary" rows="6" placeholder="Short synopsis"></textarea>

      <label>Cover Image</label>
      <input type="file" name="cover" accept="image/*">

      <button class="btn">Create Story</button>
    </form>
  </div>
</body>
</html>

