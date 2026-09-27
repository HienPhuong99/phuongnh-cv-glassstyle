-- ==============================================================================
-- CHUYỂN DỮ LIỆU TỪ CMS MẪU 4 (cv_mau4_developer) SANG CV LIQUID GLASS
--
-- Dùng khi bạn đã có dữ liệu thật ở CMS Mẫu 4 và muốn giữ lại nội dung.
-- CÁCH DÙNG (an toàn, không đụng tới DB cũ):
--   1. Tạo DB mới:  CREATE DATABASE cv_liquid_glass CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--   2. Sao chép DB cũ sang DB mới (phpMyAdmin: Operations → Copy database to,
--      hoặc: mysqldump cv_mau4_developer | mysql cv_liquid_glass)
--   3. Chọn DB mới rồi chạy file này (tab SQL trong phpMyAdmin, hoặc
--      mysql --default-character-set=utf8mb4 cv_liquid_glass -e "source database/migration-from-mau4.sql")
-- Chỉ chạy MỘT LẦN trên bản sao. KHÔNG chạy trên DB cũ đang dùng cho website Mẫu 4.
-- ==============================================================================

SET NAMES utf8mb4;

-- 1. HỒ SƠ ------------------------------------------------------------------
-- "SẴN SÀNG NHẬN VIỆC • TP. THỦ ĐỨC" -> "Sẵn sàng nhận việc" (chỉ giữ phần trước dấu •)
UPDATE `profile`
   SET `status_badge` = CONCAT(
         UPPER(LEFT(TRIM(SUBSTRING_INDEX(`status_badge`, '•', 1)), 1)),
         LOWER(SUBSTRING(TRIM(SUBSTRING_INDEX(`status_badge`, '•', 1)), 2))
       )
 WHERE `status_badge` IS NOT NULL AND `status_badge` <> '';

ALTER TABLE `profile`
  CHANGE `status_badge` `status_text` VARCHAR(60) DEFAULT 'Sẵn sàng nhận việc',
  CHANGE `floating_badge_subtitle` `short_meta` VARCHAR(100) NULL,
  DROP COLUMN `floating_badge_title`;

-- 2. CỬA SỔ & DOCK (sections) ---------------------------------------------------
-- Mẫu 5 gộp "hero" vào cửa sổ "Về tôi"
DELETE FROM `sections` WHERE `key` = 'hero';

ALTER TABLE `sections`
  ADD COLUMN `dock_label` VARCHAR(30) NOT NULL DEFAULT '' AFTER `key`,
  ADD COLUMN `open_on_load` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_visible`;

UPDATE `sections` SET `dock_label` = CASE `key`
    WHEN 'about'      THEN 'Về tôi'
    WHEN 'skills'     THEN 'Kỹ năng'
    WHEN 'strengths'  THEN 'Điểm mạnh'
    WHEN 'weaknesses' THEN 'Cải thiện'
    WHEN 'experience' THEN 'Kinh nghiệm'
    WHEN 'education'  THEN 'Học vấn & Công cụ'
    WHEN 'contact'    THEN 'Liên hệ'
    ELSE LEFT(`title`, 30)
  END;

UPDATE `sections` SET `open_on_load` = 1 WHERE `key` IN ('about', 'skills', 'experience');
UPDATE `sections` SET `title` = 'Về tôi' WHERE `key` = 'about';

-- 3. BỎ CÁC CỘT ICON / MÀU KHÔNG DÙNG Ở MẪU 5 ---------------------------------------
ALTER TABLE `key_stats` DROP COLUMN `accent_color`;
ALTER TABLE `skills`    DROP COLUMN `icon`, DROP COLUMN `accent_color`;
ALTER TABLE `strengths` DROP COLUMN `icon`, DROP COLUMN `accent_color`;
ALTER TABLE `tools`     DROP COLUMN `icon`, DROP COLUMN `accent_color`;

-- 4. KINH NGHIỆM: màu nhấn cũ -> màu chấm timeline (#RRGGBB) ----------------------
UPDATE `experiences` SET `accent_color` = CASE `accent_color`
    WHEN 'gold'       THEN '#2f7fb5'
    WHEN 'terracotta' THEN '#3aa7a0'
    WHEN 'goldHover'  THEN '#7c6fd0'
    WHEN 'bronze'     THEN '#d98a4e'
    ELSE NULL
  END;
ALTER TABLE `experiences` CHANGE `accent_color` `dot_color` VARCHAR(7) NULL;

-- 5. CÀI ĐẶT ------------------------------------------------------------------
DELETE FROM `settings` WHERE `key` = 'theme_color';
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  ('default_theme', 'auto'),
  ('default_clear', '45');
