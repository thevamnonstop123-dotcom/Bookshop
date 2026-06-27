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
            slides.forEach((s) => s.classList.remove("active"));
            dots.forEach((d) => d.classList.remove("active"));

            slides[index].classList.add("active");
            if (dots[index]) dots[index].classList.add("active");
            current = index;
        }

        function next() {
            goTo((current + 1) % slides.length);
        }

        function prev() {
            goTo((current - 1 + slides.length) % slides.length);
        }

        function startAutoplay() {
            stopAutoplay();
            autoplay = setInterval(next, 5000);
        }

        function stopAutoplay() {
            clearInterval(autoplay);
        }

        if (prevBtn)
            prevBtn.addEventListener("click", () => {
                prev();
                startAutoplay();
            });
        if (nextBtn)
            nextBtn.addEventListener("click", () => {
                next();
                startAutoplay();
            });

        dots.forEach((dot) => {
            dot.addEventListener("click", function () {
                goTo(parseInt(this.dataset.index));
                startAutoplay();
            });
        });

        // Pause on hover
        const hero = document.getElementById("heroSlider");
        if (hero) {
            hero.addEventListener("mouseenter", stopAutoplay);
            hero.addEventListener("mouseleave", startAutoplay);
        }

        // Touch swipe
        let touchStartX = 0;
        let touchEndX = 0;

        if (hero) {
            hero.addEventListener(
                "touchstart",
                (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                },
                { passive: true },
            );

            hero.addEventListener("touchend", (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) next();
                    else prev();
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
            const cardWidth = card
                ? Math.round(card.getBoundingClientRect().width)
                : 220;
            const gap = parseInt(getComputedStyle(track).gap) || 24;
            const visibleWidth = track.parentElement.clientWidth;
            return {
                cardWidth,
                gap,
                visibleWidth,
                scrollAmount: cardWidth + gap,
            };
        }

        function updateButtons() {
            const { visibleWidth } = getMetrics();
            prevBtn.disabled = scrollPosition <= 0;
            nextBtn.disabled =
                scrollPosition >= track.scrollWidth - visibleWidth - 10;
        }

        function scroll(dir) {
            const { scrollAmount, visibleWidth } = getMetrics();
            scrollPosition += dir * scrollAmount;
            scrollPosition = Math.max(
                0,
                Math.min(scrollPosition, track.scrollWidth - visibleWidth),
            );
            track.style.transform = `translateX(-${scrollPosition}px)`;
            updateButtons();
        }

        prevBtn.addEventListener("click", () => scroll(-1));
        nextBtn.addEventListener("click", () => scroll(1));

        // Touch support
        let touchStart = 0;
        track.addEventListener(
            "touchstart",
            (e) => {
                touchStart = e.touches[0].clientX;
            },
            { passive: true },
        );

        track.addEventListener("touchend", (e) => {
            const diff = touchStart - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) {
                scroll(diff > 0 ? 1 : -1);
            }
        });

        updateButtons();
        window.addEventListener("resize", () => {
            const visible = getMetrics().visibleWidth;
            scrollPosition = Math.max(
                0,
                Math.min(scrollPosition, track.scrollWidth - visible),
            );
            track.style.transform = `translateX(-${scrollPosition}px)`;
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
        button.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
        button.disabled = true;

        // Simulate API call — replace with your actual endpoint
        fetch("/newsletter/subscribe", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
            },
            body: JSON.stringify({ email: email }),
        })
            .then((res) => res.json())
            .then((data) => {
                form.querySelector("input").value = "";
                button.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }, 3000);
            })
            .catch(() => {
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
    };

    // ========== INIT ==========
    document.addEventListener("DOMContentLoaded", function () {
        initHeroSlider();
        initCarousel("newArrivalsTrack", "newArrivalsPrev", "newArrivalsNext");
        initCarousel("bestSellersTrack", "bestSellersPrev", "bestSellersNext");

        // Overlay Add to Cart — delegate to existing add-cart button in the same card
        document.querySelectorAll(".overlay-add-cart").forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();
                const id = this.dataset.bookId;
                const card = this.closest(".book-card");
                if (!card) return;
                const addBtn = card.querySelector(
                    '.btn-add-cart[data-book-id="' + id + '"]',
                );
                if (addBtn) {
                    addBtn.click();
                } else {
                    // fallback: trigger global first add-cart
                    const globalBtn = document.querySelector(
                        '.btn-add-cart[data-book-id="' + id + '"]',
                    );
                    if (globalBtn) globalBtn.click();
                }
            });
        });
    });

    // Countdown timer
    document.querySelectorAll(".hero-countdown").forEach((el) => {
        const endDate = new Date(el.dataset.end).getTime();

        const update = () => {
            const diff = endDate - Date.now();
            if (diff <= 0) {
                el.innerHTML =
                    '<span style="color:#EF4444;">Offer expired</span>';
                return;
            }
            const days = Math.floor(diff / 86400000);
            const hours = Math.floor((diff % 86400000) / 3600000);
            const mins = Math.floor((diff % 3600000) / 60000);
            const secs = Math.floor((diff % 60000) / 1000);

            // Only show days if > 0
            const daysEl = el.querySelector(".countdown-days");
            if (daysEl) {
                daysEl.textContent = String(days).padStart(2, "0");
                daysEl.parentElement.style.display = days > 0 ? "" : "none";
            }
            el.querySelector(".countdown-hours").textContent = String(
                hours,
            ).padStart(2, "0");
            el.querySelector(".countdown-mins").textContent = String(
                mins,
            ).padStart(2, "0");
            el.querySelector(".countdown-secs").textContent = String(
                secs,
            ).padStart(2, "0");
        };

        update();
        setInterval(update, 1000);
    });
})();
