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
