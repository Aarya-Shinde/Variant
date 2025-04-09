<?php
session_start();
include 'db/dbconnect.php';  // Database connection

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;

// Handle search functionality
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM Novels WHERE title LIKE ? OR author_name LIKE ? OR genre LIKE ?");
    $like_search = "%$search_query%";
    $stmt->bind_param("sss", $like_search, $like_search, $like_search);
    $stmt->execute();
    $novels = $stmt->get_result();
} else {
    $novels = $conn->query("SELECT * FROM Novels ORDER BY created_at DESC LIMIT 10");
}

// Fetch user's library books
$user_library = null;
$library_novels = [];

if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT n.novel_id, n.title, n.author_name, n.cover_image_url 
                            FROM Novels n 
                            JOIN Library l ON n.novel_id = l.novel_id 
                            WHERE l.user_id = ? LIMIT 5");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_library = $stmt->get_result();

    while ($row = $user_library->fetch_assoc()) {
        $library_novels[] = $row['novel_id'];
    }

    // Reset the pointer so the HTML loop works
    $user_library->data_seek(0);
}

// Fetch recommended books (Basic logic: Get highest-rated books)
$recommended_books = $conn->query("SELECT * FROM Novels ORDER BY rating DESC LIMIT 8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variant</title>

    <!-- ------------------linking css js files------------------- -->
    <link rel="stylesheet" href="css/indexstyle.css"> 
    <link rel="stylesheet" href="css/indexdm.css"> 
    <script src="/js/scripts.js" defer></script>

     <!--=============== BOXICONS ===============-->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css"/>

     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

     <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <!---font-family: "Edu AU VIC WA NT Pre", cursive;--->
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Pre:wght@400..700&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        
     <!-- sun and moon flip for dark mode -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<!-- Swiper CSS & JS for slider-->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>



<!-- --------------------------------------------Adding favicon to website-------------- -->
     <link rel="icon" type="image/png" sizes="32x32" href="images/images.png">
    </head>

<!------------------------------------------- Nav Bar------------------------------>
    
<header>
  <div class="navbar">
        <!-- variant logo image here -->
        <div class="logo"> <a href="index.php"><img src="images/logowrite.png" alt="Variant Logo" style="max-height: 50px;"></a> </div>
      
    <div class="nav-section">

    <!-- Search Bar -->
        <div class="search-container"> 
            <!-- Search Bar -->
            <form method="GET" action="index.php">
                <input type="text" name="search" placeholder="Search books, authors, genres..." value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit">Search</button>
            </form> 
        </div>
        
        <div class="nav">
            <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="template/explore.php">Explore</a></li>

                <?php if ($isLoggedIn): ?>
                    <li><a href="pages/reader_dashboard.php">Dashboard</a></li>
                    <li><a href="pages/reader/library.php">Library</a></li>
                    <li><a href="pages/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="pages/login.php">Login</a></li>
                    <li><a href="pages/register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>    
        </div>
    
    </div>
      
  </div>
</header>


        
<!-- Nav Bar Ends -->

<body>

<!-- <button onclick="toggleDarkMode()" class="dark-mode-btn" title="Toggle Dark Mode">🌓</button> -->
<button class="dark-mode-btn" title="Toggle Theme">
  <i class="fas fa-moon"></i>
</button>


    <!-- Header Section with Background Image and Intro Text -->
<section class="header">
    <div class="header-overlay"></div>
    <div class="header-content">
        <h1>Welcome to the Antique Library</h1>
        <p class="subtitle">Read what you like, Write what you love!!!</p>
        <button type="button" class="btn-read-more"> <a href="template/newnovels.php">Read More</a></button>
    </div>


            <!-- To Display the Search Results -->
            <?php if ($search_query): ?>
        <h2>Search Results for "<?= htmlspecialchars($search_query) ?>"</h2>
        <div class="novels">
            <?php while ($row = $novels->fetch_assoc()): ?>
                <div class="novel">
                    <img src="<?= $row['cover_image_url'] ?>" alt="<?= htmlspecialchars($row['title']) ?>" width="150">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p>By <?= htmlspecialchars($row['author_name']) ?></p>
                    <p><?= htmlspecialchars($row['genre']) ?></p>
                    <a href="template/novel/novel_info.php?id=<?= $row['novel_id'] ?>">Read More</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

</section>


    <!------- Slideshow of Popular featured Novels with Smooth Transition -->
  
<section class="featured" id="featured">

    <h1 class="heading"><span>Featured Books</span></h1>
    <div class="swiper featured-slider">
        <!-- Dynamically populated swiper-wrapper -->
        
        <div class="swiper-wrapper"> 
    <?php
    $genres_result = $conn->query("SELECT DISTINCT genre FROM Novels");
    while ($genre_row = $genres_result->fetch_assoc()) {
        $genre = $genre_row['genre'];
        $stmt = $conn->prepare("SELECT * FROM Novels WHERE genre = ? ORDER BY rating DESC LIMIT 1");
        $stmt->bind_param("s", $genre);
        $stmt->execute();
        $featured_result = $stmt->get_result();
        if ($novels = $featured_result->fetch_assoc()):
            $is_in_library = in_array($novels['novel_id'], $library_novels);
    ?>
        <div class="swiper-slide box">
            <div class="image">
                <img src="<?= htmlspecialchars($novels['cover_image_url']) ?>" alt="<?= htmlspecialchars($novels['title']) ?>" />
            </div>
            <div class="content">
                <h3><?= htmlspecialchars($novels['title']) ?></h3>
                <div class="price">By <?= htmlspecialchars($novels['author_name']) ?></div>

                <form action="pages/reader/library.php" method="POST">
                    <input type="hidden" name="novel_id" value="<?= $novels['novel_id'] ?>">
                    <input type="hidden" name="title" value="<?= htmlspecialchars($novels['title']) ?>">
                    <input type="hidden" name="author" value="<?= htmlspecialchars($novels['author_name']) ?>">

                    <?php if ($is_in_library): ?>
                        <input type="hidden" name="remove_novel_id" value="<?= $novels['novel_id']; ?>">
                        <button type="submit" name="remove" class="remove-btn">Remove from Library</button>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn">Add To Library</button>
                    <?php endif; ?>
                </form>

            </div>
        </div>
    <?php 
        endif;
    }
    ?>
</div>

        
        <!-- Swiper navigation buttons -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>

  
  
  <!-- *****************************Slideshow js scriscript********************* -->
 
<script>
    const swiper = new Swiper('.featured-slider', {
        slidesPerView: 4,  // Show 4 slides at a time
        spaceBetween: 20,
        loop: true,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
          },
          768: {
            slidesPerView: 2,
          },
          1024: {
            slidesPerView: 3,
          },
          1200: {
            slidesPerView: 4, // Maintain 4 slides for larger screens
          },
        },
      });
    </script>

  
<!--------------------------------- Library or recently read books ----------------------------------->
  
<section class= "library">
          <!-- User Library Section -->
          <?php if ($isLoggedIn && $user_library->num_rows > 0): ?>
            <h2>Your Library</h2>
            <div class="novels">
                <?php while ($row = $user_library->fetch_assoc()): ?>
                    <div class="novel">
                        <img src="<?= $row['cover_image_url'] ?>" alt="<?= htmlspecialchars($row['title']) ?>" width="150">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p>By <?= htmlspecialchars($row['author_name']) ?></p>
                        <a href="novel.php?id=<?= $row['novel_id'] ?>">Continue</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

</section>

    <!--------------- Personalized Recommendations with Hover Effect --------------------------------->
    
<div class="recommendations">
    <div class="rec-container">
 <!-- Recommended Books Section -->
        <h2>Recommended Books</h2>
        <div class="novels">
            <?php while ($row = $recommended_books->fetch_assoc()): ?>
                <div class="novel">
                    <img src="<?= $row['cover_image_url'] ?>" alt="<?= htmlspecialchars($row['title']) ?>" width="150">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p>By <?= htmlspecialchars($row['author_name']) ?></p>
                    <p>⭐ <?= number_format($row['rating'], 1) ?></p>
                    <a href="novel.php?id=<?= $row['novel_id'] ?>">Read More</a>
                </div>
            <?php endwhile; ?>
        </div>
      </div>
</div>



  <script>
  document.querySelectorAll('.genre-card').forEach(card => {
    card.addEventListener('click', function() {
        const genre = this.getAttribute('data-genre');
        fetchBooks(genre);
    });
});

function fetchBooks(genre) {
    // Simulate fetching books from a database or API
    const books = [
        { title: 'Book 1', genre: 'fiction', image: 'images/book (1).png' },
        { title: 'Book 2', genre: 'fantasy', image: 'https://in.pinterest.com/pin/48343395994947180/' },
        { title: 'Book 3', genre: 'mystery', image: 'images/book.png' },
        { title: 'Book 4', genre: 'sci-fi', image: 'images/book (1).png' },
        { title: 'Book 5', genre: 'non-fiction', image: 'https://via.placeholder.com/150' },
        { title: 'Book 6', genre: 'romance', image: 'https://via.placeholder.com/150' },
        { title: 'Book 7', genre: 'thriller', image: 'https://via.placeholder.com/150' }
    ];

    const filteredBooks = books.filter(book => book.genre.toLowerCase() === genre.toLowerCase());
    displayBooks(filteredBooks);
}

function displayBooks(books) {
    const bookList = document.getElementById('book-list');
    bookList.innerHTML = '';
    books.forEach(book => {
        const bookCard = document.createElement('div');
        bookCard.className = 'book-card';
        bookCard.innerHTML = `
            <img class="book-image" src="${book.image}" alt="${book.title}">
            <h3 class="book-title">${book.title}</h3>
            <p class="book-genre">${book.genre}</p>
        `;
        bookList.appendChild(bookCard);
    });
}
</script>



    <!------------ About Us Section with Enhanced Book Animation ------------>


<section class="about-us" >

  <h2>About Us</h2>
  <p>Welcome to the <b>Unified System for Author and Audience </b>, where readers and writers connect.
    Writers can publish their novels, and readers can explore, review, and add books to their libraries. 
    Our goal is to create a seamless reading and writing experience.</p>

</section>


    <!---- Footer Section -->
 

<footer class="footer">
    <div class="footer__container">
  

      <div class="footer__social col">
        <a href="#" class="footer__social-link"><i class="bx bxl-facebook-circle"></i></a>
        <a href="https://github.com/Aarya-Shinde/Variant/" class="footer__social-link"><i class="bx bxl-github"></i></a>
        <a href="#" class="footer__social-link"><i class="bx bxl-instagram"></i></a>
      </div>
    </div>
  
    <p class="footer__copy">&#169; Aarya Shinde. All rights reserved</p>
     <p>&copy; <?= date("Y") ?> Unified System for Author & Audience. All rights reserved.</p>
        <p><a href="template/about.html">Contact Us</a> | <a href="privacy.php">Privacy Policy</a></p>

  </footer>
  <!-- Footer Section ends here -->
  
  <!-- Js for darkmode flip and localstorage -->
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const darkModeBtn = document.querySelector(".dark-mode-btn");
    const icon = darkModeBtn.querySelector("i");

    // Load saved preference
    const isDarkMode = localStorage.getItem("theme") === "dark";
    document.body.classList.toggle("dark-mode", isDarkMode);
    icon.classList.replace(isDarkMode ? "fa-moon" : "fa-sun", isDarkMode ? "fa-sun" : "fa-moon");

    // Toggle on click
    darkModeBtn.addEventListener("click", () => {
      const isDark = document.body.classList.toggle("dark-mode");
      localStorage.setItem("theme", isDark ? "dark" : "light");
      icon.classList.replace(isDark ? "fa-moon" : "fa-sun", isDark ? "fa-sun" : "fa-moon");
    });
  });
</script>




</body>
</html>
