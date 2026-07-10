(function () {
    "use strict";

    let isUpdating = false;
    let activeDropdown = null;

    const $ = (s) => document.querySelector(s);
    const $$ = (s) => document.querySelectorAll(s);

    function getFilters() {
        const p = new URLSearchParams(window.location.search);
        const f = {};
        for (const [k, v] of p) {
            if (v && v.trim() !== "") f[k] = v;
        }
        return f;
    }

    function buildUrl(filters) {
        const u = new URL(window.location.pathname, window.location.origin);
        for (const [k, v] of Object.entries(filters)) {
            if (v && v !== "0") u.searchParams.set(k, v);
        }
        return u;
    }

    function serverData() {
        return window.AppFilterState || { categories: [], authors: [], priceRange: { min: 0, max: 100000 }, sortOptions: {} };
    }

    async function fetchBooks(filters, isPopState = false) {
        if (isUpdating) return;
        const container = $("#booksContainer");
        if (!container) return;

        isUpdating = true;

        try {
            const grid = container.querySelector(".book-grid");
            if (grid) {
                grid.classList.add("loading");
                grid.innerHTML = Array(8).fill('<div class="skeleton-card" style="height:320px;background:#f1f5f9;border-radius:12px;"></div>').join("");
            }

            const targetUrl = buildUrl(filters);
            const resp = await fetch(targetUrl, {
                headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
            });

            if (!resp.ok) throw new Error(`HTTP network pipe error: ${resp.status}`);
            const data = await resp.json();

            container.innerHTML = data.html;

            if (data.categories || data.authors || data.sortOptions) {
                window.AppFilterState = {
                    categories: data.categories || window.AppFilterState.categories,
                    authors: data.authors || window.AppFilterState.authors,
                    priceRange: data.priceRange || window.AppFilterState.priceRange,
                    sortOptions: data.sortOptions || window.AppFilterState.sortOptions,
                    languages: window.AppFilterState.languages // Preserved from Blade
                };
            }

            const cnt = $("#booksCount");
            if (cnt && data.count !== undefined) {
                cnt.textContent = `${data.count} ${data.count === 1 ? "book" : "books"} found`;
            }

            // Update active filters - Synchronized with Blade classes
            if (data.activeFilters) {
                const af = $("#activeFilters");
                if (af) {
                    let h = '';
                    if (data.activeFilters.length > 0) {
                        h = data.activeFilters.map((c) => 
                            `<span class="filter-chip" data-remove="${c.param}">
                                ${c.label} &times;
                            </span>`
                        ).join('');
                        if (data.activeFilters.length > 1) {
                            h += '<button class="filter-chip-clear" id="clearAllChips">Clear All</button>';
                        }
                    }
                    af.innerHTML = h;
                }
            }

            // Update sort button - Fixed to handle bare text node
            const sortBtn = $("#bookSortButton");
            if (sortBtn) {
                const currentSort = filters.sort || "featured";
                const sortLabel = (serverData().sortOptions)[currentSort] || "Featured";
                sortBtn.textContent = `Sort: ${sortLabel} ▾`;
                sortBtn.classList.toggle("is-active", currentSort !== "featured");
            }

            updateFilterBadge();

            if (!isPopState && window.location.href !== targetUrl.toString()) {
                window.history.pushState({ filters }, "", targetUrl.toString());
            }
        } catch (e) {
            console.error("AJAX execution error:", e);
        } finally {
            isUpdating = false;
        }
    }

    function applyFilter(name, value) {
        const f = getFilters();
        delete f.page;
        if (!value || value.trim() === "") delete f[name]; else f[name] = value;
        fetchBooks(f);
    }

    function closeDropdown() {
        if (activeDropdown) {
            if (activeDropdown.dd) activeDropdown.dd.remove();
            if (activeDropdown.ov) activeDropdown.ov.remove();
            if (activeDropdown.btn) {
                activeDropdown.btn.classList.remove('is-active');
            }
            activeDropdown = null;
        }
    }
    window.closeDropdown = closeDropdown;

    function makeItemHTML(name, value, text, count, active) {
        return `
            <div class="filter-radio ${active ? "is-active" : ""}" data-name="${name}" data-value="${value}">
                <span class="filter-radio-label">${text}</span>
                ${count ? `<span class="filter-radio-count">${count}</span>` : ""}
            </div>
        `;
    }

    // ============================================
    // DROPDOWN TEMPLATES
    // ============================================
    const DropdownTemplates = {
        category(data, f) {
            let h = '<div class="filter-dropdown-scroll">' + makeItemHTML("category", "", "All Categories", null, !f.category);
            if (data.categories) {
                for (const c of data.categories) {
                    h += makeItemHTML("category", c.id, c.name, c.books_count || null, f.category == c.id);
                }
            }
            return h + "</div>";
        },
        author(data, f) {
            let h = '<div class="filter-dropdown-scroll">' + makeItemHTML("author", "", "All Authors", null, !f.author);
            if (data.authors) {
                for (const a of data.authors) {
                    h += makeItemHTML("author", a.id, a.name, a.books_count || null, f.author == a.id);
                }
            }
            return h + "</div>";
        },
        price(data, f) {
            return `
                <div style="padding: 4px;">
                    <div class="price-inputs">
                        <input type="number" class="dd-min-price" placeholder="Min ${data.priceRange?.min || 0}" value="${f.min_price || ""}">
                        <span style="color: #64748b;">-</span>
                        <input type="number" class="dd-max-price" placeholder="Max ${data.priceRange?.max || 100000}" value="${f.max_price || ""}">
                    </div>
                    <button class="dd-apply-price">Apply Price</button>
                </div>
            `;
        },
        rating(data, f) {
            let h = '<div class="filter-dropdown-scroll">' + makeItemHTML("rating", "", "Any Rating", null, !f.rating);
            for (let s = 4; s >= 2; s--) {
                h += makeItemHTML("rating", s, "★".repeat(s) + " & Up", null, f.rating == s);
            }
            return h + "</div>";
        },
        availability(data, f) {
            const options = [
                { value: "in_stock", label: "In Stock" },
                { value: "low_stock", label: "Low Stock" },
                { value: "out_of_stock", label: "Out of Stock" },
                { value: "coming_soon", label: "Coming Soon" },
                { value: "pre_order", label: "Pre-order" },
            ];
            const selected = (f.availability || "").split(",").filter(Boolean);
            let h = '<div style="padding: 4px;">';
            for (const o of options) {
                const isChecked = selected.includes(o.value);
                h += `
                    <label class="filter-checkbox ${isChecked ? "is-active" : ""}" data-value="${o.value}">
                        <input type="checkbox" class="availability-cb" value="${o.value}" ${isChecked ? "checked" : ""}>
                        <div class="filter-checkbox-icon">${isChecked ? "✓" : ""}</div>
                        <span>${o.label}</span>
                    </label>
                `;
            }
            return h + "</div>";
        },
        all(data, f) {
            return `
                <div style="padding: 4px;">
                    <details open>
                        <summary>Categories</summary>
                        ${DropdownTemplates.category(data, f)}
                    </details>
                    <details>
                        <summary>Authors</summary>
                        ${DropdownTemplates.author(data, f)}
                    </details>
                    <details>
                        <summary>Price</summary>
                        ${DropdownTemplates.price(data, f)}
                    </details>
                    <details>
                        <summary>Availability</summary>
                        ${DropdownTemplates.availability(data, f)}
                    </details>
                    <details>
                        <summary>Rating</summary>
                        ${DropdownTemplates.rating(data, f)}
                    </details>
                </div>
            `;
        },
        sort(data, f) {
            let h = '<div class="filter-dropdown-scroll">';
            for (const [value, label] of Object.entries(data.sortOptions)) {
                h += makeItemHTML("sort", value, label, null, (f.sort || 'featured') === value);
            }
            return h + "</div>";
        }
    };

    // ============================================
    // OPEN DROPDOWN
    // ============================================
    function openDropdown(btn, type) {
        if (activeDropdown && activeDropdown.btn === btn) { closeDropdown(); return; }
        closeDropdown();
        if (!DropdownTemplates[type]) return;

        const dd = document.createElement("div");
        dd.id = "activeDropdownPanel";
        dd.className = "filter-dropdown show";
        dd.innerHTML = DropdownTemplates[type](serverData(), getFilters());

        const br = btn.getBoundingClientRect();
        const isMobile = window.innerWidth <= 768;
        
        let left = br.left + window.scrollX;
        const dropdownWidth = isMobile ? 260 : 300;
        
        if (left + dropdownWidth > window.innerWidth + window.scrollX) {
            left = window.innerWidth + window.scrollX - dropdownWidth - 10;
        }
        if (left < 10) left = 10;
        
        dd.style.position = 'absolute';
        dd.style.top = `${br.bottom + window.scrollY + 4}px`;
        dd.style.left = `${left}px`;
        dd.style.transform = 'none';
        dd.style.width = 'auto';
        dd.style.minWidth = isMobile ? '200px' : '220px';
        dd.style.maxWidth = isMobile ? '260px' : '300px';
        dd.style.maxHeight = isMobile ? '350px' : '400px';
        dd.style.borderRadius = '12px';
        dd.style.padding = '8px';
        dd.style.background = '#ffffff';
        dd.style.border = '1px solid #e2e8f0';
        dd.style.boxShadow = '0 12px 40px rgba(0, 0, 0, 0.15)';
        dd.style.zIndex = '9999';

        const ov = document.createElement("div");
        ov.id = "activeDropdownOverlay";
        ov.className = "filter-dropdown-overlay";
        if (!isMobile) ov.classList.add('show');
        ov.addEventListener("click", closeDropdown);

        // ============================================
        // EVENT HANDLERS
        // ============================================
        
        // Handle native checkbox change (Race condition fixed)
        dd.addEventListener("change", function (e) {
            const cb = e.target.closest(".availability-cb");
            if (cb) {
                const label = cb.closest('.filter-checkbox');
                const icon = label.querySelector('.filter-checkbox-icon');
                
                if (cb.checked) {
                    label.classList.add('is-active');
                    if(icon) icon.textContent = '✓';
                } else {
                    label.classList.remove('is-active');
                    if(icon) icon.textContent = '';
                }

                const checked = Array.from(dd.querySelectorAll(".availability-cb:checked")).map((c) => c.value);
                const f2 = getFilters();
                delete f2.page;
                
                if (checked.length > 0) {
                    f2.availability = checked.join(",");
                } else {
                    delete f2.availability;
                }
                
                fetchBooks(f2);
            }
        });

        // Handle radio buttons and explicit buttons
        dd.addEventListener("click", function (e) {
            const item = e.target.closest(".filter-radio");
            if (item) {
                const name = item.dataset.name;
                const value = item.dataset.value;
                
                if (name === 'sort') {
                    const sortBtn = document.getElementById('bookSortButton');
                    if (sortBtn) {
                        const sortOptions = serverData().sortOptions || {};
                        const sortLabel = sortOptions[value] || value;
                        sortBtn.textContent = `Sort: ${sortLabel} ▾`;
                    }
                }
                
                applyFilter(name, value);
                closeDropdown();
                return;
            }
            
            const priceBtn = e.target.closest(".dd-apply-price");
            if (priceBtn) {
                const container = priceBtn.parentElement;
                const mn = container.querySelector(".dd-min-price").value;
                const mx = container.querySelector(".dd-max-price").value;
                const f2 = getFilters();
                delete f2.page;
                if (mn) f2.min_price = mn; else delete f2.min_price;
                if (mx) f2.max_price = mx; else delete f2.max_price;
                fetchBooks(f2);
                closeDropdown();
                return;
            }

            if (e.target === dd) {
                closeDropdown();
            }
        });

        document.body.appendChild(ov);
        document.body.appendChild(dd);
        
        btn.classList.add('is-active');
        
        activeDropdown = { dd, ov, btn };
    }

    // ============================================
    // UPDATE FILTER BADGE
    // ============================================
    function updateFilterBadge() {
        const badge = document.getElementById('filterBadge');
        if (!badge) return;
        
        const f = getFilters();
        const filterKeys = ['category', 'author', 'min_price', 'max_price', 'availability', 'rating'];
        let count = 0;
        
        for (const key of filterKeys) {
            if (f[key] && f[key] !== '0' && f[key] !== '') count++;
        }
        
        if (f.sort && f.sort !== 'featured') count++;
        
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-flex';
        } else {
            badge.style.display = 'none';
        }
    }

    // ============================================
    // GLOBAL EVENT LISTENERS
    // ============================================
    document.addEventListener("click", function (e) {
        const dBtn = e.target.closest("[data-dropdown-type]");
        if (dBtn) { 
            e.preventDefault(); 
            openDropdown(dBtn, dBtn.dataset.dropdownType); 
            return; 
        }
        
        if (e.target.closest("#clearAllChips")) {
            const currentFilters = getFilters();
            fetchBooks({ sort: currentFilters.sort || "featured" });
            return;
        }

        const chip = e.target.closest(".filter-chip[data-remove]");
        if (chip) {
            const f = getFilters(); 
            delete f.page;
            const p = chip.dataset.remove;
            if (p === "price") { delete f.min_price; delete f.max_price; }
            else if (p === "availability") { delete f.availability; }
            else { delete f[p]; }
            fetchBooks(f);
            return;
        }

        const pl = e.target.closest(".pagination-wrapper a");
        if (pl) {
            e.preventDefault();
            const u = new URL(pl.href);
            const params = {};
            u.searchParams.forEach((v, k) => { params[k] = v; });
            fetchBooks(params);
        }
    });

    // ============================================
    // SEARCH HANDLING
    // ============================================
    const searchInput = document.getElementById("bookSearchInput");
    const clearSearchBtn = document.getElementById("searchClearBtn");
    let searchDebounceTimeout = null;

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            if (clearSearchBtn) {
                clearSearchBtn.style.display = this.value ? "flex" : "none";
            }
            clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => { 
                applyFilter("search", this.value); 
            }, 400);
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            if (searchInput) { 
                searchInput.value = ""; 
                this.style.display = "none"; 
                applyFilter("search", ""); 
                searchInput.focus();
            }
        });
    }

    // ============================================
    // KEYBOARD SHORTCUTS
    // ============================================
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeDropdown();
            if (searchInput && document.activeElement === searchInput) {
                searchInput.blur();
            }
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key === "k") {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const sortTrigger = document.getElementById('bookSortButton');
        if (sortTrigger) {
            const newSortTrigger = sortTrigger.cloneNode(true);
            sortTrigger.parentNode.replaceChild(newSortTrigger, sortTrigger);
            
            newSortTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                
                if (activeDropdown && activeDropdown.btn === this) {
                    closeDropdown();
                    return;
                }
                
                openDropdown(this, 'sort');
            });
        }

        const categoryChips = document.querySelectorAll('.filter-chip-btn');
        categoryChips.forEach(chip => {
            chip.addEventListener('click', function(e) {
                e.stopPropagation();
                const type = this.dataset.dropdownType;
                if (type && type !== 'all' && type !== 'sort') {
                    const f = getFilters();
                    delete f.page;
                    
                    if (f[type]) {
                        delete f[type];
                        this.classList.remove('is-active');
                    } else {
                        f[type] = '1';
                        this.classList.add('is-active');
                    }
                    
                    fetchBooks(f);
                }
            });
        });

        updateFilterBadge();
    });

    window.addEventListener("popstate", function (e) {
        fetchBooks((e.state && e.state.filters) || getFilters(), true);
    });

    let resizeTimeout;
    window.addEventListener("resize", function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (window.innerWidth > 768 && activeDropdown) {
                closeDropdown();
            }
        }, 200);
    });

    // ============================================
    // MOBILE SEARCH TOGGLE
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const searchWrapper = document.querySelector('.books-search-bar');
        const searchContainer = document.querySelector('.search-input-wrapper');

        if (!searchWrapper || !searchInput || !searchContainer) return;

        function isMobile() {
            return window.innerWidth <= 768;
        }

        searchContainer.addEventListener('click', function(e) {
            if (!isMobile()) return;
            if (e.target.closest('.search-clear-btn')) return;
            
            e.stopPropagation();
            
            const isExpanded = searchWrapper.classList.contains('expanded');
            if (!isExpanded) {
                searchWrapper.classList.add('expanded');
                setTimeout(() => {
                    searchInput.focus();
                    searchInput.select();
                }, 100);
            } else {
                searchWrapper.classList.remove('expanded');
            }
        });

        document.addEventListener('click', function(e) {
            if (!isMobile()) return;
            if (!searchWrapper.contains(e.target) && !e.target.closest('.filter-dropdown')) {
                searchWrapper.classList.remove('expanded');
            }
        });

        searchInput.addEventListener('focus', function() {
            if (isMobile() && !searchWrapper.classList.contains('expanded')) {
                searchWrapper.classList.add('expanded');
            }
        });

        window.addEventListener('orientationchange', function() {
            if (searchWrapper) {
                searchWrapper.classList.remove('expanded');
            }
        });

        let resizeTimeout2;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout2);
            resizeTimeout2 = setTimeout(() => {
                if (window.innerWidth > 768 && searchWrapper) {
                    searchWrapper.classList.remove('expanded');
                }
            }, 200);
        });
    });

})();