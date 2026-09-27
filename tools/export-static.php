<?php
/**
 * XUẤT TRANG TĨNH (cho Netlify / repo portfolio-cv)
 *
 *   php tools/export-static.php [thư-mục-đích]     (mặc định: dist/)
 *
 * Render public/index.php với dữ liệu hiện có trong Database (cần MySQL đang chạy),
 * ở chế độ STATIC_EXPORT: bỏ form liên hệ, nút "Tải CV" tải thẳng file PDF.
 * Ghi ra: index.html, assets/ (CSS, JS, font, ảnh), file CV PDF và mọi file
 * uploads/ mà trang tham chiếu. Không động tới các file khác trong thư mục đích.
 */

define('STATIC_EXPORT', true);
define('STATIC_CV_FILE', 'CV-Hien-Phuong.pdf');

$root   = dirname(__DIR__);
$public = $root . '/public';
$cvPdf  = __DIR__ . '/cv/' . STATIC_CV_FILE;
$out    = rtrim($argv[1] ?? $root . '/dist', '/\\');

if (!is_file($cvPdf)) {
    fwrite(STDERR, "Thiếu file CV: $cvPdf (in lại từ tools/cv/cv.html)\n");
    exit(1);
}

// Giả lập request GET tới /index.php để url()/asset() sinh đường dẫn gốc "/assets/..."
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME']    = '/index.php';

ob_start();
require $public . '/index.php';
$html = ob_get_clean();

if (!str_contains($html, '</html>')) {
    fwrite(STDERR, "Render thất bại (Database chưa chạy?):\n" . strip_tags($html) . "\n");
    exit(1);
}

function copy_file(string $from, string $to): void {
    if (!is_dir(dirname($to))) {
        mkdir(dirname($to), 0777, true);
    }
    copy($from, $to);
}

function copy_dir(string $from, string $to): void {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS)) as $f) {
        copy_file($f->getPathname(), $to . substr($f->getPathname(), strlen($from)));
    }
}

if (!is_dir($out)) {
    mkdir($out, 0777, true);
}
file_put_contents($out . '/index.html', $html);

// Asset dùng cho trang public (vendor/ chỉ phục vụ trang Admin nên bỏ qua)
foreach (['style5.css', 'script5.js', 'hien-phuong.png', 'hien-phuong.webp'] as $f) {
    if (is_file("$public/assets/$f")) {
        copy_file("$public/assets/$f", "$out/assets/$f");
    }
}
copy_dir("$public/assets/fonts", "$out/assets/fonts");

// Ảnh tải lên qua Admin mà trang có dùng (vd. ảnh đại diện)
preg_match_all('~(?:src|srcset|href)="/(uploads/[^"?#\s]+)~', $html, $m);
foreach (array_unique($m[1]) as $rel) {
    if (is_file("$public/$rel")) {
        copy_file("$public/$rel", "$out/$rel");
    }
}

copy_file($cvPdf, $out . '/' . STATIC_CV_FILE);

echo "Đã xuất trang tĩnh vào: $out\n";
