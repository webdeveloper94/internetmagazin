<?php
/**
 * Admin: Kategoriyalar Boshqaruvi
 */
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

require_admin();

$page_title = 'Kategoriyalar - Admin Panel';
$db = Database::getInstance();

// Kategoriyalarni olish
$sql = "SELECT * FROM categories ORDER BY created_at DESC";
$categories = $db->fetchAll($sql);

require_once 'includes/header.php';
?>

<div class="admin-header">
    <h1>Kategoriyalar Boshqaruvi</h1>
    <button class="btn btn-primary" onclick="showAddModal()">+ Yangi Kategoriya</button>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <?php if ($categories && count($categories) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Icon</th>
                        <th>Nom</th>
                        <th>Tavsif</th>
                        <th>Yaratilgan</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr id="category-row-<?php echo $category['id']; ?>">
                            <td><?php echo $category['id']; ?></td>
                            <td><i class="bi <?php echo $category['icon'] ?: 'bi-box'; ?> category-icon-preview"></i></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['description'] ? mb_substr($category['description'], 0, 50) . '...' : '-'); ?></td>
                            <td><?php echo format_date($category['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary edit-category-btn" 
                                            data-id="<?php echo $category['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-icon="<?php echo htmlspecialchars($category['icon']); ?>"
                                            data-description="<?php echo htmlspecialchars($category['description']); ?>">
                                        Tahrirlash
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-category-btn" 
                                            data-id="<?php echo $category['id']; ?>">
                                        O'chirish
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Kategoriyalar yo'q</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Yangi Kategoriya</h2>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <form id="categoryForm">
            <input type="hidden" id="categoryId" name="id">
            
            <div class="form-group">
                <label for="categoryName">Kategoriya Nomi *</label>
                <input type="text" id="categoryName" name="name" required>
            </div>

            <style>
                .icon-selector {
                    display: flex !important;
                    flex-wrap: wrap !important;
                    gap: 10px !important;
                    padding: 15px !important;
                    background: #f8fafc !important;
                    border: 2px solid #e2e8f0 !important;
                    border-radius: 0.5rem !important;
                    max-height: 250px !important;
                    overflow-y: auto !important;
                    margin-top: 10px !important;
                    justify-content: flex-start !important;
                }
                .icon-option {
                    width: 45px !important;
                    height: 45px !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    font-size: 1.5rem !important;
                    cursor: pointer !important;
                    border-radius: 0.5rem !important;
                    border: 2px solid transparent !important;
                    background: white !important;
                    transition: all 0.2s !important;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
                    color: #475569 !important;
                }
                .icon-option:hover {
                    background: #f1f5f9 !important;
                    color: #6366f1 !important;
                    transform: translateY(-2px) !important;
                }
                .icon-option.selected {
                    background: #eef2ff !important;
                    border-color: #6366f1 !important;
                    color: #4f46e5 !important;
                    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1) !important;
                }
            </style>

            <div class="form-group">
                <label>Icon Tanlang</label>
                <div class="icon-selector" id="iconSelector">
                    <?php
                    $predefined_icons = [
                        'bi-box', 'bi-fire', 'bi-phone', 'bi-shirt', 'bi-house', 'bi-bicycle', 'bi-book', 
                        'bi-controller', 'bi-cup-hot', 'bi-apple', 'bi-luggage', 'bi-tools', 'bi-laptop', 
                        'bi-watch', 'bi-headphones', 'bi-camera', 'bi-bag', 'bi-gem', 'bi-car-front', 'bi-plugin'
                    ];
                    foreach ($predefined_icons as $ico):
                    ?>
                        <div class="icon-option" data-icon="<?php echo $ico; ?>" onclick="handleIconSelect(this)">
                            <i class="bi <?php echo $ico; ?>"></i>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="categoryIcon" name="icon" value="bi-box">
            </div>

            <script>
                function handleIconSelect(element) {
                    // Remove selected class from all options
                    document.querySelectorAll('.icon-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    // Add selected class to clicked option
                    element.classList.add('selected');
                    // Update hidden input
                    const iconValue = element.getAttribute('data-icon');
                    document.getElementById('categoryIcon').value = iconValue;
                }
            </script>
            
            <div class="form-group">
                <label for="categoryDescription">Tavsif</label>
                <textarea id="categoryDescription" name="description" rows="3"></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Bekor qilish</button>
                <button type="submit" class="btn btn-primary">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
