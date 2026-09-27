<?php
/**
 * TRANG CÀI ĐẶT HỆ THỐNG & SEO (SETTINGS)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Database.php';
require_once __DIR__ . '/../../app/Auth.php';
require_once __DIR__ . '/../../app/Csrf.php';
require_once __DIR__ . '/../../app/Repository.php';
require_once __DIR__ . '/../../app/helpers.php';


Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validateRequest();

    $settingsData = [
        'site_title'           => trim($_POST['site_title'] ?? ''),
        'meta_description'     => trim($_POST['meta_description'] ?? ''),
        'author'               => trim($_POST['author'] ?? ''),
        'default_theme'        => in_array($_POST['default_theme'] ?? '', ['auto', 'light', 'dark'], true) ? $_POST['default_theme'] : 'auto',
        'default_clear'        => (string)max(0, min(100, (int)($_POST['default_clear'] ?? 45))),
        'monogram'             => trim($_POST['monogram'] ?? 'HP'),
        'footer_title'         => trim($_POST['footer_title'] ?? ''),
        'footer_text'          => trim($_POST['footer_text'] ?? ''),
        'google_analytics_id'  => trim($_POST['google_analytics_id'] ?? '')
    ];

    Repository::updateSettings($settingsData);
    touch_content_changed();
    flash('success', 'Đã lưu cấu hình hệ thống và SEO thành công!');
    redirect('admin/settings.php');
}

$settings = Repository::getSettings();

$pageTitle = 'Cài đặt hệ thống & SEO';
$activeMenu = 'settings';
require_once __DIR__ . '/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h2 class="h3 fw-bold text-white mb-1">Cài đặt hệ thống &amp; SEO</h2>
    <p class="text-secondary small mb-0">Tùy chỉnh tiêu đề website, thẻ meta SEO, logo thương hiệu và chân trang</p>
  </div>
  <a href="<?= url() ?>" target="_blank" class="btn btn-outline-warning btn-sm">
    <i class="bi bi-eye me-1"></i> Xem trang CV
  </a>
</div>

<form method="POST" action="settings.php">
  <?= Csrf::field() ?>

  <div class="card-admin mb-4">
    <div class="card-admin-header">
      <h5 class="mb-0 fw-bold text-white"><i class="bi bi-search text-warning me-2"></i>1. Cấu hình SEO &amp; Thẻ Meta</h5>
    </div>
    <div class="p-4">
      <div class="mb-3">
        <label class="form-label" for="site_title">Tiêu đề Website (Title Tag) <span class="text-danger">*</span></label>
        <input type="text" name="site_title" id="site_title" class="form-control" value="<?= e($settings['site_title'] ?? '') ?>" required>
        <div class="form-text small text-secondary">Hiển thị trên tab trình duyệt và kết quả tìm kiếm Google.</div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="meta_description">Mô tả tóm tắt (Meta Description)</label>
        <textarea name="meta_description" id="meta_description" rows="3" class="form-control"><?= e($settings['meta_description'] ?? '') ?></textarea>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="author">Tác giả (Meta Author)</label>
          <input type="text" name="author" id="author" class="form-control" value="<?= e($settings['author'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="google_analytics_id">Google Analytics Tracking ID (nếu có)</label>
          <input type="text" name="google_analytics_id" id="google_analytics_id" class="form-control" value="<?= e($settings['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX hoặc UA-XXXXX-Y">
        </div>
      </div>
    </div>
  </div>

  <div class="card-admin mb-4">
    <div class="card-admin-header">
      <h5 class="mb-0 fw-bold text-white"><i class="bi bi-palette text-warning me-2"></i>2. Giao diện thương hiệu &amp; Chân trang</h5>
    </div>
    <div class="p-4">
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label" for="monogram">Chữ cái Monogram Logo (Header)</label>
          <input type="text" name="monogram" id="monogram" class="form-control" value="<?= e($settings['monogram'] ?? 'HP') ?>" maxlength="4" placeholder="HP">
          <div class="form-text small text-secondary">Chữ viết tắt trong vòng tròn xanh ở góc trái thanh menu (1-3 ký tự).</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="default_theme">Giao diện mặc định khi khách vào lần đầu</label>
          <?php $dt = $settings['default_theme'] ?? 'auto'; ?>
          <select name="default_theme" id="default_theme" class="form-select">
            <option value="auto" <?= $dt === 'auto' ? 'selected' : '' ?>>Tự động theo máy của khách</option>
            <option value="light" <?= $dt === 'light' ? 'selected' : '' ?>>Ngày (sáng)</option>
            <option value="dark" <?= $dt === 'dark' ? 'selected' : '' ?>>Đêm (tối)</option>
          </select>
          <div class="form-text small text-secondary">Khách vẫn tự đổi được bằng nút ngày/đêm; lựa chọn của khách được ghi nhớ trên máy họ.</div>
        </div>
      </div>

      <div class="mb-3">
        <?php $dc = (int)($settings['default_clear'] ?? 45); ?>
        <label class="form-label" for="default_clear">Độ trong suốt mặc định của kính: <output id="default_clear_val"><?= $dc ?>%</output></label>
        <input type="range" class="form-range" name="default_clear" id="default_clear" min="0" max="100" step="1" value="<?= $dc ?>" oninput="document.getElementById('default_clear_val').textContent = this.value + '%'">
        <div class="form-text small text-secondary">0% = kính mờ đục, dễ đọc · 45% = cân bằng (khuyên dùng) · 100% = rất trong.</div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="footer_title">Dòng cuối cửa sổ Liên hệ (chữ đậm)</label>
        <input type="text" name="footer_title" id="footer_title" class="form-control" value="<?= e($settings['footer_title'] ?? '') ?>" placeholder="HIỀN PHƯƠNG — Nhân Viên Kinh Doanh">
      </div>

      <div class="mb-3">
        <label class="form-label" for="footer_text">Dòng bản quyền cuối cửa sổ Liên hệ (hỗ trợ HTML cơ bản)</label>
        <textarea name="footer_text" id="footer_text" rows="2" class="form-control"><?= e($settings['footer_text'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-end gap-3 card-admin p-3 mb-4">
    <button type="submit" class="btn btn-gold px-4">
      <i class="bi bi-check-lg me-1"></i> Lưu cài đặt
    </button>
  </div>
</form>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
