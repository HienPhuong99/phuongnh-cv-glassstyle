<?php
/**
 * BẢN SAO DỮ LIỆU CV TRONG REPO (để máy khác / phiên cloud xuất lại được trang)
 *
 *   php tools/data-snapshot.php dump      Database → database/current.sql (+ file uploads/ được dùng)
 *   php tools/data-snapshot.php restore   database/schema.sql + current.sql → Database (tạo DB nếu chưa có)
 *
 * Chỉ gồm các bảng nội dung CV. KHÔNG gồm tài khoản Admin, tin nhắn liên hệ, log đăng nhập,
 * throttle, lượt tải CV. File ảnh tải lên qua Admin (public/uploads/, bị .gitignore) mà nội dung
 * có tham chiếu được chép vào database/snapshot-uploads/ để đi kèm repo.
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}
if (!defined('NO_SESSION')) {
    define('NO_SESSION', true);
}
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/app/Database.php';

const SNAPSHOT_TABLES = ['settings', 'profile', 'sections', 'key_stats', 'skills', 'strengths', 'weaknesses', 'experiences', 'educations', 'tools'];

function snapshot_paths(): array {
    $root = dirname(__DIR__);
    return [$root . '/database/current.sql', $root . '/database/snapshot-uploads', $root . '/public'];
}

function snapshot_dump(): void {
    [$sqlFile, $upDir, $public] = snapshot_paths();
    $pdo = Database::getInstance();

    $sql = "-- Bản sao nội dung CV — sinh tự động bởi tools/data-snapshot.php dump. KHÔNG sửa tay.\n"
         . "-- Nạp lại: php tools/data-snapshot.php restore\n\n"
         . "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\n";
    $uploads = [];

    foreach (SNAPSHOT_TABLES as $t) {
        $rows = $pdo->query("SELECT * FROM `$t` ORDER BY 1")->fetchAll(PDO::FETCH_ASSOC);
        $sql .= "\n-- $t\nDELETE FROM `$t`;\n";
        if (!$rows) {
            continue;
        }
        $cols = '(`' . implode('`, `', array_keys($rows[0])) . '`)';
        $vals = [];
        foreach ($rows as $r) {
            $vals[] = '(' . implode(', ', array_map(function ($v) use ($pdo, &$uploads) {
                if ($v === null) {
                    return 'NULL';
                }
                if (preg_match_all('~uploads/[^\s"\'<>?#]+~', (string)$v, $m)) {
                    array_push($uploads, ...$m[0]);
                }
                return $pdo->quote((string)$v);
            }, $r)) . ')';
        }
        $sql .= "INSERT INTO `$t` $cols VALUES\n" . implode(",\n", $vals) . ";\n";
    }
    $sql .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
    file_put_contents($sqlFile, $sql);

    foreach (array_unique($uploads) as $rel) {
        if (is_file("$public/$rel")) {
            if (!is_dir(dirname("$upDir/$rel"))) {
                mkdir(dirname("$upDir/$rel"), 0777, true);
            }
            copy("$public/$rel", "$upDir/$rel");
        }
    }
    echo "Đã ghi bản sao dữ liệu: database/current.sql\n";
}

function snapshot_restore(): void {
    [$sqlFile, $upDir, $public] = snapshot_paths();
    $root = dirname(__DIR__);

    $server = new PDO(sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT), DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $server->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', DB_NAME) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo = Database::getInstance();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true); // cho phép chạy nhiều câu lệnh một lần
    // schema.sql gắn cứng tên DB — bỏ CREATE DATABASE/USE để chạy vào DB_NAME đang cấu hình
    $schema = preg_replace('~^\s*(CREATE DATABASE|USE)\b[^;]*;~mi', '', file_get_contents($root . '/database/schema.sql'));
    $pdo->exec($schema);
    $pdo->exec(file_get_contents($sqlFile));

    if (is_dir($upDir)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upDir, FilesystemIterator::SKIP_DOTS)) as $f) {
            $to = $public . substr(str_replace('\\', '/', $f->getPathname()), strlen(str_replace('\\', '/', $upDir)));
            if (!is_dir(dirname($to))) {
                mkdir(dirname($to), 0777, true);
            }
            copy($f->getPathname(), $to);
        }
    }
    echo "Đã nạp bản sao dữ liệu vào Database `" . DB_NAME . "`\n";
}

// Chạy trực tiếp từ dòng lệnh (không chạy khi được require từ export-static.php)
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    match ($argv[1] ?? '') {
        'dump'    => snapshot_dump(),
        'restore' => snapshot_restore(),
        default   => fwrite(STDERR, "Dùng: php tools/data-snapshot.php dump|restore\n"),
    };
}
