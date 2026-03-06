<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'toggle_block':
            $userId = intval($_POST['user_id']);
            $stmt = $pdo->prepare("UPDATE users SET is_blocked = NOT is_blocked WHERE id = ? AND role != 'admin'");
            $stmt->execute([$userId]);
            $message = "Foydalanuvchi holati o'zgartirildi";
            $messageType = 'success';
            break;

        case 'update_user':
            $userId = intval($_POST['user_id']);
            $fullName = trim($_POST['full_name']);
            $phone = trim($_POST['phone']);
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            $stmt->execute([$fullName, $phone, $userId]);
            $message = "Foydalanuvchi yangilandi";
            $messageType = 'success';
            break;

        case 'change_password':
            $userId = intval($_POST['user_id']);
            $newPassword = $_POST['new_password'] ?? '';
            if (strlen($newPassword) < 6) {
                $message = "Parol kamida 6 belgidan iborat bo'lishi kerak";
                $messageType = 'danger';
            } else {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $userId]);
                $message = "Parol o'zgartirildi";
                $messageType = 'success';
            }
            break;
    }
}

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'user' AND (full_name LIKE ? OR phone LIKE ?) ORDER BY created_at DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
}
$users = $stmt->fetchAll();

$adminPageTitle = 'Foydalanuvchilar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-people"></i> Foydalanuvchilar</h3>
        </div>
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Qidirish..." 
                   value="<?= sanitize($search) ?>" style="width:200px;">
            <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
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
                    <th>Ism</th>
                    <th>Telefon</th>
                    <th>Holat</th>
                    <th>Ro'yxatdan o'tgan</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Foydalanuvchilar topilmadi</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><strong><?= sanitize($user['full_name']) ?></strong></td>
                        <td><?= sanitize($user['phone']) ?></td>
                        <td>
                            <?php if ($user['is_blocked']): ?>
                                <span class="badge bg-danger">Bloklangan</span>
                            <?php else: ?>
                                <span class="badge bg-success">Faol</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <!-- Block/Unblock -->
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_block">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $user['is_blocked'] ? 'btn-outline-success' : 'btn-outline-danger' ?>"
                                            title="<?= $user['is_blocked'] ? 'Blokdan chiqarish' : 'Bloklash' ?>">
                                        <i class="bi bi-<?= $user['is_blocked'] ? 'unlock' : 'lock' ?>"></i>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUser<?= $user['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Change Password -->
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePass<?= $user['id'] ?>">
                                    <i class="bi bi-key"></i>
                                </button>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editUser<?= $user['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tahrirlash</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update_user">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Ism</label>
                                                    <input type="text" name="full_name" class="form-control" value="<?= sanitize($user['full_name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Telefon</label>
                                                    <input type="tel" name="phone" class="form-control" value="<?= sanitize($user['phone']) ?>" required>
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

                            <!-- Change Password Modal -->
                            <div class="modal fade" id="changePass<?= $user['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Parolni o'zgartirish</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="change_password">
                                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Yangi parol</label>
                                                    <input type="password" name="new_password" class="form-control" minlength="6" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                                <button type="submit" class="btn btn-warning">O'zgartirish</button>
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

<?php include __DIR__ . '/includes/footer.php'; ?>
