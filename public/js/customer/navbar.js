/**
 * Bookshop Navbar — All Interactions
 */
(function () {
    "use strict";

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    ready(function () {
        initScrollDetection();
        initSidebar();
        initUserDropdown();
        initAuthButtons();
        initCartButtons();
        initNotificationBell();
        initMobileMore();
        initDesktopSearchDropdown();
        initMobileSearchEnter();
        loadUnreadCount();
    });

    // ========== SCROLL ==========
    function initScrollDetection() {
        const navbar = document.getElementById("navbar");
        if (!navbar) return;
        window.addEventListener("scroll", function () {
            navbar.classList.toggle("scrolled", window.scrollY > 10);
        }, { passive: true });
    }

    // ========== SIDEBAR ==========
    function initSidebar() {
        const toggle = document.getElementById("navbarMobileToggle");
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("sidebarOverlay");
        const closeBtn = document.getElementById("sidebarClose");
        if (!toggle || !sidebar) return;

        function open() {
            sidebar.classList.add("open");
            if (overlay) overlay.classList.add("show");
            if (toggle) toggle.setAttribute("aria-expanded", "true");
            document.body.style.overflow = "hidden";
        }
        function close() {
            sidebar.classList.remove("open");
            if (overlay) overlay.classList.remove("show");
            if (toggle) toggle.setAttribute("aria-expanded", "false");
            document.body.style.overflow = "";
        }

        toggle.addEventListener("click", function (e) { e.preventDefault(); e.stopPropagation(); sidebar.classList.contains("open") ? close() : open(); });
        if (overlay) overlay.addEventListener("click", close);
        if (closeBtn) closeBtn.addEventListener("click", close);
        document.addEventListener("keydown", function (e) { if (e.key === "Escape") close(); });
        if (sidebar) sidebar.querySelectorAll("a").forEach(function (link) { link.addEventListener("click", close); });
    }

    // ========== USER DROPDOWN ==========
    function initUserDropdown() {
        const user = document.getElementById("navbarUser");
        if (!user) return;
        const trigger = user.querySelector(".navbar-user-trigger");
        if (!trigger) return;
        trigger.addEventListener("click", function (e) { e.stopPropagation(); user.classList.toggle("open"); });
        document.addEventListener("click", function (e) { if (!user.contains(e.target)) user.classList.remove("open"); });
    }

    // ========== AUTH BUTTONS ==========
    function initAuthButtons() {
        const signIn = document.getElementById("navbarSignInBtn");
        if (signIn) signIn.addEventListener("click", function (e) { e.preventDefault(); if (typeof openLoginModal === "function") openLoginModal(); });

        const register = document.getElementById("navbarRegisterBtn");
        if (register) {
            register.addEventListener("click", function (e) {
                e.preventDefault();
                if (typeof openLoginModal === "function") {
                    openLoginModal();
                    setTimeout(function () {
                        if (typeof switchToRegister === "function") switchToRegister(new Event("click"));
                    }, 200);
                }
            });
        }

        const mobileSignIn = document.querySelector(".navbar-mobile-actions .navbar-auth-btn");
        if (mobileSignIn) {
            mobileSignIn.addEventListener("click", function (e) {
                e.preventDefault();
                if (typeof openLoginModal === "function") openLoginModal();
            });
        }
    }

    // ========== CART BUTTONS ==========
    function initCartButtons() {
        const desktopCartBtn = document.getElementById("navbarCartBtn");
        if (desktopCartBtn) {
            desktopCartBtn.addEventListener("click", function (e) {
                e.preventDefault(); e.stopPropagation();
                if (typeof Cart !== "undefined" && Cart.toggle) Cart.toggle();
            });
        }

        const mobileCartBtn = document.getElementById("mobileCartBtn");
        if (mobileCartBtn) {
            mobileCartBtn.addEventListener("click", function (e) {
                e.preventDefault(); e.stopPropagation();
                if (typeof Cart !== "undefined" && Cart.toggle) Cart.toggle();
            });
        }

        window.addEventListener('cartUpdated', function (e) { updateCartBadges(e.detail.count); });
        window.updateCartCount = function (count) { updateCartBadges(count); };
    }

    function updateCartBadges(count) {
        document.querySelectorAll('#cartCount, #mobileCartCount').forEach(function (badge) {
            if (!badge) return;
            if (count > 0) { badge.textContent = count; badge.style.display = 'flex'; }
            else { badge.style.display = 'none'; }
        });
    }

    // ========== NOTIFICATIONS ==========
    function initNotificationBell() {
        const desktopBtn = document.getElementById("navbarNotificationBtn");
        const mobileBtn = document.getElementById("mobileNotificationBtn");
        function handleClick(e) { e.stopPropagation(); toggleNotifications(); }
        if (desktopBtn) desktopBtn.addEventListener("click", handleClick);
        if (mobileBtn) mobileBtn.addEventListener("click", handleClick);
    }

    function loadUnreadCount() {
        fetch("/notifications", {
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" }
        }).then(function (r) { return r.json(); }).then(function (data) {
            document.querySelectorAll('#notificationBadge, #mobileNotificationBadge').forEach(function (b) {
                if (b && data.unread_count > 0) { b.textContent = data.unread_count; b.style.display = "flex"; }
            });
        }).catch(function () {});
    }

    window.toggleNotifications = function () {
        const panel = document.getElementById("notificationPanel");
        if (!panel) return;
        panel.classList.toggle("open");
        if (panel.classList.contains("open")) loadNotifications();
    };

    window.markAllRead = function () {
        fetch("/notifications/read-all", {
            method: "PATCH",
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" }
        }).then(function () {
            document.querySelectorAll('#notificationBadge, #mobileNotificationBadge').forEach(function (b) { if (b) b.style.display = "none"; });
            loadNotifications();
        });
    };

    function loadNotifications() {
        fetch("/notifications", {
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" }
        }).then(function (r) { return r.json(); }).then(function (data) {
            const list = document.getElementById("notificationList");
            if (!list) return;
            if (data.notifications.length === 0) {
                list.innerHTML = '<div class="notification-empty"><i class="fas fa-bell-slash"></i><p>No notifications yet</p></div>';
            } else {
                list.innerHTML = data.notifications.map(function (n) {
                    return '<div class="notification-item ' + (n.read_at ? '' : 'unread') + '" onclick="markRead(' + n.id + ')"><div class="notification-item-icon promotion"><i class="fas fa-tag"></i></div><div class="notification-item-content"><div class="notification-item-title">' + n.title + '</div><div class="notification-item-message">' + n.message + '</div><div class="notification-item-time">' + n.created_at + '</div></div></div>';
                }).join('');
            }
        }).catch(function () {});
    }

    window.markRead = function (id) {
        fetch("/notifications/" + id + "/read", {
            method: "PATCH",
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" }
        }).then(function () { loadNotifications(); });
    };

    document.addEventListener("click", function (e) {
        const panel = document.getElementById("notificationPanel");
        const desktopBtn = document.getElementById("navbarNotificationBtn");
        const mobileBtn = document.getElementById("mobileNotificationBtn");
        if (panel && !panel.contains(e.target) && !(desktopBtn && desktopBtn.contains(e.target)) && !(mobileBtn && mobileBtn.contains(e.target))) {
            panel.classList.remove("open");
        }
    });

    // ========== MOBILE MORE ==========
    function initMobileMore() {
        const btn = document.getElementById("navbarMoreBtn");
        if (!btn) return;
        btn.addEventListener("click", function (e) { e.preventDefault(); e.stopPropagation(); toggleMobileMore(); });
    }

    window.toggleMobileMore = function () {
        const panel = document.getElementById("mobileMorePanel");
        const overlay = document.getElementById("mobileMoreOverlay");
        if (!panel || !overlay) return;
        if (panel.classList.contains("open")) {
            panel.classList.remove("open"); overlay.classList.remove("show"); document.body.style.overflow = "";
        } else {
            panel.classList.add("open"); overlay.classList.add("show"); document.body.style.overflow = "hidden";
        }
    };

    window.toggleMorePanel = function () {
        const panel = document.getElementById('morePanel');
        const overlay = document.getElementById('morePanelOverlay');
        if (!panel || !overlay) return;
        if (panel.classList.contains('open')) {
            panel.classList.remove('open'); overlay.classList.remove('show'); document.body.style.overflow = '';
        } else {
            panel.classList.add('open'); overlay.classList.add('show'); document.body.style.overflow = 'hidden';
        }
    };

    // ========== MOBILE SEARCH ==========
   window.openMobileSearch = function () {
        const overlay = document.getElementById("navbarSearchOverlay");
        if (!overlay) return;
        if (overlay.classList.contains("open")) { closeMobileSearch(); return; }

        overlay.classList.add("open");
        document.body.style.overflow = "hidden";

        // Defer listener attachment so the button click that opened it 
        // doesn't immediately trigger the close logic
        setTimeout(function() {
            document.addEventListener("click", handleSearchOutsideClick);
        }, 0);

        const input = document.getElementById("mobileSearchInput");
        if (input) setTimeout(function () { input.focus(); }, 150);
    };

    function handleSearchOutsideClick(e) {
        const overlay = document.getElementById("navbarSearchOverlay");
        // If the click is outside the overlay, close it
        if (overlay && !overlay.contains(e.target)) {
            closeMobileSearch();
        }
    }

    // Search your entire JS file and delete ALL other instances of this function.
    window.closeMobileSearch = function () {
        const overlay = document.getElementById("navbarSearchOverlay");
        if (overlay) overlay.classList.remove("open");
        
        document.body.style.overflow = "";
        
        // This MUST execute to prevent the bug.
        document.removeEventListener("click", handleSearchOutsideClick);
    };


    function initMobileSearchEnter() {
        const input = document.getElementById('mobileSearchInput');
        const form = document.getElementById('mobileSearchForm');
        if (input && form) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.value.trim()) { window.location.href = "/books?search=" + encodeURIComponent(this.value.trim()); }
                }
            });
        }
    }

    

    // ========== DESKTOP SEARCH DROPDOWN ==========
    function initDesktopSearchDropdown() {
        const input = document.getElementById('desktopSearchInput');
        const dropdown = document.getElementById('desktopSearchDropdown');
        if (!input || !dropdown) return;

        input.addEventListener('focus', function () { dropdown.style.display = 'block'; });

        document.addEventListener('click', function (e) {
            const wrap = document.getElementById('desktopSearchWrap');
            if (wrap && !wrap.contains(e.target)) dropdown.style.display = 'none';
        });
    }

    // ========== SEARCH HISTORY ==========
    window.clearSearchHistory = async function () {
        try {
            await fetch('/search-history/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            document.querySelectorAll('.navbar-search-dropdown-section, .navbar-search-suggestions-section').forEach(function (el) {
                if (el.querySelector('.navbar-search-dropdown-title, .navbar-search-suggestions-title')?.textContent.includes('Recent')) {
                    el.remove();
                }
            });
        } catch (err) {}
    };

    window.deleteSearchHistory = async function (index) {
        try {
            await fetch('/search-history/' + index, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            // Remove item from both desktop and mobile DOM
            document.querySelectorAll('.navbar-search-suggestion-item-wrap').forEach(function (el, i) {
                if (i === index) el.remove();
            });
            // Hide section if empty
            document.querySelectorAll('.navbar-search-dropdown-section, .navbar-search-suggestions-section').forEach(function (section) {
                if (!section.querySelector('.navbar-search-suggestion-item-wrap') && section.querySelector('.navbar-search-dropdown-title, .navbar-search-suggestions-title')?.textContent.includes('Recent')) {
                    section.remove();
                }
            });
        } catch (err) {}
    };

    // ========== ESCAPE KEY ==========
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const morePanel = document.getElementById('morePanel');
            if (morePanel && morePanel.classList.contains('open')) window.toggleMorePanel();
            const mobileMorePanel = document.getElementById('mobileMorePanel');
            if (mobileMorePanel && mobileMorePanel.classList.contains('open')) window.toggleMobileMore();
        }
    });

})();