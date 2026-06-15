/**
 * Bookshop Admin Layout — Sidebar & Topbar Interactions
 */
(function () {
    'use strict';

    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('mobileHamburger');
    const collapseToggle = document.getElementById('sidebarCollapseToggle');

    // ========== MOBILE MENU SYSTEM ==========
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
            sidebar.classList.toggle('open');
        });
    }

    // ========== RESPONSIVE CLOSURE VIA LINKS ==========
    const sidebarLinks = sidebar ? sidebar.querySelectorAll('.admin-sidebar-link') : [];
    sidebarLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });

    // ========== SUBMENU CONTROLS & EVENT MANAGEMENT ==========
    const parentItems = sidebar ? sidebar.querySelectorAll('.admin-sidebar-parent') : [];
    
    parentItems.forEach(function (listItem) {
        const btn = listItem.querySelector('.admin-sidebar-parent-toggle');
        const submenu = listItem.querySelector('.admin-sidebar-submenu');
        if (!btn || !submenu) return;

        // Inline Accordion Trigger (Active when Expanded)
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            
            // Layout Guard: Bypass click accordion modifications if collapsed on tablet
            if (window.innerWidth > 768 && window.innerWidth <= 1200 && !sidebar.classList.contains('open')) {
                return; 
            }

            submenu.classList.toggle('open');
            const icon = btn.querySelector('.admin-sidebar-dropdown-icon');
            if (icon) icon.classList.toggle('open');
        });

        // Hover Class Bindings for Stable Flyout Transitions
        listItem.addEventListener('mouseenter', function () {
            if (window.innerWidth > 768 && window.innerWidth <= 1200 && !sidebar.classList.contains('open')) {
                submenu.classList.add('flyout-active');
            }
        });

        listItem.addEventListener('mouseleave', function () {
            submenu.classList.remove('flyout-active');
        });
    });

    // ========== ACCESSIBILITY KEYBOARD TRIGGERS ==========
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open') && window.innerWidth <= 768) {
            closeSidebar();
        }
    });

    // ========== WINDOW WINDOW RESIZE EVENT CLEANUP ==========
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768 && sidebar && sidebar.classList.contains('open') && overlay && overlay.classList.contains('show')) {
            closeSidebar();
        }
    });

})();