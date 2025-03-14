document.addEventListener("DOMContentLoaded", function () {
    const novelId = new URLSearchParams(window.location.search).get("novel_id");


    if (!novelId) {
        console.error("No novel ID found in URL.");
        alert("Error: No novel ID found!");
        window.location.href = "../../explore.php"; // Redirect if no novel ID
        return;
    }

    const API_BASE_URL = "/variant/template/novel/fetch_data/";

    fetch(`${API_BASE_URL}fetch_novel.php?novel_id=${novelId}`)
    .then(response => response.json())
    .then((data) => {
        console.log("Novel Data:", data); // Debugging log
        if (!data || typeof data !== "object") {
            throw new Error("Invalid novel data received");
        }
        displayNovel(data);  // Ensure data is defined before calling displayNovel()
    })
    .catch((error) => {
        console.error("Error fetching novel details:", error);
        alert("Failed to load novel details.");
    });


    fetch(`${API_BASE_URL}fetch_reviews.php?novel_id=${novelId}`)
    .then(response => response.text()) // Read response as text first
    .then(text => {
        try {
            return JSON.parse(text); // Attempt to parse JSON
        } catch (error) {
            throw new Error("Invalid JSON response: " + text);
        }
    })
    .then(data => {
        console.log("Fetched reviews data:", data); // Debugging
        displayReviews(data);
    })
    .catch(error => console.error("Error fetching reviews:", error));


    fetch(`${API_BASE_URL}fetch_comments.php?novel_id=${novelId}`)
        .then(response => response.json())
        .then(data => displayComments(data))
        .catch(error => console.error("Error fetching comments:", error));
});

// Display Novel Details
function displayNovel(data) {
    if (!data || !data.novel) {
        console.error("Invalid novel data:", data);
        alert("Error fetching novel details.");
        return;
    }

    const novel = data.novel; // Access the nested novel object
    document.getElementById("bookTitle").innerText = novel.title || "Untitled";
    document.getElementById("bookCover").src = novel.cover_image_url;
    document.getElementById("bookCover").alt = novel.title + " Cover";
    document.getElementById("bookSummary").innerText = novel.description || "No description available.";
    document.getElementById("publishDate").innerText = novel.publication_date || "Unknown";
    document.getElementById("bookAuthor").innerText = novel.author_name || "Unknown";
}

// Display Reviews
function displayReviews(data) {
    if (!data || !data.success) {
        console.error("Error:", data?.error || "Invalid response");
        return;
    }

    const reviewContainer = document.getElementById("reviews-list");
    if (!reviewContainer) {
        console.error("Review container not found.");
        return;
    }

    if (!data.reviews || data.reviews.length === 0) {
        reviewContainer.innerHTML = "<p>No reviews yet.</p>";
        return;
    }

    reviewContainer.innerHTML = "<h2>Reviews</h2>";
    data.reviews.forEach(review => {
        reviewContainer.innerHTML += `
            <div class="review">
                <p><strong>${review.reviewer_name}</strong> - <small>${review.created_at}</small></p>
                <p><strong>Rating:</strong> ${review.rating} ⭐</p>
                <p>${review.review_text}</p>
                <hr>
            </div>
        `;
    });
}

// Display Comments
function displayComments(data) {
    if (!data || !data.success) {
        console.error("Error:", data?.error || "Invalid response");
        return;
    }

    const commentSection = document.getElementById("commentSection");
    if (!commentSection) {
        console.error("Comment section not found.");
        return;
    }

    if (!data.comments || data.comments.length === 0) {
        commentSection.innerHTML = "<p>No comments yet.</p>";
        return;
    }

    commentSection.innerHTML = "<h2>Comments</h2>";
    data.comments.forEach(comment => {
        commentSection.innerHTML += `
            <div class="comment">
                <p><strong>${comment.commenter_name}</strong> - <small>${comment.created_at}</small></p>
                <p>${comment.comment_text}</p>
                <hr>
            </div>
        `;
    });
}

// Add a small delay before refetching reviews


function submitReview(novelId, reviewText, rating) {
    const reviewData = { novel_id: novelId, review_text: reviewText, rating: rating };

    fetch("/variant/template/novel/fetch_data/submit_review.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(reviewData),
    })
    .then(response => response.json())
    .then(data => {
        console.log("Review submission response:", data);
        if (data.success) {
            alert("Review submitted successfully!");
            fetchReviews(novelId); // Refresh reviews immediately
        } else {
            alert("Error submitting review.");
        }
    })
    .catch(error => console.error("Error submitting review:", error));
}