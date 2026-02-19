<?php
/**
 * Admin: Foydalanuvchilar Boshqaruvi
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Foydalanuvchilar - Admin Panel';
$db = Database::getInstance();

// Foydalanuvchilarni olish
$sql = "SELECT u.*, p.phone, p.address FROM users u LEFT JOIN profiles p ON u.id = p.user_id WHERE u.role = 'user' ORDER BY u.created_at DESC";
$users = $db->fetchAll($sql);

require_once 'includes/header.php';
?>

<div class="admin-header">
    <h1>Foydalanuvchilar Boshqaruvi</h1>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <?php if ($users && count($users) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ism</th>
                        <th>Login</th>
                        <th>Telefon</th>
                        <th>Manzil</th>
                        <th>Status</th>
                        <th>Ro'yxatdan o'tgan</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr id="user-row-<?php echo $user['id']; ?>">
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['address'] ? mb_substr($user['address'], 0, 30) . '...' : '-'); ?></td>
                            <td>
                                <?php if ($user['is_blocked']): ?>
                                    <span class="badge badge-danger">Bloklangan</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Aktiv</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo format_date($user['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($user['is_blocked']): ?>
                                        <button class="btn btn-sm btn-success toggle-block-btn" 
                                                data-user-id="<?php echo $user['id']; ?>" 
                                                data-action="unblock">
                                            Aktivlashtirish
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-warning toggle-block-btn" 
                                                data-user-id="<?php echo $user['id']; ?>" 
                                                data-action="block">
                                            Bloklash
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="btn btn-sm btn-danger delete-user-btn" 
                                            data-user-id="<?php echo $user['id']; ?>">
                                        O'chirish
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Foydalanuvchilar yo'q</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
