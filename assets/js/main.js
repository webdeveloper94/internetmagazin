/**
 * Asosiy JavaScript - Interaktiv Funksiyalar
 */

// ==================== DOM yuklanishini kutish ====================
document.addEventListener('DOMContentLoaded', function () {
    // Slider
    initSlider();

    // Mobile menu
    initMobileMenu();

    // Alert close
    initAlertClose();

    // Scroll animations
    initScrollAnimations();

    // Cart actions
    initCartActions();

    // Statistics counter
    initStatsCounter();

    // Back to top button
    initBackToTop();

    // Newsletter form
    initNewsletter();

    // Favorites functionality
    initFavorites();

    // Load favorites count
    loadFavoritesCount();
});

// ==================== Slider ====================
let currentSlide = 0;
let slideInterval;

function initSlider() {
    const slides = document.querySelectorAll('.slide');
    if (slides.length === 0) return;

    // Auto slide har 5 soniyada
    slideInterval = setInterval(() => {
        changeSlide(1);
    }, 5000);

    // Hover'da to'xtatish
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });

        sliderContainer.addEventListener('mouseleave', () => {
            slideInterval = setInterval(() => {
                changeSlide(1);
            }, 5000);
        });
    }
}

function changeSlide(direction) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    if (slides.length === 0) return;

    // Hozirgi slide'ni yashirish
    slides[currentSlide].classList.remove('active');
    if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

    // Yangi slide'ni hisoblash
    currentSlide = (currentSlide + direction + slides.length) % slides.length;

    // Yangi slide'ni ko'rsatish
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
}

function goToSlide(n) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    slides[currentSlide].classList.remove('active');
    if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

    currentSlide = n;

    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
}

// ==================== Mobile Menu ====================
function initMobileMenu() {
    const toggle = document.getElementById('mobileMenuToggle');
    const menu = document.querySelector('.navbar-menu');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        menu.classList.toggle('active');
        toggle.classList.toggle('active');
    });
}

// ==================== Alert Close ====================
function initAlertClose() {
    const closeButtons = document.querySelectorAll('.alert-close');

    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            this.closest('.alert').remove();
        });
    });
}

// ==================== Scroll Animations ====================
function initScrollAnimations() {
    const elements = document.querySelectorAll('.animate-on-scroll');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    elements.forEach(element => observer.observe(element));
}

// ==================== Cart Actions ====================
function initCartActions() {
    // Savatchaga qo'shish
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            addToCart(productId, this);
        });
    });

    // Miqdor o'zgartirish
    document.querySelectorAll('.qty-plus').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            updateCartQuantity(productId, 'increase');
        });
    });

    document.querySelectorAll('.qty-minus').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            updateCartQuantity(productId, 'decrease');
        });
    });

    // Savatchadan o'chirish
    document.querySelectorAll('.btn-remove').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            removeFromCart(productId);
        });
    });

    // Buyurtma berish
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', placeOrder);
    }
}

// ==================== AJAX: Savatchaga qo'shish ====================
function addToCart(productId, button) {
    // Button holatini o'zgartirish
    const originalText = button.innerHTML;
    button.innerHTML = '⏳';
    button.disabled = true;

    fetch('/onlineshop/ajax/cart_add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateCartBadge(data.cart_count);

                button.innerHTML = '✓ Qo\'shildi';
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
            } else {
                showNotification(data.message, 'error');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            showNotification('Xatolik yuz berdi', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        });
}

// ==================== AJAX: Miqdorni yangilash ====================
function updateCartQuantity(productId, action) {
    fetch('/onlineshop/ajax/cart_update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&action=${action}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.removed) {
                    // Elementni o'chirish
                    document.querySelector(`[data-cart-id="${productId}"]`)?.remove();
                } else {
                    // Miqdorni yangilash
                    const qtyInput = document.querySelector(`.qty-input[value="${productId}"]`);
                    if (qtyInput) qtyInput.value = data.new_quantity;

                    // Jami narxni yangilash
                    if (data.total_price !== undefined) {
                        const totalElement = document.querySelector('.total-price');
                        if (totalElement) {
                            totalElement.textContent = formatPrice(data.total_price);
                        }
                    }
                }

                updateCartBadge(data.cart_count);

                // Savatchada hech narsa qolmagan bo'lsa, sahifani yangilash
                if (data.cart_count === 0) {
                    location.reload();
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Xatolik yuz berdi', 'error');
        });
}

// ==================== AJAX: Savatchadan o'chirish ====================
function removeFromCart(productId) {
    if (!confirm('Mahsulotni o\'chirmoqchimisiz?')) return;

    fetch('/onlineshop/ajax/cart_remove.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateCartBadge(data.cart_count);

                // Elementni o'chirish
                const cartItem = document.querySelector(`[data-cart-id]`);
                if (cartItem) {
                    cartItem.style.opacity = '0';
                    setTimeout(() => {
                        cartItem.remove();

                        // Savatchada hech narsa qolmagan bo'lsa
                        if (data.cart_count === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Xatolik yuz berdi', 'error');
        });
}

// ==================== AJAX: Buyurtma berish ====================
function placeOrder() {
    if (!confirm('Buyurtmani tasdiqlaysizmi?')) return;

    const button = document.getElementById('placeOrderBtn');
    button.disabled = true;
    button.innerHTML = '⏳ Yuklanmoqda...';

    fetch('/onlineshop/ajax/place_order.php', {
        method: 'POST'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                updateCartBadge(0);

                // Buyurtmalar sahifasiga yo'naltirish
                setTimeout(() => {
                    window.location.href = '/onlineshop/orders.php';
                }, 1500);
            } else {
                // Check if missing contact info
                if (data.data && data.data.requires_contact_info) {
                    button.disabled = false;
                    button.innerHTML = 'Buyurtma Berish';
                    showContactInfoModal();
                } else {
                    showNotification(data.message, 'error');
                    button.disabled = false;
                    button.innerHTML = 'Buyurtma Berish';
                }
            }
        })
        .catch(error => {
            showNotification('Xatolik yuz berdi', 'error');
            button.disabled = false;
            button.innerHTML = 'Buyurtma Berish';
        });
}

// ==================== Contact Info Modal ====================
function showContactInfoModal() {
    const modal = document.getElementById('contactInfoModal');
    if (!modal) return;

    // Use Bootstrap modal if available
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else {
        modal.style.display = 'block';
        modal.classList.add('show');
    }

    // Setup save button handler
    const saveBtn = document.getElementById('saveContactInfoBtn');
    if (saveBtn) {
        saveBtn.onclick = saveContactInfo;
    }
}

function saveContactInfo() {
    const phone = document.getElementById('modalPhone').value.trim();
    const address = document.getElementById('modalAddress').value.trim();
    const errorDiv = document.getElementById('contactInfoError');
    const saveBtn = document.getElementById('saveContactInfoBtn');

    // Clear previous errors
    errorDiv.style.display = 'none';

    // Simple validation
    if (!phone || !address) {
        errorDiv.textContent = 'Barcha maydonlarni to\'ldiring';
        errorDiv.style.display = 'block';
        return;
    }

    if (address.length < 10) {
        errorDiv.textContent = 'Manzil kamida 10 ta belgidan iborat bo\'lishi kerak';
        errorDiv.style.display = 'block';
        return;
    }

    // Disable button
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saqlanmoqda...';

    // Send data
    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('address', address);

    fetch('/onlineshop/ajax/update_contact_info.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Ma\'lumotlar saqlandi!', 'success');

                // Close modal
                const modal = document.getElementById('contactInfoModal');
                if (typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                } else {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                }

                // Retry placing order
                setTimeout(() => {
                    placeOrder();
                }, 500);
            } else {
                errorDiv.textContent = data.message || 'Xatolik yuz berdi';
                errorDiv.style.display = 'block';
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-check-circle"></i> Saqlash va Davom Etish';
            }
        })
        .catch(error => {
            errorDiv.textContent = 'Xatolik yuz berdi';
            errorDiv.style.display = 'block';
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-circle"></i> Saqlash va Davom Etish';
        });
}

// ==================== Helper Functions ====================
function updateCartBadge(count) {
    const badge = document.querySelector('.cart-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

function formatPrice(price) {
    return new Intl.NumberFormat('uz-UZ').format(price) + ' so\'m';
}

function showNotification(message, type = 'info') {
    // Notification yaratish
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    // Body'ga qo'shish
    document.body.appendChild(notification);

    // Animatsiya
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // 3 soniyadan keyin o'chirish
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Notification CSS (dinamik qo'shish)
if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: -300px;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            z-index: 99999;
            transition: right 0.3s ease-out;
            min-width: 250px;
            max-width: 400px;
        }
        
        .notification.show {
            right: 20px;
        }
        
        .notification-success {
            border-left: 4px solid #10b981;
            color: #065f46;
        }
        
        .notification-error {
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        
        .notification-info {
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }
    `;
    document.head.appendChild(style);
}

// ==================== Statistics Counter Animation ====================
function initStatsCounter() {
    const stats = document.querySelectorAll('.stat-number');
    let activated = false;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !activated) {
                activated = true;
                stats.forEach(stat => {
                    const target = parseInt(stat.getAttribute('data-target'));
                    animateCounter(stat, target);
                });
            }
        });
    }, { threshold: 0.5 });

    const statsSection = document.querySelector('.stats-section');
    if (statsSection) observer.observe(statsSection);
}

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const duration = 2000; // 2 seconds
    const stepTime = duration / 100;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, stepTime);
}

// ==================== Back to Top Button ====================
function initBackToTop() {
    const backToTop = document.getElementById('backToTop');
    if (!backToTop) return;

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ==================== Newsletter Form ====================
function initNewsletter() {
    const form = document.getElementById('newsletterForm');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = form.querySelector('input[type="email"]').value;

        // Simulated submission
        showNotification('✉️ Rahmat! Siz muvaffaqiyatli obuna bo\'ldingiz!', 'success');
        form.reset();
    });
}

// ==================== Favorites System ====================
function initFavorites() {
    const favoriteButtons = document.querySelectorAll('.favorite-btn');

    favoriteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = this.getAttribute('data-product-id');
            toggleFavorite(productId, this);
        });
    });
}

function toggleFavorite(productId, button) {
    fetch('<?php echo SITE_URL; ?>/ajax/favorite_toggle.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle button state
                button.classList.toggle('active');

                // Animation
                button.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 200);

                // Update favorites count
                updateFavoritesCount(data.count);

                // Show notification
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Xatolik yuz berdi', 'error');
        });
}

function loadFavoritesCount() {
    // Check if user is logged in
    const favCountBadge = document.querySelector('.favorites-count');
    if (!favCountBadge) return;

    fetch('<?php echo SITE_URL; ?>/ajax/get_favorites_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                updateFavoritesCount(data.count);
            }
        })
        .catch(error => console.error('Error loading favorites count:', error));
}

function updateFavoritesCount(count) {
    const badge = document.querySelector('.favorites-count');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}
