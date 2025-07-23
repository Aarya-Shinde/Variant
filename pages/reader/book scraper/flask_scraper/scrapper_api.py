# Published ➜ scraped with the Goodreads routine ➜ saved in Novels ➜ stored in Library via novel_id.

# Fan-fic ➜ scraped with the AO3 routine ➜ saved in Fanfic ➜ stored in Library via fanfic_id.

# ──────── IMPORTS ─────────
import re, json, time, requests, mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS
from bs4 import BeautifulSoup
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry

from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options  import Options
from webdriver_manager.chrome           import ChromeDriverManager
from bs4 import BeautifulSoup
import requests, time

# ──────── CONFIG ─────────
GOOGLE_KEY = "AIzaSyC4RvCFjbie8Tx4Mg6H4PNUJqQQ3zWUAvo"  # Optional
GB_URL     = "https://www.googleapis.com/books/v1/volumes"
DB         = dict(host='localhost', user='root', password='@pokemon1', database='Variant')

app = Flask(__name__)
CORS(app)

# ──────── HELPERS ─────────
def db(): return mysql.connector.connect(**DB)
def one(cursor): r = cursor.fetchone(); return r[0] if r else None

def detect(url):
    if "archiveofourown.org" in url: return "ao3"
    if "goodreads.com" in url: return "goodreads"
    return None

def google_enrich(title, author):
    if not GOOGLE_KEY:
        return {}
    params = dict(q=f"{title} {author}", maxResults=1, key=GOOGLE_KEY)
    r = requests.get(GB_URL, params=params, timeout=10)
    if r.status_code != 200:
        return {}
    items = r.json().get("items")
    if not items:
        return {}
    info = items[0]["volumeInfo"]
    ids = {i["type"]: i["identifier"] for i in info.get("industryIdentifiers", [])}
    return dict(
        isbn           = ids.get("ISBN_13") or ids.get("ISBN_10") or "",
        description    = info.get("description"),
        publication_dt = info.get("publishedDate"),
        page_count     = info.get("pageCount"),
        categories     = ", ".join(info.get("categories", [])),
        language       = info.get("language"),
        rating         = info.get("averageRating", 0)
    )

def _headless_html(url):
    """Return rendered HTML via headless Chrome (timeouts: 30 s)."""
    opts = Options()
    opts.add_argument("--headless=new")
    opts.add_argument("--disable-gpu")
    opts.add_argument("--no-sandbox")
    opts.add_argument("--window-size=1920,1080")
    opts.add_argument(
        "user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/124.0 Safari/537.36"
    )
    drv = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=opts)
    drv.get("https://archiveofourown.org")
    drv.add_cookie({"name": "view_adult", "value": "true", "domain": "archiveofourown.org"})
    drv.get(url)
    time.sleep(2)                       # tiny wait for JS / cloudflare
    html = drv.page_source
    drv.quit()
    return html


# ──────── SCRAPERS ─────────
def scrape_ao3(url):
    """First try requests; if AO3 returns 5xx / 4xx, fall back to Selenium."""
    UA = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0 Safari/537.36"}

    try:
        resp = requests.get(url, headers=UA, timeout=45)
        resp.raise_for_status()
        html = resp.text
    except Exception as first_err:
        try:
            html = _headless_html(url)
        except Exception as headless_err:
            raise RuntimeError(f"AO3 fetch failed: {first_err} | Selenium failed: {headless_err}")

    soup = BeautifulSoup(html, "html.parser")
    txt = lambda css: soup.select_one(css).get_text(strip=True) if soup.select_one(css) else ""

    title = txt("h2.title")
    if not title:
        raise RuntimeError("AO3 parse error: title not found")

    author  = txt("a[rel='author']") or "Anonymous"
    def first_txt(*selectors):
        for css in selectors:
            tag = soup.select_one(css)
            if tag and tag.get_text(strip=True):
                return tag.get_text(strip=True)
        return ""

    summary = first_txt(
        "blockquote.userstuff.summary",          # normal works page
        "div.summary blockquote.userstuff",      # chapter page variant
        "div#workskin blockquote.userstuff"      # legacy layout
    )

    language= txt("dd.language") or "English"

    try:    words = int(txt("dd.words").replace(",", "")) if txt("dd.words") else 0
    except: words = 0
    try:    chaps = int(txt("dd.chapters").split("/")[0]) if txt("dd.chapters") else 1
    except: chaps = 1

    tags = ", ".join(t.get_text(strip=True) for t in soup.select("a.tag"))

    return dict(
        title=title, author=author, summary=summary, language=language,
        word_count=words, chapter_count=chaps, tags=tags, url=url
    )

def clean_img(src):
    src = src.replace("i.gr-assets.com", "images-na.ssl-images-amazon.com")
    return re.sub(r"\._[A-Z]+\d+_", "", src)

def scrape_goodreads(url):
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0 Safari/537.36",
        "Accept-Language": "en-US,en;q=0.9"
    }
    html = requests.get(url, headers=headers, timeout=20).text
    soup = BeautifulSoup(html, "html.parser")

    if "/list/" in url:
        first = soup.select_one('tr[itemtype="http://schema.org/Book"] a.bookTitle')
        if not first:
            raise ValueError("No book rows found on this list.")
        return scrape_goodreads("https://www.goodreads.com" + first["href"])

    sel = lambda *css: next((soup.select_one(c) for c in css if soup.select_one(c)), None)

    title_tag  = sel('h1#bookTitle', 'h1[data-testid="bookTitle"]')
    author_tag = sel('a.authorName span', 'span.ContributorLink__name')

    # --------- COVER LOGIC START ---------
    cover = ""
    cover_tag = sel('#coverImage', 'img[data-testid="bookCover"]')
    if cover_tag:
        raw = cover_tag.get("src") or ""
        if raw.startswith("https://s.gr-assets.com/assets/nocover"):
            raw = cover_tag.get("data-original", "")
        cover = clean_img(raw)

    # Fallback to meta tag if needed
    if not cover:
        og_image = soup.find("meta", property="og:image")
        if og_image and og_image.get("content"):
            cover = clean_img(og_image["content"])
    # --------- COVER LOGIC END ---------

    title  = title_tag.get_text(strip=True) if title_tag else ""
    author = author_tag.get_text(strip=True) if author_tag else ""

    desc_tag = sel('#description span:nth-of-type(2)', '#description span[style]')
    description = desc_tag.get_text(strip=True) if desc_tag else ""

    genre_tags = soup.select("a.bookPageGenreLink") or soup.select('a[data-testid="genreLink"]')
    genres = ", ".join(t.get_text(strip=True) for t in genre_tags[:3])

    rating_tag = sel("span[itemprop='ratingValue']", 'div[data-testid="ratingStar"]')
    rating = float(rating_tag.get_text(strip=True) or 0) if rating_tag else 0

    pub_row = sel("#details .row:nth-of-type(2)", "p[data-testid='publicationInfo']")
    year = re.search(r"(\d{4})", pub_row.get_text()).group(1) if pub_row and re.search(r"(\d{4})", pub_row.get_text()) else None

    series_tag = sel("#bookSeries a")
    series_name = series_pos = None
    if series_tag:
        series_text = series_tag.get_text(strip=True)
        series_name, _, num = series_text.partition(" #")
        try: series_pos = int(num)
        except: pass

    book = dict(
        title=title, author=author, cover_image_url=cover, description=description,
        genre=genres, rating=rating, publication_date=f"{year}-01-01" if year else None,
        series_name=series_name, series_position=series_pos, language="English", url=url
    )
    
    book.update({k: v for k, v in google_enrich(title, author).items() if v})
    return book


# ──────── DB INSERTS ─────────
def ensure_fanfic(cursor, b):
    cursor.execute("""SELECT fanfic_id FROM Fanfic WHERE title=%s AND author=%s AND language=%s""",
                   (b['title'], b['author'], b['language']))
    fid = one(cursor)
    if fid: return fid
    cursor.execute("""INSERT INTO Fanfic (title,url,author,language,word_count,chapter_count,summary,tags)
                      VALUES (%s,%s,%s,%s,%s,%s,%s,%s)""",
                   (b['title'], b['url'], b['author'], b['language'], b.get('word_count',0),
                    b.get('chapter_count',0), b['summary'], b.get('tags',"")))
    return cursor.lastrowid

def ensure_novel(cursor, b):
    cursor.execute("SELECT novel_id FROM Novels WHERE title=%s AND author_name=%s",
                   (b['title'], b['author']))
    nid = one(cursor)
    if nid: return nid
    cursor.execute("""INSERT INTO Novels
        (title,author_name,publication_date,genre,cover_image_url,series_name,
         series_position,description,isbn,rating,language)
         VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)""",
        (b['title'], b['author'], b.get('publication_date'), b.get('genre'),
         b.get('cover_image_url'), b.get('series_name'), b.get('series_position'),
         b.get('description'), b.get('isbn'), b.get('rating'), b.get('language')))
    return cursor.lastrowid

def already_in_library(cursor, user, novel_id=None, fanfic_id=None):
    cursor.execute("SELECT id FROM Library WHERE user_id=%s AND novel_id<=>%s AND fanfic_id<=>%s",
                   (user, novel_id, fanfic_id))
    return cursor.fetchone()

def add_to_library(cursor, user, source_url, novel_id=None, fanfic_id=None):
    if novel_id and fanfic_id:
        raise ValueError("Cannot set both novel_id and fanfic_id")
    if novel_id:
        cursor.execute(
            "INSERT INTO Library (user_id, novel_id, source_url) VALUES (%s,%s,%s)",
            (user, novel_id, source_url)
        )
    elif fanfic_id:
        cursor.execute(
            "INSERT INTO Library (user_id, fanfic_id, source_url) VALUES (%s,%s,%s)",
            (user, fanfic_id, source_url)
        )
    else:
        raise ValueError("Either novel_id or fanfic_id must be provided")



# ──────── ROUTE ─────────
@app.route('/scrape', methods=['POST'])
def scrape_route():
    data = request.get_json(force=True)
    url, user, ctype = data.get('url'), data.get('user_id'), data.get('content_type')

    if not url or not user or ctype not in ('published', 'fanfic'):
        return jsonify(error="url, user_id, content_type required"), 400

    source = detect(url)
    if ctype == 'fanfic' and source != 'ao3': return jsonify(error="Fanfic must be AO3 link"), 400
    if ctype == 'published' and source != 'goodreads': return jsonify(error="Published must be Goodreads link"), 400

    scraper = scrape_ao3 if source == 'ao3' else scrape_goodreads
    time.sleep(1)
    book = scraper(url)

    if not book.get("title") or not book.get("author"):
        return jsonify(error="Failed to read title/author from page"), 422

    with db() as conn:
        cur = conn.cursor()
        if ctype == 'fanfic':
            fid = ensure_fanfic(cur, book)
            if already_in_library(cur, user, fanfic_id=fid):
                return jsonify(message="Already in your library"), 200
            add_to_library(cur, user, fanfic_id=fid, source_url=url)

        else:
            nid = ensure_novel(cur, book)
            if already_in_library(cur, user, novel_id=nid):
                return jsonify(message="Already in your library"), 200
            add_to_library(cur, user, novel_id=nid, source_url=url)

        conn.commit()

    return jsonify(message="Book added to your library!", book=book)

# ──────── MAIN ─────────
if __name__ == '__main__':
    app.run(debug=True)
