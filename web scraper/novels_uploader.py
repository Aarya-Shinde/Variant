import pandas as pd
import mysql.connector
from mysql.connector import errorcode
import re

# === Helpers ===

def normalize_date(val):
    """Convert 'YYYY', 'YYYY-MM', or 'YYYY-MM-DD' into a valid DATE string."""
    if pd.isna(val):
        return None
    s = str(val).strip()
    if re.fullmatch(r'\d{4}-\d{2}-\d{2}', s):
        return s
    if re.fullmatch(r'\d{4}-\d{2}', s):
        return s + '-01'
    if re.fullmatch(r'\d{4}', s):
        return s + '-01-01'
    # If the string is something else, try to parse or skip
    return None

def clean(val):
    """Convert NaN or empty string to None for MySQL insertion."""
    if pd.isna(val) or (isinstance(val, str) and not val.strip()):
        return None
    return val

# === Read & Prepare Data ===

df = pd.read_excel('books_output.xlsx', engine='openpyxl')

# Pick ISBN_13 if available, else ISBN_10
df['isbn'] = df['isbn_13'].where(df['isbn_13'] != '', df['isbn_10'])

# Rename to match your table
df = df.rename(columns={
    'title': 'title',
    'author': 'author_name',
    'publishedDate': 'publication_date',
    'categories': 'genre',
    'img_url': 'cover_image_url',
    'pageCount': 'total_chapters',
    'series_name': 'series_name',
    'series_position': 'series_position',
    'description': 'description',
    'isbn': 'isbn',
    'averageRating': 'rating',
    'language': 'language'
})

# Normalize publication_date
df['publication_date'] = df['publication_date'].apply(normalize_date)

# Ensure numeric fields are proper types
df['total_chapters'] = df['total_chapters'].fillna(0).astype(int)
df['rating'] = df['rating'].fillna(0).astype(float)

# === Upload to MySQL ===

try:
    conn = mysql.connector.connect(
        host='localhost',
        user='root',
        password='@pokemon1',
        database='Variant'
    )
    cursor = conn.cursor()
    print(" Connected to MySQL")

    insert_sql = """
    INSERT INTO Novels
      (title, author_id, author_name, publication_date, genre,
       cover_image_url, total_chapters, series_name, series_position,
       description, isbn, rating, language)
    VALUES
      (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE
      publication_date = VALUES(publication_date),
      genre = VALUES(genre),
      cover_image_url = VALUES(cover_image_url),
      total_chapters = VALUES(total_chapters),
      series_name = VALUES(series_name),
      series_position = VALUES(series_position),
      description = VALUES(description),
      isbn = VALUES(isbn),
      rating = VALUES(rating),
      language = VALUES(language)
    """


    for _, row in df.iterrows():
        params = (
            clean(row['title']),
            1,  # default author_id
            clean(row['author_name']),
            clean(row['publication_date']),
            clean(row['genre']),
            clean(row['cover_image_url']),
            clean(row['total_chapters']),
            clean(row['series_name']),
            clean(row['series_position']),
            clean(row['description']),
            clean(row['isbn']),
            clean(row['rating']),
            clean(row['language'])
        )
        cursor.execute(insert_sql, params)

    conn.commit()
    print(f"🎉 Inserted {cursor.rowcount} rows into `Novels`!")
    cursor.close()
    conn.close()

except mysql.connector.Error as err:
    if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
        print("❌ Something is wrong with your MySQL credentials")
    elif err.errno == errorcode.ER_BAD_DB_ERROR:
        print("❌ Database does not exist")
    else:
        print("❌", err)
