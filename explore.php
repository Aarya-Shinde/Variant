<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Books</title>
    <style>
 
    .header {
        text-align: center;
        font-size: 2rem;
        font-weight: bold;
        padding: 1rem;
        background-color: #343a40;
        color: white;
    }

    .container {
        margin-top: 50px;
        padding: 1rem;
        max-width: 100%; /* Ensure container takes full width */
    }

    .search-filter {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .search-filter input, .search-filter select {
        padding: 0.5rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        width: 100%; /* Ensure grid takes full width */
    }

    .book-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .book-card:hover {
        transform: scale(1.05);
    }

    .book-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .content {
        padding: 1rem;
    }

    .content .title {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .content .author {
        font-size: 1rem;
        color: #555;
        margin-bottom: 0.5rem;
    }

    .content .description {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 1rem;
    }

    .content button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border-radius: 5px;
        cursor: pointer;
    }

    .content button:hover {
        background-color: #0056b3;
    }
 

    </style>
</head>


    <!-- ------------------linking css js files------------------- -->
    <link rel="stylesheet" href="nav.css"> 
    <script src="/js/scripts.js" defer></script>


    <!--=============== BOXICONS ===============-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css"/>


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
         <link rel="icon" type="image/png" sizes="32x32" href="/images/images.png">
    </head>

<body>
    <?php
    // PHP section to fetch books from the database
    include './db/dbconnect.php';

    $sql = "SELECT 
                novel_id, 
                title, 
                author_name, 
                genre, 
                cover_image_url, 
                description 
            FROM Novels 
            ORDER BY created_at DESC";

    $result = $conn->query($sql);
    $books = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    $conn->close();
    ?>

    <!------------------------------------------- Nav Bar------------------------------>
        
    <header>
        <div class="navbar">
                <!-- variant logo image here -->
            <div class="logo"> <a href="index.html"><img src="/images/logowrite.png" alt="Variant Logo" style="max-height: 40px;"></a> </div>
            
            <!-- Search Bar -->
            <div class="search-container"> 
            <input type="text" class="search-input" placeholder="Search . . ." required>
            <button class="search-btn"> 
                <i class="bx bx-search"></i> 
            </button> 
            </div>
                
            <div class="nav">
                <ul>
                    <li><a href="explore.php">Explore</a></li>
                    <li><a href="reader.html">Reader</a></li>
                    <li><a href="writer.html">Writer</a></li>
                    <li><a href="library.html">Library</a></li>
                    <li><a href="pages/login.php">Login</a></li>
                </ul>        
            </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="search-filter">
            <input id="searchInput" type="text" placeholder="Search for books, authors, genres, or categories...">
            <div class="filter">
                <select id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="novels">Novels</option>
                    <option value="fiction">Fiction</option>
                    <option value="manga">Manga</option>
                    <option value="romance">Romance</option>
                    <option value="thriller">Thriller</option>
                    <option value="fantasy">Fantasy</option>
                </select>
            </div>
        </div>

        <div class="book-grid" id="bookGrid">
            <!-- Books will be dynamically rendered here -->
        </div>
    </div>

    <script>
        const books = <?php echo json_encode($books); ?>;
        const bookGrid = document.getElementById('bookGrid');
        const categoryFilter = document.getElementById('categoryFilter');
        const searchInput = document.getElementById('searchInput');

        function displayBooks(filteredBooks) {
        bookGrid.innerHTML = '';

        if (filteredBooks.length === 0) {
            bookGrid.innerHTML = '<p>No books found</p>';
            return;
        }

        filteredBooks.forEach(book => {
            const truncatedDescription = book.description.length > 100 
                ? book.description.substring(0, 100) + '...' 
                : book.description;

            const bookCard = document.createElement('div');
            bookCard.classList.add('book-card');
            bookCard.innerHTML = `
                <img src="${book.cover_image_url}" alt="${book.title}">
                <div class="content">
                    <div class="title">${book.title}</div>
                    <div class="author">${book.author_name}</div>
                    <div class="description">${truncatedDescription}</div>
                    <button>View Details</button>
                </div>
            `;
            bookGrid.appendChild(bookCard);
        });
    }


        function filterBooks() {
            const category = categoryFilter.value.toLowerCase();
            const searchTerm = searchInput.value.toLowerCase();

            const filteredBooks = books.filter(book => {
                return (
                    (category === 'all' || book.genre.toLowerCase() === category) &&
                    (book.title.toLowerCase().includes(searchTerm) ||
                     book.author_name.toLowerCase().includes(searchTerm))
                );
            });

            displayBooks(filteredBooks);
        }

        categoryFilter.addEventListener('change', filterBooks);
        searchInput.addEventListener('input', filterBooks);

        // Initialize with all books displayed
        displayBooks(books);
    </script>
</body>
</html>
