# 🛒 Laravel E-Commerce Ultimate

> **Hệ thống Thương mại điện tử Full-Stack mạnh mẽ, hiện đại, tuân thủ kiến trúc Layered Monolith.**

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.0-38B2AC?style=flat&logo=tailwind-css)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)

---

## 📖 PHẦN 1: TỔNG QUAN HỆ THỐNG & CÔNG NGHỆ

### 1.1. Giới thiệu dự án
**Laravel E-Commerce Ultimate** là giải pháp bán hàng trực tuyến toàn diện được xây dựng trên nền tảng Laravel Framework mới nhất. Dự án không chỉ tập trung vào các tính năng thương mại điện tử tiêu chuẩn mà còn chú trọng vào trải nghiệm phát triển (Developer Experience) và khả năng mở rộng thông qua kiến trúc phần mềm chuẩn mực.

Mục tiêu của dự án là cung cấp một bộ mã nguồn mẫu (boilerplate) sạch, dễ bảo trì cho các hệ thống bán hàng quy mô vừa và lớn.

### 1.2. Kiến trúc thiết kế (Architecture)
Dự án áp dụng kiến trúc **Layered Monolith** kết hợp với **Service-Oriented Pattern**, chia tách rõ ràng trách nhiệm của từng tầng:

1.  **Presentation Layer (Controllers):** * Chỉ đóng vai trò nhận request và validate dữ liệu.
    * **Đặc biệt:** Áp dụng mô hình **Hybrid Response**, tự động trả về `View` (HTML) cho trình duyệt hoặc `JSON` cho API/Mobile App dựa trên Header của request mà không cần viết lặp lại code.
2.  **Service Layer (`app/Services`):** * Chứa toàn bộ logic nghiệp vụ (Business Logic).
    * Xử lý tính toán phức tạp, giao dịch (Transaction) và tương tác với Database.
3.  **Data Layer (Eloquent Models):** * Mapping trực tiếp với cơ sở dữ liệu MySQL.

### 1.3. Ngăn xếp công nghệ (Tech Stack)

#### Backend
* **Ngôn ngữ:** PHP 8.2+.
* **Framework:** Laravel 12.x.
* **Database:** MySQL 8.0+ (InnoDB Engine).
* **Cache/Queue:** Redis (Khuyến nghị cho Production).

#### Frontend
* **Template Engine:** Blade Templates.
* **Styling:** Tailwind CSS.
* **Interactivity:** Alpine.js (Nhẹ nhàng, Reactive) & Vanilla JS.
* **Bundler:** Vite.

### 1.4. Các gói thư viện chính (Key Dependencies)
* **`spatie/laravel-permission`**: Quản lý phân quyền (RBAC) chi tiết cho Admin và Khách hàng.
* **`spatie/laravel-activitylog`**: Ghi lại lịch sử hoạt động hệ thống (Audit Trail).
* **`laravel/breeze`**: Hệ thống xác thực người dùng cơ bản (Login, Register, Forgot Password).
* **`laravel/telescope`**: Công cụ debug mạnh mẽ cho môi trường Development.

### 1.5. Điểm nổi bật về kỹ thuật
* **Silent Console Debugging:** Một cơ chế debug thông minh, gửi log từ Backend xuống Console của trình duyệt (F12) thông qua view render, giúp Developer theo dõi luồng dữ liệu mà không làm vỡ giao diện người dùng.
* **Tổ chức module hóa:** Mã nguồn được tổ chức gọn gàng trong `app/Services` và `app/Http/Controllers/Admin`, giúp dễ dàng bảo trì và mở rộng tính năng.

---
## 🗄️ PHẦN 2: CHI TIẾT CẤU TRÚC CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

Hệ thống cơ sở dữ liệu MySQL được thiết kế chuẩn hóa (Normalized), chia thành các phân hệ rõ ràng để dễ dàng quản lý và mở rộng.

### 2.1. Nhóm Quản trị & Người dùng (Users & Auth)
* **`users`**: Bảng lõi lưu trữ thông tin đăng nhập.
    * Phân quyền (Admin/Customer) được quản lý qua bảng trung gian của gói `spatie/laravel-permission`.
* **`addresses`**: Sổ địa chỉ giao hàng (One-to-Many với User). Hỗ trợ đánh dấu địa chỉ mặc định (`is_default`).

### 2.2. Nhóm Danh mục & Sản phẩm (Catalog Module)
* **`categories`**: Quản lý danh mục đa cấp (Nested Categories) thông qua cột `parent_id` (Self-referencing).
* **`products`**: Bảng trung tâm chứa thông tin sản phẩm.
    * **Key columns:** `sku` (Mã kho), `price` (Giá bán), `quantity` (Tồn kho), `metadata` (JSON lưu thuộc tính động như màu sắc, size).
    * **Flags:** `is_hot`, `is_new`, `is_sale` để phục vụ hiển thị trang chủ.
* **`product_images`**: Thư viện ảnh sản phẩm (One-to-Many).

### 2.3. Nhóm Bán hàng & Đơn hàng (Sales Module)
* **`cart_items`**: Giỏ hàng thông minh (Hybrid Cart).
    * Hỗ trợ lưu trữ song song: `user_id` (cho thành viên) và `session_id` (cho khách vãng lai).
* **`orders`**: Lưu trữ đơn hàng tổng.
    * **Snapshot Address:** Cột `shipping_address` lưu JSON địa chỉ tại thời điểm đặt hàng (tránh sai lệch khi User sửa profile sau này).
* **`order_items`**: Chi tiết sản phẩm trong đơn hàng.
    * **Snapshot Price:** Lưu cứng giá bán (`unit_price`) tại thời điểm mua.
* **`coupons`**: Quản lý mã giảm giá (`code`, `value`, `expiry_date`).

### 2.4. Nhóm Vận hành & Hệ thống (System & Operations)
* **`payments`**: Theo dõi trạng thái thanh toán (`pending`, `paid`, `failed`) và mã giao dịch.
* **`shipments`**: Quản lý vận chuyển, mã vận đơn (`tracking_number`) và đơn vị vận chuyển (`carrier`).
* **`search_logs`**: Lưu lịch sử tìm kiếm của người dùng để phân tích từ khóa hot và cải thiện SEO.
* **`chat_sessions` & `chat_messages`**: Hệ thống Chat Support thời gian thực giữa Khách hàng và Admin.
* **`activity_log`**: (Package Spatie) Ghi lại mọi thao tác Create/Update/Delete của Admin để Audit.

---
## ⚙️ PHẦN 3: CHI TIẾT CÁC MODULE VÀ SERVICE (BUSINESS LOGIC)

Toàn bộ logic nghiệp vụ được tách biệt hoàn toàn khỏi Controller và đặt trong thư mục `app/Services`, tuân thủ nguyên tắc **"Fat Service, Skinny Controller"**.

### 3.1. OrderService (Giao dịch & Đặt hàng)
* **File:** `app/Services/OrderService.php`
* **Nhiệm vụ:** Xử lý toàn vẹn giao dịch đặt hàng (ACID Transactions).
* **Quy trình `placeOrder($user, $payload)`:**
    1.  **Locking:** Sử dụng `lockForUpdate()` trên bảng `products` để ngăn chặn "Race Condition" (nhiều khách cùng mua 1 sản phẩm cuối cùng).
    2.  **Validation:** Kiểm tra tồn kho lần cuối. Nếu thiếu -> Rollback & Báo lỗi.
    3.  **Inventory:** Trừ kho (`decrement`).
    4.  **Snapshot:** Tạo `Order` và `OrderItem`, lưu cứng giá bán tại thời điểm mua vào DB.
    5.  **Init:** Khởi tạo bản ghi `Payment` và `Shipment` ở trạng thái `pending`.
    6.  **Cleanup:** Xóa giỏ hàng của phiên hiện tại.

### 3.2. CartService (Giỏ hàng Hybrid)
* **File:** `app/Services/CartService.php`
* **Nhiệm vụ:** Quản lý giỏ hàng đa phiên.
* **Tính năng:**
    * **Dual Storage:** Lưu item theo `session_id` (Khách vãng lai) hoặc `user_id` (Thành viên).
    * **Auto Merge:** Khi khách đăng nhập, tự động gộp giỏ hàng từ Session vào tài khoản User thông qua hàm `mergeCart()`.
    * **Dynamic Pricing:** Tổng tiền (`subtotal`) luôn được tính toán lại theo giá realtime từ database, không lưu cứng để tránh sai lệch.

### 3.3. ProductService (Quản lý Sản phẩm)
* **File:** `app/Services/ProductService.php`
* **Nhiệm vụ:** CRUD sản phẩm nâng cao.
* **Tính năng:**
    * Xử lý upload và lưu trữ nhiều ảnh (Gallery) vào `storage/public`.
    * Quản lý thuộc tính động (`metadata`) dạng JSON (Màu sắc, Kích thước).
    * Lọc sản phẩm (`listing`) theo nhiều tiêu chí: Danh mục, Khoảng giá, Từ khóa.

### 3.4. ChatService (Hỗ trợ trực tuyến)
* **File:** `app/Services/ChatService.php`
* **Nhiệm vụ:** Xử lý logic chat thời gian thực (User - Admin).
* **Luồng hoạt động:**
    * Tự động tạo hoặc lấy lại `ChatSession` dựa trên Cookie hoặc User Auth.
    * Lưu trữ tin nhắn vào bảng `chat_messages`.
    * Hỗ trợ gửi tin nhắn từ cả 2 phía (Khách hàng và Admin).

### 3.5. SearchService (Tìm kiếm & Analytics)
* **File:** `app/Services/SearchService.php`
* **Nhiệm vụ:** Tìm kiếm sản phẩm và phân tích hành vi.
* **Analytics:** Tự động ghi lại từ khóa người dùng tìm kiếm vào bảng `search_logs` để Admin biết nhu cầu khách hàng (Ví dụ: Khách hay tìm "iPhone 15" nhưng shop chưa bán).

### 3.6. Các Service Khác
* **`CouponService`**: Validate mã giảm giá (Hạn sử dụng, Số lượng, Giá trị đơn hàng tối thiểu).
* **`ActivityLogService`**: Wrapper cho Spatie ActivityLog, ghi lại các sự kiện nhạy cảm (Xóa đơn, Sửa giá).
* **`DashboardService`**: Tổng hợp số liệu báo cáo cho trang Admin Dashboard (Doanh thu, Đơn mới, Tồn kho thấp).

---
## 🚦 PHẦN 4: CHI TIẾT CONTROLLER & ROUTING MAP

Hệ thống định tuyến (`routes/web.php`) được phân chia thành 3 phân hệ chính, áp dụng Middleware để kiểm soát quyền truy cập chặt chẽ.

### 4.1. Phân hệ Public (Guest & Customer)
*Namespace:* `App\Http\Controllers`

| Route URI | Controller | Action | Chức năng |
| :--- | :--- | :--- | :--- |
| `/` | `HomeController` | `index` | Trang chủ: Banner, Sản phẩm mới, Flash Sale. |
| `/search` | `SearchController` | `index` | Tìm kiếm sản phẩm với bộ lọc nâng cao (Giá, Danh mục). |
| `/product/{slug}` | `PublicProductController` | `show` | Xem chi tiết sản phẩm, Gallery ảnh, Sản phẩm liên quan. |
| `/cart` | `CartController` | `index` | Xem và quản lý giỏ hàng hiện tại. |
| `/checkout` | `CheckoutController` | `index/store`| Trang thanh toán và xử lý đặt hàng (Place Order). |
| `/chat/*` | `ChatController` | `send/history`| Widget chat hỗ trợ trực tuyến cho khách hàng. |

### 4.2. Phân hệ Customer Dashboard (Authenticated)
*Middleware:* `auth`, `verified`

| Route URI | Controller | Chức năng |
| :--- | :--- | :--- |
| `/profile` | `ProfileController` | Cập nhật thông tin cá nhân, Đổi mật khẩu. |
| `/address` | `AddressController` | Thêm, Sửa, Xóa, Đặt mặc định địa chỉ giao hàng (CRUD). |
| `/customer/orders` | `CustomerOrderController`| Xem lịch sử đơn hàng đã mua và chi tiết từng đơn. |

### 4.3. Phân hệ Admin Panel (Quản trị viên)
*Prefix:* `/admin` | *Middleware:* `auth`, `role:admin`
*Namespace:* `App\Http\Controllers\Admin`

| Module | Controller | Chức năng quản trị |
| :--- | :--- | :--- |
| **Dashboard** | `DashboardController` | Thống kê doanh thu, đơn hàng mới, sản phẩm bán chạy/tồn kho thấp. |
| **Catalog** | `ProductController` | CRUD Sản phẩm, Upload ảnh Gallery, Quản lý biến thể. |
| **Orders** | `OrderController` | Quy trình Fulfillment: Xác nhận thanh toán, Cập nhật vận chuyển, Hủy đơn. |
| **Support** | `AdminChatController` | Giao diện Chat realtime trả lời khách hàng. |
| **System** | `SettingController` | Cấu hình hệ thống (Logo, Email, SEO). |
| **Logs** | `ActivityLogController` | Xem nhật ký hoạt động hệ thống (Audit Trail). |

### 4.4. Hybrid Response Pattern (Điểm nhấn kỹ thuật)
Tất cả Controller trong dự án đều tuân thủ quy tắc **Hybrid Response**.
* **Logic:** Kiểm tra `Header` của Request.
* **Xử lý:**
    * Nếu là Browser (`Accept: text/html`): Trả về `View` (Blade Template).
    * Nếu là API (`Accept: application/json`): Trả về `JSON Response` chuẩn RESTful.
* **Lợi ích:** Tái sử dụng 100% logic Backend cho cả Website và Mobile App tương lai.

---
## 🔄 PHẦN 5: QUY TRÌNH NGHIỆP VỤ ĐIỂN HÌNH (WORKFLOWS)

Dưới đây là mô tả luồng dữ liệu cho các nghiệp vụ quan trọng nhất, giúp Developer hiểu cách hệ thống vận hành từ Frontend xuống Database.

### 5.1. Quy trình Đặt hàng (Checkout Flow)
*Đây là quy trình quan trọng nhất (Critical Path), yêu cầu tính toàn vẹn dữ liệu cao.*

1.  **Khởi tạo:** User nhấn "Đặt hàng" tại trang Checkout. Browser gửi `POST` request tới `/checkout/place-order`.
2.  **Controller:** `CheckoutController` validate dữ liệu đầu vào (Địa chỉ, Phương thức thanh toán) rồi gọi `OrderService`.
3.  **Service Layer (`OrderService`):**
    * **Bắt đầu Transaction:** Đảm bảo tính ACID.
    * **Locking:** Khóa dòng dữ liệu sản phẩm trong DB (`lockForUpdate`) để chặn các request khác sửa kho cùng lúc.
    * **Kiểm tra tồn kho:** Nếu `quantity` trong kho < số lượng mua -> Rollback & Ném lỗi ngoại lệ.
    * **Trừ kho:** Cập nhật số lượng tồn kho mới.
    * **Tạo Order:** Lưu thông tin đơn hàng và `Snapshot` địa chỉ.
    * **Tạo Order Items:** Lưu chi tiết sản phẩm và `Snapshot` giá bán tại thời điểm đó.
    * **Cleanup:** Xóa items trong giỏ hàng hiện tại.
4.  **Kết thúc:** Controller điều hướng User sang trang "Cảm ơn" (`checkout.thankyou`).

### 5.2. Quy trình Hỗ trợ Trực tuyến (Support Chat Flow)
*Hệ thống Chat thời gian thực không cần login (Guest Support).*

1.  **Guest:** Mở widget chat và gửi tin nhắn.
2.  **ChatService:**
    * Kiểm tra Cookie `chat_session_id`. Nếu chưa có -> Tạo Session mới trong bảng `chat_sessions`.
    * Lưu tin nhắn vào bảng `chat_messages`.
3.  **Admin:**
    * Truy cập Admin Panel -> Chat Management.
    * Thấy session mới -> Vào xem và trả lời.
4.  **Frontend:** Widget chat tự động polling (hoặc websocket nếu cấu hình) để hiển thị tin nhắn mới từ Admin.

---

## 🛠 PHẦN 6: HƯỚNG DẪN CÀI ĐẶT & VẬN HÀNH

### 6.1. Yêu cầu hệ thống (Prerequisites)
* **PHP:** >= 8.2
* **Composer:** Phiên bản mới nhất
* **Node.js & NPM:** >= 18.x
* **Database:** MySQL 8.0+

### 6.2. Cài đặt chi tiết

**Bước 1: Clone dự án & Cài đặt thư viện**
```bash
git clone [https://github.com/missyubibii/ecommerce-ultimate.git](https://github.com/missyubibii/ecommerce-ultimate.git)
cd ecommerce-ultimate

# Cài đặt PHP dependencies
composer install

# Cài đặt Frontend dependencies
npm install
```

**Bước 2: Cấu hình môi trường**
```bash
cp .env.example .env
php artisan key:generate
```
Mở file .env và cấu hình thông tin Database (DB_DATABASE, DB_USERNAME, DB_PASSWORD).


**Bước 3: Khởi tạo Database & Dữ liệu mẫu**
```bash
# Chạy migration và seeder (Tạo User, Category, Product mẫu)
php artisan migrate --seed
```
Lệnh này sẽ chạy `DatabaseSeeder`, tự động gọi `RealProductSeeder` để tạo dữ liệu sản phẩm giả lập như thật.

**Bước 4: Tải ảnh sản phẩm mẫu (Tool hỗ trợ)** Dự án tích hợp sẵn công cụ tự động tải ảnh từ internet cho các sản phẩm mẫu:
```bash
php artisan app:download-product-images
```
Command này được định nghĩa tại `app/Console/Commands/DownloadProductImages.php.`

**Bước 5: Chạy ứng dụng**
```bash
# Build Frontend assets (TailwindCSS/Vite)
npm run build

# Khởi chạy Server
php artisan serve
```

**6.3. Tài khoản Demo mặc định**
(Được tạo bởi DatabaseSeeder)

* **Administrator:**
    * **Email:** admin@gmail.com
    * **Password:** 123123123
* **Customer:**
    * **Email:** user@gmail.com
    * **Password:** 123123123

**6.4. Các lệnh hữu ích khác**
```bash
#Chạy Vite server (Hot Reload) để phát triển Frontend.
npm run dev

#install: Cài đặt Telescope để debug request/query.
php artisan telescope
```
---
© 2025 Laravel E-Commerce Ultimate. Built with ❤️ by Missyubibi.
