<?php session_start(); ?>
<script>
    const userId =
    <?php
        // first choice: live session
        if (isset($_SESSION['user_id'])) {
            echo json_encode($_SESSION['user_id']);
        } else {
            // fallback: read user_id cookie (or null)
            echo 'null';
        }
    ?>;
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Booka</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Libre+Baskerville&display=swap');

    body {
        background-color: #2c1d16;
        color: #e0c9a6;
        font-family: 'Libre Baskerville', serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background: #3b2a1a;
        padding: 20px;
        border-radius: 12px;
        width: 350px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        border: 2px solid #a67c52;
        text-align: center;
    }

    h2 {
        font-size: 22px;
        margin-bottom: 15px;
        color: #e5d0b8;
    }

    label {
        display: block;
        margin-top: 10px;
        font-size: 14px;
        color: #f5e3cc;
    }

    input, select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #a67c52;
        background-color: #5b4229;
        color: #f5e3cc;
        font-size: 14px;
    }

    button {
        background-color: #a67c52;
        color: #2c1d16;
        border: none;
        padding: 10px;
        margin-top: 15px;
        width: 100%;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    button:hover {
        background-color: #8b6746;
    }

    .hidden {
        display: none;
    }

    .message {
        margin-top: 10px;
        font-size: 14px;
        color: #d9a76e;
    }
</style>
<style>
        #spinner-wrap{display:none;text-align:center;margin-top:20px}
        .hourglass{width:40px;height:60px;border:4px solid #a67c52;border-radius:4px;position:relative;overflow:hidden;box-shadow:0 0 6px rgba(0,0,0,.4)}
        .hourglass:before,.hourglass:after{content:"";position:absolute;left:0;right:0;height:50%;background:#e0c9a6;animation:sand 2s infinite}
        .hourglass:after{bottom:0;animation-delay:1s}
        @keyframes sand{0%{transform:scaleY(1)}49%{transform:scaleY(.05)}50%{transform:scaleY(.05)}100%{transform:scaleY(1)}}
        .hourglass-container{display:inline-block;animation:flip 4s infinite}
        @keyframes flip{0%,49%{transform:rotate(0)}50%,99%{transform:rotate(180deg)}}
</style>

</head>
<body>

    <!-- Cute animation while we wait of it to save -->
    <div id="spinner-wrap">
    <div class="hourglass-container">
        <div class="hourglass"></div>
    </div>
    <p style="color:#d9a76e;margin-top:8px;">Saving to library…</p>
    </div>

        <div class="form-container">
          <h2>Add Books / Fan-fics </h2>
      
          <!-- simple picker -->
          <form id="scrapeForm">
              <label for="linkType">Type</label>
              <select id="linkType" required>
                  <option value="published">Published (Goodreads)</option>
                  <option value="fanfic">Fan-fic (AO3)</option>
              </select>
      
              <label for="bookUrl">URL</label>
              <input type="url" id="bookUrl" placeholder="Paste Goodreads or AO3 link" required>
      
              <button type="submit">Save to My Library</button>
          </form>
        </div>
      

<script>
    const spinner = document.getElementById('spinner-wrap');

    document.getElementById('scrapeForm').addEventListener('submit', async e => {
    e.preventDefault();

    const url     = document.getElementById('bookUrl').value.trim();
    const content = document.getElementById('linkType').value;

    if (!userId) {
        alert("Please log in first!");
        return;
    }

    spinner.style.display = "block";          // show spinner

    try {
        const r = await fetch('http://127.0.0.1:5000/scrape', {
            method : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body   : JSON.stringify({ url, user_id: userId, content_type: content })
        });
        const json = await r.json();
        spinner.style.display = "none";       // hide spinner

        if (r.ok) {
            alert('Success ' + json.message);
            window.location.href = "../library.php";   // redirect to library
        } else {
            alert('Error ' + (json.error || 'Unknown error'));
        }
    } catch(err) {
        spinner.style.display = "none";
        alert('Could not reach the scraper service.');
        console.error(err);
    }
    });
</script>

      
</body>
</html>
