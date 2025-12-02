import mysql.connector
from duckduckgo_search import DDGS
import requests
import os
import time
import random
import json
from slugify import slugify
from PIL import Image
from io import BytesIO

# --- 1. CẤU HÌNH DATABASE ---
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '', # Điền pass DB của bạn nếu có
    'database': 'ecommerce'
}

# --- 2. CẤU HÌNH ĐƯỜNG DẪN ---
# Đường dẫn gốc đến folder public của Laravel (Cần chính xác)
# Windows thường là: C:\laragon\www\ecommerce-ultimate\storage\app\public
# Ở đây dùng đường dẫn tương đối từ vị trí file chạy tool
PHYSICAL_STORAGE_ROOT = os.path.join('storage', 'app', 'public')

# Folder chính trong storage
PRODUCT_FOLDER_NAME = 'products'

# Số lượng ảnh muốn tải mỗi sản phẩm
MAX_IMAGES = 4

def create_directory(path):
    if not os.path.exists(path):
        os.makedirs(path)

def get_db_connection():
    return mysql.connector.connect(**DB_CONFIG)

def download_image(url, save_folder, file_name):
    try:
        headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'}
        response = requests.get(url, headers=headers, timeout=10)

        if response.status_code == 200:
            image = Image.open(BytesIO(response.content))
            if image.mode in ("RGBA", "P"):
                image = image.convert("RGB")

            full_path = os.path.join(save_folder, file_name)
            image.save(full_path, "JPEG", quality=85)
            return True
    except Exception as e:
        print(f"      ⚠️ Lỗi tải ảnh: {e}")
        return False
    return False

def main():
    print("--- 🤖 TOOL CRAWL ẢNH (DIRECT MODE) ---")

    # Tạo folder gốc nếu chưa có
    base_product_path = os.path.join(PHYSICAL_STORAGE_ROOT, PRODUCT_FOLDER_NAME)
    create_directory(base_product_path)

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    # Lấy SP chưa có ảnh
    print("⏳ Đang lấy danh sách sản phẩm...")
    cursor.execute("SELECT id, name, sku, colors FROM products WHERE image IS NULL OR image = ''")
    products = cursor.fetchall()

    print(f"🔍 Tìm thấy {len(products)} sản phẩm cần 'bơm' ảnh.")

    ddgs = DDGS()

    for index, product in enumerate(products):
        p_id = product['id']
        p_name = product['name']

        # Parse màu để tìm chính xác hơn
        colors = []
        if product['colors']:
            try:
                colors = json.loads(product['colors'])
            except:
                pass
        primary_color = colors[0] if colors else ""

        print(f"\n[{index+1}/{len(products)}] Xử lý: {p_name} (ID: {p_id})")

        # 1. TẠO FOLDER THEO ID: storage/app/public/products/{id}/
        # Việc này giúp quản lý file gọn gàng hơn là vứt hết vào root products
        product_dir_path = os.path.join(PHYSICAL_STORAGE_ROOT, PRODUCT_FOLDER_NAME, str(p_id))
        create_directory(product_dir_path)

        # 2. TÌM KIẾM
        search_query = f"{p_name} {primary_color} official product image white background"
        print(f"      🔎 Search: '{search_query}'")

        downloaded_db_paths = [] # List đường dẫn tương đối để lưu DB

        try:
            results = ddgs.images(search_query, region="wt-wt", safesearch="off", max_results=10)

            count = 0
            for img_data in results:
                if count >= MAX_IMAGES: break

                # Tên file: sku_1.jpg
                safe_sku = slugify(product['sku']) if product['sku'] else f"prod_{p_id}"
                file_name = f"{safe_sku}_{count+1}.jpg"

                # Tải vào folder ID
                if download_image(img_data['image'], product_dir_path, file_name):
                    # Đường dẫn DB: products/{id}/filename.jpg
                    # Lưu ý: Thay dấu gạch chéo ngược \ của Windows thành / cho chuẩn Web
                    db_relative_path = f"{PRODUCT_FOLDER_NAME}/{p_id}/{file_name}".replace("\\", "/")

                    downloaded_db_paths.append(db_relative_path)
                    print(f"      ✅ OK: {file_name}")
                    count += 1
                    time.sleep(random.uniform(0.5, 1.2))

            # 3. UPDATE DB
            if downloaded_db_paths:
                # Ảnh 1 -> Làm Avatar (cột image)
                main_image = downloaded_db_paths[0]
                cursor.execute("UPDATE products SET image = %s WHERE id = %s", (main_image, p_id))

                # Các ảnh còn lại (hoặc tất cả) -> Vào Gallery (bảng product_images)
                gallery_values = []
                for idx, path in enumerate(downloaded_db_paths):
                    # idx=0 là ảnh đại diện, ta vẫn lưu vào gallery để show đủ bộ
                    gallery_values.append((p_id, path, idx)) # sort_order = idx

                if gallery_values:
                    # Xóa gallery cũ của SP này trước (cho sạch, vì bạn bảo đã xóa file)
                    cursor.execute("DELETE FROM product_images WHERE product_id = %s", (p_id,))

                    stmt = "INSERT INTO product_images (product_id, path, sort_order, created_at, updated_at) VALUES (%s, %s, %s, NOW(), NOW())"
                    cursor.executemany(stmt, gallery_values)

                conn.commit()
                print("      💾 Saved to DB.")
            else:
                print("      ⚠️ Không tải được ảnh nào.")

        except Exception as e:
            print(f"      ❌ Lỗi: {e}")

        time.sleep(random.uniform(1, 3))

    conn.close()
    print("\n🏁 DONE! Nhớ chạy lệnh: php artisan storage:link")

if __name__ == "__main__":
    main()
