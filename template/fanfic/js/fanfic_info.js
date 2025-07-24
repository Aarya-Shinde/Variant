document.addEventListener("DOMContentLoaded", () => {
    if (typeof fanficId === "undefined") {
        console.error("Fanfic ID not found");
        return;
    }

    loadReviews();
    loadComments();

    const reviewBtn = document.querySelector("button[onclick='addReview()']");
    if (reviewBtn) reviewBtn.onclick = addReview;

    const commentBtn = document.querySelector("button[onclick='addComment()']");
    if (commentBtn) commentBtn.onclick = addComment;
});

function loadReviews() {
    fetch(`/variant/template/fanfic/fetch_reviews.php?fanfic_id=${fanficId}`)
        .then(r => r.json())
        .then(drawReviews)
        .catch(console.error);
}

function drawReviews(data) {
    const box = document.getElementById("reviews-list");
    box.innerHTML = data.success
        ? data.reviews.map(r => `
            <div class="review">
                <strong>${r.rating}⭐</strong> — ${r.review_text}
                <hr>
            </div>
        `).join("")
        : "<p>No reviews yet.</p>";
}

function addReview() {
    const text = document.getElementById("reviewText").value.trim();
    const rating = document.querySelector('input[name="rating"]:checked')?.value;
    if (!text || !rating) {
        alert("Please fill in review text and rating.");
        return;
    }

    fetch('/variant/template/fanfic/submit_review.php', {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            fanfic_id: fanficId,
            review_text: text,
            rating: Number(rating)
        })
    })
    .then(r => r.json())
    .then(x => {
        if (x.success) {
            document.getElementById("reviewText").value = '';
            loadReviews();
        } else {
            alert(x.error || "Error submitting review.");
        }
    });
}

function loadComments() {
    fetch(`/variant/template/fanfic/fetch_comments.php?fanfic_id=${fanficId}`)
        .then(r => r.json())
        .then(data => {
            const c = document.getElementById("comments-container");
            c.innerHTML = data.success
                ? data.comments.map(cm => `
                    <div class="comment">
                        ${cm.comment_text}
                        <hr>
                    </div>
                `).join("")
                : "<p>No comments yet.</p>";
        })
        .catch(console.error);
}

function addComment() {
    const text = document.getElementById("commentText").value.trim();
    if (!text) {
        alert("Enter a comment.");
        return;
    }

    fetch('/variant/template/fanfic/submit_comment.php', {
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `fanfic_id=${encodeURIComponent(fanficId)}&comment_text=${encodeURIComponent(text)}`
    })
    .then(r => r.json())
    .then(x => {
        if (x.success) {
            document.getElementById("commentText").value = '';
            loadComments();
        } else {
            alert(x.error || "Error submitting comment.");
        }
    });
}
