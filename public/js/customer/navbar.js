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
            toggle.setAttribute("aria-expanded", "true");
            document.body.style.overflow = "hidden";
        }
        function close() {
            sidebar.classList.remove("open");
            if (overlay) overlay.classList.remove("show");
            toggle.setAttribute("aria-expanded", "false");
            document.body.style.overflow = "";
        }

        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            sidebar.classList.contains("open") ? close() : open();
        });
        if (overlay) overlay.addEventListener("click", close);
        if (closeBtn) closeBtn.addEventListener("click", close);
        document.addEventListener("keydown", function (e) { if (e.key === "Escape") close(); });
        if (sidebar) {
            sidebar.querySelectorAll("a").forEach(function (link) { link.addEventListener("click", close); });
        }
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
        if (signIn) {
            signIn.addEventListener("click", function (e) {
                e.preventDefault();
                openLoginModal();
            });
        }

        const register = document.getElementById("navbarRegisterBtn");
        if (register) {
            register.addEventListener("click", function (e) {
                e.preventDefault();
                openLoginModal();
                setTimeout(function () { switchToRegister(new Event("click")); }, 200);
            });
        }
    }

    function initCartButton() {
        const cartBtn = document.getElementById("navbarCartBtn");
        if (!cartBtn) return;
        cartBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (typeof Cart !== "undefined" && Cart.toggle) Cart.toggle();
        });
    }
})();
