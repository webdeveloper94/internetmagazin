<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$message = '';
$messageType = '';
$uploadDir = __DIR__ . '/../uploads/products/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
$allowed = ['jpg','jpeg','png','gif','webp'];

// Helper: upload a single image file
function uploadProductImage($fileData, $uploadDir, $allowed) {
    if (empty($fileData['name']) || $fileData['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;
    $filename = uniqid('prod_') . '.' . $ext;
    move_uploaded_file($fileData['tmp_name'], $uploadDir . $filename);
    return $filename;
}

// Helper: delete image file
function deleteImage($filename, $uploadDir) {
    if ($filename && file_exists($uploadDir . $filename)) {
        unlink($uploadDir . $filename);
    }
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $name = trim($_POST['name']);
            $categoryId = intval($_POST['category_id']);
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $hasSizes = isset($_POST['has_sizes']) ? 1 : 0;

            // Upload 3 images
            $image = uploadProductImage($_FILES['image'], $uploadDir, $allowed);
            $image2 = uploadProductImage($_FILES['image2'], $uploadDir, $allowed);
            $image3 = uploadProductImage($_FILES['image3'], $uploadDir, $allowed);

            $stmt = $pdo->prepare("INSERT INTO products (name, category_id, description, price, image, image2, image3, has_sizes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $categoryId, $description, $price, $image, $image2, $image3, $hasSizes]);
            $productId = $pdo->lastInsertId();

            // Add sizes
            if ($hasSizes && !empty($_POST['size_name'])) {
                $sizeNames = $_POST['size_name'];
                $sizePrices = $_POST['size_price'];
                for ($i = 0; $i < count($sizeNames); $i++) {
                    if (!empty($sizeNames[$i]) && isset($sizePrices[$i])) {
                        $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size_name, price) VALUES (?, ?, ?)");
                        $stmt->execute([$productId, trim($sizeNames[$i]), floatval($sizePrices[$i])]);
                    }
                }
            }

            $message = "Mahsulot qo'shildi";
            $messageType = 'success';
            break;

        case 'update':
            $prodId = intval($_POST['prod_id']);
            $name = trim($_POST['name']);
            $categoryId = intval($_POST['category_id']);
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $hasSizes = isset($_POST['has_sizes']) ? 1 : 0;

            // Get old images
            $old = $pdo->prepare("SELECT image, image2, image3 FROM products WHERE id = ?");
            $old->execute([$prodId]);
            $oldData = $old->fetch();

            // Upload new images (only if provided)
            $image = uploadProductImage($_FILES['image'], $uploadDir, $allowed);
            $image2 = uploadProductImage($_FILES['image2'], $uploadDir, $allowed);
            $image3 = uploadProductImage($_FILES['image3'], $uploadDir, $allowed);

            // Delete replaced old files
            if ($image) deleteImage($oldData['image'], $uploadDir);
            if ($image2) deleteImage($oldData['image2'], $uploadDir);
            if ($image3) deleteImage($oldData['image3'], $uploadDir);

            // Delete image if checkbox checked
            if (isset($_POST['delete_image2']) && $oldData['image2']) {
                deleteImage($oldData['image2'], $uploadDir);
                $image2 = ''; // set to empty to clear
            }
            if (isset($_POST['delete_image3']) && $oldData['image3']) {
                deleteImage($oldData['image3'], $uploadDir);
                $image3 = '';
            }

            // Build update query
            $sql = "UPDATE products SET name=?, category_id=?, description=?, price=?, has_sizes=?";
            $params = [$name, $categoryId, $description, $price, $hasSizes];

            if ($image) { $sql .= ", image=?"; $params[] = $image; }
            if ($image2 !== null) { $sql .= ", image2=?"; $params[] = $image2 ?: null; }
            if ($image3 !== null) { $sql .= ", image3=?"; $params[] = $image3 ?: null; }

            $sql .= " WHERE id=?";
            $params[] = $prodId;
            $pdo->prepare($sql)->execute($params);

            // Update sizes
            $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?")->execute([$prodId]);
            if ($hasSizes && !empty($_POST['size_name'])) {
                $sizeNames = $_POST['size_name'];
                $sizePrices = $_POST['size_price'];
                for ($i = 0; $i < count($sizeNames); $i++) {
                    if (!empty($sizeNames[$i]) && isset($sizePrices[$i])) {
                        $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size_name, price) VALUES (?, ?, ?)");
                        $stmt->execute([$prodId, trim($sizeNames[$i]), floatval($sizePrices[$i])]);
                    }
                }
            }

            $message = "Mahsulot yangilandi";
            $messageType = 'success';
            break;

        case 'delete':
            $prodId = intval($_POST['prod_id']);
            $old = $pdo->prepare("SELECT image, image2, image3 FROM products WHERE id = ?");
            $old->execute([$prodId]);
            $oldData = $old->fetch();
            if ($oldData) {
                deleteImage($oldData['image'], $uploadDir);
                deleteImage($oldData['image2'], $uploadDir);
                deleteImage($oldData['image3'], $uploadDir);
            }
            $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$prodId]);
            $message = "Mahsulot o'chirildi";
            $messageType = 'success';
            break;
    }
}

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Get products
// Search & Pagination
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$filterCat = isset($_GET['cat']) ? intval($_GET['cat']) : 0;

$where = " WHERE 1=1";
$params = [];

if ($filterCat) {
    $where .= " AND p.category_id = ?";
    $params[] = $filterCat;
}

if ($search) {
    $where .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}

// Total count for pagination
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM products p" . $where);
$totalStmt->execute($params);
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// Fetch products
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id" . $where . " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($query);
foreach ($params as $i => $p) {
    $stmt->bindValue($i + 1, $p);
}
$stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$adminPageTitle = 'Mahsulotlar';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-lg-none me-2" onclick="toggleAdminSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="d-inline"><i class="bi bi-box-seam"></i> Mahsulotlar</h3>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Qidiruv..." value="<?= sanitize($search) ?>" style="width:200px;">
                <select name="cat" class="form-select form-select-sm" onchange="this.form.submit()" style="width:160px;">
                    <option value="0">Barcha kategoriyalar</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $filterCat == $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-lg"></i> Qo'shish
            </button>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="admin-table">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Rasmlar</th>
                    <th>Nomi</th>
                    <th>Kategoriya</th>
                    <th>Narx</th>
                    <th>O'lchamlar</th>
                    <th>Amallar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Mahsulotlar topilmadi</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                    <?php
                        $sizes = [];
                        if ($prod['has_sizes']) {
                            $sStmt = $pdo->prepare("SELECT * FROM product_sizes WHERE product_id = ?");
                            $sStmt->execute([$prod['id']]);
                            $sizes = $sStmt->fetchAll();
                        }
                        $imgCount = ($prod['image'] ? 1 : 0) + ($prod['image2'] ? 1 : 0) + ($prod['image3'] ? 1 : 0);
                    ?>
                    <tr>
                        <td><?= $prod['id'] ?></td>
                        <td>
                            <div class="d-flex gap-1 align-items-center">
                                <?php if ($prod['image']): ?>
                                    <img src="<?= SITE_URL ?>/uploads/products/<?= $prod['image'] ?>" 
                                         style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <div style="width:40px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if ($imgCount > 1): ?>
                                    <span class="badge bg-info" style="font-size:0.65rem;">+<?= $imgCount - 1 ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><strong><?= sanitize($prod['name']) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= sanitize($prod['category_name']) ?></span></td>
                        <td>
                            <?php if ($prod['has_sizes'] && !empty($sizes)): ?>
                                <small>
                                    <?php foreach ($sizes as $s): ?>
                                        <?= sanitize($s['size_name']) ?>: <?= formatPrice($s['price']) ?><br>
                                    <?php endforeach; ?>
                                </small>
                            <?php else: ?>
                                <strong><?= formatPrice($prod['price']) ?></strong> so'm
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($prod['has_sizes']): ?>
                                <span class="badge bg-info"><?= count($sizes) ?> ta</span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProd<?= $prod['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('O\'chirilsinmi?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="prod_id" value="<?= $prod['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>

                            <!-- Edit Product Modal -->
                            <div class="modal fade" id="editProd<?= $prod['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Mahsulotni tahrirlash</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="prod_id" value="<?= $prod['id'] ?>">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Nomi</label>
                                                        <input type="text" name="name" class="form-control" value="<?= sanitize($prod['name']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Kategoriya</label>
                                                        <select name="category_id" class="form-select" required>
                                                            <?php foreach ($categories as $cat): ?>
                                                                <option value="<?= $cat['id'] ?>" <?= $prod['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                                    <?= sanitize($cat['name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tavsif</label>
                                                    <textarea name="description" class="form-control" rows="2"><?= sanitize($prod['description'] ?? '') ?></textarea>
                                                </div>

                                                <!-- 3 Images -->
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-bold">Asosiy rasm <span class="text-danger">*</span></label>
                                                        <?php if ($prod['image']): ?>
                                                            <div class="mb-2"><img src="<?= SITE_URL ?>/uploads/products/<?= $prod['image'] ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--primary);"></div>
                                                        <?php endif; ?>
                                                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">2-rasm <small class="text-muted">(ixtiyoriy)</small></label>
                                                        <?php if ($prod['image2']): ?>
                                                            <div class="mb-2 d-flex align-items-end gap-1">
                                                                <img src="<?= SITE_URL ?>/uploads/products/<?= $prod['image2'] ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                                                                <label class="text-danger" style="font-size:0.7rem;cursor:pointer;">
                                                                    <input type="checkbox" name="delete_image2" value="1" class="form-check-input form-check-input-sm"> o'chirish
                                                                </label>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="image2" class="form-control form-control-sm" accept="image/*">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">3-rasm <small class="text-muted">(ixtiyoriy)</small></label>
                                                        <?php if ($prod['image3']): ?>
                                                            <div class="mb-2 d-flex align-items-end gap-1">
                                                                <img src="<?= SITE_URL ?>/uploads/products/<?= $prod['image3'] ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                                                                <label class="text-danger" style="font-size:0.7rem;cursor:pointer;">
                                                                    <input type="checkbox" name="delete_image3" value="1" class="form-check-input form-check-input-sm"> o'chirish
                                                                </label>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="image3" class="form-control form-control-sm" accept="image/*">
                                                    </div>
                                                </div>

                                                <div class="mb-3" id="basePriceFieldEdit<?= $prod['id'] ?>" style="<?= $prod['has_sizes'] ? 'display:none;' : '' ?>">
                                                    <label class="form-label">Narxi (so'm)</label>
                                                    <input type="number" name="price" class="form-control" value="<?= $prod['price'] ?>" step="100">
                                                </div>
                                                <div class="form-check mb-3">
                                                    <input type="checkbox" name="has_sizes" class="form-check-input" id="hasSizesEdit<?= $prod['id'] ?>" 
                                                           <?= $prod['has_sizes'] ? 'checked' : '' ?>
                                                           onchange="toggleSizesEdit(this, <?= $prod['id'] ?>)">
                                                    <label class="form-check-label" for="hasSizesEdit<?= $prod['id'] ?>">O'lchamlar mavjud</label>
                                                </div>
                                                <div id="sizesSectionEdit<?= $prod['id'] ?>" style="<?= $prod['has_sizes'] ? '' : 'display:none;' ?>">
                                                    <label class="form-label fw-bold">O'lchamlar va narxlar:</label>
                                                    <div id="sizesContainerEdit<?= $prod['id'] ?>">
                                                        <?php foreach ($sizes as $s): ?>
                                                        <div class="row mb-2 size-row">
                                                            <div class="col-5">
                                                                <input type="text" name="size_name[]" class="form-control" value="<?= sanitize($s['size_name']) ?>" placeholder="O'lcham" required>
                                                            </div>
                                                            <div class="col-5">
                                                                <input type="number" name="size_price[]" class="form-control" value="<?= $s['price'] ?>" placeholder="Narxi" step="100" required>
                                                            </div>
                                                            <div class="col-2">
                                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.size-row').remove()">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="addSizeRowTo('sizesContainerEdit<?= $prod['id'] ?>')">
                                                        <i class="bi bi-plus"></i> O'lcham qo'shish
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                                                <button type="submit" class="btn btn-primary">Saqlash</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?><?= $filterCat ? "&cat=$filterCat" : '' ?><?= $search ? "&q=" . urlencode($search) : "" ?>">Oldingi</a>
            </li>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $filterCat ? "&cat=$filterCat" : '' ?><?= $search ? "&q=" . urlencode($search) : "" ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?><?= $filterCat ? "&cat=$filterCat" : '' ?><?= $search ? "&q=" . urlencode($search) : "" ?>">Keyingi</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Mahsulot qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomi</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategoriya</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Tanlang...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tavsif</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <!-- 3 Images -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Asosiy rasm <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*" required>
                            <small class="text-muted">Majburiy</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">2-rasm <small class="text-muted">(ixtiyoriy)</small></label>
                            <input type="file" name="image2" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">3-rasm <small class="text-muted">(ixtiyoriy)</small></label>
                            <input type="file" name="image3" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3" id="basePriceField">
                        <label class="form-label">Narxi (so'm)</label>
                        <input type="number" name="price" class="form-control" step="100" value="0">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="has_sizes" class="form-check-input" id="hasSizesNew" onchange="toggleSizes(this)">
                        <label class="form-check-label" for="hasSizesNew">O'lchamlar mavjud (dona, gram, millilitr, kiyim o'lchami va h.k.)</label>
                    </div>
                    <div id="sizesSection" style="display:none;">
                        <label class="form-label fw-bold">O'lchamlar va narxlar:</label>
                        <div id="sizesContainer"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSizeRow()">
                            <i class="bi bi-plus"></i> O'lcham qo'shish
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>
function toggleSizesEdit(checkbox, prodId) {
    const section = document.getElementById('sizesSectionEdit' + prodId);
    const priceField = document.getElementById('basePriceFieldEdit' + prodId);
    section.style.display = checkbox.checked ? 'block' : 'none';
    priceField.style.display = checkbox.checked ? 'none' : 'block';
}

function addSizeRowTo(containerId) {
    const container = document.getElementById(containerId);
    const row = document.createElement('div');
    row.className = 'row mb-2 size-row';
    row.innerHTML = `
        <div class="col-5">
            <input type="text" name="size_name[]" class="form-control" placeholder="O'lcham nomi (500ml, XL...)" required>
        </div>
        <div class="col-5">
            <input type="number" name="size_price[]" class="form-control" placeholder="Narxi" step="100" required>
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.size-row').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
}
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
