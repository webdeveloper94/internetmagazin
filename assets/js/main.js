// ===== Toast Notifications =====
function showToast(message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast-msg ${type}`;
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ===== Phone Mask =====
function applyPhoneMask(input) {
    input.addEventListener('input', function (e) {
        let value = this.value.replace(/\D/g, '');
        // Remove leading 998 if present (we add it as prefix)
        if (value.startsWith('998')) {
            value = value.substring(3);
        }
        let formatted = '+998 ';
        if (value.length > 0) formatted += value.substring(0, 2);
        if (value.length > 2) formatted += ' ' + value.substring(2, 5);
        if (value.length > 5) formatted += '-' + value.substring(5, 7);
        if (value.length > 7) formatted += '-' + value.substring(7, 9);
        this.value = formatted;
    });

    input.addEventListener('focus', function () {
        if (!this.value) this.value = '+998 ';
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && this.value.length <= 5) {
            e.preventDefault();
        }
    });
}

// Init phone masks
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.phone-mask').forEach(applyPhoneMask);
});

// ===== Cart Operations (AJAX) =====
function addToCart(productId, sizeId = null, qty = 1) {
    fetch(SITE_URL + '/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', product_id: productId, size_id: sizeId, quantity: qty })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast("Savatga qo'shildi!");
                updateCartBadge(data.cart_count);
            } else {
                showToast(data.message || "Xatolik yuz berdi", 'error');
            }
        })
        .catch(() => showToast("Server xatoligi", 'error'));
}

function updateCartQuantity(cartId, change) {
    fetch(SITE_URL + '/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update', cart_id: cartId, change: change })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast(data.message || "Xatolik", 'error');
            }
        });
}

function removeFromCart(cartId) {
    fetch(SITE_URL + '/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove', cart_id: cartId })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
}

function updateCartBadge(count) {
    document.querySelectorAll('.cart-badge').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}

// ===== Favorites (AJAX) =====
function toggleFavorite(productId, btn) {
    fetch(SITE_URL + '/api/favorites.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    btn.classList.add('active');
                    btn.innerHTML = '<i class="bi bi-heart-fill"></i>';
                    showToast("Sevimlilarga qo'shildi!");
                } else {
                    btn.classList.remove('active');
                    btn.innerHTML = '<i class="bi bi-heart"></i>';
                    showToast("Sevimlilardan olib tashlandi");
                }
                updateFavBadge(data.fav_count);
            } else {
                showToast(data.message || "Xatolik", 'error');
            }
        });
}

function updateFavBadge(count) {
    document.querySelectorAll('.fav-badge').forEach(el => {
        el.textContent = count;
        el.style.display = count > 0 ? 'flex' : 'none';
    });
}

// ===== Admin: Dynamic Size Rows =====
function addSizeRow() {
    const container = document.getElementById('sizesContainer');
    if (!container) return;
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'row mb-2 size-row';
    row.innerHTML = `
        <div class="col-5">
            <input type="text" name="size_name[]" class="form-control" placeholder="O'lcham nomi (masalan: 500ml, XL)" required>
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

// ===== Admin: Toggle Sizes =====
function toggleSizes(checkbox) {
    const sizesSection = document.getElementById('sizesSection');
    const basePriceField = document.getElementById('basePriceField');
    if (checkbox.checked) {
        sizesSection.style.display = 'block';
        basePriceField.style.display = 'none';
    } else {
        sizesSection.style.display = 'none';
        basePriceField.style.display = 'block';
    }
}

// ===== Admin: Sidebar Toggle (Mobile) =====
function toggleAdminSidebar() {
    document.querySelector('.admin-sidebar').classList.toggle('show');
}

// ===== Search =====
document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            const input = this.querySelector('input[name="q"]');
            if (!input.value.trim()) {
                e.preventDefault();
                input.focus();
            }
        });
    }
});

// ===== Card Slider (mini image carousel) =====
function slideCard(sliderId, direction) {
    const slider = document.getElementById(sliderId);
    if (!slider) return;
    const slides = slider.querySelectorAll('.card-slide');
    const dots = slider.querySelectorAll('.card-dot');
    let current = 0;
    slides.forEach((s, i) => { if (s.classList.contains('active')) current = i; });

    let next;
    if (direction === 'prev') {
        next = (current - 1 + slides.length) % slides.length;
    } else if (direction === 'next') {
        next = (current + 1) % slides.length;
    } else {
        next = parseInt(direction);
    }

    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    slides[next].classList.add('active');
    dots[next].classList.add('active');
}
