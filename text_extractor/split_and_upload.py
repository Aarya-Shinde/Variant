import re
import mysql.connector

# Load book text file
with open(r"D:\Xammp\htdocs\Variant\text_extractor\hunger-games.txt", "r", encoding="utf-8") as f:
    book_text = f.read()

# Ignore Table of Contents
book_text = re.sub(r"\b(?:TABLE OF CONTENTS|Contents)\b.*?(?=\b(?:CHAPTER|Chapter)\s+\d+\b)", "", book_text, flags=re.DOTALL)

# Split text using "Chapter X" or "CHAPTER X" pattern
raw_chapters = re.split(r"\b(?:CHAPTER|Chapter)\s+(\d+)\b", book_text)[1:]  # Skip first empty match

# Process chapters into a structured list
chapters = []
for i in range(0, len(raw_chapters), 2):  
    chapter_number = int(raw_chapters[i].strip())  # Extract chapter number
    chapter_content = raw_chapters[i + 1].strip()  # Extract chapter text
    formatted_content = "<p>" + chapter_content.replace("\n\n", "</p>\n<p>") + "</p>"  # Preserve paragraphs
    chapters.append((14, chapter_number, f"Chapter {chapter_number}", formatted_content))  # (novel_id, num, title, content)

# Connect to MySQL
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="@pokemon1",
    database="Variant"
)
cursor = conn.cursor()

# Insert chapters into MySQL
query = """
INSERT INTO Chapters (novel_id, chapter_number, title, content)
VALUES (%s, %s, %s, %s)
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content);
"""

cursor.executemany(query, chapters)

conn.commit()
cursor.close()
conn.close()

print(f"Uploaded {len(chapters)} chapters to MySQL with proper formatting!")
