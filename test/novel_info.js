// <!-- Book Details Section -->

(function () {
    var chapters = []; // Global variable
    var startIndex = 0;
    const chaptersPerPage = 15;

    document.addEventListener("DOMContentLoaded", function () {
        const chapterList = document.getElementById("chapterList");
        if (!chapterList) {
            console.error("Error: Element with ID 'chapterList' not found.");
            return;
        }

        fetch("fetch_novel.php")
            .then(response => response.json())
            .then(data => {
                if (!data.novel) throw new Error("Novel data missing");

                document.getElementById("bookTitle").textContent = data.novel.title || "Unknown Title";
                document.getElementById("bookCover").src = data.novel.cover_image_url || "placeholder.jpg";
                document.getElementById("bookSummary").textContent = data.novel.description || "No description available.";
                document.getElementById("publishDate").textContent = data.novel.publication_date || "Unknown Date";
                document.getElementById("bookAuthor").textContent = data.novel.author_name || "Unknown Author";

                let tagsContainer = document.getElementById("tagsContainer");
                if (tagsContainer) {
                    tagsContainer.innerHTML = "";
                    if (data.tags && Array.isArray(data.tags) && data.tags.length > 0) {
                        data.tags.forEach(tag => {
                            let span = document.createElement("span");
                            span.className = "tag";
                            span.textContent = tag;
                            tagsContainer.appendChild(span);
                        });
                    } else {
                        tagsContainer.textContent = "No tags available.";
                    }
                }

                // Ensure chapters array is valid
                chapters = Array.isArray(data.chapters) ? data.chapters : [];
                displayChapters();
            })
            .catch(error => console.error("Error loading novel:", error));
    });

    function displayChapters() {
        const chapterList = document.getElementById("chapterList");
        if (!chapterList) {
            console.error("Error: Element with ID 'chapterList' not found.");
            return;
        }

        chapterList.innerHTML = ""; // Clear previous chapters

        if (chapters.length === 0) {
            chapterList.innerHTML = "<li>No chapters available.</li>";
            return;
        }

        for (let i = startIndex; i < startIndex + chaptersPerPage && i < chapters.length; i++) {
            const li = document.createElement("li");
            li.textContent = `Chapter ${chapters[i].chapter_number}: ${chapters[i].title}`;
            chapterList.appendChild(li);
        }
    }

    window.nextChapters = function () {
        if (startIndex + chaptersPerPage < chapters.length) {
            startIndex += chaptersPerPage;
            displayChapters();
        }
    };

    window.prevChapters = function () {
        if (startIndex - chaptersPerPage >= 0) {
            startIndex -= chaptersPerPage;
            displayChapters();
        }
    };
})();




// review fetching section


document.addEventListener("DOMContentLoaded", fetchReviews);

function fetchReviews() {
  fetch("fetch_reviews.php")
    .then(response => response.json())
    .then(data => {
      const reviewsContainer = document.getElementById("reviews");

      // Clear only the reviews while keeping the form
      const reviewForm = document.querySelector(".review-form");
      reviewsContainer.innerHTML = "<h2>Reviews</h2>"; 
      reviewsContainer.appendChild(reviewForm); 

      if (!data.reviews || !Array.isArray(data.reviews) || data.reviews.length === 0) {
        let noReviewsMsg = document.createElement("p");
        noReviewsMsg.textContent = "No reviews yet. Be the first to review!";
        reviewsContainer.insertBefore(noReviewsMsg, reviewForm);
        return;
      }

      data.reviews.forEach(review => {
        let reviewDiv = document.createElement("div");
        reviewDiv.className = "review";

        let stars = document.createElement("div");
        stars.className = "stars";
        stars.textContent = "★".repeat(review.rating) + "☆".repeat(5 - review.rating);

        reviewDiv.innerHTML = `<strong>${review.reviewer_name}:</strong> "${review.review_text}"
                              <small>Reviewed on ${new Date(review.created_at).toLocaleDateString()}</small>`;
        reviewDiv.appendChild(stars);
        reviewsContainer.insertBefore(reviewDiv, reviewForm);
      });
    })
    .catch(error => {
      console.error("Error fetching reviews:", error);
      document.getElementById("reviews").innerHTML += "<p>Error loading reviews.</p>";
    });
}

  
// Review adding form function
  function addReview() {
    const reviewText = document.getElementById("reviewText").value.trim();
    const ratingInput = document.querySelector('input[name="rating"]:checked');

    if (!reviewText) {
      alert("Please share your thoughts before submitting.");
      return;
    }
    if (!ratingInput) {
      alert("Please select a star rating.");
      return;
    }

    const rating = parseInt(ratingInput.value);
    const formData = new URLSearchParams();
    formData.append("review_text", reviewText);
    formData.append("rating", rating);

    fetch("submit_review.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: formData.toString()
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Review submitted successfully!");
          document.getElementById("reviewText").value = "";
          document.querySelectorAll('input[name="rating"]').forEach(input => (input.checked = false));
          fetchReviews();
        } else {
          alert("Failed to submit review.");
        }
      })
      .catch(error => {
        console.error("Error submitting review:", error);
        alert("An error occurred while submitting your review.");
      });
  }