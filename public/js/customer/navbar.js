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
        initCartButton();
        initSearchOverlay();
        initNotificationBell();
        initMoreMenu();
        loadUnreadCount();
    });

    function initScrollDetection() {
        const navbar = document.getElementById("navbar");
        if (!navbar) return;
        window.addEventListener("scroll", function () {
            navbar.classList.toggle("scrolled", window.scrollY > 10);
        }, { passive: true });
    }

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

    function initUserDropdown() {
        const user = document.getElementById("navbarUser");
        if (!user) return;
        const trigger = user.querySelector(".navbar-user-trigger");
        if (!trigger) return;
        trigger.addEventListener("click", function (e) { e.stopPropagation(); user.classList.toggle("open"); });
        document.addEventListener("click", function (e) { if (!user.contains(e.target)) user.classList.remove("open"); });
    }

    function initAuthButtons() {
        const signIn = document.getElementById("navbarSignInBtn");
        if (signIn) signIn.addEventListener("click", function (e) { e.preventDefault(); if (typeof openLoginModal === "function") openLoginModal(); });
        const register = document.getElementById("navbarRegisterBtn");
        if (register) {
            register.addEventListener("click", function (e) {
                e.preventDefault();
                if (typeof openLoginModal === "function") { openLoginModal(); setTimeout(function () { if (typeof switchToRegister === "function") switchToRegister(new Event("click")); }, 200); }
            });
        }
    }

    function initCartButton() {
        const cartBtn = document.getElementById("navbarCartBtn");
        if (!cartBtn) return;
        cartBtn.addEventListener("click", function (e) { e.preventDefault(); e.stopPropagation(); if (typeof Cart !== "undefined" && Cart.toggle) Cart.toggle(); });
    }

    function initSearchOverlay() {
        const toggle = document.getElementById("navbarSearchToggle");
        const overlay = document.getElementById("navbarSearchOverlay");
        const back = document.getElementById("navbarSearchBack");
        const input = overlay ? overlay.querySelector("input") : null;
        if (!toggle || !overlay) return;
        toggle.addEventListener("click", function () { overlay.classList.add("open"); if (input) input.focus(); document.body.style.overflow = "hidden"; });
        if (back) back.addEventListener("click", function () { overlay.classList.remove("open"); document.body.style.overflow = ""; });
    }

    function initNotificationBell() {
        const btn = document.getElementById("navbarNotificationBtn");
        if (!btn) return;
        btn.addEventListener("click", function (e) { e.stopPropagation(); toggleNotifications(); });
    }

    function loadUnreadCount() {
        fetch("/notifications", { headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" } })
            .then(r => r.json()).then(data => { const b = document.getElementById("notificationBadge"); if (b && data.unread_count > 0) { b.textContent = data.unread_count; b.style.display = "flex"; } }).catch(() => {});
    }

    window.toggleNotifications = function () {
        const panel = document.getElementById("notificationPanel");
        if (!panel) return;
        panel.classList.toggle("open");
        if (panel.classList.contains("open")) loadNotifications();
    };

    window.markAllRead = function () {
        fetch("/notifications/read-all", { method: "PATCH", headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" } })
            .then(() => { const b = document.getElementById("notificationBadge"); if (b) b.style.display = "none"; loadNotifications(); });
    };

    function loadNotifications() {
        fetch("/notifications", { headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" } })
            .then(r => r.json()).then(data => {
                const list = document.getElementById("notificationList");
                if (!list) return;
                list.innerHTML = data.notifications.length === 0
                    ? '<div class="notification-empty"><i class="fas fa-bell-slash"></i><p>No notifications yet</p></div>'
                    : data.notifications.map(n => `<div class="notification-item ${n.read_at ? '' : 'unread'}" onclick="markRead(${n.id})"><div class="notification-item-icon promotion"><i class="fas fa-tag"></i></div><div class="notification-item-content"><div class="notification-item-title">${n.title}</div><div class="notification-item-message">${n.message}</div><div class="notification-item-time">${n.created_at}</div></div></div>`).join('');
            }).catch(() => {});
    }

    function markRead(id) {
        fetch(`/notifications/${id}/read`, { method: "PATCH", headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" } }).then(() => loadNotifications());
    }

    document.addEventListener("click", function (e) {
        const panel = document.getElementById("notificationPanel");
        const btn = document.getElementById("navbarNotificationBtn");
        if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) panel.classList.remove("open");
    });

    function initMoreMenu() {
        const btn = document.getElementById("moreMenuBtn");
        if (!btn) return;
        btn.addEventListener("click", function (e) { e.preventDefault(); e.stopPropagation(); toggleMoreMenu(); });
    }

    window.toggleMoreMenu = function () {
        const menu = document.getElementById("moreMenu");
        const overlay = document.getElementById("moreMenuOverlay");
        if (!menu || !overlay) return;
        if (menu.classList.contains("open")) { menu.classList.remove("open"); overlay.classList.remove("show"); document.body.style.overflow = ""; }
        else { menu.classList.add("open"); overlay.classList.add("show"); document.body.style.overflow = "hidden"; }
    };

})();