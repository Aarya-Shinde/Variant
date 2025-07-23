import mysql.connector
import pandas as pd

# Connect to MySQL Database
def connect_db():
    return mysql.connector.connect(
        host='localhost',
        user='root',
        password='@pokemon1',
        database='Variant'     
    )

# Insert data into the fanfic table
def insert_fanfics(fanfics):
    conn = connect_db()
    cursor = conn.cursor()

    # Query to insert data into the fanfic table
    insert_query = """
    INSERT IGNORE INTO fanfic (title, url, author, language, word_count, chapter_count, summary, tags)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """



    for fanfic in fanfics:
        cursor.execute(insert_query, (
            fanfic['title'],
            fanfic['url'],
            fanfic['author'],
            fanfic['language'],
            fanfic['words'],
            fanfic['chapters'],
            fanfic['summary'],
            fanfic['tags']
        ))

    conn.commit()
    cursor.close()
    conn.close()

# Read data from the CSV or Excel file
def read_fanfic_data(file_path):
    if file_path.endswith(".csv"):
        return pd.read_csv(file_path)
    elif file_path.endswith(".xlsx"):
        return pd.read_excel(file_path)

# Main function
def main():
    file_path = 'ao3_fics.csv'  # Or 'ao3_fics.xlsx'
    fanfics = read_fanfic_data(file_path).to_dict(orient="records")
    insert_fanfics(fanfics)

if __name__ == "__main__":
    main()
