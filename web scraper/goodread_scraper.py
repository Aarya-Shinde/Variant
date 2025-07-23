# Version - 3.6

# Used goodreads url  of public lists to fetch data regardring title , author and image
#  then used that with google api to fetch data using those titles
# also fixed the commpressed image issue


import requests
from bs4 import BeautifulSoup
import csv
import time
import re
import pandas as pd

# Step 0: Clean Goodreads Image URL
def clean_goodreads_image_url(img_url):
    img_url = img_url.replace('i.gr-assets.com', 'images-na.ssl-images-amazon.com')
    img_url = re.sub(r'\._[A-Z]+\d+_', '', img_url)  # Remove compressed size part
    return img_url

# Step 1: Fetch basic info from Goodreads
def fetch_goodreads_books(url):
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
    }
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')

    books = []
    book_rows = soup.find_all('tr', {'itemtype': 'http://schema.org/Book'})

    for row in book_rows:
        try:
            title = row.find('a', class_='bookTitle').text.strip()
        except AttributeError:
            title = "Unknown Title"

        try:
            author = row.find('a', class_='authorName').text.strip()
        except AttributeError:
            author = "Unknown Author"

        try:
            img_url = row.find('img')['src']
            img_url = clean_goodreads_image_url(img_url)
        except (AttributeError, KeyError):
            img_url = "No Image URL"
        
        # Fetch series name and position if found
        series_name = None
        series_position = None
        series_tag = row.find('span', class_='series')
        if series_tag:
            series_info = series_tag.text.strip().split(' #')
            if len(series_info) == 2:
                series_name = series_info[0].strip()
                series_position = series_info[1].strip()

        books.append({
            'title': title,
            'author': author,
            'img_url': img_url,
            'series_name': series_name,
            'series_position': series_position
        })

    return books

# Step 2: Fetch extra info from Google Books API
def fetch_google_books_info(title, author):
    query = f"{title} {author}"
    params = {
        'q': query,
        'key': 'AIzaSyC4RvCFjbie8Tx4Mg6H4PNUJqQQ3zWUAvo',  # <-- Insert your Google Books API Key here
        'maxResults': 1
    }
    response = requests.get("https://www.googleapis.com/books/v1/volumes", params=params)

    if response.status_code == 200:
        data = response.json()
        if 'items' in data:
            volume_info = data['items'][0]['volumeInfo']

            # Fetch ISBN
            isbn_10 = ''
            isbn_13 = ''
            if 'industryIdentifiers' in volume_info:
                for identifier in volume_info['industryIdentifiers']:
                    if identifier['type'] == 'ISBN_10':
                        isbn_10 = identifier['identifier']
                    if identifier['type'] == 'ISBN_13':
                        isbn_13 = identifier['identifier']

            return {
                'description': volume_info.get('description', 'No description available'),
                'publishedDate': volume_info.get('publishedDate', 'Unknown'),
                'averageRating': volume_info.get('averageRating', 0),
                'pageCount': volume_info.get('pageCount', 0),
                'categories': ', '.join(volume_info.get('categories', [])),
                'publisher': volume_info.get('publisher', 'Unknown'),
                'language': volume_info.get('language', 'Unknown'),
                'previewLink': volume_info.get('previewLink', ''),
                'infoLink': volume_info.get('infoLink', ''),
                'isbn_10': isbn_10,
                'isbn_13': isbn_13
            }
    return {
        'description': 'No description available',
        'publishedDate': 'Unknown',
        'averageRating': 0,
        'pageCount': 0,
        'categories': '',
        'publisher': 'Unknown',
        'language': 'Unknown',
        'previewLink': '',
        'infoLink': '',
        'isbn_10': '',
        'isbn_13': ''
    }

# Step 3: Save to CSV and Excel (xlsx)
def save_to_csv(books, filename):
    keys = books[0].keys()
    with open(filename, 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=keys)
        writer.writeheader()
        writer.writerows(books)

def save_to_excel(books, filename):
    df = pd.DataFrame(books)
    df.to_excel(filename, index=False)

# ========== Main Script ==========

# A goodreads_url to my favourite lists
goodreads_url = "https://www.goodreads.com/list/show/50.The_Best_Epic_Fantasy_fiction_"
books = fetch_goodreads_books(goodreads_url)

final_books = []
for book in books:
    print(f"Fetching extra info for: {book['title']}...")
    extra_info = fetch_google_books_info(book['title'], book['author'])
    combined = {**book, **extra_info}
    final_books.append(combined)
    time.sleep(1)  # Be a good API citizen

# Save all combined data in both CSV and Excel formats
save_to_csv(final_books, 'books_output.csv')
save_to_excel(final_books, 'books_output.xlsx')

print(" Done! All books saved to 'books_output.csv' and 'books_output.xlsx'. ")
