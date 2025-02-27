// <!-- Book Details Section -->

// Book Details Section
// Global Variables

// chapters: Stores the fetched chapter data.
// startIndex: Tracks the current position for pagination.
// chaptersPerPage: Defines how many chapters are displayed per page.
// Fetching Novel Data

// Uses fetch("template/novel/fetch_data/fetch_novel.php") to retrieve book details.
// Updates elements like title, cover, summary, author, and publication date.
// Displays tags if available.
// Displaying Chapters with Pagination

// displayChapters(): Clears and renders a paginated list of chapters.
// nextChapters(): Moves to the next set of chapters if available.
// prevChapters(): Moves back to the previous set.


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

        fetch("/Variant/template/novel/fetch_data/fetch_novel.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
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

        chapterList.innerHTML = "";

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

// Review Fetching Section
// Review Fetching Section
// Fetching and Displaying Reviews

// Calls fetchReviews() when the page loads.
// Fetches reviews from fetch_reviews.php and updates the UI.
// Handles empty reviews by showing a placeholder message.
// Adding a Review

// Reads review text and selected rating.
// Sends a POST request to submit_review.php.
// Reloads reviews after successful submission.

function fetchReviews() {
    fetch("/template/novel/fetch_data/fetch_reviews.php")
    .then(response => response.json())
    .then(data => {
        const reviewsContainer = document.getElementById("reviews");
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

//adding review to the website

function addReview() {
    const reviewText = document.getElementById("reviewText")?.value.trim();
    const ratingInput = document.querySelector('input[name="rating"]:checked');
    const novelId = document.getElementById("novelId")?.value;

    if (!reviewText || !ratingInput || !novelId) {
        alert("All fields are required.");
        return;
    }

    console.log("Review Data:", { novel_id: novelId, review_text: reviewText, rating: ratingInput.value });

    const formData = new URLSearchParams();
    formData.append("novel_id", novelId);
    formData.append("review_text", reviewText);
    formData.append("rating", parseInt(ratingInput.value, 10));

    fetch("/Variant/template/novel/submit_data/submit_review.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Review submitted successfully!");
            fetchReviews();
        } else {
            alert("Failed to submit review: " + (data.error || "Unknown error"));
        }
    })
    .catch(error => console.error("Error submitting review:", error));
}



///Comments fetching and review

function fetchComments(start = 0) {
    fetch(`/template/novel/fetch_data/fetch_comments.php?offset=${start}&limit=5`)
    .then(response => response.json())
    .then(data => {
        const commentSection = document.getElementById("commentSection");

        if (start === 0) commentSection.innerHTML = "";

        if (!data.comments || data.comments.length === 0) {
            if (start === 0) commentSection.innerHTML = "<p>No reflections yet. Be the first to share your thoughts!</p>";
            return;
        }

        data.comments.forEach(comment => {
            let commentDiv = document.createElement("div");
            commentDiv.className = "comment";
            commentDiv.innerHTML = `<strong>${comment.commenter_name || "Anonymous"}:</strong>
                                    <p>${comment.comment_text}</p>
                                    <small>Posted on ${new Date(comment.created_at).toLocaleDateString()}</small>`;
            commentSection.appendChild(commentDiv);
        });

        if (data.has_more) {
            let loadMoreBtn = document.getElementById("loadMoreBtn");
            if (!loadMoreBtn) {
                loadMoreBtn = document.createElement("button");
                loadMoreBtn.id = "loadMoreBtn";
                loadMoreBtn.textContent = "Load More Comments";
                loadMoreBtn.classList.add("load-more");
                loadMoreBtn.onclick = () => fetchComments(start + 5);
                commentSection.appendChild(loadMoreBtn);
            }
        } else {
            document.getElementById("loadMoreBtn")?.remove();
        }
    })
    .catch(error => {
        console.error("Error fetching comments:", error);
        document.getElementById("commentSection").innerHTML = "<p>Error loading comments.</p>";
    });
}

function addComment() {
    const commentText = document.getElementById("commentText")?.value.trim();
    if (!commentText) {
        alert("Please write your thoughts before submitting.");
        return;
    }

    const formData = new URLSearchParams();
    formData.append("novel_id", document.getElementById("novelId")?.value || "");
    formData.append("comment_text", commentText); // No need to send user_id manually

    fetch("/Variant/template/novel/submit_data/submit_comment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Comment added successfully!");
            document.getElementById("commentText").value = "";
            fetchComments();
        } else {
            alert("Failed to submit comment: " + (data.error || "Unknown error"));
        }
    })
    .catch(error => console.error("Fetch Error:", error));
}

