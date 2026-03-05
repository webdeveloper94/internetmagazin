<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <h4><i class="bi bi-shield-lock"></i> Admin Panel</h4>
    </div>
    <ul class="nav-links">
        <li>
            <a href="<?= SITE_URL ?>/admin/" class="<?= $currentPage === 'index' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= SITE_URL ?>/admin/users.php" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Foydalanuvchilar
            </a>
        </li>
        <li>
            <a href="<?= SITE_URL ?>/admin/categories.php" class="<?= $currentPage === 'categories' ? 'active' : '' ?>">
                <i class="bi bi-grid"></i> Kategoriyalar
            </a>
        </li>
        <li>
            <a href="<?= SITE_URL ?>/admin/products.php" class="<?= $currentPage === 'products' ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i> Mahsulotlar
            </a>
        </li>
        <li>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="<?= $currentPage === 'orders' ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i> Buyurtmalar
            </a>
        </li>
        <li>
            <a href="<?= SITE_URL ?>/admin/reports.php" class="<?= $currentPage === 'reports' ? 'active' : '' ?>">
                <i class="bi bi-graph-up"></i> Hisobotlar
            </a>
        </li>
        <li style="margin-top:20px;border-top:1px solid rgba(255,255,255,0.1);padding-top:12px;">
            <a href="<?= SITE_URL ?>">
                <i class="bi bi-house"></i> Saytga qaytish
            </a>
        </li>
        <li>
            <a href="<?= SITE_URL ?>/auth/logout.php">
                <i class="bi bi-box-arrow-left"></i> Chiqish
            </a>
        </li>
    </ul>
</div>
