/**
 * SKINORA - Shopping Cart & Wishlist AJAX Operations
 */

document.addEventListener('DOMContentLoaded', function () {
    // Add to Cart handler (buttons with class .btn-add-to-cart)
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-add-to-cart');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.getAttribute('data-product-id');
        const qtyInput = document.querySelector(`.qty-input[data-product-id="${productId}"]`) || document.getElementById('single-product-qty');
        const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Adding...';

        fetch(skinoraConfig.baseUrl + 'actions/add-to-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                product_id: productId,
                quantity: quantity,
                csrf_token: skinoraConfig.csrfToken
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.success) {
                showToast(data.message || 'Product added to cart!', 'success');
                // Update badge counters
                const cartBadges = document.querySelectorAll('.cart-count-badge');
                cartBadges.forEach(b => b.textContent = data.cart_count);
            } else {
                showToast(data.message || 'Could not add product.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            console.error(err);
            showToast('An error occurred. Please try again.', 'error');
        });
    });

    // Wishlist Toggle
    document.body.addEventListener('click', function (e) {
        const wishBtn = e.target.closest('.product-wishlist-btn');
        if (!wishBtn) return;

        e.preventDefault();
        const productId = wishBtn.getAttribute('data-product-id');

        fetch(skinoraConfig.baseUrl + 'actions/add-wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                product_id: productId,
                csrf_token: skinoraConfig.csrfToken
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                wishBtn.classList.toggle('active', data.in_wishlist);
                const wishBadges = document.querySelectorAll('.wishlist-count-badge');
                wishBadges.forEach(b => b.textContent = data.wishlist_count);
            } else {
                showToast(data.message || 'Please log in to manage your wishlist.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Please sign in to save wishlist items.', 'error');
        });
    });

    // Cart Quantity change on Cart Page
    document.querySelectorAll('.cart-qty-change').forEach(input => {
        input.addEventListener('change', function () {
            const itemId = this.getAttribute('data-item-id');
            const quantity = parseInt(this.value) || 1;

            fetch(skinoraConfig.baseUrl + 'actions/update-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    item_id: itemId,
                    quantity: quantity,
                    csrf_token: skinoraConfig.csrfToken
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to recalculate discounts & totals
                } else {
                    showToast(data.message || 'Could not update item.', 'error');
                }
            });
        });
    });
});
