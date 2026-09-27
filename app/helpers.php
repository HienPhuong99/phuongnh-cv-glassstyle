<?php
/**
 * File helpers dùng chung cho toàn bộ dự án
 */

/**
 * Escape XSS an toàn cho chuỗi HTML
 */
function e(?string $string): string {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Lấy địa chỉ IP của Client an toàn
 * Mặc định dùng REMOTE_ADDR, chỉ đọc X-Forwarded-For khi hằng TRUST_PROXY được bật (true)
 */
function client_ip(): string {
    if (defined('TRUST_PROXY') && TRUST_PROXY === true) {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }
    return '0.0.0.0';
}

/**
 * Chuyển chuỗi tiếng Việt thành slug an toàn (Ví dụ: "HIỀN PHƯƠNG" -> "Hien-Phuong")
 */
function slugify_vi(string $str): string {
    $str = mb_convert_case(trim($str), MB_CASE_TITLE, 'UTF-8');
    $map = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'À'=>'A','Á'=>'A','Ạ'=>'A','Ả'=>'A','Ã'=>'A','Â'=>'A','Ầ'=>'A','Ấ'=>'A','Ậ'=>'A','Ẩ'=>'A','Ẫ'=>'A','Ă'=>'A','Ằ'=>'A','Ắ'=>'A','Ặ'=>'A','Ẳ'=>'A','Ẵ'=>'A',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'È'=>'E','É'=>'E','Ẹ'=>'E','Ẻ'=>'E','Ẽ'=>'E','Ê'=>'E','Ề'=>'E','Ế'=>'E','Ệ'=>'E','Ể'=>'E','Ễ'=>'E',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'Ì'=>'I','Í'=>'I','Ị'=>'I','Ỉ'=>'I','Ĩ'=>'I',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'Ò'=>'O','Ó'=>'O','Ọ'=>'O','Ỏ'=>'O','Õ'=>'O','Ô'=>'O','Ồ'=>'O','Ố'=>'O','Ộ'=>'O','Ổ'=>'O','Ỗ'=>'O','Ơ'=>'O','Ờ'=>'O','Ớ'=>'O','Ợ'=>'O','Ở'=>'O','Ỡ'=>'O',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'Ù'=>'U','Ú'=>'U','Ụ'=>'U','Ủ'=>'U','Ũ'=>'U','Ư'=>'U','Ừ'=>'U','Ứ'=>'U','Ự'=>'U','Ử'=>'U','Ữ'=>'U',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
        'Ỳ'=>'Y','Ý'=>'Y','Ỵ'=>'Y','Ỷ'=>'Y','Ỹ'=>'Y',
        'đ'=>'d','Đ'=>'D'
    ];
    $str = strtr($str, $map);
    $str = preg_replace('/[^A-Za-z0-9]+/', '-', $str);
    return trim($str, '-');
}



/**
 * Xây dựng đường dẫn URL đầy đủ
 */
function url(string $path = ''): string {
    $baseUrl = defined('BASE_URL') && BASE_URL !== '' ? rtrim(BASE_URL, '/') : '';
    
    if ($baseUrl === '') {
        // Tự động nhận diện base url tương đối từ SCRIPT_NAME
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        
        // 1. Cắt bỏ hậu tố /admin, /admin/ajax, /admin/partials
        $scriptDir = preg_replace('~/admin(/ajax|/partials)?$~', '', $scriptDir);
        
        // 2. Cắt tiếp hậu tố /public nếu có (đây là hành vi cố ý để xử lý trường hợp
        // document root đặt sai ở thư mục gốc project thay vì trỏ vào public/).
        // CẢNH BÁO: Logic này sẽ cắt nhầm nếu website thực sự được đặt trong thư mục con tên là "public".
        $scriptDir = preg_replace('~/public$~', '', $scriptDir);
        
        $baseUrl = rtrim($scriptDir, '/');
    }

    $cleanPath = ltrim($path, '/');
    return ($baseUrl === '' ? '' : $baseUrl) . ($cleanPath !== '' ? '/' . $cleanPath : '');
}

/**
 * Đường dẫn tới asset tĩnh (tự động loại bỏ tiền tố assets/ nếu người gọi truyền thừa)
 */
function asset(string $path = ''): string {
    $cleanPath = ltrim($path, '/');
    if (str_starts_with($cleanPath, 'assets/')) {
        $cleanPath = substr($cleanPath, 7);
    }
    return url('assets/' . ltrim($cleanPath, '/'));
}

/**
 * Đường dẫn tới file upload (fallback ảnh mặc định hien-phuong.png chuẩn xác)
 */
function upload_url(?string $path = ''): string {
    if (empty($path)) {
        return asset('hien-phuong.png');
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    if (str_starts_with($path, 'assets/')) {
        return url($path);
    }
    return url(ltrim($path, '/'));
}


/**
 * Chuyển hướng trang an toàn
 */
function redirect(string $path): void {
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . url($path));
    }
    exit;
}

/**
 * Flash message (lấy hoặc đặt thông báo trong session)
 */
function flash(string $type, ?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['_flash'][$type] = $message;
        return null;
    }

    if (!empty($_SESSION['_flash'][$type])) {
        $msg = $_SESSION['_flash'][$type];
        unset($_SESSION['_flash'][$type]);
        return $msg;
    }

    return null;
}

/**
 * Hiển thị khối HTML Flash Message (cho Admin)
 */
function render_flash(): string {
    $html = '';
    if (!empty($_SESSION['_flash'])) {
        foreach ($_SESSION['_flash'] as $type => $msg) {
            $alertClass = match ($type) {
                'success'          => 'alert-success',
                'error', 'error_html' => 'alert-danger',
                'warning'          => 'alert-warning',
                default            => 'alert-info'
            };
            $icon = match ($type) {
                'success'          => '<i class="bi bi-check-circle-fill me-2"></i>',
                'error', 'error_html' => '<i class="bi bi-exclamation-triangle-fill me-2"></i>',
                'warning'          => '<i class="bi bi-exclamation-circle-fill me-2"></i>',
                default            => '<i class="bi bi-info-circle-fill me-2"></i>'
            };
            // Mọi message đều được escape toàn bộ bằng htmlspecialchars trước.
            // Riêng loại 'error_html': KHÔNG chạy qua bộ lọc regex nào (không đáng tin nếu
            // sau này có nội dung từ input người dùng). Chỉ cho phép ĐÚNG một placeholder
            // cứng là "{cv_link}", được thay bằng một thẻ <a> cố định. Không có cách nào
            // nhúng HTML tuỳ ý qua flash, kể cả vô tình.
            $escaped = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            if ($type === 'error_html') {
                $escaped = str_replace(
                    '{cv_link}',
                    '<a href="#cv-upload-block">File CV đính kèm</a>',
                    $escaped
                );
            }
            $html .= "<div class=\"alert {$alertClass} alert-dismissible fade show shadow-sm\" role=\"alert\">
                {$icon}{$escaped}
                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Đóng\"></button>
            </div>";
        }
        unset($_SESSION['_flash']);
    }
    return $html;
}

/**
 * Lưu dữ liệu form cũ vào session khi validate thất bại
 */
function set_old(array $data): void {
    $_SESSION['_old_input'] = $data;
}

/**
 * Lấy dữ liệu form cũ
 */
function old(string $key, $default = '') {
    return $_SESSION['_old_input'][$key] ?? $default;
}

/**
 * Xóa dữ liệu form cũ
 */
function clear_old(): void {
    unset($_SESSION['_old_input']);
}

/**
 * Làm sạch chuỗi HTML chỉ cho phép các tag an toàn cơ bản
 */
function sanitize_html(?string $html): string {
    if (empty($html)) {
        return '';
    }

    // Danh sách tag cho phép
    $allowedTags = '<p><br><b><strong><i><em><u><span><ul><ol><li><a><blockquote>';
    $clean = strip_tags($html, $allowedTags);

    // Loại bỏ các thuộc tính nguy hiểm (onload, onclick, javascript:...)
    $clean = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);
    $clean = preg_replace('/href\s*=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $clean);

    return $clean;
}

/**
 * Đánh dấu thời điểm nội dung CV được chỉnh sửa mới nhất
 */
function touch_content_changed(): void {
    try {
        Database::query(
            "INSERT INTO `settings` (`key`, `value`) VALUES ('content_changed_at', NOW()) 
             ON DUPLICATE KEY UPDATE `value` = NOW()"
        );
    } catch (\Throwable $e) {
        error_log('touch_content_changed error: ' . $e->getMessage());
    }
}

/**
 * Kiểm tra xem bản PDF của CV có bị cũ (lỗi thời) so với nội dung chỉnh sửa hay không
 */
function is_cv_stale(): bool {
    $status = get_cv_status();
    return $status['is_stale'];
}

/**
 * Lấy trạng thái đồng bộ chi tiết của file CV so với nội dung trên CMS
 */
function get_cv_status(): array {
    static $status = null;
    if ($status !== null) {
        return $status;
    }

    $profile = Repository::getProfile();
    $hasCv = !empty($profile['cv_pdf_file']);
    $cvUploadedAt = $profile['cv_uploaded_at'] ?? null;

    $row = Database::fetch("SELECT `value` FROM `settings` WHERE `key` = 'content_changed_at'");
    $contentChangedAt = $row['value'] ?? null;

    $isStale = false;
    if ($hasCv && $contentChangedAt && $cvUploadedAt) {
        $isStale = (strtotime($contentChangedAt) > strtotime($cvUploadedAt));
    }

    $status = [
        'has_cv'             => $hasCv,
        'cv_file'            => $profile['cv_pdf_file'] ?? null,
        'cv_original_name'   => $profile['cv_original_name'] ?? null,
        'cv_uploaded_at'     => $cvUploadedAt,
        'content_changed_at' => $contentChangedAt,
        'is_stale'           => $isStale
    ];

    return $status;
}

/**
 * Sinh mã băm ẩn danh cho khách truy cập (dùng chung cho toàn bộ phần theo dõi).
 * Có thêm date('Y-m-d') nên hash đổi mới mỗi ngày => không truy vết được người dùng
 * qua nhiều ngày. Không bao giờ lưu IP thô.
 */
function visitor_hash(): string {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return hash('sha256', client_ip() . $userAgent . date('Y-m-d') . APP_KEY);
}

/**
 * Nhận diện loại thiết bị từ user-agent: 'desktop' | 'mobile' | 'tablet'
 */
function detect_device(): string {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if ($ua === '') {
        return 'desktop';
    }

    // Tablet phải kiểm tra trước: iPad, Android không kèm "Mobile", Kindle Silk, PlayBook...
    if (preg_match('/ipad|tablet|playbook|silk|kindle|(android(?!.*mobile))/i', $ua)) {
        return 'tablet';
    }

    if (preg_match('/mobile|iphone|ipod|android|blackberry|iemobile|opera mini|windows phone/i', $ua)) {
        return 'mobile';
    }

    return 'desktop';
}

/**
 * Nhận diện bot / trình thu thập tự động (dùng chung: cv-download + theo dõi truy cập).
 * User-agent rỗng coi như bot. KHÔNG khớp Zalo / trình duyệt in-app của nhà tuyển dụng.
 */
function is_bot(?string $userAgent = null): bool {
    $ua = $userAgent ?? ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if (trim($ua) === '') {
        return true;
    }

    return (bool) preg_match(
        '~bot|crawler|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegram|headless'
        . '|python-requests|curl|wget|java/|go-http|axios|lighthouse|pagespeed|gtmetrix'
        . '|ahrefs|semrush|mj12|dotbot|petalbot~i',
        $ua
    );
}


/**
 * Chuẩn hóa mã màu hex (#RGB / #RRGGBB). Trả về chuỗi rỗng nếu không hợp lệ (= dùng màu tự động).
 */
function normalize_hex_color(?string $color): string {
    $color = trim((string)$color);
    if ($color === '') {
        return '';
    }
    if ($color[0] !== '#') {
        $color = '#' . $color;
    }
    return preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color) ? strtolower($color) : '';
}
