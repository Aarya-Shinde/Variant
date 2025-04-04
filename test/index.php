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
$user_library = [];
if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT N.* FROM Library L JOIN Novels N ON L.novel_id = N.novel_id WHERE L.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_library = $stmt->get_result();
}

// Fetch recommended books (Basic logic: Get highest-rated books)
$recommended_books = $conn->query("SELECT * FROM Novels ORDER BY rating DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variant</title>

    <!-- ------------------linking css js files------------------- -->
    <link rel="stylesheet" href="css/indexstyle.css"> 
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

     

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<!-- Swiper JS -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


<!-- --------------------------------------------Adding favicon to website-------------- -->
     <link rel="icon" type="image/png" sizes="32x32" href="images/images.png">
    </head>

<!------------------------------------------- Nav Bar------------------------------>
    
<header>
  <div class="navbar">
        <!-- variant logo image here -->
      <div class="logo"> <a href="index.html"><img src="images/logowrite.png" alt="Variant Logo" style="max-height: 50px;"></a> </div>
      
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
</header>


        
<!-- Nav Bar Ends -->

<body>

    <!-- Header Section with Background Image and Intro Text -->
<section class="header">
    <div class="header-overlay"></div>
    <div class="header-content">
        <h1>Welcome to the Antique Library</h1>
        <p class="subtitle">A treasure trove of timeless stories</p>
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
      <div class="swiper-wrapper">
        <!-- Book 1 -->
        <div class="swiper-slide box">
          <div class="image">
            <img src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1562726234i/13496.jpg" alt="Book 1" />
          </div>
          <div class="content">
            <h3>The Hunger Games</h3>
            <div class="price">Suzanne Collins</div>
            <a href="#" class="btn">Add To Library</a>
          </div>
        </div>
        <!-- Book 2 -->
        <div class="swiper-slide box">
          <div class="image">
            <img src="images/book (1).jpg" alt="Book 2" />
          </div>
          <div class="content">
            <h3>Featured Books</h3>
            <div class="price">$12.99 </div>
            <a href="#" class="btn">Add To Library</a>
          </div>
        </div>
        <!-- Book 3 -->
        <div class="swiper-slide box">
          <div class="image">
            <img src="images/book (1).png" alt="Book 3" />
          </div>
          <div class="content">
            <h3>Featured Book</h3>
            <div class="price">$9.99 </div>
            <a href="#" class="btn">Add To Library</a>
          </div>
        </div>
        <!-- Book 4 -->
        <div class="swiper-slide box">
          <div class="image">
            <img src="images/book (2).jpeg" alt="Book 4" />
          </div>
          <div class="content">
            <h3>Featured Book</h3>
            <div class="price">$19.99 </div>
            <a href="#" class="btn">Add To Library</a>
          </div>
        </div>
        <!-- Book 5 -->
        <div class="swiper-slide box">
          <div class="image">
            <img src="images/book (4).jpeg" alt="Book 5" />
          </div>
          <div class="content">
            <h3>Featured Book</h3>
            <div class="price">$22.99 </div>
            <a href="#" class="btn">Add To Library</a>
          </div>
        </div>
        <!-- Book 6 -->
        <div class="swiper-slide box">
          <div class="image">
            <img src="images/book (1).jpg" alt="Book 6" />
          </div>
          <div class="content">
            <h3>Featured Book</h3>
            <div class="price">$18.99 </div>
            <a href="#" class="btn">Add To Library</a>
          </div>
        </div>
        <!-- Add more books here -->
      </div>
      <!-- Navigation buttons -->
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
                        <a href="novel.php?id=<?= $row['novel_id'] ?>">Continue Reading</a>
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
<p>Welcome to the **Unified System for Author and Audience**, where readers and writers connect.
   Writers can publish their novels, and readers can explore, review, and add books to their libraries. 
  Our goal is to create a seamless reading and writing experience.</p>

</section>


    <!---- Footer Section -->
 

<footer class="footer">
    <div class="footer__container">
  
      <div class="footer__content col">
        <h3 class="footer__title">Community</h3>
        <ul class="footer__links">
          <li><a href="#" class="footer__link">Support</a></li>
          <li><a href="#" class="footer__link">Questions</a></li>
          <li><a href="#" class="footer__link">Reviews</a></li>
        </ul>
      </div>
  
      <div class="footer__social col">
        <a href="#" class="footer__social-link"><i class="bx bxl-facebook-circle"></i></a>
        <a href="https://github.com/Aarya-Shinde/Variant/" class="footer__social-link"><i class="bx bxl-github"></i></a>
        <a href="#" class="footer__social-link"><i class="bx bxl-instagram"></i></a>
      </div>
    </div>
  
    <p class="footer__copy">&#169; Aarya Shinde. All rights reserved</p>
     <p>&copy; <?= date("Y") ?> Unified System for Author & Audience. All rights reserved.</p>
        <p><a href="contact.php">Contact Us</a> | <a href="privacy.php">Privacy Policy</a></p>

  </footer>
  <!-- Footer Section ends here -->
  
</body>
</html>
