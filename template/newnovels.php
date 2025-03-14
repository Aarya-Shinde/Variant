<?php
include '../db/dbconnect.php';

// Fetch novels data
$sql = "SELECT 
            novel_id, 
            title, 
            author_name, 
            publication_date, 
            genre, 
            cover_image_url, 
            total_chapters, 
            series_name, 
            series_position, 
            description, 
            rating, 
            language 
        FROM Novels 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Novels</title>
</head>
        <!-- ------------------linking css js files------------------- -->
        <link rel="stylesheet" href="../indexstyle.css"> 
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
    
    <!------------------------------------------- Nav Bar------------------------------>
        
          <header>
            <div class="navbar">
                 <!-- variant logo image here -->
                <div class="logo"> <a href="../index.html"><img src="../images/logowrite.png" alt="Variant Logo" style="max-height: 40px;"></a> </div>
                
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
                        <li><a href="../pages/reader_dashboard.php">Reader</a></li>
                        <li><a href="../pages/writer_dashboard.php">Writer</a></li>
                        <li><a href="../pages/reader/library.php">Library</a></li>
                        <li><a href="../pages/reader_dashboard.php">User</a></li>
                    </ul>      
                </div>
                </div>
            </div>
        </header>
            
     <!-- Nav Bar Ends -->

  
        <!-- Main Content -->
 <body>  
       
        <main>
        <!-- New Novels Section -->
        <section class="new-novels">
            <h2>New Novels</h2>
            <div class="novels-grid">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <div class='novel-card'>
                            <img src='{$row['cover_image_url']}' alt='{$row['title']}' class='novel-cover'>
                            <h3>{$row['title']}</h3>
                            <p><strong>Author:</strong> {$row['author_name']}</p>
                            <p><strong>Genre:</strong> {$row['genre']}</p>
                            <p><strong>Chapters:</strong> {$row['total_chapters']}</p>
                            <p><strong>Series:</strong> {$row['series_name']} (Book {$row['series_position']})</p>
                            <p><strong>Rating:</strong> ⭐{$row['rating']}</p>
                            <p><strong>Language:</strong> {$row['language']}</p>
                            <!-- <p><strong>Description:</strong> {$row['description']}</p> -->
                            <p><strong>Published:</strong> {$row['publication_date']}</p>
                            
                        </div>
                        ";
                    }
                } else {
                    echo "<p>No novels available.</p>";
                }

                $conn->close();
                ?>
            </div>
        </section>
    </main>
    <style>

        .novels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px;
        }

        .novel-card {
            background: rgb(252, 229, 229);
            border: 1px solid #ffe2e2;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .novel-cover {
            width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        .novel-card h3 {
            margin: 10px 0 5px;
            font-size: 18px;
        }

        .novel-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }  
</style>

</body>
</html>
