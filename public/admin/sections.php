<?php
/**
 * TRANG QUẢN LÝ CỬA SỔ & DOCK (mỗi section = 1 cửa sổ + 1 icon trên dock / màn hình chính)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Database.php';
require_once __DIR__ . '/../../app/Auth.php';
require_once __DIR__ . '/../../app/Csrf.php';
require_once __DIR__ . '/../../app/Repository.php';
require_once __DIR__ . '/../../app/helpers.php';


Auth::requireAuth();

// Xử lý cập nhật thông tin section
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    Csrf::validateRequest();

    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'dock_label'   => trim($_POST['dock_label'] ?? ''),
        'badge_code'   => trim($_POST['badge_code'] ?? ''),
        'title'        => trim($_POST['title'] ?? ''),
        'subtitle'     => trim($_POST['subtitle'] ?? ''),
        'is_visible'   => isset($_POST['is_visible']) ? 1 : 0,
        'open_on_load' => isset($_POST['open_on_load']) ? 1 : 0,
    ];

    if ($id > 0 && $data['title'] !== '' && $data['dock_label'] !== '') {
        Repository::updateSection($id, $data);
        touch_content_changed();
        flash('success', 'Đã cập nhật cấu hình section thành công!');
    } else {
        flash('error', 'Tên trên dock và tiêu đề cửa sổ không được để trống.');
    }

    redirect('admin/sections.php');
}

$sections = Repository::getSections(false);

$pageTitle = 'Cửa sổ & Dock';

// Tên thân thiện cho từng khóa section
$sectionNames = [
    'about'      => 'Về tôi',
    'skills'     => 'Kỹ năng',
    'strengths'  => 'Điểm mạnh',
    'weaknesses' => 'Điểm cần cải thiện',
    'experience' => 'Kinh nghiệm',
    'education'  => 'Học vấn & Công cụ',
    'contact'    => 'Liên hệ',
];
$activeMenu = 'sections';
require_once __DIR__ . '/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h2 class="h3 fw-bold text-white mb-1">Cửa sổ &amp; Dock</h2>
    <p class="text-secondary small mb-0">Mỗi dòng là một cửa sổ trên trang CV và một icon trên dock / màn hình chính điện thoại. Kéo thả để đổi thứ tự icon.</p>
  </div>
  <a href="<?= url() ?>" target="_blank" class="btn btn-outline-warning btn-sm">
    <i class="bi bi-eye me-1"></i> Xem trang CV
  </a>
</div>

<div class="card-admin">
  <div class="card-admin-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-window-stack text-warning me-2"></i>Danh sách cửa sổ</h5>
    <span class="badge bg-secondary">Kéo icon <i class="bi bi-grip-vertical"></i> để sắp xếp</span>
  </div>
  <div class="table-responsive">
    <table class="table table-dark-custom mb-0 align-middle">
      <thead>
        <tr>
          <th style="width: 50px;" class="ps-4">Thứ tự</th>
          <th>Cửa sổ</th>
          <th>Tên trên dock</th>
          <th>Tiêu đề cửa sổ</th>
          <th>Dòng nhỏ đầu cửa sổ</th>
          <th class="text-center" style="width: 130px;" title="Tự mở khi vào trang trên máy tính">Mở sẵn</th>
          <th class="text-center" style="width: 110px;">Hiển thị</th>
          <th class="text-end pe-4" style="width: 120px;">Thao tác</th>
        </tr>
      </thead>
      <tbody data-sortable-table="sections" data-sortable-handle=".drag-handle">
        <?php foreach ($sections as $sec): ?>
          <tr data-id="<?= $sec['id'] ?>">
            <td class="ps-4 text-center">
              <i class="bi bi-grip-vertical drag-handle fs-5"></i>
            </td>
            <td>
              <div class="fw-semibold text-white"><?= e($sectionNames[$sec['key']] ?? $sec['key']) ?></div>
              <code class="text-secondary small">#<?= e($sec['key']) ?></code>
            </td>
            <td class="text-warning fw-semibold"><?= e($sec['dock_label']) ?></td>
            <td class="fw-bold text-white">
              <?= e($sec['title']) ?>
            </td>
            <td class="text-secondary small">
              <?= e($sec['badge_code'] ?: '—') ?>
            </td>
            <td class="text-center">
              <div class="form-check form-switch d-inline-block">
                <input class="form-check-input ajax-toggle" type="checkbox" role="switch"
                       data-table="sections" data-id="<?= $sec['id'] ?>" data-column="open_on_load"
                       <?= $sec['open_on_load'] ? 'checked' : '' ?>>
              </div>
            </td>
            <td class="text-center">
              <div class="form-check form-switch d-inline-block">
                <input class="form-check-input ajax-toggle" type="checkbox" role="switch" 
                       data-table="sections" data-id="<?= $sec['id'] ?>" data-column="is_visible"
                       <?= $sec['is_visible'] ? 'checked' : '' ?>>
              </div>
            </td>
            <td class="text-end pe-4">
              <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $sec['id'] ?>">
                <i class="bi bi-pencil"></i> Sửa
              </button>
            </td>
          </tr>

          <!-- Modal Sửa Section -->
          <div class="modal fade" id="editModal<?= $sec['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content card-admin border-secondary">
                <div class="modal-header border-secondary">
                  <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-pencil-square text-warning me-2"></i>Sửa cửa sổ "<?= e($sectionNames[$sec['key']] ?? $sec['key']) ?>"
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <form method="POST" action="sections.php">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" value="<?= $sec['id'] ?>">

                  <div class="modal-body space-y-3">
                    <div class="row g-3 mb-3">
                      <div class="col-sm-5">
                        <label class="form-label" for="dock_label_<?= $sec['id'] ?>">Tên trên dock / menu <span class="text-danger">*</span></label>
                        <input type="text" name="dock_label" id="dock_label_<?= $sec['id'] ?>" class="form-control" value="<?= e($sec['dock_label']) ?>" maxlength="30" required>
                      </div>
                      <div class="col-sm-7">
                        <label class="form-label" for="title_<?= $sec['id'] ?>">Tiêu đề trên thanh cửa sổ <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title_<?= $sec['id'] ?>" class="form-control" value="<?= e($sec['title']) ?>" maxlength="150" required>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label" for="badge_code_<?= $sec['id'] ?>">Dòng nhỏ đầu cửa sổ (chữ xanh, in hoa)</label>
                      <input type="text" name="badge_code" id="badge_code_<?= $sec['id'] ?>" class="form-control" value="<?= e($sec['badge_code']) ?>" placeholder="02 // NĂNG LỰC CHUYÊN MÔN">
                    </div>

                    <div class="mb-3">
                      <label class="form-label" for="subtitle_<?= $sec['id'] ?>">Mô tả ngắn dưới dòng nhỏ</label>
                      <input type="text" name="subtitle" id="subtitle_<?= $sec['id'] ?>" class="form-control" value="<?= e($sec['subtitle']) ?>" placeholder="Bộ kỹ năng tư vấn...">
                      <?php if ($sec['key'] === 'contact'): ?>
                        <div class="form-text small text-secondary">Cửa sổ Liên hệ dùng tiêu đề &amp; lời nhắn ở <a href="profile.php">Hồ sơ cá nhân</a> mục 4.</div>
                      <?php endif; ?>
                    </div>

                    <div class="form-check form-switch pt-2">
                      <input class="form-check-input" type="checkbox" name="is_visible" id="vis_<?= $sec['id'] ?>" value="1" <?= $sec['is_visible'] ? 'checked' : '' ?>>
                      <label class="form-check-label text-light" for="vis_<?= $sec['id'] ?>">Hiển thị cửa sổ này trên trang CV</label>
                    </div>
                    <div class="form-check form-switch pt-2">
                      <input class="form-check-input" type="checkbox" name="open_on_load" id="open_<?= $sec['id'] ?>" value="1" <?= $sec['open_on_load'] ? 'checked' : '' ?>>
                      <label class="form-check-label text-light" for="open_<?= $sec['id'] ?>">Tự mở sẵn khi vào trang (máy tính)</label>
                    </div>
                  </div>

                  <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-gold">Lưu thay đổi</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
