# google book api key----   AIzaSyC4RvCFjbie8Tx4Mg6H4PNUJqQQ3zWUAvo

# Version 1.6 using gogle books query to fetch books that come up on the search

'''
Query Google Books with a specific search term (e.g., "fantasy novels")

Fetch 100+ results (we can paginate over multiple result sets)

Extract real descriptions, genres, ratings, and other metadata!
'''

import requests
import csv
import datetime
import pandas as pd

# Function to search Google Books API with dynamic query
def search_googlebooks_by_query(query, start_index=0):
    url = f"https://www.googleapis.com/books/v1/volumes?q={query}&startIndex={start_index}&maxResults=40"
    response = requests.get(url)
    if response.status_code == 200:
        data = response.json()
        if 'items' in data:
            return data['items']
    return None

# Search term for dynamic fetching (can customize)
search_query = "fantasy novels"

# Placeholder for novel list
novel_list = []
total_books = 0
max_books = 100  # Fetch up to 100 books

# Loop through pages of Google Books search
start_index = 0
while total_books < max_books:
    try:
        books = search_googlebooks_by_query(search_query, start_index)
        
        if not books:
            print(" No more books found.")
            break
        
        for book in books:
            volume_info = book['volumeInfo']
            
            # Collect required data from each book
            novel = {
                "title": volume_info.get('title', 'Unknown Title'),
                "author_id": 1,  # Default author ID
                "author_name": ", ".join(volume_info.get('authors', ['Unknown Author'])),
                "publication_date": volume_info.get('publishedDate', '2000-01-01'),
                "genre": ", ".join(volume_info.get('categories', ['Fiction'])),
                "cover_image_url": volume_info.get('imageLinks', {}).get('thumbnail', ''),
                "total_chapters": round(volume_info.get('pageCount', 300)/10),  # Approximate total chapters (pages/10)
                "series_name": volume_info.get('subtitle', 'Standalone') if 'subtitle' in volume_info else 'Standalone',
                "series_position": 1,  # Default series position
                "description": volume_info.get('description', 'No description available.'),
                "isbn": volume_info.get('industryIdentifiers', [{}])[0].get('identifier', '0000000000'),
                "rating": volume_info.get('averageRating', 4.0),
                "language": volume_info.get('language', 'en')
            }
            
            novel_list.append(novel)
            total_books += 1
            print(f" {novel['title']} fetched successfully!")
        
        # Increment start_index for pagination
        start_index += 40  # Google allows up to 40 books per page
        
        if total_books >= max_books:
            print("Reached the target of 100 books.")
            break

    except Exception as e:
        print(f" Error while fetching books: {e}")
        break

# Save to CSV
with open('novels_data.csv', 'w', newline='', encoding='utf-8') as csvfile:
    fieldnames = novel_list[0].keys()
    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
    writer.writeheader()
    writer.writerows(novel_list)

# Save to Excel
df = pd.read_csv('novels_data.csv')
df.to_excel('novels_data.xlsx', index=False)

print("\n🎯 FINAL REPORT:")
print(f" Total novels fetched: {len(novel_list)}")
print("Saved to novels_data.csv and novels_data.xlsx!")
