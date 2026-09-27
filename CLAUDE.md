# CV Liquid Glass — ghi chú cho Claude

CV cá nhân (PHP thuần + MySQL, có trang Admin). Chủ dự án giao tiếp bằng tiếng Việt.

## Trang CV công khai = bản xuất tĩnh trên Netlify

- Nhà tuyển dụng xem CV tại site Netlify của repo **https://github.com/HienPhuong99/portfolio-cv**
  (Netlify tự deploy khi push `main`, publish = thư mục gốc). Netlify không chạy PHP/MySQL nên
  trang được xuất tĩnh bằng `tools/export-static.php`.
- Quy tắc đã chốt với chủ dự án:
  - `index.html` gốc của portfolio-cv = Mẫu 5 xuất từ dự án này, hiện ngay khi vào (không có trang chọn mẫu).
    Trang chọn mẫu cũ ở `chon-mau.html`; `templates/` giữ nguyên, không link tới.
  - Bản tĩnh không có form liên hệ, không có Admin. Nút "Tải CV" tải thẳng `CV-Hien-Phuong.pdf`.
  - **Chỉ cập nhật Netlify khi chủ dự án yêu cầu** (vd. "cập nhật CV lên Netlify", `/cap-nhat-cv`).
    Sửa giao diện ở repo này KHÔNG tự đồng bộ lên Netlify.
  - Được tự commit + push lên portfolio-cv sau khi đã kiểm tra bản xuất trên trình duyệt.
  - CV PDF (`tools/cv/cv.html` → `tools/cv/CV-Hien-Phuong.pdf`): nội dung giữ nguyên từng chữ trừ khi
    được yêu cầu sửa; mọi thay đổi PDF phải gửi bản xuất cho chủ dự án duyệt TRƯỚC khi đẩy lên.
- Quy trình từng bước: skill `.claude/skills/cap-nhat-cv/SKILL.md`.

## Dữ liệu

- Nội dung CV nằm trong MySQL. `database/current.sql` là bản sao nội dung (không có tài khoản Admin,
  tin nhắn, log) để máy khác / phiên cloud dựng lại DB: `php tools/data-snapshot.php restore`.
  Sinh tự động mỗi lần chạy `export-static.php`; không sửa tay.
- `config/config.php` bị .gitignore — máy mới: `cp config/config.example.php config/config.php`
  (đọc DB_HOST/DB_USER/DB_PASS/DB_NAME từ biến môi trường).
