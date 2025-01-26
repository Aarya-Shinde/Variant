<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #DABAA5 60%, #A37C70 30%, #DCAE96 10%);
            color: #0B0A07;
        }

        .header {
            background-color: #88292F;
            color: white;
            padding: 1.5rem;
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .container {
            padding: 2rem;
        }

        .search-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .search-filter input {
            width: 60%;
            padding: 1rem;
            font-size: 1.1rem;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter {
            position: relative;
        }

        .filter select {
            padding: 1rem;
            font-size: 1.1rem;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            cursor: pointer;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .book-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .book-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .book-card .content {
            padding: 1rem;
            text-align: center;
        }

        .book-card .title {
            font-size: 1.3rem;
            margin: 0.5rem 0;
            font-weight: bold;
            color: #88292F;
        }

        .book-card .author {
            font-size: 1rem;
            color: #6b7280;
        }

        .book-card .description {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #333;
        }

        .book-card button {
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            background-color: #A77464;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .book-card button:hover {
            background-color: #88292F;
        }
    </style>
</head>
<body>
    <div class="header">Explore Books & More</div>

    <div class="container">
        <div class="search-filter">
            <input type="text" placeholder="Search for books, authors, genres, or categories...">
            <div class="filter">
                <select id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="novels">Novels</option>
                    <option value="comics">Comics</option>
                    <option value="manga">Manga</option>
                    <option value="manhwa">Manhwa</option>
                    <option value="horror">Horror</option>
                    <option value="romance">Romance</option>
                    <option value="thriller">Thriller</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="scientific">Scientific</option>
                </select>
            </div>
        </div>

        <div class="book-grid" id="bookGrid"></div>
    </div>

    <script>
        const books = [
            { title: "The Great Gatsby", author: "F. Scott Fitzgerald", category: "novels", description: "A tale of love and ambition in the roaring twenties.", image: "https://via.placeholder.com/250x300?text=The+Great+Gatsby" },
            { title: "Naruto", author: "Masashi Kishimoto", category: "manga", description: "Follow Naruto Uzumaki's ninja journey.", image: "https://via.placeholder.com/250x300?text=Naruto" },
            { title: "The Hobbit", author: "J.R.R. Tolkien", category: "fantasy", description: "An epic journey through Middle-earth.", image: "https://via.placeholder.com/250x300?text=The+Hobbit" },
            { title: "Attack on Titan", author: "Hajime Isayama", category: "manga", description: "A fight against human-eating giants.", image: "https://via.placeholder.com/250x300?text=Attack+on+Titan" },
            { title: "Spider-Man", author: "Marvel Comics", category: "comics", description: "The adventures of Peter Parker, aka Spider-Man.", image: "https://via.placeholder.com/250x300?text=Spider-Man" },
            { title: "Jane Eyre", author: "Charlotte Brontë", category: "novels", description: "A story of love and resilience.", image: "https://via.placeholder.com/250x300?text=Jane+Eyre" },
            { title: "Dr. Stone", author: "Riichiro Inagaki", category: "scientific", description: "Reviving humanity through science.", image: "https://via.placeholder.com/250x300?text=Dr.+Stone" },
            { title: "Solo Leveling", author: "Chu-Gong", category: "manhwa", description: "The journey of the weakest hunter.", image: "https://via.placeholder.com/250x300?text=Solo+Leveling" },
            { title: "Chainsaw Man", author: "Tatsuki Fujimoto", category: "manga", description: "A devil-hunting saga.", image: "https://via.placeholder.com/250x300?text=Chainsaw+Man" },
            { title: "Demon Slayer", author: "Koyoharu Gotouge", category: "manga", description: "Fighting demons to save humanity.", image: "https://via.placeholder.com/250x300?text=Demon+Slayer" },
            { title: "Pride and Prejudice", author: "Jane Austen", category: "romance", description: "A timeless romantic tale.", image: "https://via.placeholder.com/250x300?text=Pride+and+Prejudice" },
            { title: "The Odyssey", author: "Homer", category: "thriller", description: "A thrilling journey of Odysseus.", image: "https://via.placeholder.com/250x300?text=The+Odyssey" }
        ];

        const bookGrid = document.getElementById('bookGrid');
        const categoryFilter = document.getElementById('categoryFilter');

        function displayBooks(category = 'all') {
            bookGrid.innerHTML = '';
            const filteredBooks = category === 'all' ? books : books.filter(book => book.category === category);
            filteredBooks.forEach(book => {
                const bookCard = document.createElement('div');
                bookCard.classList.add('book-card');
                bookCard.innerHTML = `
                    <img src="${book.image}" alt="${book.title}">
                    <div class="content">
                        <div class="title">${book.title}</div>
                        <div class="author">${book.author}</div>
                        <div class="description">${book.description}</div>
                        <button>View Details</button>
                    </div>
                `;
                bookGrid.appendChild(bookCard);
            });
        }

        categoryFilter.addEventListener('change', () => {
            displayBooks(categoryFilter.value);
        });

        displayBooks();
    </script>
</body>
</html>
