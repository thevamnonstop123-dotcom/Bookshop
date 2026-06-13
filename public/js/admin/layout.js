/**
 * Bookshop Admin Layout — Sidebar & Topbar Interactions
 * Mobile toggle, tablet collapse, overlay
 */
(function () {
    'use strict';

    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('mobileHamburger');
    const collapseToggle = document.getElementById('sidebarCollapseToggle');

    // ========== MOBILE MENU ==========
    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // ========== TABLET COLLAPSE ==========
    if (collapseToggle) {
        collapseToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // ========== CLOSE SIDEBAR ON MOBILE LINK CLICK ==========
    const sidebarLinks = sidebar ? sidebar.querySelectorAll('.admin-sidebar-link') : [];
    sidebarLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });

    // ========== KEYBOARD ESCAPE ==========
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open') && window.innerWidth <= 768) {
            closeSidebar();
        }
    });

    // ========== RESIZE HANDLER ==========
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768 && sidebar && sidebar.classList.contains('open') && overlay && overlay.classList.contains('show')) {
            closeSidebar();
        }
    });

})();