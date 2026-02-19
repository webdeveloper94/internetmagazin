<?php
/**
 * Admin Registration Page
 * Faqat birinchi admin yaratish uchun
 */
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';

$db = Database::getInstance();

// Agar admin allaqachon mavjud bo'lsa, access denied
$admin_check = $db->fetchOne("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
if ($admin_check) {
    die('Admin allaqachon mavjud. Bu sahifa faqat birinchi admin yaratish uchun ishlatiladi.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($username) || empty($password)) {
        $error = 'Barcha maydonlarni to\'ldiring';
    } elseif (strlen($password) < 6) {
        $error = 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak';
    } elseif ($password !== $confirm_password) {
        $error = 'Parollar mos kelmadi';
    } else {
        // Check if username already exists
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $existing = $db->fetchOne($check_sql, [$username]);
        
        if ($existing) {
            $error = 'Bu username allaqachon band';
        } else {
            // Create admin user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_sql = "INSERT INTO users (name, username, password_hash, role, created_at) 
                          VALUES (?, ?, ?, 'admin', NOW())";
            
            try {
                $result = $db->execute($insert_sql, [$name, $username, $hashed_password]);
                
                if ($result) {
                    $success = 'Admin muvaffaqiyatli yaratildi! Endi login sahifasiga o\'ting.';
                    
                    // Redirect after 3 seconds
                    header('refresh:3;url=' . SITE_URL . '/auth/login.php');
                } else {
                    $error = 'Admin yaratishda xatolik yuz berdi';
                }
            } catch (Exception $e) {
                $error = 'Xatolik yuz berdi: ' . $e->getMessage();
            }
        }
    }
}

$page_title = 'Admin Registration - ' . SITE_NAME;
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .admin-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 3rem;
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .admin-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .admin-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.875rem;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            width: 100%;
            transition: transform 0.3s;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-card">
        <div class="admin-header">
            <div class="admin-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1>Admin Yaratish</h1>
            <p class="subtitle">Birinchi admin user ro'yxatdan o'tkazish</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="bi bi-person"></i> To'liq ism
                    </label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-at"></i> Username
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-key"></i> Parol
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="text-muted">Kamida 6 ta belgi</small>
                </div>
                
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">
                        <i class="bi bi-key-fill"></i> Parolni tasdiqlang
                    </label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-admin">
                    <i class="bi bi-shield-check"></i> Admin Yaratish
                </button>
            </form>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="<?php echo SITE_URL; ?>">
                <i class="bi bi-arrow-left"></i> Asosiy sahifaga qaytish
            </a>
        </div>
    </div>
</body>
</html>
