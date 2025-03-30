import sys
import os

# Add the parent directory (Variant) to Python's module search path
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from db.dbconnect import get_db_connection  # Use centralized DB connection

import fitz  # PyMuPDF for PDF extraction
import re

# Function to extract text while minimizing excessive <p> tags
def extract_text_with_formatting(pdf_path):
    doc = fitz.open(pdf_path)
    paragraphs = []
    current_paragraph = []

    for page in doc:
        blocks = page.get_text("dict")["blocks"]  # Extract text blocks
        for block in blocks:
            if "lines" in block:
                for line in block["lines"]:
                    line_text = []
                    for span in line["spans"]:
                        text = span["text"].strip()
                        if text:
                            if span["flags"] & 2:  # Check if bold
                                text = f"<b>{text}</b>"
                            line_text.append(text)

                    if line_text:
                        current_paragraph.append(" ".join(line_text))  # Merge line texts

                # Add paragraph when there's a significant gap
                if current_paragraph:
                    paragraphs.append(f"<p>{' '.join(current_paragraph).strip()}</p>")
                    current_paragraph = []

    # Merge consecutive paragraphs and remove excessive <p> tags
    formatted_text = "\n".join(paragraphs).strip()
    formatted_text = re.sub(r'(<p>\s*)+', '<p>', formatted_text)  # Merge multiple opening <p>
    formatted_text = re.sub(r'(\s*</p>)+', '</p>', formatted_text)  # Merge multiple closing </p>
    formatted_text = re.sub(r'<p>\s*<\/p>', '', formatted_text)  # Remove empty <p> tags.
    formatted_text = formatted_text.replace("\n", "</p><p>")  # Wrap paragraphs properly
    formatted_text = f"<p>{formatted_text}</p>"

    return formatted_text

# Load PDF and extract formatted text
pdf_path = r"D:\Xammp\htdocs\Variant\text_extractor\Manacled.pdf"
formatted_text = extract_text_with_formatting(pdf_path)

# Split into chapters, filtering out 'Table of Contents'
raw_chapters = re.split(r"\b(?:CHAPTER|Chapter)\s+(\d+)\b", formatted_text)[1:]
chapters = []

for i in range(0, len(raw_chapters), 2):
    chapter_number = int(raw_chapters[i].strip())
    chapter_content = raw_chapters[i + 1].strip()

    if "Table of Contents" in chapter_content:
        continue  

    chapters.append((27, chapter_number, f"Chapter {chapter_number}", chapter_content)) # (novel_id, num, title, content)

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
        print(f"Uploaded {len(chapters)} chapters to MySQL with optimized text formatting!")
    except Exception as e:
        conn.rollback()
        print("Error:", e)
    finally:
        cursor.close()
        conn.close()
else:
    print("Database connection failed.")
