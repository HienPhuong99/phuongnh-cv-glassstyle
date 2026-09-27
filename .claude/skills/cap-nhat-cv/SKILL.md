---
name: cap-nhat-cv
description: Cập nhật trang CV công khai trên Netlify (repo HienPhuong99/portfolio-cv) từ dữ liệu của dự án này — xuất trang tĩnh, kiểm tra, push. Dùng khi chủ dự án nói "cập nhật CV", "cập nhật CV lên Netlify", "đẩy CV lên", hoặc yêu cầu sửa file CV PDF.
---

# Cập nhật CV lên Netlify

Đọc quy tắc trong `CLAUDE.md` trước. Tóm tắt: chỉ chạy khi được yêu cầu; được tự push portfolio-cv
sau khi kiểm tra; sửa PDF thì phải gửi bản xuất cho chủ dự án duyệt trước.

## 1. Chuẩn bị Database

- Chạy `git pull` ở repo này.
- Có sẵn DB với dữ liệu thật (máy nhà của chủ dự án, MySQL qua XAMPP/Laragon):
  - Chạy `php tools/data-snapshot.php dump` rồi `git diff database/current.sql`.
    - Không khác → DB và repo khớp, sang bước 2.
    - Khác → DB (có thể vừa sửa qua Admin) và bản sao trong repo (có thể vừa sửa từ máy khác)
      lệch nhau. **Hỏi chủ dự án bản nào mới hơn** trước khi làm tiếp. Nếu bản trong repo mới hơn:
      `git checkout database/current.sql && php tools/data-snapshot.php restore`.
- Chưa có DB (máy mới / phiên cloud):
  - `cp config/config.example.php config/config.php` (nếu chưa có).
  - Cài + bật MySQL/MariaDB (Linux: `apt-get install -y mariadb-server && service mariadb start`;
    đặt DB_USER/DB_PASS qua biến môi trường nếu không dùng root không mật khẩu).
  - `php tools/data-snapshot.php restore` — tạo DB, bảng, nạp nội dung từ `database/current.sql`.
- Muốn sửa nội dung CV trên trang web mà không có Admin: sửa trực tiếp trong DB (SQL), không sửa tay
  `current.sql` — bước xuất sẽ tự sinh lại file này.

## 2. (Chỉ khi được yêu cầu) Sửa CV PDF

- Sửa `tools/cv/cv.html` đúng phần được yêu cầu; không đổi chữ nào khác.
- In ra PDF, phải vừa **1 trang A4**:
  - Windows: `"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" --headless=new --no-pdf-header-footer --print-to-pdf=<đường dẫn tuyệt đối>\tools\cv\CV-Hien-Phuong.pdf file:///<đường dẫn tuyệt đối>/tools/cv/cv.html`
  - Linux/cloud: `chromium --headless=new --no-pdf-header-footer --print-to-pdf=tools/cv/CV-Hien-Phuong.pdf file://$PWD/tools/cv/cv.html`
  - Đếm trang: số lần khớp `/Type /Page` (không tính `/Pages`) phải là 1.
- **Gửi file PDF cho chủ dự án duyệt, dừng lại chờ đồng ý** rồi mới sang bước 3.

## 3. Xuất trang tĩnh

- Clone/pull portfolio-cv vào thư mục tạm (scratchpad):
  `git clone https://github.com/HienPhuong99/portfolio-cv` (hoặc `git -C <dir> pull`).
- `php tools/export-static.php <thư mục portfolio-cv>` — ghi `index.html`, `assets/`,
  `CV-Hien-Phuong.pdf`; đồng thời cập nhật `database/current.sql` ở repo này.
  Không động tới `chon-mau.html`, `templates/`, `netlify.toml`.

## 4. Kiểm tra trước khi push

- Phục vụ thư mục portfolio-cv bằng server tĩnh (vd. `php -S localhost:8001 -t <dir>`) và mở trình duyệt:
  - Mobile 375px và desktop ≥1280px, cả chế độ sáng/tối: trang hiện Mẫu 5 ngay, icon/dock hoạt động.
  - Không lỗi console; `fetch('/CV-Hien-Phuong.pdf')` trả 200 `application/pdf`; sheet Liên hệ không có form.
- `git -C <dir> status` chỉ nên thay đổi các file ở trên.

## 5. Đăng lên

- portfolio-cv: commit (mô tả ngắn gọn thay đổi, tiếng Việt) và `git push origin main` → Netlify tự deploy.
- Repo này: commit `database/current.sql` (+ `database/snapshot-uploads/`, `tools/cv/` nếu có đổi)
  lên `main` và push, để lần sau máy khác có đúng dữ liệu vừa đăng.
- Báo lại cho chủ dự án: đã đổi gì, commit nào, và nhắc Netlify cập nhật sau 1–2 phút.
