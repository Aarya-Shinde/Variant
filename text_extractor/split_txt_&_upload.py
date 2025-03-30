import sys
import os

# Add the parent directory (Variant) to Python's module search path
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from db.dbconnect import get_db_connection  # Use centralized DB connection

import re

# Load book text file
txt_path = r"D:\Xammp\htdocs\Variant\text_extractor\hunger-games.txt"
with open(txt_path, "r", encoding="utf-8") as f:
    book_text = f.read()

# Ignore Table of Contents
book_text = re.sub(r"\b(?:TABLE OF CONTENTS|Contents)\b.*?(?=\b(?:CHAPTER|Chapter)\s+\d+\b)", "", book_text, flags=re.DOTALL)

# Split text using "Chapter X" or "CHAPTER X" pattern
raw_chapters = re.split(r"\b(?:CHAPTER|Chapter)\s+(\d+)\b", book_text)[1:]  # Skip first empty match

# Process chapters into a structured list
chapters = []
novel_id = 14  # Update with the correct novel ID

for i in range(0, len(raw_chapters), 2):  
    chapter_number = int(raw_chapters[i].strip())  # Extract chapter number
    chapter_content = raw_chapters[i + 1].strip()  # Extract chapter text

    # **Preserve paragraphs properly**
    formatted_content = "<p>" + chapter_content.replace("\n\n", "</p>\n<p>") + "</p>"

    # **Store structured chapter data**
    chapters.append((novel_id, chapter_number, f"Chapter {chapter_number}", formatted_content))

# **Connect to MySQL using dbconnect.py**
conn = get_db_connection()
if conn:
    cursor = conn.cursor()

    # **Insert chapters into MySQL**
    query = """
    INSERT INTO Chapters (novel_id, chapter_number, title, content)
    VALUES (%s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content);
    """

    try:
        cursor.executemany(query, chapters)  # Efficient batch insert
        conn.commit()
        print(f"Uploaded {len(chapters)} chapters to MySQL with proper formatting!")
    except Exception as e:
        conn.rollback()
        print("Error:", e)
    finally:
        cursor.close()
        conn.close()
else:
    print("Database connection failed.")
