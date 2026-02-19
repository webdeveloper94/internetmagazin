<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Panel - ' . SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
 </head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>👑 Admin Panel</h2>
                <p><?php echo htmlspecialchars($_SESSION['name']); ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="<?php echo SITE_URL; ?>/admin/index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/users.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
                    👥 Foydalanuvchilar
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>">
                    📁 Kategoriyalar
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/products.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : ''; ?>">
                    📦 Mahsulotlar
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>">
                    🛒 Buyurtmalar
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/reports.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>">
                    📈 Hisobotlar
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-secondary btn-sm btn-block">🏠 Saytga qaytish</a>
                <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="btn btn-danger btn-sm btn-block">🚪 Chiqish</a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <?php 
            $flash = get_flash_message();
            if ($flash): 
            ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible">
                    <?php echo $flash['message']; ?>
                    <button class="alert-close">&times;</button>
                </div>
            <?php endif; ?>
