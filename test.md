ECOMMERCE-ULTIMATE
├── app
│ ├── Console
│ │ └── Commands
│ │ └── DownloadProductImages.php
│ ├── Http
│ │ ├── Controllers
│ │ │ ├── Admin <-- (Namespace: Admin)
│ │ │ │ ├── ActivityLogController.php
│ │ │ │ ├── AdminChatController.php
│ │ │ │ ├── BannerController.php
│ │ │ │ ├── CartController.php
│ │ │ │ ├── CategoryController.php
│ │ │ │ ├── CouponController.php
│ │ │ │ ├── DashboardController.php
│ │ │ │ ├── OrderController.php
│ │ │ │ ├── ProductController.php
│ │ │ │ ├── SearchReportController.php
│ │ │ │ ├── SettingController.php
│ │ │ │ └── UserController.php
│ │ │ ├── Auth <-- (Authentication Logic)
│ │ │ │ ├── AuthenticatedSessionController.php
│ │ │ │ ├── RegisteredUserController.php
│ │ │ │ └── ...
│ │ │ ├── AddressController.php <-- (Web/Front-office Controllers)
│ │ │ ├── CartController.php
│ │ │ ├── ChatController.php
│ │ │ ├── CheckoutController.php
│ │ │ ├── CustomerOrderController.php
│ │ │ ├── HomeController.php
│ │ │ ├── ProfileController.php
│ │ │ ├── PublicProductController.php
│ │ │ └── SearchController.php
│ ├── Models <-- (ENTITIES / DATA LAYER)
│ │ ├── ActivityLog.php
│ │ ├── Address.php
│ │ ├── Banner.php
│ │ ├── CartItem.php
│ │ ├── Category.php
│ │ ├── ChatMessage.php
│ │ ├── ChatSession.php
│ │ ├── Coupon.php
│ │ ├── Order.php
│ │ ├── OrderItem.php
│ │ ├── Payment.php
│ │ ├── Product.php
│ │ ├── ProductImage.php
│ │ ├── SearchLog.php
│ │ ├── SearchTerm.php
│ │ ├── Setting.php
│ │ ├── Shipment.php
│ │ └── User.php
│ └── Services <-- (BUSINESS LOGIC LAYER)
│ ├── ActivityLogService.php
│ ├── BannerService.php
│ ├── CartService.php
│ ├── CategoryService.php
│ ├── ChatService.php
│ ├── CouponService.php
│ ├── DashboardService.php
│ ├── OrderService.php
│ ├── ProductService.php
│ ├── SearchService.php
│ ├── SystemSettingService.php
│ └── UserService.php
├── config
├── database
│ ├── migrations
│ └── seeders
├── public
├── resources
│ ├── css
│ ├── js
│ └── views
│ ├── components <-- (Reusable UI Components)
│ │ ├── card-product.blade.php
│ │ ├── modal.blade.php
│ │ ├── toast.blade.php
│ │ └── ...
│ ├── admin
│ ├── auth
│ ├── cart
│ ├── checkout
│ └── ...
├── routes
│ ├── auth.php
│ ├── console.php
│ └── web.php
├── storage
├── tests
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
└── vite.config.js
