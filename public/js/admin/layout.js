/**
 * Bookshop Admin Layout — Sidebar & Topbar Interactions
 */
(function () {
    'use strict';

    var sidebar = document.getElementById('adminSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var hamburger = document.getElementById('mobileHamburger');
    var collapseToggle = document.getElementById('sidebarCollapseToggle');

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

    // ========== TABLET COLLAPSE TOGGLE ==========
    if (collapseToggle) {
        collapseToggle.addEventListener('click', function () {
            if (sidebar) sidebar.classList.toggle('open');
        });
    }

    // ========== CLOSE ON LINK CLICK (mobile) ==========
    if (sidebar) {
        var sidebarLinks = sidebar.querySelectorAll('.admin-sidebar-link');
        sidebarLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
    }

    // ========== SUBMENU ACCORDION + HOVER FLYOUT ==========
    if (sidebar) {
        var parentItems = sidebar.querySelectorAll('.admin-sidebar-parent');

        parentItems.forEach(function (listItem) {
            var btn = listItem.querySelector('.admin-sidebar-parent-toggle');
            var submenu = listItem.querySelector('.admin-sidebar-submenu');
            if (!btn || !submenu) return;

            // Click — accordion toggle
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                // Skip accordion behavior in collapsed tablet mode
                if (window.innerWidth > 768 && window.innerWidth <= 1200 && !sidebar.classList.contains('open')) {
                    return;
                }

                submenu.classList.toggle('open');
                var icon = btn.querySelector('.admin-sidebar-dropdown-icon');
                if (icon) icon.classList.toggle('open');
            });

            // Hover — flyout in collapsed tablet mode
            listItem.addEventListener('mouseenter', function () {
                if (window.innerWidth > 768 && window.innerWidth <= 1200 && !sidebar.classList.contains('open')) {
                    submenu.classList.add('flyout-active');
                }
            });

            listItem.addEventListener('mouseleave', function () {
                submenu.classList.remove('flyout-active');
            });
        });
    }

    // ========== ESCAPE KEY ==========
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open') && window.innerWidth <= 768) {
            closeSidebar();
        }
    });

    // ========== RESIZE CLEANUP ==========
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768 && sidebar && sidebar.classList.contains('open') && overlay && overlay.classList.contains('show')) {
            closeSidebar();
        }
    });

})();