(function () {
    'use strict';

    const sidebar = document.getElementById('adminSidebar');
    // Explicitly target the element that actually contains the scrollbar
    const scrollContainer = sidebar ? sidebar.querySelector('.admin-sidebar-nav') : null;
    
    const overlay = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('mobileHamburger');
    const collapseToggle = document.getElementById('sidebarCollapseToggle');
    const scrollStorageKey = 'admin_sidebar_scroll';

    // ========== SCROLL STATE PERSISTENCE ==========
    function saveScroll() {
        if (scrollContainer) {
            sessionStorage.setItem(scrollStorageKey, scrollContainer.scrollTop);
        }
    }

    function restoreScroll() {
        if (!scrollContainer) return;
        const saved = sessionStorage.getItem(scrollStorageKey);
        if (saved) {
            // Dual requestAnimationFrame ensures the DOM layout calculation is complete
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    scrollContainer.scrollTop = parseInt(saved, 10);
                });
            });
        }
    }

    restoreScroll();

    // Bind scroll saving exclusively to anchor links inside the menu
    if (sidebar) {
        sidebar.addEventListener('click', function (e) {
            if (e.target.closest('a')) saveScroll();
        });
    }
    window.addEventListener('beforeunload', saveScroll);

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

    // ========== SUBMENU CONTROLS ==========
    const parentItems = sidebar ? sidebar.querySelectorAll('.admin-sidebar-parent') : [];
    
    parentItems.forEach(function (listItem) {
        const btn = listItem.querySelector('.admin-sidebar-parent-toggle');
        const submenu = listItem.querySelector('.admin-sidebar-submenu');
        if (!btn || !submenu) return;

        btn.addEventListener('click', function (e) {
            if (btn.tagName === 'BUTTON' || btn.getAttribute('href') === '#') {
                e.preventDefault();
            }
            
            if (window.innerWidth > 768 && window.innerWidth <= 1200 && !sidebar.classList.contains('open')) {
                return; 
            }

            submenu.classList.toggle('open');
            const icon = btn.querySelector('.admin-sidebar-dropdown-icon');
            if (icon) icon.classList.toggle('open');
        });

        listItem.addEventListener('mouseenter', function () {
            if (window.innerWidth > 768 && window.innerWidth <= 1200 && !sidebar.classList.contains('open')) {
                submenu.classList.add('flyout-active');
            }
        });

        listItem.addEventListener('mouseleave', function () {
            submenu.classList.remove('flyout-active');
        });
    });

    // ========== KEYBOARD ==========
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