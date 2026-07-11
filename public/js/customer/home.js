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
            slides.forEach(function (s) {
                s.classList.remove("active");
            });
            dots.forEach(function (d) {
                d.classList.remove("active");
            });
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
            prevBtn.addEventListener("click", function () {
                prev();
                startAutoplay();
            });
        if (nextBtn)
            nextBtn.addEventListener("click", function () {
                next();
                startAutoplay();
            });

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
            hero.addEventListener(
                "touchstart",
                function (e) {
                    touchStartX = e.changedTouches[0].screenX;
                },
                { passive: true },
            );

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

    // ============================================
    // CAROUSEL - Infinite Loop with Dots
    // ============================================
    document.addEventListener("DOMContentLoaded", function () {
        function initCarousel(trackId, prevId, nextId, dotsId) {
            const track = document.getElementById(trackId);
            const prevBtn = document.getElementById(prevId);
            const nextBtn = document.getElementById(nextId);
            const dotsContainer = document.getElementById(dotsId);

            if (!track) return;

            let currentIndex = 0;
            let cardWidth = 200;
            let gap = 20;
            let visibleCards = 4;
            let totalCards = track.children.length;
            let maxIndex = 0;
            let isTransitioning = false;

            function getVisibleCards() {
                const isMobile = window.innerWidth <= 768;
                const isSmallMobile = window.innerWidth <= 480;

                if (isSmallMobile) {
                    cardWidth = 140;
                    gap = 10;
                    return 2;
                } else if (isMobile) {
                    cardWidth = 160;
                    gap = 12;
                    return 2;
                } else {
                    cardWidth = 200;
                    gap = 20;
                    return 4;
                }
            }

            // ============================================
            // INFINITE LOOP LOGIC
            // ============================================
            function updateCarousel() {
                const total = track.children.length;
                visibleCards = getVisibleCards();

                // Calculate total pages
                const totalPages = Math.ceil(total / visibleCards);

                // If we have less than visible cards, no loop needed
                if (total <= visibleCards) {
                    maxIndex = 0;
                    currentIndex = 0;
                    const offset = 0;
                    track.style.transform = `translateX(-${offset}px)`;
                    updateButtons();
                    updateDots(totalPages);
                    return;
                }

                // Calculate max index based on total pages
                maxIndex = totalPages - 1;

                // ===== INFINITE LOOP: Reset to 0 when reaching the end =====
                if (currentIndex > maxIndex) {
                    currentIndex = 0;
                    // Smooth animation back to start
                    track.style.transition =
                        "transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)";
                }

                if (currentIndex < 0) {
                    currentIndex = maxIndex;
                    track.style.transition =
                        "transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)";
                }

                const offset = currentIndex * (cardWidth + gap);
                track.style.transform = `translateX(-${offset}px)`;

                // Update buttons
                updateButtons();

                // Update dots
                updateDots(totalPages);
            }

            function updateButtons() {
                // With infinite loop, buttons are never disabled
                if (prevBtn) {
                    prevBtn.disabled = false;
                }
                if (nextBtn) {
                    nextBtn.disabled = false;
                }
            }

            function updateDots(totalPages) {
                if (!dotsContainer) return;

                // Clear dots
                dotsContainer.innerHTML = "";

                // Create dots based on total pages
                for (let i = 0; i < totalPages; i++) {
                    const dot = document.createElement("button");
                    dot.className = `carousel-dot ${i === currentIndex ? "active" : ""}`;
                    dot.setAttribute("data-index", i);
                    dot.setAttribute("aria-label", `Go to slide ${i + 1}`);

                    dot.addEventListener("click", function () {
                        if (isTransitioning) return;
                        currentIndex = parseInt(this.dataset.index);
                        updateCarousel();
                    });

                    dotsContainer.appendChild(dot);
                }
            }

            // ============================================
            // CLICK HANDLERS
            // ============================================
            if (prevBtn) {
                prevBtn.addEventListener("click", function () {
                    if (isTransitioning) return;
                    isTransitioning = true;

                    currentIndex--;
                    updateCarousel();

                    setTimeout(() => {
                        isTransitioning = false;
                    }, 500);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener("click", function () {
                    if (isTransitioning) return;
                    isTransitioning = true;

                    currentIndex++;
                    updateCarousel();

                    setTimeout(() => {
                        isTransitioning = false;
                    }, 500);
                });
            }

            // ============================================
            // MOBILE SWIPE SUPPORT
            // ============================================
            let startX = 0;
            let isDragging = false;
            const wrapper = track.closest(".carousel-track-wrapper");

            if (wrapper) {
                wrapper.addEventListener(
                    "touchstart",
                    function (e) {
                        startX = e.touches[0].clientX;
                        isDragging = true;
                    },
                    { passive: true },
                );

                wrapper.addEventListener(
                    "touchend",
                    function (e) {
                        if (!isDragging) return;
                        isDragging = false;

                        const endX = e.changedTouches[0].clientX;
                        const diff = startX - endX;

                        // Swipe threshold: 50px
                        if (Math.abs(diff) > 50 && !isTransitioning) {
                            isTransitioning = true;

                            if (diff > 0) {
                                currentIndex++;
                            } else {
                                currentIndex--;
                            }

                            updateCarousel();

                            setTimeout(() => {
                                isTransitioning = false;
                            }, 500);
                        }
                    },
                    { passive: true },
                );
            }

            // ============================================
            // UPDATE ON RESIZE
            // ============================================
            let resizeTimeout;
            window.addEventListener("resize", function () {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(updateCarousel, 200);
            });

            // ============================================
            // KEYBOARD NAVIGATION
            // ============================================
            document.addEventListener("keydown", function (e) {
                // Only if carousel is focused or visible
                const rect = track.getBoundingClientRect();
                const isVisible =
                    rect.top < window.innerHeight && rect.bottom > 0;

                if (!isVisible) return;

                if (e.key === "ArrowLeft") {
                    e.preventDefault();
                    if (prevBtn) prevBtn.click();
                } else if (e.key === "ArrowRight") {
                    e.preventDefault();
                    if (nextBtn) nextBtn.click();
                }
            });

            // ============================================
            // OBSERVER: Reset when carousel comes into view
            // ============================================
            if (wrapper && "IntersectionObserver" in window) {
                const observer = new IntersectionObserver(
                    function (entries) {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                // Reset to first position when carousel comes into view
                                currentIndex = 0;
                                updateCarousel();
                            }
                        });
                    },
                    {
                        threshold: 0.3,
                    },
                );

                observer.observe(wrapper);
            }

            // Initial update
            setTimeout(updateCarousel, 100);
        }

        // Initialize both carousels
        if (document.getElementById("bestSellersTrack")) {
            initCarousel(
                "bestSellersTrack",
                "bestSellersPrev",
                "bestSellersNext",
                "bestSellersDots",
            );
        }

        if (document.getElementById("newArrivalsTrack")) {
            initCarousel(
                "newArrivalsTrack",
                "newArrivalsPrev",
                "newArrivalsNext",
                "newArrivalsDots",
            );
        }
    });

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
            .then(function (res) {
                return res.json();
            })
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
                    el.innerHTML =
                        '<span style="color:var(--color-danger);">Offer expired</span>';
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
                if (hoursEl)
                    hoursEl.textContent = String(hours).padStart(2, "0");
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
                const addBtn = card
                    ? card.querySelector(
                          '.btn-add-cart[data-book-id="' + id + '"]',
                      )
                    : null;
                if (addBtn) {
                    addBtn.click();
                } else {
                    const globalBtn = document.querySelector(
                        '.btn-add-cart[data-book-id="' + id + '"]',
                    );
                    if (globalBtn) globalBtn.click();
                }
            });
        });
    }

    // ========== INIT ==========
    document.addEventListener("DOMContentLoaded", function () {
        initHeroSlider();
        initCountdown();
        initOverlayCart();
    });
})();
