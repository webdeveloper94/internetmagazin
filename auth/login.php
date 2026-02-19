<?php
/**
 * Login Sahifasi
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Agar login qilgan bo'lsa, asosiy sahifaga yo'naltirish
if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

// POST so'rovi kelsa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $errors[] = 'Login va parolni kiriting';
    } else {
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE username = ?";
        $user = $db->fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Bloklangan foydalanuvchini tekshirish
            if ($user['is_blocked']) {
                $errors[] = 'Sizning hisobingiz bloklangan. Administrator bilan bog\'laning.';
            } else {
                // Session yaratish
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                // Eslab qolish
                if ($remember) {
                    setcookie('remember_user', $user['id'], time() + (86400 * 30), '/');
                }
                
                set_flash_message('success', 'Xush kelibsiz, ' . $user['name'] . '!');
                
                // Admin bo'lsa admin panelga, aks holda asosiy sahifaga
                if ($user['role'] === 'admin') {
                    redirect('admin/index.php');
                } else {
                    redirect('index.php');
                }
            }
        } else {
            $errors[] = 'Login yoki parol noto\'g\'ri';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tizimga Kirish - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Tizimga Kirish</h1>
                <p>Hisobingizga kiring</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="username">Login</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>" 
                           required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Parol</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group form-checkbox">
                    <label>
                        <input type="checkbox" name="remember">
                        <span>Eslab qolish</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Kirish
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Hisobingiz yo'qmi? <a href="register.php">Ro'yxatdan o'tish</a></p>
                <p><a href="<?php echo SITE_URL; ?>">Asosiy sahifaga qaytish</a></p>
            </div>
            
            <div class="demo-credentials">
                <p><strong>Demo login:</strong></p>
                <p>Admin: <code>admin</code> / <code>admin123</code></p>
                <p>User: <code>user1</code> / <code>password123</code></p>
            </div>
        </div>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
