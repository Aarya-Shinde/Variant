
<!-- ************php connection ends here********************* -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville&display=swap" rel="stylesheet">
</head>
<body>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">📖 Bookish Writer</div>
            <ul>
                <li class="menu-item active" data-section="dashboard">📂 Dashboard</li>
                <li class="menu-item" data-section="stories">📜 My Stories</li>
                <li class="menu-item" data-section="income">💰 Income</li>
                <li class="menu-item" data-section="inbox">📩 Inbox</li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            
            <!-- Dashboard Section -->
            <section id="dashboard" class="section active">
                <h2>📂 Welcome to Your Dashboard</h2>
                <p>Overview of your writing journey.</p>
            </section>

            <!-- My Stories Section -->
            <section id="stories" class="section">
                <div class="top-bar">
                    <h2>📖 My Stories</h2>
                    <button id="createStoryBtn">+ Create a Story</button>
                </div>

                <div class="story-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Stories</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Operations</th>
                            </tr>
                        </thead>
                        <tbody id="storyList">
                            <tr>
                                <td><img src="../images/book (2).jpeg" alt="Book Cover"></td>
                                <td>The Lost Chronicles</td>
                                <td>Published</td>
                                <td>2.5K</td>
                                <td><button class="explore-btn">Explore</button></td>
                            </tr>
                            <tr>
                                <td><img src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1721069346i/61374793.jpg" alt="Book Cover"></td>
                                <td>Midnight Whispers</td>
                                <td>Draft</td>
                                <td>785</td>
                                <td><button class="explore-btn">Explore</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Income Section -->
            <section id="income" class="section">
                <h2>💰 Income</h2>
                <p>Your earnings from published stories.</p>
            </section>

            <!-- Inbox Section -->
            <section id="inbox" class="section">
                <h2>📩 Inbox</h2>
                <p>Messages from readers and publishers.</p>
            </section>
        </main>
    </div>

<style>
    body {
        margin: 0;
        font-family: 'Libre Baskerville', serif;
        background-color: #f5f1e8;
        color: #5a4234;
    }

    .dashboard-container {
        display: flex;
        height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background-color: #4d2c1a;
        color: white;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sidebar .logo {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        padding-bottom: 20px;
        border-bottom: 2px solid #7a5640;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 5px;
        transition: background 0.3s ease-in-out;
    }

    .sidebar ul li:hover,
    .sidebar ul .active {
        background-color: #7a5640;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        padding: 20px;
    }

    /* Hide sections by default */
    .section {
        display: none;
    }

    .section.active {
        display: block;
    }

    /* Top Bar */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #c0a080;
        padding: 15px;
        border-radius: 10px;
    }

    #createStoryBtn {
        background-color: #5a4234;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
    }

    #createStoryBtn:hover {
        background-color: #3a291f;
    }

    /* Story Table */
    .story-table {
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 15px;
        border-bottom: 1px solid #ccc;
    }

    th {
        background-color: #c0a080;
        color: white;
        text-align: left;
    }

    /* Story Cover Image */
    td img {
        width: auto;
        height: 100px;
        border-radius: 5px;
    }

    .explore-btn {
        background-color: #5a4234;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
    }

    .explore-btn:hover {
        background-color: #3a291f;
    }

</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll(".menu-item");
    const sections = document.querySelectorAll(".section");

    // Sidebar click event
    menuItems.forEach((item) => {
        item.addEventListener("click", function () {
            // Remove active class from all menu items
            menuItems.forEach((el) => el.classList.remove("active"));
            this.classList.add("active");

            // Show the selected section
            const sectionId = this.getAttribute("data-section");
            sections.forEach((section) => {
                section.classList.remove("active");
                if (section.id === sectionId) {
                    section.classList.add("active");
                }
            });
        });
    });

    // "Create Story" button
    document.getElementById("createStoryBtn").addEventListener("click", function () {
        alert("Redirect to Story Creation Page (Implement as Needed)");
    });
});


</script>
</body>
</html>

