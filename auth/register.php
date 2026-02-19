<?php
/**
 * Ro'yxatdan O'tish Sahifasi
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
$success = '';

// POST so'rovi kelsa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validatsiya
    if (empty($name)) {
        $errors[] = 'Ismni kiriting';
    }
    
    if (empty($username)) {
        $errors[] = 'Login kiriting';
    } else {
        $validation = validate_username($username);
        if ($validation !== true) {
            $errors[] = $validation;
        }
    }
    
    if (empty($password)) {
        $errors[] = 'Parol kiriting';
    } else {
        $validation = validate_password($password);
        if ($validation !== true) {
            $errors[] = $validation;
        }
    }
    
    if ($password !== $password_confirm) {
        $errors[] = 'Parollar mos kelmadi';
    }
    
    // Agar xato bo'lmasa, registratsiya qilish
    if (empty($errors)) {
        $db = Database::getInstance();
        
        // Username borligini tekshirish
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $existing_user = $db->fetchOne($check_sql, [$username]);
        
        if ($existing_user) {
            $errors[] = 'Bu login band, boshqasini tanlang';
        } else {
            // Parolni hash qilish
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Foydalanuvchini yaratish
            $insert_sql = "INSERT INTO users (username, name, password_hash) VALUES (?, ?, ?)";
            if ($db->execute($insert_sql, [$username, $name, $password_hash])) {
                $user_id = $db->lastInsertId();
                
                // Bo'sh profil yaratish
                $profile_sql = "INSERT INTO profiles (user_id) VALUES (?)";
                $db->execute($profile_sql, [$user_id]);
                
                set_flash_message('success', 'Ro\'yxatdan o\'tdingiz! Endi tizimga kiring.');
                redirect('auth/login.php');
            } else {
                $errors[] = 'Ro\'yxatdan o\'tishda xatolik yuz berdi';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ro'yxatdan O'tish - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Ro'yxatdan O'tish</h1>
                <p>Yangi hisob yarating</p>
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
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form" id="registerForm">
                <div class="form-group">
                    <label for="name">Ismingiz</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="username">Login</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>" 
                           required>
                    <small>Harflar, raqamlar va _ belgisidan foydalaning</small>
                </div>
                
                <div class="form-group">
                    <label for="password">Parol</label>
                    <input type="password" id="password" name="password" required>
                    <small>Kamida <?php echo PASSWORD_MIN_LENGTH; ?> ta belgi</small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Parolni Tasdiqlang</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Ro'yxatdan O'tish
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Hisobingiz bormi? <a href="login.php">Tizimga kirish</a></p>
                <p><a href="<?php echo SITE_URL; ?>">Asosiy sahifaga qaytish</a></p>
            </div>
        </div>
    </div>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
