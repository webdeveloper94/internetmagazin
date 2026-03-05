<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($full_name) || empty($phone) || empty($password)) {
        $error = "Barcha maydonlarni to'ldiring";
    } elseif (strlen($password) < 6) {
        $error = "Parol kamida 6 ta belgidan iborat bo'lishi kerak";
    } elseif ($password !== $password_confirm) {
        $error = "Parollar mos kelmadi";
    } else {
        // Check if phone already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $error = "Bu telefon raqam allaqachon ro'yxatdan o'tgan";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, password) VALUES (?, ?, ?)");
            $stmt->execute([$full_name, $phone, $hashedPassword]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_role'] = 'user';
            $_SESSION['user_name'] = $full_name;

            header('Location: ' . SITE_URL);
            exit;
        }
    }
}

$pageTitle = "Ro'yxatdan o'tish";
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> — Online Shop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2><i class="bi bi-shop"></i> Online Shop</h2>
        <p class="auth-subtitle">Yangi hisob yarating</p>

        <?php if ($error): ?>
            <div class="alert-custom alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-floating-custom">
                <input type="text" name="full_name" id="full_name" placeholder=" " 
                       value="<?= isset($full_name) ? sanitize($full_name) : '' ?>" required>
                <label for="full_name">To'liq ism</label>
            </div>

            <div class="form-floating-custom">
                <input type="tel" name="phone" id="phone" class="phone-mask" placeholder=" " 
                       value="<?= isset($phone) ? sanitize($phone) : '' ?>" required>
                <label for="phone">Telefon raqam</label>
            </div>

            <div class="form-floating-custom">
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Parol</label>
            </div>

            <div class="form-floating-custom">
                <input type="password" name="password_confirm" id="password_confirm" placeholder=" " required>
                <label for="password_confirm">Parolni tasdiqlang</label>
            </div>

            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-person-plus"></i> Ro'yxatdan o'tish
            </button>
        </form>

        <div class="auth-footer">
            Hisobingiz bormi? <a href="<?= SITE_URL ?>/auth/login.php">Kirish</a>
        </div>
    </div>
</div>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
