document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const novelId = urlParams.get("novel_id");

    const chapterList = document.getElementById("chapterList");
    const novelTitle = document.getElementById("novelTitle");
    const addChapterBtn = document.getElementById("addChapter");
    const exportPdfBtn = document.getElementById("exportPdf");
    const toggleDarkModeBtn = document.getElementById("toggleDarkMode");
    const bulkDeleteBtn = document.getElementById("bulkDelete");
    const writingInterface = document.getElementById("writingInterface");
    const chapterTitleInput = document.getElementById("chapterTitleInput");
    const chapterContentInput = document.getElementById("chapterContentInput");
    const saveChapterBtn = document.getElementById("saveChapterButton");

    async function fetchNovelTitle() {
        try {
            const response = await fetch(`./api/getNovelTitle.php?novel_id=${novelId}`);
            if (!response.ok) throw new Error("Failed to fetch novel title");
            const data = await response.json();
            novelTitle.textContent = data.title || "Untitled Novel";
        } catch (error) {
            console.error("Error fetching title:", error);
        }
    }

    async function fetchChapters() {
        try {
            const response = await fetch(`./api/editStories.php?novel_id=${novelId}`);
            if (!response.ok) throw new Error("Failed to fetch chapters");
            const data = await response.json();
            renderChapters(data);
        } catch (error) {
            console.error("Error fetching chapters:", error);
        }
    }

    function renderChapters(chapters) {
        chapterList.innerHTML = "";
        chapters.forEach((chapter) => {
            const li = document.createElement("li");
            li.classList.add("chapter-item");
            li.innerHTML = `
                <input type="checkbox" class="bulk-delete-checkbox">
                <input type="text" class="chapter-title" value="${chapter.title}" onchange="autoSaveChapter(${chapter.chapter_id}, this)">
                <div contenteditable="true" class="chapter-content" oninput="autoSaveChapter(${chapter.chapter_id}, this)">${chapter.content}</div>
                <button onclick="deleteChapter(${chapter.chapter_id})"> Delete</button>
            `;
            chapterList.appendChild(li);
        });
    }

    async function autoSaveChapter(chapterId, element) {
        const title = element.closest("li").querySelector(".chapter-title").value;
        const content = element.closest("li").querySelector(".chapter-content").innerHTML;
        
        try {
            await fetch("./api/editStories.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ chapter_id: chapterId, title, content, novel_id: novelId }),
            });
        } catch (error) {
            console.error("Auto-save failed:", error);
        }
    }

    async function deleteChapter(chapterId) {
        if (!confirm("Delete this chapter?")) return;
        try {
            await fetch("./api/editStories.php", {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ chapter_id: chapterId }),
            });
            fetchChapters();
        } catch (error) {
            console.error("Error deleting chapter:", error);
        }
    }

    async function bulkDeleteChapters() {
        const selectedChapters = document.querySelectorAll(".bulk-delete-checkbox:checked");
        if (selectedChapters.length === 0) {
            alert("No chapters selected for deletion.");
            return;
        }
        if (!confirm(`Are you sure you want to delete ${selectedChapters.length} chapters?`)) return;

        const chapterIds = Array.from(selectedChapters).map(cb => cb.closest("li").querySelector(".chapter-title").getAttribute("data-id"));

        try {
            await fetch("./api/editStories.php", {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ chapter_ids: chapterIds }),
            });
            fetchChapters();
        } catch (error) {
            console.error("Error in bulk delete:", error);
        }
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let contentArray = [];

        document.querySelectorAll(".chapter-item").forEach((item, index) => {
            contentArray.push(`Chapter ${index + 1}: ${item.querySelector(".chapter-title").value}\n`);
            contentArray.push(item.querySelector(".chapter-content").innerText + "\n\n");
        });

        const pageContent = doc.splitTextToSize(contentArray.join(""), 180);
        doc.text(pageContent, 10, 10);
        doc.save("novel.pdf");
    }

    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
    }

    function openWritingInterface() {
        writingInterface.classList.remove("hidden");
        chapterTitleInput.value = "";
        chapterContentInput.innerHTML = "";
    }

    function closeWritingInterface() {
        writingInterface.classList.add("hidden");
    }
    window.closeWritingInterface = closeWritingInterface;

    async function saveNewChapter() {
        const title = chapterTitleInput.value || "Untitled Chapter";
        const content = chapterContentInput.innerHTML;
        if (!content.trim()) {
            alert("Chapter content cannot be empty!");
            return;
        }
        await fetch("./api/editStories.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ novel_id: novelId, title, content }),
        });
        closeWritingInterface();
        fetchChapters();
    }

    function formatText(command) {
        document.execCommand(command, false, null);
    }
    window.formatText = formatText;

    addChapterBtn.addEventListener("click", openWritingInterface);
    saveChapterBtn.addEventListener("click", saveNewChapter);
    exportPdfBtn.addEventListener("click", exportToPDF);
    toggleDarkModeBtn.addEventListener("click", toggleDarkMode);
    bulkDeleteBtn.addEventListener("click", bulkDeleteChapters);

    fetchNovelTitle().then(fetchChapters);
});
