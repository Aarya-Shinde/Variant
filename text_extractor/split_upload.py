import sys
print("Received arguments:", sys.argv)

import os
import re
import fitz  # PyMuPDF for PDF processing
import ebooklib
from ebooklib import epub
from bs4 import BeautifulSoup
from db.dbconnect import get_db_connection  # Import database connection

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
print("Updated sys.path:", sys.path)  # Debugging print

def extract_epub(epub_path, novel_id):
    """ Extracts chapters from an EPUB file and returns a list of tuples (novel_id, chapter_number, title, content) """
    book = epub.read_epub(epub_path)
    chapters = []
    
    for idx, item in enumerate(book.get_items(), start=1):
        if item.get_type() == ebooklib.ITEM_DOCUMENT:
            soup = BeautifulSoup(item.get_body_content(), "html.parser")
            
            title = soup.find(["h1", "h2"])
            title = title.get_text(strip=True) if title else f"Chapter {idx}"

            for tag in soup.find_all(["b", "strong"]):
                tag.replace_with(f"<b>{tag.get_text(strip=True)}</b>")
            for tag in soup.find_all(["i", "em"]):
                tag.replace_with(f"<i>{tag.get_text(strip=True)}</i>")

            formatted_content = "<p>" + soup.get_text("\n").replace("\n\n", "</p>\n<p>") + "</p>"
            chapters.append((novel_id, idx, title, formatted_content))

    return chapters

def extract_pdf(pdf_path, novel_id):
    """ Extracts chapters from a PDF file and returns a list of tuples (novel_id, chapter_number, title, content) """
    doc = fitz.open(pdf_path)
    formatted_text = ""

    for page in doc:
        formatted_text += page.get_text("text") + "\n"

    raw_chapters = re.split(r"\b(?:CHAPTER|Chapter)\s+(\d+)\b", formatted_text)[1:]
    chapters = []

    for i in range(0, len(raw_chapters), 2):
        chapter_number = int(raw_chapters[i].strip())
        chapter_content = raw_chapters[i + 1].strip()
        if "Table of Contents" in chapter_content:
            continue  

        formatted_content = "<p>" + chapter_content.replace("\n\n", "</p>\n<p>") + "</p>"
        chapters.append((novel_id, chapter_number, f"Chapter {chapter_number}", formatted_content))
        print("Extracted raw chapters:", raw_chapters)


    return chapters

def extract_txt(txt_path, novel_id):
    """ Extracts chapters from a TXT file and returns a list of tuples (novel_id, chapter_number, title, content) """
    with open(txt_path, "r", encoding="utf-8") as f:
        book_text = f.read()

    book_text = re.sub(r"\b(?:TABLE OF CONTENTS|Contents)\b.*?(?=\b(?:CHAPTER|Chapter)\s+\d+\b)", "", book_text, flags=re.DOTALL)

    raw_chapters = re.split(r"\b(?:CHAPTER|Chapter)\s+(\d+)\b", book_text)[1:]
    chapters = []

    for i in range(0, len(raw_chapters), 2):
        chapter_number = int(raw_chapters[i].strip())
        chapter_content = raw_chapters[i + 1].strip()
        formatted_content = "<p>" + chapter_content.replace("\n\n", "</p>\n<p>") + "</p>"
        chapters.append((novel_id, chapter_number, f"Chapter {chapter_number}", formatted_content))

    return chapters

def process_uploaded_file(file_path, novel_id):
    """ Determines file type and extracts chapters """
    ext = os.path.splitext(file_path)[-1].lower()
    if ext == ".epub":
        return extract_epub(file_path, novel_id)
    elif ext == ".pdf":
        return extract_pdf(file_path, novel_id)
    elif ext == ".txt":
        return extract_txt(file_path, novel_id)
    else:
        print(f"Unsupported file format: {ext}")
        return []

def insert_into_db(chapters):
    """ Inserts extracted chapters into MySQL database """
    if not chapters:
        print("No chapters extracted.")
        return

    print("Extracted Chapters:", chapters)  # Debugging print
    conn = get_db_connection()
    if not conn:
        print("Database connection failed.")
        return
    
    cursor = conn.cursor()

    # Check if novel_id exists
    cursor.execute("SELECT 1 FROM Novels WHERE novel_id = %s LIMIT 1", (chapters[0][0],))
    if not cursor.fetchone():
        print(f"Novel ID {chapters[0][0]} does not exist in Novels table.")
        conn.close()
        return

    query = """
    INSERT INTO Chapters (novel_id, chapter_number, title, content)
    VALUES (%s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content);
    """
    
    try:
        cursor.executemany(query, chapters)
        conn.commit()
        print(f"Uploaded {len(chapters)} chapters to MySQL!")
    except Exception as e:
        conn.rollback()
        print("SQL Error:", e)  # Debugging print
    finally:
        cursor.close()
        conn.close()

# **Example: Call from Upload Handler**
def handle_upload(file_path, novel_id):
    chapters = process_uploaded_file(file_path, novel_id)
    insert_into_db(chapters)
