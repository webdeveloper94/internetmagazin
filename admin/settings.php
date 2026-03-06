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

    if ($action === 'save_settings') {
        try {
            $pdo->beginTransaction();
            foreach ($_POST['settings'] as $key => $value) {
                $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
                $stmt->execute([trim($value), $key]);
            }
            $pdo->commit();
            $message = "Sozlamalar saqlandi!";
            $messageType = 'success';
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Xatolik yuz berdi: " . $e->getMessage();
            $messageType = 'danger';
        }
    } elseif ($action === 'test_tg') {
        require_once __DIR__ . '/../includes/telegram_helper.php';
        if (sendTelegramMessage("🔔 Test xabari! Online Shop Telegram botingiz muvaffaqiyatli sozlangan.")) {
            $message = "Test xabari Telegramga yuborildi!";
            $messageType = 'success';
        } else {
            $message = "Telegramga yuborishda xatolik! Token va Chat ID-ni tekshiring.";
            $messageType = 'danger';
        }
    }
}

// Get settings
$settingsRaw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settingsRaw as $s) {
    $settings[$s['key']] = $s['value'];
}

$adminPageTitle = 'Sozlamalar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-gear"></i> Sozlamalar</h3>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4"><i class="bi bi-telegram text-primary"></i> Telegram Xabarnomalar</h5>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_settings">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telegram Bot Token</label>
                            <input type="text" name="settings[tg_bot_token]" class="form-control" 
                                   value="<?= sanitize($settings['tg_bot_token'] ?? '') ?>" 
                                   placeholder="123456789:ABCDefgh..." required>
                            <div class="form-text">@BotFather orqali olingan token</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Admin Telegram Chat ID</label>
                            <input type="text" name="settings[tg_admin_chat_id]" class="form-control" 
                                   value="<?= sanitize($settings['tg_admin_chat_id'] ?? '') ?>" 
                                   placeholder="123456789" required>
                            <div class="form-text">Xabarlar yuborilishi kerak bo'lgan foydalanuvchi yoki guruh ID-si</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Xabarnomalarni yoqish</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="settings[tg_notifications_enabled]" 
                                       value="1" <?= ($settings['tg_notifications_enabled'] ?? '0') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label">Yangi buyurtmalar haqida Telegramga xabar yuborish</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Saqlash
                            </button>
                            <button type="submit" name="action" value="test_tg" class="btn btn-outline-info">
                                <i class="bi bi-send"></i> Test yuborish
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3"><i class="bi bi-info-circle text-info"></i> Qanday sozlanadi?</h5>
                    <ol class="text-muted" style="font-size: 0.9rem;">
                        <li>Telegramda <a href="https://t.me/botfather" target="_blank">@BotFather</a> orqali yangi bot yarating.</li>
                        <li>Bot bergan <strong>API Token</strong>ni yuqoridagi maydonga yozing.</li>
                        <li>Telegram ID-ingizni bilish uchun <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a>dan foydalaning.</li>
                        <li>ID-ni <strong>Chat ID</strong> maydoniga yozing va saqlang.</li>
                        <li>Sozlamalar to'g'riligini "Test yuborish" orqali tekshirib ko'ring.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
