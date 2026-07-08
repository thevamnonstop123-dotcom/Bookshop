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
        initSearchClearButtons();
        initMobileSearch();
        initDesktopSearch();
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
            toggle.setAttribute("aria-expanded", "true");
            document.body.style.overflow = "hidden";
        }
        function close() {
            sidebar.classList.remove("open");
            if (overlay) overlay.classList.remove("show");
            toggle.setAttribute("aria-expanded", "false");
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
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            const list = document.getElementById("notificationList");
            if (!list) return;
            list.innerHTML = "";
            if (data.notifications.length === 0) {
                list.innerHTML = '<div class="notification-empty"><i class="fas fa-bell-slash"></i><p>No notifications yet</p></div>';
                return;
            }
            data.notifications.forEach(function (n) {
                const item = document.createElement("div");
                item.className = "notification-item " + (n.read_at ? "" : "unread");
                item.onclick = function () { markRead(n.id, n.url); };
                const iconClass = n.type === "order_status" ? "order" : "promotion";
                const iconEl = n.type === "order_status" ? "fa-box" : "fa-tag";
                item.innerHTML = '<div class="notification-item-icon ' + iconClass + '"><i class="fas ' + iconEl + '"></i></div>' +
                    '<div class="notification-item-content">' +
                    '<div class="notification-item-title"></div>' +
                    '<div class="notification-item-message"></div>' +
                    '<div class="notification-item-time"></div>' +
                    '</div>';
                item.querySelector(".notification-item-title").textContent = n.title;
                item.querySelector(".notification-item-message").textContent = n.message;
                item.querySelector(".notification-item-time").textContent = n.created_at;
                list.appendChild(item);
            });
        })
        .catch(function () {});
    }

    window.markRead = function (id, url) {
        fetch("/notifications/" + id + "/read", {
            method: "PATCH",
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content, "Accept": "application/json" }
        })
        .then(function (response) {
            if (!response.ok) throw new Error("error");
            if (url) { window.location.href = url; }
            else { loadNotifications(); }
        })
        .catch(function () {});
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
        setTimeout(function() { document.addEventListener("click", handleSearchOutsideClick); }, 0);
        const input = document.getElementById("mobileSearchInput");
        if (input) setTimeout(function () { input.focus(); }, 150);
    };

    function handleSearchOutsideClick(e) {
        const overlay = document.getElementById("navbarSearchOverlay");
        if (overlay && !overlay.contains(e.target)) { closeMobileSearch(); }
    }

    window.closeMobileSearch = function () {
        const overlay = document.getElementById("navbarSearchOverlay");
        if (overlay) overlay.classList.remove("open");
        document.body.style.overflow = "";
        document.removeEventListener("click", handleSearchOutsideClick);
    };

    function initMobileSearchEnter() {
        const input = document.getElementById('mobileSearchInput');
        const form = document.getElementById('mobileSearchForm');
        if (input && form) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.value.trim()) { 
                        closeMobileSearch();
                        window.location.href = "/books?search=" + encodeURIComponent(this.value.trim()); 
                    }
                }
            });
        }
    }

    // ========== DESKTOP SEARCH DROPDOWN ==========
    function initDesktopSearchDropdown() {
        const input = document.getElementById('desktopSearchInput');
        const dropdown = document.getElementById('desktopSearchDropdown');
        if (!input || !dropdown) return;
        input.addEventListener('focus', function () { 
            if (!this.value.trim()) {
                dropdown.innerHTML = originalDropdownContent;
            }
            dropdown.style.display = 'block'; 
        });
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
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
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
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            });
            document.querySelectorAll('.navbar-search-suggestion-item-wrap').forEach(function (el, i) {
                if (i === index) el.remove();
            });
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
            const overlay = document.getElementById('navbarSearchOverlay');
            if (overlay && overlay.classList.contains('open')) closeMobileSearch();
        }
    });

    // ========== SEARCH CLEAR BUTTONS ==========
    function initSearchClearButtons() {
        // Desktop clear button
        const desktopInput = document.getElementById('desktopSearchInput');
        const desktopClear = document.querySelector('.navbar-search-clear');
        
        if (desktopInput && desktopClear) {
            desktopInput.addEventListener('input', function() {
                desktopClear.style.display = this.value.trim().length > 0 ? 'flex' : 'none';
            });
            
            desktopClear.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                desktopInput.value = '';
                desktopInput.focus();
                desktopClear.style.display = 'none';
                desktopInput.dispatchEvent(new Event('input', { bubbles: true }));
            });
            
            desktopClear.style.display = desktopInput.value.trim().length > 0 ? 'flex' : 'none';
        }
        
        // Mobile clear button
        const mobileInput = document.getElementById('mobileSearchInput');
        if (mobileInput) {
            let mobileClear = document.querySelector('.mobile-search-clear-btn');
            
            if (!mobileClear) {
                const form = document.getElementById('mobileSearchForm');
                if (form) {
                    const wrapper = form.querySelector('.navbar-search-overlay-form');
                    if (wrapper) {
                        mobileClear = document.createElement('button');
                        mobileClear.type = 'button';
                        mobileClear.className = 'mobile-search-clear-btn';
                        mobileClear.innerHTML = '<i class="fas fa-times"></i>';
                        mobileClear.style.cssText = `
                            display: none;
                            background: transparent;
                            border: none;
                            color: var(--color-text-muted);
                            cursor: pointer;
                            padding: 4px 8px;
                            font-size: 16px;
                            border-radius: 50%;
                            transition: all 0.2s ease;
                            flex-shrink: 0;
                        `;
                        mobileClear.addEventListener('mouseenter', function() {
                            this.style.background = '#fee2e2';
                            this.style.color = '#dc2626';
                        });
                        mobileClear.addEventListener('mouseleave', function() {
                            this.style.background = 'transparent';
                            this.style.color = 'var(--color-text-muted)';
                        });
                        
                        const input = wrapper.querySelector('.navbar-search-overlay-input');
                        if (input) {
                            input.parentNode.insertBefore(mobileClear, input.nextSibling);
                        }
                    }
                }
            }
            
            if (mobileClear) {
                mobileInput.addEventListener('input', function() {
                    mobileClear.style.display = this.value.trim().length > 0 ? 'flex' : 'none';
                });
                
                mobileClear.addEventListener('click', function() {
                    mobileInput.value = '';
                    mobileInput.focus();
                    mobileClear.style.display = 'none';
                    mobileInput.dispatchEvent(new Event('input', { bubbles: true }));
                });
                
                mobileClear.style.display = mobileInput.value.trim().length > 0 ? 'flex' : 'none';
            }
        }
    }

    // ========== MOBILE SEARCH LIVE RESULTS ==========
    function initMobileSearch() {
        const input = document.getElementById('mobileSearchInput');
        const form = document.getElementById('mobileSearchForm');
        const suggestionsContainer = document.querySelector('.navbar-search-suggestions');
        
        if (!input || !form || !suggestionsContainer) return;
        
        let searchTimeout = null;
        let isSearching = false;
        const originalSuggestions = suggestionsContainer.innerHTML;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 1) {
                suggestionsContainer.innerHTML = originalSuggestions;
                return;
            }
            
            suggestionsContainer.innerHTML = `
                <div style="text-align:center;padding:40px 0;color:var(--color-text-muted);">
                    <i class="fas fa-spinner fa-spin" style="font-size:24px;"></i>
                    <p style="margin-top:8px;font-size:13px;">Searching...</p>
                </div>
            `;
            
            searchTimeout = setTimeout(function() {
                fetchLiveResults(query);
            }, 300);
        });
        
        function fetchLiveResults(query) {
            if (isSearching) return;
            isSearching = true;
            
            fetch(`/books?search=${encodeURIComponent(query)}&ajax=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                isSearching = false;
                
                if (data.html) {
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;
                    const books = temp.querySelectorAll('.book-card');
                    
                    if (books.length > 0) {
                        let html = `
                            <div class="navbar-search-suggestions-section">
                                <div class="navbar-search-suggestions-header">
                                    <h4 class="navbar-search-suggestions-title">Results (${data.count || books.length})</h4>
                                </div>
                        `;
                        
                        books.forEach(book => {
                            const title = book.querySelector('.book-card-title')?.textContent?.trim() || '';
                            const author = book.querySelector('.book-card-author')?.textContent?.trim() || '';
                            const price = book.querySelector('.price-regular')?.textContent?.trim() || 
                                         book.querySelector('.price-sale')?.textContent?.trim() || '';
                            const image = book.querySelector('.book-card-img')?.getAttribute('src') || '';
                            const link = book.querySelector('a.book-card-cover')?.getAttribute('href') || '#';
                            
                            html += `
                                <a href="${link}" class="navbar-search-result-item">
                                    <img src="${image}" alt="${title}" class="navbar-search-result-image">
                                    <div class="navbar-search-result-info">
                                        <div class="navbar-search-result-title">${title}</div>
                                        <div class="navbar-search-result-author">${author}</div>
                                        <div class="navbar-search-result-price">${price}</div>
                                    </div>
                                </a>
                            `;
                        });
                        
                        html += `
                                <div style="text-align:center;margin-top:12px;">
                                    <a href="/books?search=${encodeURIComponent(query)}" class="navbar-search-view-all">
                                        View All Results <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        suggestionsContainer.innerHTML = html;
                    } else {
                        suggestionsContainer.innerHTML = `
                            <div class="navbar-search-suggestions-section" style="text-align:center;padding:40px 20px;color:var(--color-text-muted);">
                                <i class="fas fa-search" style="font-size:32px;opacity:0.3;"></i>
                                <p style="margin-top:8px;font-size:14px;">No results found for "<strong>${query}</strong>"</p>
                                <p style="font-size:12px;margin-top:4px;">Try different keywords</p>
                            </div>
                        `;
                    }
                }
            })
            .catch(() => {
                isSearching = false;
                suggestionsContainer.innerHTML = originalSuggestions;
            });
        }
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = input.value.trim();
            if (query) {
                closeMobileSearch();
                window.location.href = `/books?search=${encodeURIComponent(query)}`;
            }
        });
    }

    // ========== DESKTOP SEARCH LIVE RESULTS ==========
    function initDesktopSearch() {
        const input = document.getElementById('desktopSearchInput');
        const dropdown = document.getElementById('desktopSearchDropdown');
        const wrap = document.getElementById('desktopSearchWrap');
        
        if (!input || !dropdown) return;
        
        let searchTimeout = null;
        let isSearching = false;
        const originalContent = dropdown.innerHTML;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 1) {
                dropdown.innerHTML = originalContent;
                dropdown.style.display = 'block';
                return;
            }
            
            dropdown.innerHTML = `
                <div style="text-align:center;padding:30px 0;color:var(--color-text-muted);">
                    <i class="fas fa-spinner fa-spin" style="font-size:20px;"></i>
                    <p style="margin-top:8px;font-size:13px;">Searching...</p>
                </div>
            `;
            dropdown.style.display = 'block';
            
            searchTimeout = setTimeout(function() {
                fetchDesktopResults(query);
            }, 300);
        });
        
        function fetchDesktopResults(query) {
            if (isSearching) return;
            isSearching = true;
            
            fetch(`/books?search=${encodeURIComponent(query)}&ajax=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                isSearching = false;
                
                if (data.html) {
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;
                    const books = temp.querySelectorAll('.book-card');
                    
                    if (books.length > 0) {
                        let html = `
                            <div style="padding:4px 0;">
                                <div style="padding:8px 12px 4px;font-size:12px;font-weight:700;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;">
                                    Results (${data.count || books.length})
                                </div>
                        `;
                        
                        books.forEach(book => {
                            const title = book.querySelector('.book-card-title')?.textContent?.trim() || '';
                            const author = book.querySelector('.book-card-author')?.textContent?.trim() || '';
                            const price = book.querySelector('.price-regular')?.textContent?.trim() || 
                                         book.querySelector('.price-sale')?.textContent?.trim() || '';
                            const image = book.querySelector('.book-card-img')?.getAttribute('src') || '';
                            const link = book.querySelector('a.book-card-cover')?.getAttribute('href') || '#';
                            
                            html += `
                                <a href="${link}" style="display:flex;align-items:center;gap:10px;padding:8px 12px;text-decoration:none;color:inherit;transition:background 0.15s ease;border-radius:6px;">
                                    <img src="${image}" alt="${title}" style="width:36px;height:48px;object-fit:cover;border-radius:4px;flex-shrink:0;background:var(--color-bg);">
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-weight:600;font-size:13px;color:var(--color-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${title}</div>
                                        <div style="font-size:11px;color:var(--color-text-muted);">${author}</div>
                                        <div style="font-size:12px;font-weight:700;color:var(--color-primary);">${price}</div>
                                    </div>
                                </a>
                            `;
                        });
                        
                        html += `
                                <div style="padding:8px 12px;border-top:1px solid var(--color-border-light);margin-top:4px;">
                                    <a href="/books?search=${encodeURIComponent(query)}" style="display:block;text-align:center;padding:8px;background:var(--color-primary);color:#fff;border-radius:6px;text-decoration:none;font-weight:600;font-size:12px;transition:all 0.2s ease;">
                                        View All Results <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        dropdown.innerHTML = html;
                    } else {
                        dropdown.innerHTML = `
                            <div style="text-align:center;padding:30px 20px;color:var(--color-text-muted);">
                                <i class="fas fa-search" style="font-size:24px;opacity:0.3;"></i>
                                <p style="margin-top:8px;font-size:14px;">No results found for "<strong>${query}</strong>"</p>
                            </div>
                        `;
                    }
                }
            })
            .catch(() => {
                isSearching = false;
                dropdown.innerHTML = originalContent;
            });
        }
        
        input.addEventListener('focus', function() {
            if (!this.value.trim()) {
                dropdown.innerHTML = originalContent;
            }
            dropdown.style.display = 'block';
        });
        
        document.addEventListener('click', function(e) {
            if (wrap && !wrap.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

})();

// ========== FIX: RECENT SEARCH CLICKS ==========
// Force recent search items to be clickable
function fixRecentSearchClicks() {
    // Find all recent search items
    document.querySelectorAll('.navbar-search-suggestion-item-wrap').forEach(function(wrap) {
        const link = wrap.querySelector('.navbar-search-suggestion-item');
        if (link) {
            // Make the entire wrap clickable
            wrap.style.cursor = 'pointer';
            wrap.addEventListener('click', function(e) {
                // Don't trigger if clicking the delete button
                if (e.target.closest('.navbar-search-suggestion-delete')) {
                    return;
                }
                e.preventDefault();
                const href = link.getAttribute('href');
                if (href) {
                    window.location.href = href;
                }
            });
        }
    });
}

// Also fix desktop recent searches
function fixDesktopRecentClicks() {
    document.querySelectorAll('.navbar-search-dropdown .navbar-search-suggestion-item-wrap').forEach(function(wrap) {
        const link = wrap.querySelector('.navbar-search-dropdown-item');
        if (link) {
            wrap.style.cursor = 'pointer';
            wrap.addEventListener('click', function(e) {
                if (e.target.closest('.navbar-search-suggestion-delete')) {
                    return;
                }
                e.preventDefault();
                const href = link.getAttribute('href');
                if (href) {
                    window.location.href = href;
                }
            });
        }
    });
}

// ========== FIX: SEARCH DROPDOWN ITEMS ==========
// Make sure all dropdown items are clickable
function fixDropdownItems() {
    document.querySelectorAll('.navbar-search-dropdown-item').forEach(function(item) {
        // If it's wrapped, make the parent clickable too
        const parent = item.closest('.navbar-search-suggestion-item-wrap');
        if (parent) {
            parent.style.cursor = 'pointer';
            parent.addEventListener('click', function(e) {
                if (e.target.closest('.navbar-search-suggestion-delete')) {
                    return;
                }
                e.preventDefault();
                const href = item.getAttribute('href');
                if (href) {
                    window.location.href = href;
                }
            });
        }
    });
}

// Run fixes when DOM is ready and after AJAX updates
function initRecentSearchFixes() {
    fixRecentSearchClicks();
    fixDesktopRecentClicks();
    fixDropdownItems();
}

// Run on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initRecentSearchFixes();
});

// Also run after any AJAX updates (for live search results)
// Use MutationObserver to watch for changes
if (window.MutationObserver) {
    const observer = new MutationObserver(function() {
        initRecentSearchFixes();
    });
    
    // Watch the search suggestions container
    const container = document.querySelector('.navbar-search-suggestions');
    if (container) {
        observer.observe(container, { childList: true, subtree: true });
    }
    
    // Also watch desktop dropdown
    const dropdown = document.getElementById('desktopSearchDropdown');
    if (dropdown) {
        observer.observe(dropdown, { childList: true, subtree: true });
    }
}
