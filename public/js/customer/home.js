/**
 * Bookshop Homepage — Premium Interactions
 * Hero Slider, Carousels, Newsletter
 */

(function () {
    "use strict";

    // ========== HERO SLIDER ==========
    function initHeroSlider() {
        const slides = document.querySelectorAll(".hero-slide");
        const dots = document.querySelectorAll(".hero-dot");
        const prevBtn = document.getElementById("heroPrev");
        const nextBtn = document.getElementById("heroNext");

        if (slides.length <= 1) return;

        let current = 0;
        let autoplay;

        function goTo(index) {
            slides.forEach(function (s) { s.classList.remove("active"); });
            dots.forEach(function (d) { d.classList.remove("active"); });
            slides[index].classList.add("active");
            if (dots[index]) dots[index].classList.add("active");
            current = index;
        }

        function next() { goTo((current + 1) % slides.length); }
        function prev() { goTo((current - 1 + slides.length) % slides.length); }
        function startAutoplay() { stopAutoplay(); autoplay = setInterval(next, 5000); }
        function stopAutoplay() { clearInterval(autoplay); }

        if (prevBtn) prevBtn.addEventListener("click", function () { prev(); startAutoplay(); });
        if (nextBtn) nextBtn.addEventListener("click", function () { next(); startAutoplay(); });

        dots.forEach(function (dot) {
            dot.addEventListener("click", function () {
                goTo(parseInt(this.dataset.index));
                startAutoplay();
            });
        });

        const hero = document.getElementById("heroSlider");
        if (hero) {
            hero.addEventListener("mouseenter", stopAutoplay);
            hero.addEventListener("mouseleave", startAutoplay);

            let touchStartX = 0;
            hero.addEventListener("touchstart", function (e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            hero.addEventListener("touchend", function (e) {
                const touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    diff > 0 ? next() : prev();
                    startAutoplay();
                }
            });
        }

        startAutoplay();
    }

    // ========== CAROUSEL ==========
    function initCarousel(trackId, prevId, nextId) {
        const track = document.getElementById(trackId);
        const prevBtn = document.getElementById(prevId);
        const nextBtn = document.getElementById(nextId);

        if (!track || !prevBtn || !nextBtn) return;

        const cards = track.querySelectorAll(".book-card");
        if (cards.length === 0) return;

        let scrollPosition = 0;

        function getMetrics() {
            const card = track.querySelector(".book-card");
            const cardWidth = card ? Math.round(card.getBoundingClientRect().width) : 220;
            const gap = parseInt(getComputedStyle(track).gap) || 24;
            const visibleWidth = track.parentElement.clientWidth;
            return { cardWidth, gap, visibleWidth, scrollAmount: cardWidth + gap };
        }

        function updateButtons() {
            const metrics = getMetrics();
            prevBtn.disabled = scrollPosition <= 0;
            nextBtn.disabled = scrollPosition >= track.scrollWidth - metrics.visibleWidth - 10;
        }

        function scroll(dir) {
            const metrics = getMetrics();
            scrollPosition += dir * metrics.scrollAmount;
            scrollPosition = Math.max(0, Math.min(scrollPosition, track.scrollWidth - metrics.visibleWidth));
            track.style.transform = 'translateX(-' + scrollPosition + 'px)';
            updateButtons();
        }

        prevBtn.addEventListener("click", function () { scroll(-1); });
        nextBtn.addEventListener("click", function () { scroll(1); });

        let touchStart = 0;
        track.addEventListener("touchstart", function (e) {
            touchStart = e.touches[0].clientX;
        }, { passive: true });

        track.addEventListener("touchend", function (e) {
            const diff = touchStart - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) scroll(diff > 0 ? 1 : -1);
        });

        updateButtons();
        window.addEventListener("resize", function () {
            const visible = getMetrics().visibleWidth;
            scrollPosition = Math.max(0, Math.min(scrollPosition, track.scrollWidth - visible));
            track.style.transform = 'translateX(-' + scrollPosition + 'px)';
            updateButtons();
        });
    }

    // ========== NEWSLETTER ==========
    window.handleNewsletter = function (event) {
        event.preventDefault();
        const form = event.target;
        const email = form.querySelector('input[name="email"]').value;
        const button = form.querySelector("button");

        if (!email) return;

        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
        button.disabled = true;

        fetch("/newsletter/subscribe", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ email: email }),
        })
        .then(function (res) { return res.json(); })
        .then(function () {
            form.querySelector("input").value = "";
            button.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
            setTimeout(function () {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }, 3000);
        })
        .catch(function () {
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    };

    // ========== COUNTDOWN TIMER ==========
    function initCountdown() {
        document.querySelectorAll(".hero-countdown").forEach(function (el) {
            const endDate = new Date(el.dataset.end).getTime();

            function update() {
                const diff = endDate - Date.now();
                if (diff <= 0) {
                    el.innerHTML = '<span style="color:var(--color-danger);">Offer expired</span>';
                    return;
                }
                const days = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                const mins = Math.floor((diff % 3600000) / 60000);
                const secs = Math.floor((diff % 60000) / 1000);

                const daysEl = el.querySelector(".countdown-days");
                if (daysEl) {
                    daysEl.textContent = String(days).padStart(2, "0");
                    daysEl.parentElement.style.display = days > 0 ? "" : "none";
                }
                const hoursEl = el.querySelector(".countdown-hours");
                const minsEl = el.querySelector(".countdown-mins");
                const secsEl = el.querySelector(".countdown-secs");
                if (hoursEl) hoursEl.textContent = String(hours).padStart(2, "0");
                if (minsEl) minsEl.textContent = String(mins).padStart(2, "0");
                if (secsEl) secsEl.textContent = String(secs).padStart(2, "0");
            }

            update();
            setInterval(update, 1000);
        });
    }

    // ========== OVERLAY ADD TO CART ==========
    function initOverlayCart() {
        document.querySelectorAll(".overlay-add-cart").forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                const id = this.dataset.bookId;
                const card = this.closest(".book-card");
                const addBtn = card ? card.querySelector('.btn-add-cart[data-book-id="' + id + '"]') : null;
                if (addBtn) {
                    addBtn.click();
                } else {
                    const globalBtn = document.querySelector('.btn-add-cart[data-book-id="' + id + '"]');
                    if (globalBtn) globalBtn.click();
                }
            });
        });
    }

    // ========== INIT ==========
    document.addEventListener("DOMContentLoaded", function () {
        initHeroSlider();
        initCarousel("newArrivalsTrack", "newArrivalsPrev", "newArrivalsNext");
        initCarousel("bestSellersTrack", "bestSellersPrev", "bestSellersNext");
        initCountdown();
        initOverlayCart();
    });

})();