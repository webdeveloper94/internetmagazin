<?php
require_once __DIR__ . '/config/database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($fullName) || empty($phone) || empty($password)) {
        $message = "Barcha maydonlarni to'ldiring!";
        $messageType = 'danger';
    } elseif (strlen($password) < 6) {
        $message = "Parol kamida 6 ta belgidan iborat bo'lishi kerak";
        $messageType = 'danger';
    } else {
        // Check if phone already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $message = "Bu telefon raqam bilan foydalanuvchi allaqachon mavjud!";
            $messageType = 'danger';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$fullName, $phone, $hashedPassword]);
            $message = "Admin muvaffaqiyatli yaratildi! Endi tizimga kirishingiz mumkin.";
            $messageType = 'success';
        }
    }
}

// Show existing admins
$admins = $pdo->query("SELECT id, full_name, phone, created_at FROM users WHERE role = 'admin' ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin yaratish — Online Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card" style="max-width:480px;">
        <h2><i class="bi bi-shield-lock"></i> Admin yaratish</h2>
        <p class="auth-subtitle">Yangi admin hisob yarating</p>

        <?php if ($message): ?>
            <div class="alert-custom alert-<?= $messageType ?>">
                <?= $message ?>
                <?php if ($messageType === 'success'): ?>
                    <br><a href="<?= SITE_URL ?>/auth/login.php" style="color:var(--success);font-weight:600;">→ Kirish sahifasiga o'tish</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-floating-custom">
                <input type="text" name="full_name" id="full_name" placeholder=" " required>
                <label for="full_name">To'liq ism</label>
            </div>

            <div class="form-floating-custom">
                <input type="tel" name="phone" id="phone" class="phone-mask" placeholder=" " required>
                <label for="phone">Telefon raqam</label>
            </div>

            <div class="form-floating-custom">
                <input type="password" name="password" id="password" placeholder=" " minlength="6" required>
                <label for="password">Parol</label>
            </div>

            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-shield-plus"></i> Admin yaratish
            </button>
        </form>

        <?php if (!empty($admins)): ?>
        <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);">
            <h6 style="font-weight:700;font-size:0.9rem;color:var(--text-muted);">Mavjud adminlar:</h6>
            <?php foreach ($admins as $admin): ?>
                <div style="padding:8px 0;border-bottom:1px solid var(--border);font-size:0.85rem;">
                    <strong><?= htmlspecialchars($admin['full_name']) ?></strong> 
                    <span class="text-muted">— <?= htmlspecialchars($admin['phone']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="auth-footer">
            <a href="<?= SITE_URL ?>/auth/login.php">← Kirish sahifasiga</a>
        </div>
    </div>
</div>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
