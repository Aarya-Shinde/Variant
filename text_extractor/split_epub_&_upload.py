import re
import mysql.connector
import ebooklib
from ebooklib import epub
from bs4 import BeautifulSoup

# Load EPUB file
epub_path = r"D:\Xammp\htdocs\Variant\text_extractor\Harry Potter.epub"
book = epub.read_epub(epub_path)

# Extract raw text from EPUB
chapters = []
novel_id = 11  # Update with the correct novel ID

for item in book.get_items():
    if item.get_type() == ebooklib.ITEM_DOCUMENT:  # Process HTML/XHTML content
        soup = BeautifulSoup(item.get_body_content(), "html.parser")
        
        # **Extract chapter title (H1, H2) if available**
        title = "Unknown Chapter"
        heading = soup.find(["h1", "h2"])  # Get first chapter title
        if heading:
            title = heading.get_text(strip=True)

        # **Preserve Bold & Italic Formatting**
        for tag in soup.find_all(["b", "strong"]):
            tag.replace_with(f"<b>{tag.get_text(strip=True)}</b>")  # Keep bold
        for tag in soup.find_all(["i", "em"]):
            tag.replace_with(f"<i>{tag.get_text(strip=True)}</i>")  # Keep italics

        # **Convert remaining HTML structure into text with basic formatting**
        chapter_html = str(soup)  # Convert processed HTML to string

        # **Remove Page Numbers**
        chapter_html = re.sub(r"\b\d{1,4}\b", "", chapter_html)  # Remove isolated numbers (1-4 digits)

        # **Remove the first line if it matches the chapter title**
        chapter_lines = chapter_html.split("\n")
        if chapter_lines and BeautifulSoup(chapter_lines[0], "html.parser").get_text(strip=True) == title:
            chapter_html = "\n".join(chapter_lines[1:])  # Remove title line
        
        # **Format content to preserve paragraphs**
        paragraphs = [f"<p>{p.strip()}</p>" for p in chapter_html.split("\n") if p.strip()]
        formatted_content = "\n".join(paragraphs)  # Convert to HTML-style paragraphs

        # **Assign a chapter number (fallback to auto-increment)**
        chapter_number = len(chapters) + 1  

        # **Store extracted chapter**
        chapters.append((novel_id, chapter_number, title, formatted_content))

# **Connect to MySQL**
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="@pokemon1",
    database="Variant"
)
cursor = conn.cursor()

# **Insert chapters into MySQL**
query = """
INSERT INTO Chapters (novel_id, chapter_number, title, content)
VALUES (%s, %s, %s, %s)
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content);
"""

cursor.executemany(query, chapters)
conn.commit()

cursor.close()
conn.close()

print(f"Uploaded {len(chapters)} chapters to MySQL from EPUB!")

# ebooklib → Read EPUB files
# 📌 Why?

# The EPUB format is a zip archive containing XHTML files.

# ebooklib allows us to extract and process the chapters.

# Specifically, it lets us load an EPUB and get its text content.

# 🔹 Alternative? → Manually unzip & parse EPUB (but ebooklib makes it easy).

# 2️⃣ beautifulsoup4 → Parse HTML content from EPUB
# 📌 Why?

# EPUB chapters are stored as XHTML files.

# BeautifulSoup helps clean and extract meaningful text.

# It removes unnecessary tags while keeping readable structure.



# 3️⃣mysql-connector-python → Connect to MySQL


# This library lets Python communicate with a MySQL database.

# We need it to store extracted EPUB chapters in MySQL.