<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $name = trim($_POST['name']);
            $description = trim($_POST['description'] ?? '');
            $icon = null;

            // Upload icon
            if (!empty($_FILES['icon']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/categories/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = strtolower(pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','svg','webp'];
                if (in_array($ext, $allowed)) {
                    $icon = uniqid('cat_') . '.' . $ext;
                    move_uploaded_file($_FILES['icon']['tmp_name'], $uploadDir . $icon);
                }
            }

            $stmt = $pdo->prepare("INSERT INTO categories (name, icon, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $icon, $description]);
            $message = "Kategoriya qo'shildi";
            $messageType = 'success';
            break;

        case 'update':
            $catId = intval($_POST['cat_id']);
            $name = trim($_POST['name']);
            $description = trim($_POST['description'] ?? '');

            // Upload new icon if provided
            if (!empty($_FILES['icon']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/categories/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = strtolower(pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','svg','webp'];
                if (in_array($ext, $allowed)) {
                    // Delete old icon
                    $old = $pdo->prepare("SELECT icon FROM categories WHERE id = ?");
                    $old->execute([$catId]);
                    $oldIcon = $old->fetchColumn();
                    if ($oldIcon && file_exists($uploadDir . $oldIcon)) {
                        unlink($uploadDir . $oldIcon);
                    }
                    $icon = uniqid('cat_') . '.' . $ext;
                    move_uploaded_file($_FILES['icon']['tmp_name'], $uploadDir . $icon);
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ?, description = ? WHERE id = ?");
                    $stmt->execute([$name, $icon, $description, $catId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $catId]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $description, $catId]);
            }
            $message = "Kategoriya yangilandi";
            $messageType = 'success';
            break;

        case 'delete':
            $catId = intval($_POST['cat_id']);
            // Delete icon file
            $old = $pdo->prepare("SELECT icon FROM categories WHERE id = ?");
            $old->execute([$catId]);
            $oldIcon = $old->fetchColumn();
            if ($oldIcon) {
                $iconPath = __DIR__ . '/../uploads/categories/' . $oldIcon;
                if (file_exists($iconPath)) unlink($iconPath);
            }
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$catId]);
            $message = "Kategoriya o'chirildi";
            $messageType = 'success';
            break;
    }
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c ORDER BY c.name")->fetchAll();

$adminPageTitle = 'Kategoriyalar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-grid"></i> Kategoriyalar</h3>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
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
                    <th>Icon</th>
                    <th>Nomi</th>
                    <th>Tavsif</th>
                    <th>Mahsulotlar</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Kategoriyalar topilmadi</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td>
                            <?php if ($cat['icon']): ?>
                                <img src="<?= SITE_URL ?>/uploads/categories/<?= $cat['icon'] ?>" 
                                     style="width:36px;height:36px;object-fit:cover;border-radius:8px;">
                            <?php else: ?>
                                <div style="width:36px;height:36px;background:var(--bg);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-folder" style="color:var(--primary);"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= sanitize($cat['name']) ?></strong></td>
                        <td class="text-muted"><?= sanitize(mb_substr($cat['description'] ?? '', 0, 50)) ?></td>
                        <td><span class="badge bg-primary"><?= $cat['product_count'] ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCat<?= $cat['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Ishonchingiz komilmi?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editCat<?= $cat['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Kategoriyani tahrirlash</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Nomi</label>
                                                    <input type="text" name="name" class="form-control" value="<?= sanitize($cat['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Icon (rasm)</label>
                                                    <input type="file" name="icon" class="form-control" accept="image/*">
                                                    <?php if ($cat['icon']): ?>
                                                        <small class="text-muted">Hozirgi: <?= $cat['icon'] ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tavsif</label>
                                                    <textarea name="description" class="form-control" rows="3"><?= sanitize($cat['description'] ?? '') ?></textarea>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Kategoriya qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Nomi</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (rasm)</label>
                        <input type="file" name="icon" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tavsif</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
