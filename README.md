# 🌟 CV Liquid Glass — CV cá nhân + trang quản trị CMS (PHP thuần & MySQL)

Trang CV cá nhân giao diện **Mẫu 5 Liquid Glass**: menu bar, cửa sổ kính kéo thả / thu nhỏ / phóng to, dock kiểu macOS, chuyển ngày/đêm, thanh chỉnh độ trong suốt, bảng màu Biển sương. Trên điện thoại là màn hình ứng dụng, mỗi mục mở dạng sheet vuốt xuống để đóng.

Kèm **trang quản trị CMS** được thiết kế riêng cho giao diện này: mọi trường trong Admin đều hiển thị trên trang CV, không có trường thừa.

| Trong Admin | Hiện ở đâu trên trang CV |
|---|---|
| **Cửa sổ & Dock** | Mỗi dòng = 1 cửa sổ + 1 icon trên dock / màn hình chính. Bật/tắt, kéo thả thứ tự, tên trên dock, tiêu đề cửa sổ, cửa sổ nào tự mở sẵn khi vào trang |
| **Hồ sơ cá nhân** | Cửa sổ "Về tôi", trạng thái trên thanh menu, thẻ hồ sơ trên điện thoại, cửa sổ "Liên hệ", nút Gọi / Zalo, file CV PDF |
| **Chỉ số thống kê** | 4 ô số liệu trong "Về tôi" (số như `3+`, `100%` có hiệu ứng đếm) |
| **Kỹ năng / Điểm mạnh / Điểm cần cải thiện** | Các thẻ trong cửa sổ tương ứng |
| **Kinh nghiệm** | Timeline trong cửa sổ "Kinh nghiệm" (chọn được màu chấm cho từng mốc) |
| **Học vấn & Công cụ** | Cửa sổ "Học vấn & Công cụ" (hiện tất cả trường học) |
| **Cài đặt & SEO** | Tiêu đề tab, mô tả SEO, chữ viết tắt logo, giao diện ngày/đêm mặc định, độ trong suốt mặc định, Google Analytics, dòng chân trang |
| **Tin nhắn** | Lời nhắn khách gửi từ form trong cửa sổ "Liên hệ" |

> Đã có dữ liệu ở CMS Mẫu 4 cũ? Xem mục **3.1** để chuyển dữ liệu sang bằng `database/migration-from-mau4.sql`.

---

## 📌 1. Yêu cầu môi trường

- **PHP**: Phiên bản `>= 8.0` (Hỗ trợ tốt PHP 8.1, 8.2, 8.3, 8.4).
- **PHP Extensions bắt buộc**: `pdo_mysql`, `fileinfo`, `gd`, `mbstring`, `session`.
- **Cơ sở dữ liệu**: MySQL `5.7+` hoặc MariaDB `10.3+`, Charset `utf8mb4`, Collation `utf8mb4_unicode_ci`.
- **Web Server**: Apache (có bật `mod_rewrite`, `mod_headers`) hoặc Nginx.
- **Không yêu cầu Composer/npm khi chạy production** — mã nguồn đã được biên dịch sẵn file CSS tĩnh và chạy được ngay trên mọi hosting (cPanel, DirectAdmin, VPS, Localhost XAMPP/Laragon).

---

## 🔐 2. Tài khoản quản trị Admin mặc định

- **Đường dẫn Admin**: `http://your-domain.com/admin/login.php` (hoặc `http://localhost/phuongnh-cv-glassstyle/admin/login.php`)
- **Tên đăng nhập**: `admin`
- **Mật khẩu mặc định**: `Admin@123`

> ⚠️ **CẢNH BÁO BẢO MẬT BẮT BUỘC:**  
> Ngay sau khi cài đặt và đăng nhập lần đầu tiên thành công, bạn hãy vào mục **Tài khoản Admin** (`admin/account.php`) để **đổi ngay mật khẩu mặc định** và cập nhật email cá nhân.

### 2.1. Hướng dẫn Khôi phục mật khẩu Admin (Quên mật khẩu)
Nếu bạn quên mật khẩu đăng nhập Admin, có thể thiết lập lại trực tiếp qua CSDL bằng 2 bước sau:

1. **Sinh chuỗi Hash Bcrypt mới bằng PHP CLI**:
   Mở Terminal/CMD và chạy lệnh sau (thay `'MatKhauMoiCuaBan'` bằng mật khẩu bạn muốn đặt):
   ```bash
   php -r "echo password_hash('MatKhauMoiCuaBan', PASSWORD_DEFAULT) . PHP_EOL;"
   ```
   *(Kết quả trả về sẽ có dạng `$2y$10$...` hoặc `$2y$12$...`)*

2. **Cập nhật vào Database qua phpMyAdmin / MySQL CLI**:
   Dán câu lệnh SQL sau vào tab **SQL** trong phpMyAdmin:
   ```sql
   UPDATE users SET password_hash = '<chuỗi-hash-vừa-sinh-ở-bước-1>' WHERE username = 'admin';
   ```

> 🔒 **Cảnh báo an toàn:** Sau khi đổi xong, hãy xóa lịch sử dòng lệnh Terminal (hoặc xóa buffer) và không lưu trữ mật khẩu dạng plain text ở bất kỳ file nào.


---

## 💻 3. Cài đặt trên máy mới (Setup Local Environment)

Khi clone dự án về máy mới, thực hiện lần lượt các bước sau:

### Bước 1: Tạo Database và Import Dữ liệu mẫu (Schema & Seed)
1. Tạo CSDL với bảng mã `utf8mb4`:
   ```powershell
   mysql -u root -p -e "DROP DATABASE IF EXISTS cv_liquid_glass; CREATE DATABASE cv_liquid_glass CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
2. Import schema và seed dữ liệu:
   ```powershell
   mysql -u root -p --default-character-set=utf8mb4 cv_liquid_glass -e "source database/schema.sql"
   mysql -u root -p --default-character-set=utf8mb4 cv_liquid_glass -e "source database/seed.sql"
   ```

> ⚠️ **LƯU Ý ĐẶC BIỆT QUAN TRỌNG TRÊN POWERSHELL:**  
> Trên Windows PowerShell, **tuyệt đối KHÔNG** sử dụng pipe `|` hoặc toán tử chuyển hướng `<` (ví dụ `Get-Content ... | mysql` hoặc `mysql < database/seed.sql`). PowerShell sẽ tự động decode/encode qua console code page (UTF-16/OEM) khiến toàn bộ dấu tiếng Việt bị vỡ thành ký tự rác hoặc dấu `?`.  
> **Bắt buộc** phải sử dụng lệnh nội tại của MySQL Client: `-e "source database/schema.sql"`.

3. **Kiểm tra tính toàn vẹn UTF-8 bằng HEX:**
   Chạy câu lệnh kiểm tra chuỗi họ tên tiếng Việt trong bảng `profile`:
   ```powershell
   mysql -u root -p cv_liquid_glass --default-character-set=utf8mb4 -e "SELECT CHAR_LENGTH(full_name) AS so_ky_tu, LENGTH(full_name) AS so_byte, HEX(LEFT(full_name,3)) AS hex_dau FROM profile;"
   ```
   Kết quả kỳ vọng:
   ```text
   +----------+---------+------------+
   | so_ky_tu | so_byte | hex_dau    |
   +----------+---------+------------+
   |       11 |      15 | 4849E1BB80 |
   +----------+---------+------------+
   ```
   - `48` = `H`
   - `49` = `I`
   - `E1BB80` = `Ề` (3 byte UTF-8 chuẩn của chữ `Ề`). Nếu hiển thị `3F` (dấu `?`) hoặc byte khác là import bị lỗi font.

---

### 3.1. (Tùy chọn) Chuyển dữ liệu từ CMS Mẫu 4 cũ
Nếu bạn đã có nội dung thật trong DB `cv_mau4_developer` của bản Mẫu 4, **thay cho Bước 1** ở trên:
1. Tạo DB mới `cv_liquid_glass` (utf8mb4) và sao chép toàn bộ DB cũ sang (phpMyAdmin → *Operations* → *Copy database to*, hoặc `mysqldump cv_mau4_developer | mysql cv_liquid_glass`).
2. Chạy script chuyển đổi trên **bản sao** (không chạy trên DB cũ):
   ```powershell
   mysql -u root -p --default-character-set=utf8mb4 cv_liquid_glass -e "source database/migration-from-mau4.sql"
   ```
3. Chép thư mục `public/uploads/` (ảnh đại diện, file CV) từ bản cũ sang bản mới.

Script sẽ: gộp section `hero` vào cửa sổ "Về tôi", thêm tên dock & tùy chọn "mở sẵn", rút gọn trạng thái (`SẴN SÀNG NHẬN VIỆC • ...` → `Sẵn sàng nhận việc`), đổi màu nhấn kinh nghiệm thành màu chấm timeline, và bỏ các cột icon/màu không dùng.

---

### Bước 2: Cấu hình `config/config.php`
1. Sao chép từ file mẫu:
   ```powershell
   cp config/config.example.php config/config.php
   ```
2. Sinh mã `APP_KEY` mới ngẫu nhiên (tối thiểu 32 ký tự):
   ```powershell
   php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
   ```
3. Mở `config/config.php` và cập nhật:
   - `DB_NAME`: `'cv_liquid_glass'`
   - `DB_PASS`: Mật khẩu root MySQL của bạn
   - `APP_KEY`: Chuỗi hex 64 ký tự vừa sinh ở trên

---

### Bước 3: Kiểm tra các Extension bắt buộc trong `php.ini`
Kiểm tra file cấu hình PHP đang dùng bằng lệnh `php --ini` (ví dụ `C:\php\php.ini`).  
Đảm bảo đã kích hoạt `extension_dir = "ext"` (hoặc đường dẫn tuyệt đối) và bật đủ 5 extension:
```ini
extension=pdo_mysql
extension=fileinfo
extension=curl
extension=openssl
extension=gd
```
Xác nhận lại qua Terminal:
```powershell
php -m
```
Đảm bảo danh sách hiển thị đủ các module: `pdo_mysql`, `fileinfo`, `curl`, `openssl`, `gd`.

---

### Bước 4: Khởi chạy Server và Truy cập
Khởi chạy PHP Built-in Server trỏ webroot vào thư mục `public/`:
```powershell
php -S localhost:8081 -t public
```
- **Trang CV công khai**: `http://localhost:8081/`
- **Trang Quản trị CMS**: `http://localhost:8081/admin/login.php` (Đăng nhập: `admin` / `Admin@123`)

---

## 🚀 4. Hướng dẫn cài đặt nhanh qua giao diện phpMyAdmin (XAMPP / Laragon)

### Bước 1: Tạo cơ sở dữ liệu
1. Mở `phpMyAdmin` (ví dụ `http://localhost/phpmyadmin`).
2. Tạo mới database có tên: `cv_liquid_glass` (Collation: `utf8mb4_unicode_ci`).
3. Chọn database vừa tạo -> vào tab **Import** (Nhập):
   - Chọn file `database/schema.sql` -> Nhấn **Import** để tạo bảng.
   - Chọn file `database/seed.sql` -> Nhấn **Import** để nạp dữ liệu mẫu ban đầu.

### Bước 2: Cấu hình kết nối
1. Mở file `config/config.php` (hoặc sao chép từ `config/config.example.php`).
2. Điều chỉnh thông tin kết nối DB và thiết lập khóa bảo mật:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', '3306');
   define('DB_NAME', 'cv_liquid_glass');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   
   // Bắt buộc đổi sang chuỗi ngẫu nhiên tối thiểu 32 ký tự
   define('APP_KEY', 'sinh-bang-lenh-php-r-echo-bin2hex-random_bytes-32');
   
   // Bật nếu chạy sau Cloudflare / Nginx Reverse Proxy (mặc định: false)
   define('TRUST_PROXY', false);
   ```

> 💡 **Ghi chú về `TRUST_PROXY`:**
> - Nếu bạn chạy web trực tiếp trên Apache/Nginx (không qua proxy trung gian): Giữ nguyên `TRUST_PROXY = false` để lấy IP chính xác từ `REMOTE_ADDR` chống giả mạo header.
> - Nếu website chạy sau **Cloudflare CDN** hoặc **Reverse Proxy / Load Balancer**: Đổi `TRUST_PROXY = true` để hệ thống nhận diện đúng IP thật của người dùng qua `CF-Connecting-IP` và `X-Forwarded-For`.

### Bước 3: Khởi chạy
- Khởi chạy bằng PHP Built-in Server (khuyên dùng, trỏ document root vào thư mục `public`):
  ```bash
  php -S localhost:8081 -t public
  ```
- Hoặc nếu cấu hình VirtualHost Apache / Nginx, hãy trỏ DocumentRoot vào thư mục `public/`:
  - Truy cập trang CV Public: `http://localhost:8081/` (hoặc `http://localhost/phuongnh-cv-glassstyle/public/`)
  - Truy cập trang Quản trị: `http://localhost:8081/admin/login.php`

---

## 🌐 5. Hướng dẫn Triển khai (Deploy)

### 4.1. Triển khai trên Shared Hosting cPanel

1. **Upload mã nguồn**:
   - Nén toàn bộ thư mục `phuongnh-cv-glassstyle` thành file `.zip`.
   - Đăng nhập cPanel -> Mở **File Manager** -> Upload lên thư mục gốc hosting.
   - Cấu hình DocumentRoot của domain/subdomain trỏ vào thư mục `public` (hoặc chuyển nội dung `public/` ra `public_html/` và đưa các thư mục `app/`, `config/`, `database/` ra ngoài thư mục webroot để bảo mật tối đa).
2. **Tạo Database MySQL**:
   - Vào mục **MySQL Databases** trên cPanel -> Tạo Database mới (ví dụ: `u123_cv`).
   - Tạo MySQL User mới (ví dụ: `u123_cvuser`) và đặt mật khẩu an toàn.
   - Gán User vào Database và chọn quyền **ALL PRIVILEGES**.
3. **Import Dữ liệu**:
   - Mở **phpMyAdmin** trên cPanel -> Chọn database vừa tạo.
   - Import lần lượt `database/schema.sql` rồi đến `database/seed.sql`.
4. **Cập nhật `config/config.php`**:
   - Chỉnh sửa các hằng số `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_KEY` theo thông tin thực tế.
5. **Cấp quyền ghi thư mục Uploads**:
   - Đảm bảo thư mục `public/uploads/` có quyền ghi `0755` hoặc `0775` để có thể upload ảnh đại diện.

---

### 4.2. Triển khai trên VPS (Ubuntu / Debian / CentOS)

#### 1. Phân quyền thư mục
```bash
sudo chown -R www-data:www-data /var/www/phuongnh-cv-glassstyle
sudo chmod -R 755 /var/www/phuongnh-cv-glassstyle
sudo chmod -R 775 /var/www/phuongnh-cv-glassstyle/public/uploads
```

#### 2. Cấu hình Nginx Vhost Mẫu
> ⚠️ **LƯU Ý QUAN TRỌNG:** Webroot phải trỏ vào `/var/www/phuongnh-cv-glassstyle/public`. File `.htaccess` **KHÔNG CÓ TÁC DỤNG** trên Nginx. Do đó bạn bắt buộc phải cấu hình block `location ^~ /uploads/` trực tiếp trong file cấu hình Nginx bên dưới.
> Đồng thời, trong Apache/LiteSpeed, `php_flag engine off` chỉ hiệu lực với `mod_php` cũ và không có tác dụng trên PHP-FPM/LiteSpeed. Lớp chặn chính xác và an toàn nhất là `<FilesMatch>` chặn toàn bộ extension thực thi.

Tạo file `/etc/nginx/sites-available/cv-developer.conf`:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/phuongnh-cv-glassstyle/public;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # BẢO VỆ THƯ MỤC UPLOADS TRÊN NGINX
    location ^~ /uploads/ {
        location ~ \.(php|phtml|phar|cgi|pl|py|sh)$ {
            deny all;
        }
        add_header X-Content-Type-Options nosniff;
        autoindex off;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Kích hoạt site và reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/cv-developer.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

### 4.3. ✅ Kiểm tra BẮT BUỘC sau khi deploy (trước khi công khai website)

> Các bước dưới đây chạy trên **domain thật**, không phải `php -S`. PHP built-in server có
> cơ chế fallback: URL không khớp file nào trong document root sẽ được route về `index.php` —
> nên trên `php -S` các URL nhạy cảm "trông có vẻ" trả trang chủ, KHÔNG chứng minh hosting an toàn.

**1. Bốn URL sau phải trả `404` hoặc `403` (KHÔNG được trả nội dung file):**

```
https://your-domain.com/config/config.php
https://your-domain.com/app/Auth.php
https://your-domain.com/database/schema.sql
https://your-domain.com/tests/
```

- Nếu `/database/schema.sql` **tải xuống được file `.sql`** → document root đang trỏ SAI
  (trỏ vào thư mục gốc dự án thay vì `public/`). **Phải sửa ngay trước khi công khai website.**
- Cách đúng: DocumentRoot / Nginx `root` phải trỏ vào `.../phuongnh-cv-glassstyle/public`,
  còn `app/`, `config/`, `database/`, `tests/` nằm NGOÀI webroot.

**2. Kiểm tra thư mục `uploads/` không thực thi được PHP:**

```bash
# Tạo file thử
echo "<?php echo 'HACKED';" > public/uploads/test.php
```
Mở `https://your-domain.com/uploads/test.php` trên trình duyệt.
→ **Phải KHÔNG thấy chữ `HACKED`** (phải thấy 403, hoặc tải file thô về, hoặc trang trắng).
Nếu thấy `HACKED` → server đang thực thi PHP trong `uploads/`, lỗ hổng nghiêm trọng.
**Xoá file `test.php` ngay sau khi kiểm tra xong.**

**3. `.htaccess` KHÔNG có tác dụng trên Nginx.**
File `public/uploads/.htaccess` chỉ hiệu lực với Apache / LiteSpeed. Trên Nginx bắt buộc phải
dùng block `location ^~ /uploads/ { ... }` trong file cấu hình Vhost đã cho ở mục **4.2** ở trên.
Tương tự, `php_flag engine off` chỉ chạy với `mod_php` cũ — lớp chặn chính là block
`<FilesMatch "\.(php|phtml|phar|...)$">` (Apache) hoặc `location ~ \.php$ { deny all; }` bên trong
`location ^~ /uploads/` (Nginx).

---

## 🎨 6. Giao diện trang công khai

- `public/assets/style5.css`: toàn bộ CSS (CSS thuần, không cần build). Font tự host qua `@import url('fonts/fonts.css')`.
- `public/assets/script5.js`: cửa sổ kéo thả / thu nhỏ / phóng to, dock, ngày/đêm, độ trong suốt, form liên hệ gửi AJAX (tự chuyển sang submit thường nếu lỗi mạng).
- Trang Admin dùng Bootstrap 5.3.3, Bootstrap Icons 1.11.3, SweetAlert2 11, SortableJS 1.15 — đã có sẵn trong `public/assets/vendor/`, không cần CDN.
- Khách truy cập tự chọn ngày/đêm và độ trong suốt; lựa chọn được nhớ trên máy của họ. Admin → Cài đặt chỉ đặt giá trị **mặc định** cho lần đầu vào trang.

---

## 📁 7. Cấu trúc thư mục dự án

```
phuongnh-cv-glassstyle/
├── config/
│   ├── config.example.php     # File cấu hình mẫu
│   └── config.php             # Cấu hình DB, BASE_URL, APP_KEY, PUBLIC_PATH, Session
├── app/
│   ├── Database.php           # PDO Singleton chuẩn UTF-8mb4, Prepared Statements
│   ├── Auth.php               # Đăng nhập, rate-limit theo IP trong DB, dummy verify chống timing attack
│   ├── Csrf.php               # Quản lý CSRF Session Admin, chặn Open Redirect & Stateless Token cho Form public
│   ├── Upload.php             # Xử lý upload finfo, resize GD max 1600px, dọn dẹp file cũ
│   ├── Repository.php         # Model truy vấn CRUD dùng chung, checkContactThrottle
│   └── helpers.php            # e() escape XSS, url(), asset(), upload_url(), flash(), old()
├── public/                    # THƯ MỤC WEBROOT CÔNG KHAI (DocumentRoot)
│   ├── index.php              # Trang CV Public giao diện Liquid Glass (render động, stateless token, antispam form)
│   ├── admin/                 # Khu vực quản trị CMS
│   │   ├── login.php, logout.php # Đăng nhập & Đăng xuất
│   │   ├── index.php          # Dashboard tổng quan
│   │   ├── profile.php        # Hồ sơ: cửa sổ Về tôi, thẻ hồ sơ mobile, Liên hệ, ảnh đại diện, file CV PDF
│   │   ├── sections.php       # Cửa sổ & Dock: bật/tắt, tên dock, tiêu đề, mở sẵn, kéo thả thứ tự
│   │   ├── stats.php          # Quản lý 4 chỉ số thống kê (Key Stats)
│   │   ├── skills.php         # Quản lý kỹ năng cốt lõi & tags
│   │   ├── strengths.php      # Quản lý điểm mạnh & phẩm chất
│   │   ├── experience.php     # Quản lý các mốc kinh nghiệm timeline
│   │   ├── education.php      # Quản lý học vấn & danh sách công cụ làm việc
│   │   ├── settings.php       # Cấu hình SEO meta, monogram logo, footer
│   │   ├── messages.php       # Quản lý tin nhắn liên hệ gửi về
│   │   ├── account.php        # Đổi thông tin admin & mật khẩu
│   │   ├── ajax/              # Endpoints AJAX (reorder, toggle, upload)
│   │   └── partials/          # Header, Footer, Sidebar admin
│   ├── assets/                # style5.css, script5.js, fonts/, vendor/ (Bootstrap, icons, SweetAlert2, SortableJS)
│   └── uploads/               # Thư mục chứa file tải lên (YYYY/MM/)
│       ├── .htaccess          # Chặn thực thi PHP script
│       ├── index.html         # Chặn liệt kê danh mục thư mục (Directory Listing)
│       └── cv/                # Thư mục lưu trữ file CV PDF tải lên
│           └── .gitkeep       # Giữ cấu trúc thư mục uploads/cv/ trên Git
├── database/
│   ├── schema.sql             # Cấu trúc bảng MySQL chuẩn (15 bảng)
│   ├── seed.sql               # Dữ liệu khởi tạo theo template Mẫu 5
│   └── migration-from-mau4.sql # Chuyển dữ liệu từ CMS Mẫu 4 cũ (tùy chọn)
└── README.md                  # Hướng dẫn sử dụng & triển khai
```

---

## 🛡️ 8. Tính năng bảo mật tích hợp

1. **Chống SQL Injection**: 100% các câu truy vấn sử dụng PDO Prepared Statements với tham số ràng buộc `?`.
2. **Chống tấn công CSRF & Open Redirect**:
   - Khu vực Admin sử dụng Token Session 32-byte ngẫu nhiên kèm bộ lọc `parse_url` kiểm tra trùng khớp host trước khi chuyển hướng.
   - Form liên hệ Public sử dụng cơ chế **Stateless HMAC Token** (dựa trên timestamp + `APP_KEY`), hoàn toàn không sinh cookie session cho khách vãng lai.
3. **Chống Spam Form Liên hệ (Multi-layer Antispam)**:
   - **IP Throttle**: Giới hạn tối đa 3 tin/giờ và 10 tin/24 giờ theo địa chỉ IP lưu trong bảng `contact_throttle`.
   - **Honeypot Trap**: Đặt trường ẩn `website` nằm ngoài màn hình; nếu bot tự điền sẽ giả vờ thành công nhưng không ghi vào CSDL.
   - **Time-trap**: Từ chối các lượt submit biểu mẫu được gửi dưới 3 giây kể từ khi tải trang hoặc quá 2 giờ.
   - **Validation độ dài nghiêm ngặt**: Name <= 100, Email <= 191, Phone <= 50, Message <= 5000 ký tự.
4. **Chống tấn công Brute-force Login**:
   - Rate Limiting dựa trên địa chỉ IP thực tế lưu trong bảng `login_attempts`. Nếu nhập sai quá 5 lần trong 15 phút sẽ khóa IP 5 phút.
   - Tích hợp `password_verify` với Dummy Hash cố định khi không tìm thấy tài khoản để ngăn ngừa tấn công phân tích độ trễ phản hồi (Timing Attack).
5. **Bảo vệ tải file an toàn (Upload Security)**:
   - Kiểm tra MIME type thực tế bằng `finfo_file` (không phụ thuộc vào đuôi mở rộng).
   - Đổi tên file ngẫu nhiên `{slug}-{uniqid}.{ext}` để tránh trùng lặp và tấn công ghi đè.
   - Thư mục `uploads/` có file `.htaccess` và `index.html` chặn thực thi mọi script PHP, phtml, phar, cgi... và cấm directory listing.
   - Tự động xóa file cũ khi thay đổi ảnh hoặc xóa bản ghi (bảo vệ chống Path Traversal).
6. **Chống tấn công XSS**: Toàn bộ dữ liệu hiển thị ra ngoài HTML đều được lọc qua hàm `e()` (`htmlspecialchars`). Các đoạn văn bản cho phép định dạng được lọc qua whitelist tag thủ công.
7. **Không rò rỉ thông tin lỗi Database**: Trên môi trường Production (`APP_ENV !== 'development'`), mọi ngoại lệ Database đều được ẩn thông tin nhạy cảm và ghi vào `error_log()`.

---

## 🧪 9. Lưu ý khi kiểm thử

**Fatal error trong PHP vẫn trả HTTP `200` kèm nội dung một phần.** Khi PHP gặp lỗi chí mạng
giữa lúc render, phần HTML sinh ra trước đó vẫn được gửi đi với mã `200`, chỉ dừng đột ngột ở
điểm lỗi. Vì vậy khi test một trang:

- **KHÔNG được coi HTTP `200` là bằng chứng trang chạy đúng.** `200` chỉ nói "file tồn tại".
- Phải `grep` nội dung phản hồi để tìm các chuỗi lỗi:
  `Fatal error`, `Parse error`, `Warning:`, `Notice:`, `Deprecated:`, `Uncaught`.
- Kiểm tra cả log server (`error_log`, log của PHP-FPM / `php -S`).

```bash
# Ví dụ kiểm tra nhanh 1 URL
curl -s https://your-domain.com/admin/profile.php \
  | grep -E "Fatal error|Parse error|Warning:|Notice:|Deprecated:|Uncaught" \
  && echo "!!! TRANG CÓ LỖI PHP" || echo "OK (không thấy chuỗi lỗi)"
```

> Lỗi `Call to undefined method Csrf::token()` trong `admin/profile.php` từng lọt qua nhiều vòng
> kiểm thử đúng vì lý do này: trang vẫn trả `200`, chỉ là khối 5 (upload CV) không bao giờ hiện ra.

---

## 🔤 10. Ghi chú về font self-hosted (`public/assets/fonts/`)

Trang public dùng 3 font local: **JetBrains Mono** (400/500/600), **Plus Jakarta Sans**
(400/500/600/700), **Space Grotesk** (chỉ 700 — font này KHÔNG có weight 800).
Mỗi weight có 2 subset: **Vietnamese** + **Latin**.

- **Đã lược bỏ subset Latin-ext** (ā ē š ž ł ć ř ... dành cho Ba Lan / Séc / Thổ Nhĩ Kỳ).
  Đã kiểm chứng toàn bộ text trang public + dữ liệu DB hiện tại không có ký tự nào chỉ thuộc
  dải Latin mở rộng (`tests/font_latinext_audit.php`).
- ⚠️ **Nếu sau này nội dung có ký tự Latin mở rộng** (tên công ty / thuật ngữ nước ngoài như
  *Škoda*, *Nestlé* dùng é thì OK vì é ∈ Latin, nhưng *Łódź*, *Beyoncé*→ không, *Citroën* OK...),
  cụ thể là các ký tự trong `U+0100–017F` (trừ ký tự tiếng Việt) hoặc `U+1E00–1E9F`, thì phải
  **nạp lại subset Latin-ext**: tải 3 file `*-<hash latin-ext>.woff2` cho mỗi weight đang dùng và
  thêm lại khối `@font-face` với `unicode-range` latin-ext vào `fonts.css`. Chạy lại
  `tests/font_latinext_audit.php` để biết ký tự nào đang thiếu.
- Nếu cần đổi/bổ sung weight: file tĩnh lấy từ https://fonts.google.com hoặc
  `@fontsource/*`, đặt vào `public/assets/fonts/` và khai báo `@font-face` trong `fonts.css`.

## 📤 11. Xuất trang tĩnh lên Netlify (repo `portfolio-cv`)

Trang CV công khai cho nhà tuyển dụng chạy trên Netlify, lấy từ repo
https://github.com/HienPhuong99/portfolio-cv (Netlify tự deploy khi có push). Netlify không chạy
PHP/MySQL nên trang được **xuất tĩnh** từ dữ liệu hiện có trong Database:

```bash
php tools/export-static.php ../portfolio-cv
```

- Cần MySQL đang chạy. Script ghi `index.html`, `assets/` và `CV-Hien-Phuong.pdf` vào thư mục đích,
  không động tới các file khác (`chon-mau.html`, `templates/`, `netlify.toml`).
- Bản tĩnh không có form liên hệ; nút "Tải CV" tải thẳng file PDF.
- CV PDF dựng từ `tools/cv/cv.html`. Sửa nội dung ở đó rồi in lại (Edge/Chrome headless):
  `msedge --headless=new --no-pdf-header-footer --print-to-pdf=tools/cv/CV-Hien-Phuong.pdf tools/cv/cv.html`
