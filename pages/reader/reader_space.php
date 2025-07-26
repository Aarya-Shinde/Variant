
    <title>Reader's Space</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        display: flex;
        justify-content: space-between; /* Space elements evenly */
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #e6ccb2;
        padding: 0 80px; /* Ensures spacing from edges */
    }

    #reader-space-container {
        display: flex;
        justify-content: center; /* Center content */
        align-items: center;
        height: 100vh;
        width: 130vh;
        margin: 0;
        padding: 0 50px;
        background-color: #e6ccb2;
        gap: 170px; /* Adds spacing between book containers and button container */
    }


    .book-container {
        flex: 1;
        max-width: 400px; /* Adjust for book size */
    }

    .button-container {
        flex: 0.5;
        display: flex;
        justify-content: center;
    }

    /* Book container shared styles */
    .book-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 200px;
        position: relative;
    }

    /* First book container (Left) */
    .book-container.left {
        align-items: flex-start;
    }

    /* Second book container (Right) */
    .book-container.right {
        align-items: flex-end;
    }

    /* Individual book styling */
    .book {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 200px;
        height: 50px;
        background-color: #EDE0D4;
        color: #4b3f3f;
        font-size: large;
        font-weight: bold;
        border: 2px solid #654321;
        border-radius: 4px;
        box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        position: relative;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    /* 3D effect on books */
    .book:before, .book:after {
        content: '';
        position: absolute;
        width: 10px;
        height: 100%;
        background-color: #654321;
    }

    .book:before { left: -10px; border-radius: 5px 0 0 5px; }
    .book:after { right: -10px; border-radius: 0 5px 5px 0; }

    /* Hover effect for books */
    .book:hover {
        background-color: #764134;
        color: #fff;
        transform: translateY(-4px) scale(1.02);
    }

    /* Button container (Centered) */
    .button-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }

    /* Button styles */
    .button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 280px;
        height: 50px;
        background-color: #764134;
        color: #fff;
        font-size: large;
        font-weight: bold;
        border: 2px solid #654321;
        border-radius: 6px;
        box-shadow: 3px 5px 10px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        position: relative;
        text-decoration: none;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    /* Hover effects for buttons */
    .button:hover {
        background-color: #411d0c;
        transform: translateY(-4px) scale(1.05);
        color: #ffffff;
    }

    /* Click effect */
    .button:active {
        background-color: #2c1d0e;
        transform: translateY(0);
    }

    /* Book entry animation */
    @keyframes bookEntry {
        0% { transform: translateY(-100px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }

    /* Apply animation to books */
    .book {
        animation: bookEntry 0.6s ease-in-out;
    }

    /* Button entry animation */
    @keyframes buttonEntry {
        0% { transform: translateX(100px); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }

    .button {
        animation: buttonEntry 0.8s ease-in-out;
    }

    /******** Styling books in darktoogle mode  ***********/
    .dark-mode body {
        background-color: #212529 !important;
        color: #e0e0e0 !important;
    }

    .dark-mode #reader-space-container {
        background-color: #1b1f23 !important;
        color: #e0e0e0 !important;
    }

    .dark-mode .book {
        background-color: #2c3034 !important;
        color: #e0e0e0 !important;
        border: 2px solid #555 !important;
    }

    .dark-mode .book:hover {
        background-color: #4a4f54 !important;
    }

    .dark-mode .button {
        background-color: #343a40 !important;
        color: #e0e0e0 !important;
        border: 2px solid #555 !important;
    }

    .dark-mode .button:hover {
        background-color: #495057 !important;
    }

</style>


    <div id="reader-space-container">
        <!-- Left Side: First Bookshelf -->
        <div class="book-container left">
            <button class="book">The Hobbit</button>
            <button class="book">Catching Fire</button>
            <button class="book">The Death Cure</button>
            <button class="book">A Game of Thrones</button>
            <button class="book">The Catcher in the Rye</button>
            <button class="book">The Maze Runner</button>
            <button class="book">The Death Cure</button>
            <button class="book">The Sea of Monsters</button>
        </div>

        <!-- Center: Buttons Navigation -->
        <div class="button-container">
            <a class="button" href="/Variant/pages/reader/diary/diary.html">Diary</a>
            <!-- Library button (opens in a new tab) -->
            <a class="button" href="/Variant/pages/reader/library.php">Library</a>

            <a class="button" href="/Variant/template/explore.php">Explore</a>
            <a class="button" href="add-book.html">Add Book</a>
        </div>

        <!-- Right Side: Second Bookshelf -->
        <div class="book-container right">
            <button class="book">Mancled</button>
            <button class="book">The Scorch Trials</button>
            <button class="book">Iron Flame</button>
            <button class="book">Catching Fire</button>
            <button class="book">Onyx Storm</button>
            <button class="book">Fourth Wing</button>
            <button class="book">The Chronicles of Narnia</button>
            <button class="book">Twisted Lies</button>
            <button class="book">The Way of Kings</button>
        </div>
    </div>

    <script>
        // Random book positioning for a natural look
        document.querySelectorAll('.book').forEach(book => { 
            const randomOffset = Math.floor(Math.random() * 15) - 7;  
            book.style.transform = `translateX(${randomOffset}px) rotate(${randomOffset / 5}deg)`;
        });

        // Random slight offset for buttons
        document.querySelectorAll('.button').forEach(button => { 
            const randomOffset = Math.floor(Math.random() * 10) - 5;  
            button.style.transform = `translateX(${randomOffset}px)`;
        });
    </script>

