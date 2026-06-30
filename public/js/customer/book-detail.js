/**
 * Book Detail Page — Interactions
 */
(function () {
    'use strict';

    // ========== QUANTITY SELECTOR ==========
    window.changeQuantity = function (delta) {
        var input = document.getElementById('quantity');
        if (!input) return;
        var val = parseInt(input.value) || 1;
        var max = parseInt(input.max) || 99;
        var newVal = val + delta;
        if (newVal < 1) newVal = 1;
        if (newVal > max) newVal = max;
        input.value = newVal;
    };

    // ========== STICKY MOBILE BAR ==========
    function initMobileBar() {
        var bar = document.getElementById('bookMobileBar');
        if (!bar) return;

        var purchaseCard = document.querySelector('.book-hero-purchase');
        if (!purchaseCard) return;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    bar.style.display = 'none';
                } else {
                    if (window.innerWidth <= 768) {
                        bar.style.display = 'flex';
                    }
                }
            });
        }, { threshold: 0 });

        observer.observe(purchaseCard);

        // Hide on desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                bar.style.display = 'none';
            }
        });
    }

    // ========== BUY NOW ==========
    function initBuyNow() {
        var btn = document.getElementById('buyNowBtn');
        if (!btn) return;

        btn.addEventListener('click', async function () {
            var bookId = document.querySelector('.btn-add-cart')?.dataset?.bookId;
            if (!bookId) return;

            var qty = document.getElementById('quantity')?.value || 1;

            try {
                await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ book_id: bookId, quantity: parseInt(qty) }),
                });
                window.location.href = '/checkout'; // Keep redirect for checkout flow
            } catch (err) {
                console.error('Buy now error:', err);
            }
        });
    }

    // ========== LOAD REVIEWS (AJAX) ==========
    window.loadReviews = async function (bookId, sort) {
        var list = document.getElementById('reviewsList');
        if (!list) return;

        list.innerHTML = '<div style="text-align:center;padding:40px;color:var(--color-text-muted);"><i class="fas fa-spinner fa-spin"></i></div>';

        try {
            var resp = await fetch('/books/' + bookId + '/reviews?sort=' + sort);
            var data = await resp.json();
            var reviews = data.data;

            if (reviews.length === 0) {
                list.innerHTML = '<div class="book-reviews-empty"><i class="fas fa-star-half-stroke"></i><h4>No reviews yet</h4><p>Be the first person to review this book.</p></div>';
            } else {
                list.innerHTML = reviews.map(function (r) {
                    var stars = '';
                    for (var i = 1; i <= 5; i++) {
                        stars += '<i class="fas fa-star' + (i <= r.rating ? '' : '-empty') + '"></i>';
                    }
                    var date = new Date(r.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    var avatar = r.customer?.image_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(r.customer?.name || 'User') + '&background=1E3A8A&color=fff&size=80';
                    return '<div class="review-card" data-review-id="' + r.id + '">' +
                        '<div class="review-card-header">' +
                            '<div class="review-card-user">' +
                                '<img src="' + avatar + '" alt="' + (r.customer?.name || '') + '" class="review-card-avatar" loading="lazy">' +
                                '<div class="review-card-user-info">' +
                                    '<span class="review-card-name">' + (r.customer?.name || 'Anonymous') + '</span>' +
                                    '<span class="review-card-badge"><i class="fas fa-check-circle"></i> Verified Purchase</span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="review-card-meta">' +
                                '<div class="review-card-stars">' + stars + '</div>' +
                                '<span class="review-card-date">' + date + '</span>' +
                            '</div>' +
                        '</div>' +
                        (r.review ? '<p class="review-card-text">' + r.review + '</p>' : '') +
                        '<div class="review-card-footer">' +
                            '<button class="review-card-helpful" data-review-id="' + r.id + '" onclick="toggleHelpful(' + r.id + ', this)">' +
                                '<i class="fas fa-thumbs-up"></i> Helpful <span class="review-card-helpful-count">(' + (r.helpful_count || 0) + ')</span>' +
                            '</button>' +
                        '</div>' +
                    '</div>';
                }).join('');
            }
        } catch (err) {
            list.innerHTML = '<p style="text-align:center;color:var(--color-text-muted);">Failed to load reviews.</p>';
        }
    };

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        initMobileBar();
        initBuyNow();
    });

})();