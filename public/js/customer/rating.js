/**
 * Bookshop Rating System — All Interactions
 */
(function () {
    'use strict';

    // ========== STAR INPUT ==========
    function initStarInputs() {
        document.querySelectorAll('[data-star-input]').forEach(function (container) {
            const name = container.dataset.starInput;
            const stars = container.querySelectorAll('.star-rating-star');
            const hiddenInput = document.getElementById('starInput' + name);
            const label = document.querySelector('[data-star-label="' + name + '"]');
            const labels = ['', 'Poor', 'Fair', 'Good', 'Great', 'Excellent'];

            stars.forEach(function (star) {
                star.addEventListener('click', function () {
                    const value = parseInt(this.dataset.starValue);
                    if (hiddenInput) hiddenInput.value = value;

                    stars.forEach(function (s, index) {
                        s.classList.toggle('active', index < value);
                    });

                    if (label) {
                        label.querySelector('span').textContent = labels[value] + '!';
                        label.querySelector('span').style.color = value >= 4 ? 'var(--color-success)' : 'var(--color-text-secondary)';
                    }

                    const textSection = document.getElementById('reviewTextSection');
                    if (textSection) {
                        textSection.style.display = value >= 5 ? '' : 'none';
                    }

                    const submitBtn = document.getElementById('reviewSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = value === 0;
                    }
                });

                star.addEventListener('mouseenter', function () {
                    const value = parseInt(this.dataset.starValue);
                    stars.forEach(function (s, index) {
                        s.classList.toggle('active', index < value);
                    });
                });
            });

            container.addEventListener('mouseleave', function () {
                const currentValue = hiddenInput ? parseInt(hiddenInput.value) || 0 : 0;
                stars.forEach(function (s, index) {
                    s.classList.toggle('active', index < currentValue);
                });
            });
        });
    }

    // ========== CHARACTER COUNT ==========
    function initCharCount() {
        const textarea = document.getElementById('reviewText');
        const counter = document.getElementById('reviewCharCount');
        if (!textarea || !counter) return;

        textarea.addEventListener('input', function () {
            counter.textContent = this.value.length;
            if (this.value.length >= 480) {
                counter.style.color = 'var(--color-danger)';
            } else if (this.value.length >= 400) {
                counter.style.color = 'var(--color-accent)';
            } else {
                counter.style.color = 'var(--color-text-muted)';
            }
        });

        counter.textContent = textarea.value.length;
    }

    // ========== MODAL ==========
    window.openReviewModal = function () {
        const overlay = document.getElementById('reviewModalOverlay');
        if (!overlay) return;
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeReviewModal = function () {
        const overlay = document.getElementById('reviewModalOverlay');
        if (!overlay) return;
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    };

    // ========== SUBMIT REVIEW ==========
    window.submitReview = async function (e, bookId) {
        e.preventDefault();

        const ratingInput = document.getElementById('starInputreview_rating');
        const reviewText = document.getElementById('reviewText');
        const submitBtn = document.getElementById('reviewSubmitBtn');
        const spinner = submitBtn?.querySelector('.review-modal-spinner');
        const btnText = submitBtn?.querySelector('span:first-of-type');

        if (!ratingInput || !ratingInput.value) return;

        submitBtn.disabled = true;
        if (spinner) spinner.style.display = 'inline-flex';
        if (btnText) btnText.style.display = 'none';

        try {
            const resp = await fetch('/books/' + bookId + '/rate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    rating: parseInt(ratingInput.value),
                    review: reviewText?.value || null,
                }),
            });

            const data = await resp.json();

            if (resp.ok) {
                const form = document.getElementById('reviewForm');
                const success = document.getElementById('reviewSuccess');
                if (form) form.style.display = 'none';
                if (success) success.style.display = 'block';

                // Update book rating in the page if displayed
                if (data.book_rating) {
                    const ratingEl = document.querySelector('.book-detail-rating');
                    if (ratingEl) ratingEl.textContent = data.book_rating.rating;
                }

                // Reload reviews via AJAX instead of full page reload
                setTimeout(async function () {
                    if (typeof loadReviews === 'function') {
                        await loadReviews(bookId, 'newest');
                    }
                    closeReviewModal();
                    if (form) { form.style.display = ''; form.reset(); }
                    if (success) success.style.display = 'none';
                }, 1500);
            } else {
                showToast(data.message || 'Something went wrong.', 'error');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            if (spinner) spinner.style.display = 'none';
            if (btnText) btnText.style.display = '';
        }
    };

    // ========== HELPFUL VOTE ==========
    window.toggleHelpful = async function (reviewId, btn) {
        try {
            const resp = await fetch('/ratings/' + reviewId + '/helpful', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const data = await resp.json();
            if (resp.ok) {
                btn.classList.toggle('active', data.action === 'added');
                const countEl = btn.querySelector('.review-card-helpful-count');
                if (countEl) countEl.textContent = '(' + data.count + ')';
            }
        } catch (err) {
            console.error('Helpful vote error:', err);
        }
    };

    // ========== EDIT REVIEW ==========
    window.editReview = function (reviewId) {
        openReviewModal();
    };

    // ========== DELETE REVIEW ==========
    window.deleteReview = async function (reviewId) {
        if (!confirm('Delete your review? This cannot be undone.')) return;

        try {
            const resp = await fetch('/ratings/' + reviewId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (resp.ok) {
                const card = document.querySelector('[data-review-id="' + reviewId + '"]');
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    card.style.transition = 'all 0.3s ease';
                    setTimeout(function () { card.remove(); }, 300);
                }
                // No full page reload — card removed from DOM
            }
        } catch (err) {
            console.error('Delete error:', err);
        }
    };

    // ========== TOAST HELPER ==========
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;border-radius:12px;font-size:14px;font-weight:500;animation:toastIn 0.3s ease;max-width:400px;';
        toast.style.background = type === 'error' ? 'var(--color-danger-bg)' : 'var(--color-success-bg)';
        toast.style.color = type === 'error' ? '#991B1B' : '#065F46';
        toast.style.border = '1px solid ' + (type === 'error' ? '#FECACA' : '#A7F3D0');
        toast.innerHTML = '<i class="fas fa-' + (type === 'error' ? 'circle-exclamation' : 'circle-check') + '"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 4000);
    }

    // ========== ANIMATE BARS ==========
    function animateRatingBars() {
        const bars = document.querySelectorAll('.rating-stats-bar');
        if (bars.length === 0) return;

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    bars.forEach(function (bar) {
                        const width = bar.style.width;
                        bar.style.width = '0%';
                        setTimeout(function () { bar.style.width = width; }, 100);
                    });
                    observer.disconnect();
                }
            });
        }, { threshold: 0.3 });

        const statsContainer = bars[0].closest('.rating-stats');
        if (statsContainer) observer.observe(statsContainer);
    }

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        initStarInputs();
        initCharCount();
        animateRatingBars();
    });

})();