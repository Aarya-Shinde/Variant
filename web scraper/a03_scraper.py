#scraping ao3 data using 

# --- imports ---
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
from bs4 import BeautifulSoup
import pandas as pd
import time
from urllib.parse import urljoin

# --- create browser ---
def create_browser(headless=True):
    opts = Options()
    if headless:
        opts.add_argument("--headless=new")
        opts.add_argument("--disable-gpu")
    opts.add_argument("--window-size=1920,1080")
    opts.add_argument("--no-sandbox")
    opts.add_argument(
        "user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/112.0.0.0 Safari/537.36"
    )
    driver = webdriver.Chrome(
        service=Service(ChromeDriverManager().install()),
        options=opts
    )
    driver.get("https://archiveofourown.org")
    driver.add_cookie({"name": "view_adult", "value": "true", "domain": "archiveofourown.org"})
    return driver

# --- parse one fic block ---
def parse_fic_li(li):
    title = li.select_one("h4.heading a")
    lang  = li.select_one("dd.language")
    words = li.select_one("dd.words")
    chaps = li.select_one("dd.chapters")
    summary = li.select_one("blockquote.userstuff.summary")
    tags = [t.text.strip() for t in li.select("ul.tags li a.tag")]

    return {
        "title": title.text.strip() if title else "",
        "url": urljoin("https://archiveofourown.org", title["href"]) if title else "",
        "author": li.select_one("h4.heading a[rel='author']").text.strip() if li.select_one("h4.heading a[rel='author']") else "Anonymous",
        "language": lang.text.strip() if lang else "",
        "words": words.text.strip().replace(",", "") if words else "0",
        "chapters": (chaps.text.strip().split("/")[0] if chaps else "0"),
        "summary": summary.text.strip() if summary else "",
        "tags": ", ".join(tags),
    }

# --- scrape starting from a URL ---
def scrape_ao3_from_url(start_url, max_works=50, min_words=50000):
    driver = create_browser()
    collected = []
    url = start_url
    empty_pages = 0
    page_num = 1

    try:
        while len(collected) < max_works and empty_pages < 5:
            print(f"→ Scraping page {page_num}: {url}")
            driver.get(url)

            try:
                WebDriverWait(driver, 10).until(
                    EC.presence_of_element_located(
                        (By.CSS_SELECTOR, "li.work.blurb.group, ol.pagination")
                    )
                )
            except:
                print(f"   ⚠️ No works or pagination found on page {page_num}. Stopping.")
                break

            soup = BeautifulSoup(driver.page_source, "html.parser")
            items = soup.select("li.work.blurb.group")
            print(f"  * Found {len(items)} works")
            
            # rest of your loop

            if not items:
                empty_pages += 1
                print(f"    ⚠️ Empty page {empty_pages}/5")
            else:
                empty_pages = 0
                for li in items:
                    if len(collected) >= max_works:
                        break
                    data = parse_fic_li(li)
                    if data["language"].lower()=="english" and int(data["words"])>=min_words:
                        collected.append(data)
                        print(f"   ✓ {data['title']} | {data['words']}w | {data['chapters']}ch")

            # find “Next →” link in the pagination
            next_tag = soup.select_one("li.next > a")
            if next_tag:
                url = urljoin("https://archiveofourown.org", next_tag["href"])
                page_num += 1
                # tiny sleep so AO3 can breathe
                time.sleep(1)
            else:
                print("   ✨ No more pages. Stopping.")
                break

    finally:
        driver.quit()

    return collected

# --- main ---
if __name__ == "__main__":
    start = input("Paste AO3 starting URL: ").strip()
    mx    = int(input("Max works to collect? (default 50): ") or 50)
    mw    = int(input("Minimum word count? (default 50000): ") or 50000)

    results = scrape_ao3_from_url(start, max_works=mx, min_words=mw)
    if results:
        df = pd.DataFrame(results)
        df.to_csv("ao3_fics.csv", index=False)
        df.to_excel("ao3_fics.xlsx", index=False)
        print(f"\n✨ Saved {len(results)} works.")
    else:
        print("\n No works matched your filters.")
