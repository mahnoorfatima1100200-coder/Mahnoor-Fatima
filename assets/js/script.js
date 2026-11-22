/**
 * SKINORA - Main Frontend Interactive Script
 */

document.addEventListener('DOMContentLoaded', function () {
    // Mobile navigation drawer toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const navClose = document.querySelector('.nav-close-btn');
    const navBackdrop = document.querySelector('.nav-backdrop');

    function openMobileMenu() {
        if (mainNav) mainNav.classList.add('open');
        if (navBackdrop) navBackdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (mainNav) mainNav.classList.remove('open');
        if (navBackdrop) navBackdrop.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (menuToggle) menuToggle.addEventListener('click', openMobileMenu);
    if (navClose) navClose.addEventListener('click', closeMobileMenu);
    if (navBackdrop) navBackdrop.addEventListener('click', closeMobileMenu);

    // Product Gallery Thumbnail Clicker
    const galleryMain = document.getElementById('gallery-main-display');
    const galleryThumbs = document.querySelectorAll('.thumb-item');

    galleryThumbs.forEach(thumb => {
        thumb.addEventListener('click', function () {
            galleryThumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const targetSrc = this.getAttribute('data-full-src');
            if (galleryMain && targetSrc) {
                galleryMain.src = targetSrc;
            }
        });
    });

    // Product Detail Tabs Switcher
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetTab = this.getAttribute('data-tab');
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            const activePane = document.getElementById(targetTab);
            if (activePane) activePane.classList.add('active');
        });
    });

    // Quantity selectors increment / decrement
    document.querySelectorAll('.qty-control').forEach(control => {
        const minusBtn = control.querySelector('.qty-minus');
        const plusBtn = control.querySelector('.qty-plus');
        const input = control.querySelector('.qty-input');

        if (minusBtn && input) {
            minusBtn.addEventListener('click', () => {
                let currentVal = parseInt(input.value) || 1;
                if (currentVal > 1) {
                    input.value = currentVal - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }

        if (plusBtn && input) {
            plusBtn.addEventListener('click', () => {
                let currentVal = parseInt(input.value) || 1;
                let max = parseInt(input.getAttribute('max')) || 99;
                if (currentVal < max) {
                    input.value = currentVal + 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
    });

    // Skincare FAQ Accordion
    document.querySelectorAll('.faq-item-header').forEach(header => {
        header.addEventListener('click', function () {
            const item = this.parentElement;
            item.classList.toggle('active');
            const content = item.querySelector('.faq-item-body');
            if (content) {
                if (item.classList.contains('active')) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                } else {
                    content.style.maxHeight = '0px';
                }
            }
        });
    });
});

/**
 * Universal Toast Notification Generator
 */
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icon = type === 'success' ? '✓' : (type === 'error' ? '✕' : 'ℹ');
    toast.innerHTML = `<span class="toast-icon">${icon}</span> <span>${message}</span>`;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}
