<?php
/**
 * Profil Sahifasi
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Login talab qilish
require_login();

$page_title = 'Profil - ' . SITE_NAME;
$errors = [];
$success = '';

// Foydalanuvchi ma'lumotlarini olish
$user = get_user_info();

// POST so'rovi kelsa (profil yangilash)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = sanitize_input($_POST['phone'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    
    // Validatsiya
    $phone_validation = validate_phone($phone);
    if ($phone_validation !== true) {
        $errors[] = $phone_validation;
    }
    
    if (empty($errors)) {
        $db = Database::getInstance();
        
        // Profil yangilash
        $sql = "UPDATE profiles SET phone = ?, address = ? WHERE user_id = ?";
        if ($db->execute($sql, [$phone, $address, $_SESSION['user_id']])) {
            set_flash_message('success', 'Profil muvaffaqiyatli yangilandi');
            redirect('profile.php');
        } else {
            $errors[] = 'Profilni yangilashda xatolik yuz berdi';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <div class="page-header">
            <h1>Mening Profilim</h1>
        </div>
        
        <div class="profile-layout">
            <!-- Profil Info Card -->
            <div class="profile-card">
                <div class="profile-avatar">
                    <span><?php echo mb_substr($user['name'], 0, 2, 'UTF-8'); ?></span>
                </div>
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="profile-username">@<?php echo htmlspecialchars($user['username']); ?></p>
                <p class="profile-role">
                    <?php echo $user['role'] === 'admin' ? '👑 Administrator' : '👤 Foydalanuvchi'; ?>
                </p>
                <p class="profile-date">
                    Ro'yxatdan o'tgan: <?php echo format_date($user['created_at']); ?>
                </p>
                
                <div style="margin-top: 1.5rem;">
                    <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="btn btn-danger" 
                       onclick="return confirm('Rostdan ham chiqmoqchimisiz?')">
                        <i class="bi bi-box-arrow-right"></i>
                        Chiqish
                    </a>
                </div>
            </div>
            
            <!-- Profil Form -->
            <div class="profile-form-card">
                <h3>Profil Ma'lumotlari</h3>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="name">Ism</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($user['name']); ?>" 
                               readonly>
                        <small>Ismni o'zgartirish uchun administrator bilan bog'laning</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Login</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" 
                               readonly>
                        <small>Loginni o'zgartirish mumkin emas</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefon Raqami</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                               placeholder="+998901234567">
                        <small>Misol: +998901234567</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">To'liq Manzil</label>
                        <textarea id="address" name="address" rows="3" 
                                  placeholder="Shahar, tuman, ko'cha, uy"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <i class="bi bi-check-circle"></i>
                            Saqlash
                        </button>
                        
                        <a href="<?php echo SITE_URL; ?>/auth/logout.php" 
                           class="btn btn-secondary"
                           onclick="return confirm('Rostdan ham chiqmoqchimisiz?')">
                            <i class="bi bi-box-arrow-right"></i>
                            Chiqish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
