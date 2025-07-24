<!-- // PHP section to fetch all books from the database -->
<?php
include '../db/dbconnect.php';

/* ───── Novels ───── */
$sqlNovels = "
  SELECT novel_id           AS work_id,
         'novel'            AS work_type,
         title,
         author_name,
         genre,
         cover_image_url,
         description
  FROM   Novels
  ORDER  BY created_at DESC";
$novelRes = $conn->query($sqlNovels);

/* ───── Fan-fics ───── */
$sqlFanfics = "
  SELECT fanfic_id          AS work_id,
         'fanfic'           AS work_type,
         title,
         author             AS author_name,
         'fanfic'           AS genre,
         NULL               AS cover_image_url,  -- placeholder later
         summary            AS description
  FROM   Fanfic
  ORDER  BY fanfic_id DESC";
$fanficRes = $conn->query($sqlFanfics);

/* ───── Writer Stories (original works) ───── */
$sqlStories = "
  SELECT ws.story_id        AS work_id,
         'story'            AS work_type,
         ws.title,
         u.username         AS author_name,
         ws.genre,
         ws.cover_url       AS cover_image_url,
         ws.summary         AS description
  FROM   WriterStories ws
  JOIN   Users u ON u.user_id = ws.author_id
  ORDER  BY ws.created_at DESC";
$storyRes  = $conn->query($sqlStories);

/* ───── Merge into one array ───── */
$books = [];
foreach ([$novelRes,$fanficRes,$storyRes] as $r) {
    if ($r && $r->num_rows) {
        while ($row = $r->fetch_assoc()) $books[] = $row;
    }
}
$conn->close();
?>



    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Novels</title>

    
    <!-- ------------------linking css js files------------------- -->
    <script src="../js/darkmode.js"></script>
    <link rel="stylesheet" href="../css/explore_new_dm.css">

    <!------------------------------------------- Nav Bar------------------------------>
        
    <header>
        <div class="navbar">
                <!-- variant logo image here -->
            <div class="logo"> <a href="../index.php"><img src="../images/logowrite.png" alt="Variant Logo" style="max-height: 40px;"></a> </div>
                
            <div class="nav">
                <ul>
                    <li><a href="explore.php">Explore</a></li>
                    <li><a href="../pages/writer_dashboard.php">Writer</a></li>
                    <li><a href="../pages/reader/library.php">Library</a></li>
                    <li><a href="../pages/reader_dashboard.php">User</a></li>
                </ul>        
            </div>
            </div>
        </div>
    </header>

<style>
    :root {
        --parchment: #f5f0e1;
        --ink: #2c1c0f;
        --leather-brown: #5a3e2b; /* Darker to match main page */
        --antique-gold: #d4af37;
        --soft-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        --accent: #a67b5b;
        --hover-glow: 0 0 8px rgba(166, 123, 91, 0.5);
        --font-primary: 'Edu AU VIC WA NT Pre', cursive;
        --font-secondary: 'Open Sans', sans-serif;
    }


    body {
        font-family: var(--font-secondary);
        background-color: var(--parchment);
        margin: 0;
        padding: 0;
        color: var(--ink);
    }

    .navbar {
        background-color: var(--leather-brown); /* Matched */
        color: var(--antique-gold);
        padding: 1rem 1rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        position: fixed;
        top: 0;
        width: 100%;
        box-shadow: var(--soft-shadow);
        z-index: 1000;
    }

    .logo img {
        max-height: 50px;
        filter: drop-shadow(2px 2px 2px rgba(0,0,0,0.4));
    }

    .nav ul {
        display: flex;
        list-style: none;
        gap: 2rem;
        font-family: var(--font-primary);
    }

    .nav ul li a {
        color: var(--antique-gold);
        text-decoration: none;
        font-size: 1rem;
        transition: all 0.3s ease-in-out;
    }

    .nav ul li a:hover {
        text-shadow: var(--hover-glow);
        color: white;
    }

    .search-container {
        display: flex;
        align-items: center;
    }

    .search-input {
        padding: 0.6rem;
        font-family: var(--font-secondary);
        border-radius: 8px;
        border: 1px solid #a67b5b;
        background-color: var(--parchment);
        color: var(--ink);
        box-shadow: var(--soft-shadow);
    }

    .search-btn {
        margin-left: 0.5rem;
        background-color: var(--accent); /* Soft brown, not gold */
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        font-family: var(--font-primary);
        border-radius: 8px;
        cursor: pointer;
        box-shadow: var(--soft-shadow);
        transition: all 0.3s ease-in-out;
    }

    .search-btn:hover {
        background-color: var(--leather-brown); /* darker hover */
        box-shadow: var(--hover-glow);
    }

    .container {
        margin-top: 8rem;
        padding: 2rem;
    }

    .search-filter {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .search-filter input,
    .search-filter select {
        padding: 0.6rem;
        font-size: 1rem;
        border-radius: 8px;
        border: 1px solid #a67b5b;
        background-color: var(--parchment);
        color: var(--ink);
        font-family: var(--font-secondary);
        box-shadow: var(--soft-shadow);
    }

    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
        background-color: transparent;
    }

    .book-card {
        background-color: #fff9e6;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--soft-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        font-family: var(--font-secondary);
    }

    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-glow);
    }

    .book-card img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        border-bottom: 1px solid #ccc;
    }

    .content {
        padding: 1rem;
    }

    .content .title {
        font-size: 1.3rem;
        font-weight: bold;
        font-family: var(--font-primary);
        margin-bottom: 0.4rem;
        color: var(--ink);
    }

    .content .author {
        font-size: 1rem;
        color: #7c5e44;
        margin-bottom: 0.6rem;
    }

    .content .description {
        font-size: 0.95rem;
        color: #333;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .content button {
        background-color: var(--accent); /* Soft brown, not gold */
        color: white;
        font-weight: bold;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        font-family: var(--font-primary);
        box-shadow: var(--soft-shadow);
    }

    .content button:hover {
        background-color: var(--leather-brown); /* darker hover */
        box-shadow: var(--hover-glow);
        color: white;
    }

    .book-card img{
   width:100%;height:220px;object-fit:cover;border-radius:6px 6px 0 0;
}

</style>

    </head>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />


    <!--=============== BOXICONS ===============-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css"/>


    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!---font-family: "Edu AU VIC WA NT Pre", cursive;--->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Pre:wght@400..700&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">


    
    
    <!-- --------------------------------------------Adding favicon to website-------------- -->
         <link rel="icon" type="image/png" sizes="32x32" href="../images/images.png">

<body>
   
<div class="container">
        <div class="search-filter">
            <input id="searchInput" type="text" placeholder="Search for books, authors, genres, or categories...">
            <div class="filter">
            <select id="categoryFilter">
                <option value="all">All</option>
                <option value="novels">Novels</option>
                <option value="stories">Stories</option>
                <option value="fanfic">Fan-fic</option>
                <option value="romance">Romance</option>
                <option value="fantasy">Fantasy</option>
            </select>



            </div>
        </div>

        <!-- Books will be dynamically rendered here -->
    <div class="book-grid" id="bookGrid">

    </div>

<script>

const books       = <?php echo json_encode($books); ?>;
const bookGrid    = document.getElementById("bookGrid");
const categorySel = document.getElementById("categoryFilter");
const searchInput = document.getElementById("searchInput");

/* ---------- build card ---------- */
/* ---------- helper: resolve proper cover path ---------- */
function getCover(b){
  if (b.work_type === 'fanfic') {
      return '../images/fanfic.jpg';
  }

  // If it's an absolute URL (http… or https…) just return it
  if (b.cover_image_url && /^https?:\/\//i.test(b.cover_image_url)) {
      return b.cover_image_url;
  }

  // If it's already rooted with /Variant/
  if (b.cover_image_url && b.cover_image_url.startsWith('/Variant/')) {
      return b.cover_image_url;
  }

  // story covers are saved as images/covers/filename.ext
  if (b.work_type === 'story' && b.cover_image_url) {
      return '/Variant/' + b.cover_image_url;
  }

  // novels with relative path saved in DB (rare)
  if (b.work_type === 'novel' && b.cover_image_url) {
      return '/Variant/' + b.cover_image_url;
  }

  // ultimate fallback
  return '../images/placeholder.jpg';
}

/* ---------- build card ---------- */
function render(list){
  bookGrid.innerHTML = "";
  list.forEach(b=>{
     const card = document.createElement("div");
     card.className = "book-card";
     const cover = getCover(b);

     card.innerHTML = `
        <img src="${cover}" alt="${b.title}">
        <div class="content">
           <div class="title">${b.title}</div>
           <div class="author">By ${b.author_name}</div>
           <div class="description">${(b.description||'').substring(0,100)}…</div>
           <button class="view-btn">View More</button>
        </div>`;

     card.querySelector(".view-btn").onclick = ()=>{
        if      (b.work_type === "novel")
            location.href = "novel/novel_info.php?novel_id="+b.work_id;
        else if (b.work_type === "fanfic")
            location.href = "fanfic/fanfic_info.php?fanfic_id="+b.work_id;
        else  /* story */
            location.href = "../pages/writer/story_view.php?story_id="+b.work_id;
     };
     bookGrid.appendChild(card);
  });
}


/* ---------- search + category filter ---------- */
function applyFilter(){
  const cat  = categorySel.value.toLowerCase();
  const term = searchInput.value.toLowerCase();

  const list = books.filter(b=>{
      const matchCat  =  cat === "all"
                      || (cat === "novels"  && b.work_type === "novel")
                      || (cat === "stories" && b.work_type === "story")
                      || (cat === "fanfic"  && b.work_type === "fanfic")
                      || (cat === b.genre?.toLowerCase());
      const matchTerm = [b.title, b.author_name, b.genre]
                      .some(x => x && x.toLowerCase().includes(term));
      return matchCat && matchTerm;
  });
  render(list);
}

categorySel.onchange = applyFilter;
searchInput.oninput  = applyFilter;
render(books);          // initial load

</script>



// <!-- Dark mode button toggle -->
<button class="dark-mode-btn" title="Toggle Dark Mode">
        <i class="fas fa-moon"></i>
    </button> 
    

</body>
</html>
