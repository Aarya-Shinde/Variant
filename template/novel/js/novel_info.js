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

// ✅ Move fetchReviews and fetchComments to the global scope
function fetchReviews() {
    const novelId = document.getElementById("novelId")?.value;
    fetch(`/Variant/template/novel/fetch_data/fetch_reviews.php?novel_id=${novelId}`)
    .then(response => response.json())
    .then(data => {
        console.log("Reviews API Response:", data);
        const reviewsContainer = document.getElementById("reviews");
        reviewsContainer.innerHTML = data.reviews.length
            ? data.reviews.map(r => `<p><strong>${r.reviewer_name}</strong>: ${r.review_text} (${r.rating}★)</p>`).join("")
            : "<p>No reviews yet.</p>";
    })
    .catch(error => console.error("Error fetching reviews:", error));
}

function fetchComments() {
    const novelId = document.getElementById("novelId")?.value;
    fetch(`/Variant/template/novel/fetch_data/fetch_comments.php?novel_id=${novelId}&limit=5`)
    .then(response => response.json())
    .then(data => {
        console.log("Comments API Response:", data);
        const commentSection = document.getElementById("commentSection");
        commentSection.innerHTML = data.comments.length
            ? data.comments.map(c => `<p><strong>${c.commenter_name}</strong>: ${c.comment_text}</p>`).join("")
            : "<p>No reflections yet.</p>";
    })
    .catch(error => console.error("Error fetching comments:", error));
}

// ✅ Move these functions to global scope to be accessible by onclick
function addReview() {
    const novelId = document.getElementById("novelId")?.value;
    const reviewText = document.getElementById("reviewText")?.value.trim();
    const ratingInput = document.querySelector('input[name="rating"]:checked');

    if (!reviewText || !ratingInput) {
        alert("All fields are required.");
        return;
    }

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
            document.getElementById("reviewText").value = "";
            fetchReviews();  // ✅ Now this will work
        } else {
            alert("Failed to submit review: " + (data.error || "Unknown error"));
        }
    })
    .catch(error => console.error("Error submitting review:", error));
}

function addComment() {
    const novelId = document.getElementById("novelId")?.value;
    const commentText = document.getElementById("commentText")?.value.trim();
    if (!commentText) {
        alert("Please write your thoughts before submitting.");
        return;
    }

    const formData = new URLSearchParams();
    formData.append("novel_id", novelId);
    formData.append("comment_text", commentText);

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
            fetchComments();  // ✅ Now this will work
        } else {
            alert("Failed to submit comment: " + (data.error || "Unknown error"));
        }
    })
    .catch(error => console.error("Error submitting comment:", error));
}

// ✅ Keep DOMContentLoaded only for initial fetching
document.addEventListener("DOMContentLoaded", function () {
    fetchReviews();
    fetchComments();
});
