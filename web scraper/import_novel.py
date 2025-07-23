
import requests
from bs4 import BeautifulSoup
from urllib.parse import urlparse
import re

def clean_goodreads_image_url(img_url):
    img_url = img_url.replace('i.gr-assets.com', 'images-na.ssl-images-amazon.com')
    img_url = re.sub(r'\._[A-Z]+\d+_', '', img_url)  # Remove compressed size part
    return img_url

def scrape_book_info(book_url):
    headers = {"User-Agent": "Mozilla/5.0"}  # Pretend you're a browser
    response = requests.get(book_url, headers=headers)
    soup = BeautifulSoup(response.text, "html.parser")

    domain = urlparse(book_url).netloc.lower()
    
    if "goodreads.com" in domain:
        return scrape_goodreads(soup)
    elif "archiveofourown.org" in domain:
        return scrape_ao3(soup)
    elif "wattpad.com" in domain:
        return scrape_wattpad(soup)
    else:
        return {"title": "Unknown", "author": "Unknown", "summary": "Unknown", "rating": "Unknown", "genres": [], "image_url": "Unknown"}

def scrape_goodreads(soup):
    title_tag = soup.select_one('h1#bookTitle') or soup.select_one('h1[data-testid="bookTitle"]')
    title = title_tag.get_text(strip=True) if title_tag else 'No Title'

    author_tag = soup.select_one('span[itemprop="name"]') or soup.select_one('span.ContributorLink__name')
    author = author_tag.get_text(strip=True) if author_tag else 'No Author'

    description_tag = soup.select_one('#description span[style="display:none"]') or soup.select_one('div[data-testid="description"] span')
    if not description_tag:
        description_tag = soup.select_one('#description span')  
    summary = description_tag.get_text(strip=True) if description_tag else 'No Summary'

    rating_tag = soup.select_one('span[itemprop="ratingValue"]') or soup.select_one('div.RatingStatistics__rating')
    rating = rating_tag.get_text(strip=True) if rating_tag else 'No Rating'

    genre_tags = soup.select('a.bookPageGenreLink') or soup.select('span.BookPageMetadataSection__genreButton')
    genres = [genre.get_text(strip=True) for genre in genre_tags]

    image_tag = (
        soup.select_one('img#coverImage') or 
        soup.select_one('img[data-testid="bookCoverImage"]') or 
        soup.find('img', {'alt': re.compile(r'Cover of.*', re.I)})
    )

    if image_tag and image_tag.has_attr('src'):
        image_url = clean_goodreads_image_url(image_tag['src'])
    else:
        image_url = 'No Image'

    return {
        'title': title,
        'author': author,
        'summary': summary,
        'rating': rating,
        'genres': genres,
        'image_url': image_url
    }



def scrape_ao3(soup):
    title = soup.select_one('h2.title.heading').text.strip() if soup.select_one('h2.title.heading') else 'No Title'
    author = soup.select_one('a[rel="author"]').text.strip() if soup.select_one('a[rel="author"]') else 'No Author'
    summary = soup.select_one('blockquote.userstuff').text.strip() if soup.select_one('blockquote.userstuff') else 'No Summary'
    rating = "N/A"
    genre = [g.text.strip() for g in soup.select('dd.fandom.tags a.tag')]
    image_url = "No Image"  # AO3 doesn't usually have covers easily scrapable

    return {
        'title': title,
        'author': author,
        'summary': summary,
        'rating': rating,
        'genres': genre,
        'image_url': image_url
    }

def scrape_wattpad(soup):
    title = soup.select_one('h1.story-info__title').text.strip() if soup.select_one('h1.story-info__title') else 'No Title'
    author = soup.select_one('a.author-info__username').text.strip() if soup.select_one('a.author-info__username') else 'No Author'
    summary = soup.select_one('p.description').text.strip() if soup.select_one('p.description') else 'No Summary'
    rating = "N/A"
    genre = [g.text.strip() for g in soup.select('a.tag')]
    image_tag = soup.select_one('img.cover')
    image_url = image_tag['src'] if image_tag else 'No Image'

    return {
        'title': title,
        'author': author,
        'summary': summary,
        'rating': rating,
        'genres': genre,
        'image_url': image_url
    }

# Main
if __name__ == "__main__":
    book_url = input("Enter the book URL: ").strip()
    book_details = scrape_book_info(book_url)
    print(book_details)
