import sqlite3
from duckduckgo_search import DDGS
import requests
import os
import time
import random
from slugify import slugify
from PIL import Image
from io import BytesIO
import dotenv
import google.generativeai as genai

# Load .env file
dotenv.load_dotenv()

# --- 1. CẤU HÌNH API ---
GEMINI_KEY = os.getenv('GEMINI_API_KEYS')
if GEMINI_KEY:
    genai.configure(api_key=GEMINI_KEY)
    model = genai.GenerativeModel('gemini-1.5-flash')

# --- 2. CẤU HÌNH DATABASE ---
DB_PATH = os.path.join('database', 'database.sqlite')

# --- 3. CẤU HÌNH ĐƯỜNG DẪN ---
PHYSICAL_STORAGE_ROOT = os.path.join('storage', 'app', 'public')
BRAND_FOLDER_NAME = 'brands'

def create_directory(path):
    if not os.path.exists(path):
        os.makedirs(path, exist_ok=True)

def get_db_connection():
    return sqlite3.connect(DB_PATH)

def process_logo(img, target_size=(512, 512)):
    """
    Hậu xử lý logo: Trim whitespace, Resize giữ tỷ lệ và đặt vào giữa khung hình vuông.
    """
    # 1. Chuyển về RGBA để đảm bảo xử lý được kênh Alpha
    if img.mode != 'RGBA':
        img = img.convert('RGBA')

    # 2. Trim whitespace (Xóa khoảng trắng/trong suốt thừa)
    bbox = img.getbbox()
    if bbox:
        img = img.crop(bbox)

    # 3. Resize giữ nguyên tỷ lệ (Aspect Ratio)
    img.thumbnail(target_size, Image.Resampling.LANCZOS)

    # 4. Tạo khung hình vuông mới với nền trong suốt
    new_img = Image.new("RGBA", target_size, (255, 255, 255, 0))
    
    # Tính toán vị trí để dán logo vào chính giữa
    upper = (target_size[0] - img.size[0]) // 2
    left = (target_size[1] - img.size[1]) // 2
    new_img.paste(img, (upper, left))
    
    return new_img

def download_image(url, save_folder, file_name):
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        }
        response = requests.get(url, headers=headers, timeout=15)

        if response.status_code == 200:
            image = Image.open(BytesIO(response.content))
            
            # KIỂM TRA ALPHA CHANNEL (Transparency)
            # Chỉ chấp nhận ảnh có kênh Alpha hoặc có bảng màu trong suốt
            if image.mode not in ('RGBA', 'LA') and not (image.mode == 'P' and 'transparency' in image.info):
                print(f"      - Bo qua: Anh khong co do trong suot (Alpha channel).")
                return False

            # Xử lý ảnh (Crop & Resize)
            processed_image = process_logo(image)
            
            full_path = os.path.join(save_folder, file_name)
            processed_image.save(full_path, "PNG")
            
            return True
    except Exception as e:
        print(f"      - Loi tai/xu ly anh: {e}")
        return False
    return False

def get_logo_url_from_ai(brand_name):
    """Sử dụng Gemini để tìm URL logo chính thức (nếu có thể)"""
    if not GEMINI_KEY:
        return None
    
    prompt = f"Find the official direct URL for a high-quality transparent PNG logo of the brand '{brand_name}'. Return ONLY the direct URL of the image. If you cannot find a direct link, return 'NONE'."
    try:
        response = model.generate_content(prompt)
        url = response.text.strip()
        if url.startswith('http') and ('.png' in url or '.jpg' in url or '.svg' in url):
            return url
    except:
        pass
    return None

def main():
    print("--- TOOL CRAWL LOGO THUONG HIEU (AI MODE) ---")

    brand_path = os.path.join(PHYSICAL_STORAGE_ROOT, BRAND_FOLDER_NAME)
    create_directory(brand_path)

    conn = get_db_connection()
    conn.row_factory = sqlite3.Row
    cursor = conn.cursor()

    # Lấy danh sách thương hiệu (Có thể buộc crawl lại bằng cách xóa điều kiện WHERE)
    print("... Dang lay danh sach thuong hieu ...")
    cursor.execute("SELECT id, name, slug FROM brands")
    brands = cursor.fetchall()

    print(f"-> Xu ly {len(brands)} thuong hieu.")

    ddgs = DDGS()

    for index, brand in enumerate(brands):
        b_id = brand['id']
        b_name = brand['name']
        b_slug = brand['slug'] if brand['slug'] else slugify(b_name)

        print(f"\n[{index+1}/{len(brands)}] Xu ly: {b_name}")

        # 1. THỬ LẤY URL TỪ AI TRƯỚC
        logo_url = get_logo_url_from_ai(b_name)
        file_name = f"{b_slug}.png"

        if logo_url:
            print(f"      - AI found URL: {logo_url[:60]}...")
            if download_image(logo_url, brand_path, file_name):
                db_relative_path = f"{BRAND_FOLDER_NAME}/{file_name}"
                cursor.execute("UPDATE brands SET logo = ? WHERE id = ?", (db_relative_path, b_id))
                conn.commit()
                print(f"      - OK: {file_name} (via AI)")
                continue

        # 2. THỬ LẤY TỪ HUNTER.IO LOGO API (Cực kỳ ổn định cho các brand công nghệ)
        domain_map = {
            'Apple': 'apple.com', 'Samsung': 'samsung.com', 'Xiaomi': 'xiaomi.com',
            'Oppo': 'oppo.com', 'Acer': 'acer.com', 'Asus': 'asus.com',
            'Lenovo': 'lenovo.com', 'MSI': 'msi.com', 'HP': 'hp.com',
            'Dell': 'dell.com', 'Microsoft': 'microsoft.com', 'Sony': 'sony.com',
            'Logitech': 'logitech.com', 'Razer': 'razer.com'
        }
        domain = domain_map.get(b_name, f"{b_slug}.com")
        hunter_url = f"https://logos.hunter.io/{domain}"
        
        print(f"      - Trying Hunter.io: {domain}")
        if download_image(hunter_url, brand_path, file_name):
            db_relative_path = f"{BRAND_FOLDER_NAME}/{file_name}"
            cursor.execute("UPDATE brands SET logo = ? WHERE id = ?", (db_relative_path, b_id))
            conn.commit()
            print(f"      - OK: {file_name} (via Hunter.io)")
            continue

        # 3. NẾU CÁC CÁCH TRÊN THẤT BẠI, DÙNG DUCKDUCKGO (Dễ bị Ratelimit)
        search_query = f"{b_name} official vector logo transparent png high resolution"
        print(f"      - Fallback Search: '{search_query}'")

        try:
            # Tăng thời gian chờ trước khi crawl DuckDuckGo
            time.sleep(random.uniform(3, 6))
            results = ddgs.images(search_query, region="wt-wt", safesearch="off", max_results=5)

            success = False
            for img_data in results:
                if download_image(img_data['image'], brand_path, file_name):
                    db_relative_path = f"{BRAND_FOLDER_NAME}/{file_name}"
                    cursor.execute("UPDATE brands SET logo = ? WHERE id = ?", (db_relative_path, b_id))
                    conn.commit()
                    print(f"      - OK: {file_name} (via Search)")
                    success = True
                    break
                
                time.sleep(random.uniform(1.5, 3))

            if not success:
                print(f"      - Khong tai duoc logo cho {b_name}")

        except Exception as e:
            print(f"      - Loi khi tim kiem: {e}")

        # Nghỉ lâu hơn giữa các brand để tránh bị block IP
        time.sleep(random.uniform(4, 8))

    conn.close()
    print("\n--- HOAN THANH! ---")

if __name__ == "__main__":
    main()
