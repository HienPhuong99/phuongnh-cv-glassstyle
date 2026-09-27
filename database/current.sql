-- Bản sao nội dung CV — sinh tự động bởi tools/data-snapshot.php dump. KHÔNG sửa tay.
-- Nạp lại: php tools/data-snapshot.php restore

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- settings
DELETE FROM `settings`;
INSERT INTO `settings` (`key`, `value`) VALUES
('author', 'Hiền Phương'),
('content_changed_at', '2026-09-02 14:00:00'),
('default_clear', '45'),
('default_theme', 'auto'),
('footer_text', 'Giao diện Liquid Glass · All rights reserved &copy; 2026.'),
('footer_title', 'HIỀN PHƯƠNG — Nhân Viên Kinh Doanh'),
('google_analytics_id', ''),
('meta_description', 'Portfolio & CV cá nhân của Hiền Phương - Nhân Viên Kinh Doanh với hơn 3 năm kinh nghiệm thực chiến tư vấn bán hàng đa kênh, mở rộng tệp khách hàng B2B/B2C và quản lý đơn hàng sàn TMĐT.'),
('monogram', 'HP'),
('site_title', 'Hiền Phương — Nhân Viên Kinh Doanh | Liquid Glass Portfolio & CV');

-- profile
DELETE FROM `profile`;
INSERT INTO `profile` (`id`, `full_name`, `job_title`, `status_text`, `pre_title`, `tagline`, `avatar`, `short_meta`, `email`, `phone`, `phone_display`, `zalo_url`, `address`, `about_quote`, `about_subtext`, `commitment_1_title`, `commitment_1_desc`, `commitment_2_title`, `commitment_2_desc`, `contact_heading`, `contact_subtext`, `cv_pdf_file`, `cv_original_name`, `cv_uploaded_at`, `cv_size`, `updated_at`) VALUES
('1', 'HIỀN PHƯƠNG', 'Nhân Viên Kinh Doanh', 'Sẵn sàng nhận việc', 'Hello, I\'m', 'Nhân viên kinh doanh với hơn 3 năm kinh nghiệm thực chiến tư vấn bán hàng đa kênh, mở rộng tệp khách hàng tiềm năng (B2B/B2C) và quản lý đơn hàng trên sàn thương mại điện tử.', 'assets/hien-phuong.png', '3+ năm · TP. Thủ Đức', 'hphuong123123@gmail.com', '0876488047', '087 6488 047', 'https://zalo.me/0876488047', 'Trường Thọ, TP. Thủ Đức, TP.HCM', '“Trong vòng 3 tháng đầu làm việc, tôi đặt mục tiêu trở thành nhân viên chính thức bằng cách đạt doanh số tối thiểu công ty đề ra. Đồng thời, tôi sẽ mở rộng mạng lưới khách hàng tiềm năng bằng cách tiếp cận và thiết lập mối quan hệ với ít nhất 10 khách hàng mới mỗi tháng.”', 'Tôi cũng chủ động tìm hiểu và áp dụng các kỹ năng bán hàng hiệu quả để nâng cao năng suất và đạt được kết quả tốt nhất trong công việc.', 'Cam kết chỉ tiêu', 'Bám sát KPI doanh số và tiến độ công việc', 'Mở rộng tệp khách hàng', 'Thiết lập quan hệ với 10+ đối tác mới/tháng', 'Sẵn sàng đồng hành cùng doanh nghiệp đạt mục tiêu doanh số.', 'Quý nhà tuyển dụng có thể liên hệ trực tiếp với tôi qua các kênh bên dưới để trao đổi chi tiết hơn về cơ hội hợp tác.', NULL, NULL, NULL, NULL, '2026-09-27 13:35:52');

-- sections
DELETE FROM `sections`;
INSERT INTO `sections` (`id`, `key`, `dock_label`, `badge_code`, `title`, `subtitle`, `is_visible`, `open_on_load`, `sort_order`) VALUES
('1', 'about', 'Về tôi', '01 // MỤC TIÊU NGHỀ NGHIỆP', 'Về tôi', 'Kế hoạch hành động', '1', '1', '10'),
('2', 'skills', 'Kỹ năng', '02 // NĂNG LỰC CHUYÊN MÔN', 'Kỹ năng cốt lõi', 'Bộ kỹ năng tư vấn, đàm phán và vận hành kinh doanh thực tế.', '1', '1', '20'),
('3', 'experience', 'Kinh nghiệm', '03 // HÀNH TRÌNH THỰC CHIẾN', 'Kinh nghiệm làm việc', '', '1', '1', '30'),
('4', 'strengths', 'Điểm mạnh', '04 // PHẨM CHẤT NỔI BẬT', 'Điểm mạnh & Kỷ luật', '', '1', '0', '40'),
('5', 'weaknesses', 'Cải thiện', '', 'Điểm cần cải thiện', '', '0', '0', '50'),
('6', 'education', 'Học vấn', '05 // NỀN TẢNG & CÔNG CỤ', 'Học vấn & Công cụ', '', '1', '0', '60'),
('7', 'contact', 'Liên hệ', '', 'Liên hệ', '', '1', '0', '70');

-- key_stats
DELETE FROM `key_stats`;
INSERT INTO `key_stats` (`id`, `value`, `label`, `subtext`, `is_active`, `sort_order`) VALUES
('1', '3+', 'Năm thực chiến', 'Kinh doanh & Tư vấn bán hàng', '1', '1'),
('2', '10+', 'KH mới / tháng', 'Mục tiêu phát triển tệp khách hàng', '1', '2'),
('3', '100%', 'Cam kết KPI', 'Đạt định mức trong 3 tháng đầu', '1', '3'),
('4', 'Đa kênh', 'B2B, Social & TMĐT', 'Khai thác khách hàng đa nền tảng', '1', '4');

-- skills
DELETE FROM `skills`;
INSERT INTO `skills` (`id`, `title`, `description`, `tags`, `is_active`, `sort_order`) VALUES
('1', 'Kỹ Năng Giao Tiếp', 'Truyền đạt thông tin rõ ràng, mạch lạc; chủ động lắng nghe và thấu hiểu sâu sắc mong muốn của khách hàng.', '[\"Lắng nghe chủ động\", \"Giao tiếp đa kênh\", \"Thấu cảm\"]', '1', '1'),
('2', 'Tư Vấn & Thuyết Phục', 'Nắm vững kiến thức sản phẩm/dịch vụ, đưa ra giải pháp tối ưu phù hợp với từng nhu cầu và ngân sách của khách hàng.', '[\"Tư vấn giải pháp\", \"Đàm phán báo giá\", \"Chốt đơn\"]', '1', '2'),
('3', 'Tìm Kiếm Khách Hàng', 'Hiểu rõ thị trường mục tiêu, xác định tệp khách hàng tiềm năng và khai thác qua Social, Trang Vàng, Google Maps.', '[\"Trang Vàng B2B\", \"Google Maps\", \"Social Media\"]', '1', '3'),
('4', 'Vận Hành & Đơn Hàng', 'Cập nhật thông tin sản phẩm, quản lý đơn hàng Shopee/Lazada, theo dõi công nợ và chăm sóc khách hàng sau bán.', '[\"Shopee / Lazada\", \"Thu hồi công nợ\", \"After-sales\"]', '1', '4');

-- strengths
DELETE FROM `strengths`;
INSERT INTO `strengths` (`id`, `title`, `description`, `is_active`, `sort_order`) VALUES
('1', 'Chịu Áp Lực Cao', 'Khả năng làm việc tốt dưới áp lực chỉ tiêu doanh số và tiến độ công việc, duy trì năng lượng tích cực và sự tập trung cao độ.', '1', '1'),
('2', 'Kiên Nhẫn & Bền Bỉ', 'Chủ động theo sát, chăm sóc và nuôi dưỡng tệp khách hàng tiềm năng, không nản lòng trước các thương vụ kéo dài.', '1', '2'),
('3', 'Tinh Thần Tự Học Hỏi', 'Nhanh nhạy cập nhật kiến thức sản phẩm mới, tiếp thu nhanh các công cụ số và không ngừng trau dồi kỹ năng chuyên môn.', '1', '3');

-- weaknesses
DELETE FROM `weaknesses`;

-- experiences
DELETE FROM `experiences`;
INSERT INTO `experiences` (`id`, `position`, `company`, `period_text`, `description`, `tags`, `dot_color`, `is_active`, `sort_order`) VALUES
('1', 'Nhân Viên Kinh Doanh', 'Công Ty TNHH Kinh Doanh Siêu Việt', '04/2021 — 12/2024 (3 năm 8 tháng)', 'Chủ động tìm kiếm, mở rộng tệp khách hàng tiềm năng qua các nền tảng mạng xã hội, e-mail,…\nTư vấn bán hàng, giải đáp các thắc mắc và khiếu nại của khách hàng về sản phẩm/dịch vụ qua Zalo, Facebook, Hotline.\nCập nhật thông tin sản phẩm và quản lý đơn hàng trên website cùng các sàn thương mại điện tử (Shopee, Lazada).\nLập báo cáo định kỳ theo tháng về doanh số bán hàng và tình hình kinh doanh.\nSoạn thảo hợp đồng và làm báo giá cho thuê máy photocopy theo mẫu có sẵn.\nThiết kế hình ảnh, tem dán mừng các dịp lễ/tết và chương trình ưu đãi theo mẫu trên Canva.', '[\"Tư vấn B2B\", \"Sàn Shopee/Lazada\", \"Hợp đồng kinh tế\", \"Canva Design\"]', '#2f7fb5', '1', '1'),
('2', 'Nhân Viên Bán Hàng — Trực Page', 'Hộ Kinh Doanh 79 Store', '03/2025 — 07/2025 (5 tháng)', 'Trực page, tư vấn bán hàng, giải đáp thắc mắc và khiếu nại của khách hàng qua Facebook, Zalo.\nTheo dõi, nhắc nhở và thu hồi công nợ đối với khách hàng mua trả góp đúng hạn.\nChăm sóc khách hàng sau bán hàng nhằm duy trì mối quan hệ và tăng tỷ lệ khách hàng quay lại.', '[\"Trực Page FB/Zalo\", \"Quản lý công nợ\", \"Chăm sóc sau bán\"]', '#3aa7a0', '1', '2'),
('3', 'Nhân Viên Kinh Doanh (Thử Việc)', 'Công ty TNHH Mr.Rin Group', '08/2025 — 09/2025', 'Chăm sóc, chào hàng qua tin nhắn tệp khách hàng tiềm năng theo data công ty bàn giao.\nChủ động học hỏi, nắm bắt đặc tính kỹ thuật sản phẩm và nhận biết chính xác mã hàng.', '[\"Xử lý Data nóng\", \"Nắm bắt thông số kỹ thuật\"]', '#7c6fd0', '1', '3'),
('4', 'Nhân Viên Kinh Doanh (Thử Việc)', 'Công ty TNHH TM Điện Thái Dương — Thadeco', '09/2025 — 11/2025', 'Tìm kiếm và khai thác danh sách khách hàng doanh nghiệp qua Trang Vàng, Google Maps.\nTư vấn giải pháp sản phẩm và gửi báo giá chi tiết cho khách hàng qua Zalo.', '[\"Trang Vàng B2B\", \"Google Maps\", \"Báo giá Zalo\"]', '#d98a4e', '1', '4');

-- educations
DELETE FROM `educations`;
INSERT INTO `educations` (`id`, `school`, `degree`, `major`, `period_text`, `description`, `footer_text`, `is_active`, `sort_order`) VALUES
('1', 'Cao Đẳng Thực Hành', '', 'IT - Phần Mềm • Lập trình di động', '07/2017 — 09/2020', 'Trang bị tư duy logic vững vàng, khả năng tiếp cận và ứng dụng nhanh chóng các công nghệ, phần mềm quản lý kinh doanh, quản trị sàn TMĐT và nền tảng số hiện đại.', 'Tư duy logic • Khả năng tiếp cận công nghệ số nhanh', '1', '1');

-- tools
DELETE FROM `tools`;
INSERT INTO `tools` (`id`, `name`, `description`, `is_active`, `sort_order`) VALUES
('1', 'Canva Pro', 'Thiết kế hình ảnh, tem dán mừng các dịp lễ/tết và banner chương trình ưu đãi bán hàng.', '1', '1'),
('2', 'Shopee & Lazada', 'Quản trị thông tin sản phẩm, xử lý đơn hàng và theo dõi vận chuyển sàn thương mại điện tử.', '1', '2'),
('3', 'MS Excel & Word', 'Lập báo cáo doanh số, soạn thảo hợp đồng kinh tế và làm báo giá chi tiết cho khách hàng.', '1', '3'),
('4', 'Zalo OA & Fanpage', 'Trực page, tư vấn khách hàng, giải đáp thắc mắc và chăm sóc khách hàng sau bán hàng.', '1', '4');

SET FOREIGN_KEY_CHECKS = 1;
