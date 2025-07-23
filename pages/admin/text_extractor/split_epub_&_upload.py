import sys
import os
import re
import ebooklib
from ebooklib import epub
from bs4 import BeautifulSoup

# Add Variant to path
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from db.dbconnect import get_db_connection

# Get arguments from PHP
if len(sys.argv) < 3:
    print("Usage: python extract_epub.py <epub_path> <novel_id>")
    sys.exit(1)

epub_path = sys.argv[1]
novel_id = int(sys.argv[2])

book = epub.read_epub(epub_path)
chapters = []

for item in book.get_items():
    if item.get_type() == ebooklib.ITEM_DOCUMENT:
        soup = BeautifulSoup(item.get_body_content(), "html.parser")
        title = soup.find(["h1", "h2"]).get_text(strip=True) if soup.find(["h1", "h2"]) else "Unknown Chapter"

        for tag in soup.find_all(["b", "strong"]):
            tag.replace_with(f"<b>{tag.get_text(strip=True)}</b>")
        for tag in soup.find_all(["i", "em"]):
            tag.replace_with(f"<i>{tag.get_text(strip=True)}</i>")

        chapter_html = str(soup)
        chapter_html = re.sub(r"\b\d{1,4}\b", "", chapter_html)
        chapter_lines = chapter_html.split("\n")
        if chapter_lines and BeautifulSoup(chapter_lines[0], "html.parser").get_text(strip=True) == title:
            chapter_html = "\n".join(chapter_lines[1:])
        paragraphs = [f"<p>{p.strip()}</p>" for p in chapter_html.split("\n") if p.strip()]
        formatted_content = "\n".join(paragraphs)
        chapter_number = len(chapters) + 1
        chapters.append((novel_id, chapter_number, title, formatted_content))

conn = get_db_connection()
if conn:
    cursor = conn.cursor()
    query = """
    INSERT INTO Chapters (novel_id, chapter_number, title, content)
    VALUES (%s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content);
    """
    cursor.executemany(query, chapters)
    conn.commit()
    cursor.close()
    conn.close()
    print(f"Uploaded {len(chapters)} chapters from EPUB!")
else:
    print("Database connection failed.")



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