<?php
require_once __DIR__ . '/../includes/auth.php';

// Check admin
if (!isAdmin()) {
    header("Location: " . SITE_URL . "/auth/login.php");
    exit;
}

$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = trim($_POST['title']);
        $subtitle = trim($_POST['subtitle']);
        $bg_color = trim($_POST['bg_color']);
        $btn_text = trim($_POST['btn_text']);
        $btn_link = trim($_POST['btn_link']);
        $sort_order = intval($_POST['sort_order']);
        $image = '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'slider_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/sliders/' . $image);
        }

        $stmt = $pdo->prepare("INSERT INTO home_sliders (title, subtitle, bg_color, btn_text, btn_link, image, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $subtitle, $bg_color, $btn_text, $btn_link, $image, $sort_order]);
        $message = "Slayder muvaffaqiyatli qo'shildi!";
        $messageType = 'success';
    } elseif ($action === 'update') {
        $id = intval($_POST['id']);
        $title = trim($_POST['title']);
        $subtitle = trim($_POST['subtitle']);
        $bg_color = trim($_POST['bg_color']);
        $btn_text = trim($_POST['btn_text']);
        $btn_link = trim($_POST['btn_link']);
        $sort_order = intval($_POST['sort_order']);
        $status = isset($_POST['status']) ? 1 : 0;

        $stmt = $pdo->prepare("UPDATE home_sliders SET title = ?, subtitle = ?, bg_color = ?, btn_text = ?, btn_link = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $subtitle, $bg_color, $btn_text, $btn_link, $sort_order, $status, $id]);

        if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            $stmt = $pdo->prepare("SELECT image FROM home_sliders WHERE id = ?");
            $stmt->execute([$id]);
            $oldImage = $stmt->fetchColumn();
            if ($oldImage && file_exists(__DIR__ . '/../uploads/sliders/' . $oldImage)) {
                unlink(__DIR__ . '/../uploads/sliders/' . $oldImage);
            }
            $pdo->prepare("UPDATE home_sliders SET image = '' WHERE id = ?")->execute([$id]);
        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = 'slider_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/sliders/' . $image)) {
                $pdo->prepare("UPDATE home_sliders SET image = ? WHERE id = ?")->execute([$image, $id]);
            }
        }
        $message = "Slayder muvaffaqiyatli yangilandi!";
        $messageType = 'success';
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("DELETE FROM home_sliders WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Slayder o'chirildi!";
        $messageType = 'success';
    }
}

// Get sliders
$sliders = $pdo->query("SELECT * FROM home_sliders ORDER BY sort_order ASC, id DESC")->fetchAll();

// Create uploads/sliders directory if it doesn't exist
if (!is_dir(__DIR__ . '/../uploads/sliders')) {
    mkdir(__DIR__ . '/../uploads/sliders', 0777, true);
}

$adminPageTitle = 'Slayderlar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-images"></i> Slayderlar</h3>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSliderModal">
            <i class="bi bi-plus-lg"></i> Qo'shish
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="admin-table">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ko'rinishi</th>
                    <th>Ma'lumotlar</th>
                    <th>Tartib</th>
                    <th>Status</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sliders)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Slayderlar topilmadi</td></tr>
                <?php else: ?>
                    <?php foreach ($sliders as $slide): ?>
                    <tr>
                        <td><?= $slide['id'] ?></td>
                        <td>
                            <div style="width:120px;height:60px;border-radius:8px;background:<?= $slide['bg_color'] ?>;display:flex;align-items:center;justify-content:center;color:white;font-size:0.6rem;text-align:center;padding:5px;overflow:hidden;position:relative;">
                                <?php if ($slide['image']): ?>
                                    <img src="<?= SITE_URL ?>/uploads/sliders/<?= $slide['image'] ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;opacity:0.6;z-index:0;">
                                <?php endif; ?>
                                <div style="position:relative;z-index:1;">
                                    <strong><?= sanitize($slide['title']) ?></strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong><?= sanitize($slide['title']) ?></strong><br>
                            <small class="text-muted"><?= sanitize($slide['subtitle']) ?></small><br>
                            <small class="text-primary"><?= sanitize($slide['btn_text']) ?> (<?= sanitize($slide['btn_link']) ?>)</small>
                        </td>
                        <td><?= $slide['sort_order'] ?></td>
                        <td>
                            <span class="badge bg-<?= $slide['status'] ? 'success' : 'secondary' ?>">
                                <?= $slide['status'] ? 'Faol' : 'Faol emas' ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSlider<?= $slide['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Ishonchingiz komilmi?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editSlider<?= $slide['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Slayderni tahrirlash</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Sarlavha</label>
                                                    <input type="text" name="title" class="form-control" value="<?= sanitize($slide['title']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Kichik sarlavha</label>
                                                    <input type="text" name="subtitle" class="form-control" value="<?= sanitize($slide['subtitle']) ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Orqa fon rangi (CSS gradient ham bo'ladi)</label>
                                                    <input type="text" name="bg_color" class="form-control" value="<?= sanitize($slide['bg_color']) ?>" placeholder="masalan: #7000FF yoki linear-gradient(...)">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Background rasm (ixtiyoriy)</label>
                                                    <?php if ($slide['image']): ?>
                                                        <div class="mb-2 d-flex align-items-center gap-2">
                                                            <img src="<?= SITE_URL ?>/uploads/sliders/<?= $slide['image'] ?>" style="height:50px; border-radius:4px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="rmImg<?= $slide['id'] ?>">
                                                                <label class="form-check-label text-danger" for="rmImg<?= $slide['id'] ?>" style="font-size:0.85rem;">Rasmni olib tashlash</label>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Tugma matni</label>
                                                        <input type="text" name="btn_text" class="form-control" value="<?= sanitize($slide['btn_text']) ?>">
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Tugma havolasi</label>
                                                        <input type="text" name="btn_link" class="form-control" value="<?= sanitize($slide['btn_link']) ?>">
                                                        <div class="form-text" style="font-size:0.7rem;">Masalan: <code>/?view=catalog</code> yoki <code>https://google.com</code></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Tartib raqami</label>
                                                        <input type="number" name="sort_order" class="form-control" value="<?= $slide['sort_order'] ?>">
                                                    </div>
                                                    <div class="col-6 mb-3 d-flex align-items-end">
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input" type="checkbox" name="status" value="1" <?= $slide['status'] ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Aktiv</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                                <button type="submit" class="btn btn-primary">Saqlash</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Yangi slayder qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Sarlavha</label>
                        <input type="text" name="title" class="form-control" placeholder="🛍️ Online Shop" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kichik sarlavha</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="Eng yaxshi narxlarda...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Orqa fon rangi</label>
                        <input type="text" name="bg_color" class="form-control" value="linear-gradient(135deg, #7000FF, #9B4DFF)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Background rasm (ixtiyoriy)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tugma matni</label>
                            <input type="text" name="btn_text" class="form-control" value="Katalogni ko'rish">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Tugma havolasi</label>
                            <input type="text" name="btn_link" class="form-control" value="/?view=catalog">
                            <div class="form-text" style="font-size:0.7rem;">Masalan: <code>/?view=catalog</code> yoki <code>https://google.com</code></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tartib raqami</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
