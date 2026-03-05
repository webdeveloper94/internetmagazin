<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($phone) || empty($password)) {
        $error = "Barcha maydonlarni to'ldiring";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_blocked']) {
                $error = "Sizning hisobingiz bloklangan. Administratorga murojaat qiling.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];

                if ($user['role'] === 'admin') {
                    header('Location: ' . SITE_URL . '/admin/');
                } else {
                    header('Location: ' . SITE_URL);
                }
                exit;
            }
        } else {
            $error = "Telefon raqam yoki parol noto'g'ri";
        }
    }
}

$pageTitle = "Kirish";
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
        <p class="auth-subtitle">Hisobingizga kiring</p>

        <?php if ($error): ?>
            <div class="alert-custom alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-floating-custom">
                <input type="tel" name="phone" id="phone" class="phone-mask" placeholder=" "
                       value="<?= isset($phone) ? sanitize($phone) : '' ?>" required>
                <label for="phone">Telefon raqam</label>
            </div>

            <div class="form-floating-custom">
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Parol</label>
            </div>

            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-box-arrow-in-right"></i> Kirish
            </button>
        </form>

        <div class="auth-footer">
            Hisobingiz yo'qmi? <a href="<?= SITE_URL ?>/auth/register.php">Ro'yxatdan o'tish</a>
        </div>
    </div>
</div>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
