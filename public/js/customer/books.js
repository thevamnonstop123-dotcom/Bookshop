(function () {
    "use strict";

    let isUpdating = false;
    let activeDropdown = null;

    const $ = (s) => document.querySelector(s);

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
                grid.innerHTML = Array(8).fill('<div class="skeleton-card" style="height:320px;background:var(--color-surface);"><div class="skeleton" style="height:100%;"></div></div>').join("");
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
                    sortOptions: data.sortOptions || window.AppFilterState.sortOptions
                };
            }

            const cnt = $("#booksCount");
            if (cnt && data.count !== undefined) {
                cnt.textContent = `${data.count} ${data.count === 1 ? "book" : "books"} found`;
            }

            if (data.activeFilters) {
                const af = $("#activeFilters");
                if (af) {
                    let h = data.activeFilters.map((c) => `<span class="filter-chip" data-remove="${c.param}">${c.label} &times;</span>`).join("");
                    if (data.activeFilters.length > 1) h += '<button class="filter-chip-clear" id="clearAllChips">Clear All</button>';
                    af.innerHTML = h;
                }
            }

            const sortBtn = $("#bookSortButton");
            if (sortBtn) {
                const currentSort = filters.sort || "featured";
                sortBtn.classList.toggle("is-active", currentSort !== "featured");
                sortBtn.innerHTML = `Sort: ${(serverData().sortOptions)[currentSort] || "Featured"} ▾`;
            }

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
            activeDropdown = null;
        }
    }

    function makeItemHTML(name, value, text, count, active) {
        return `
            <div class="filter-radio ${active ? "is-active" : ""}" data-name="${name}" data-value="${value}">
                <span class="filter-radio-label">${text}</span>
                ${count ? `<span class="filter-radio-count">${count}</span>` : ""}
            </div>
        `;
    }

    const DropdownTemplates = {
        category(data, f) {
            let h = '<div class="filter-dropdown-scroll">' + makeItemHTML("category", "", "All Categories", null, !f.category);
            for (const c of data.categories) h += makeItemHTML("category", c.id, c.name, c.books_count || null, f.category == c.id);
            return h + "</div>";
        },
        author(data, f) {
            let h = '<div class="filter-dropdown-scroll">' + makeItemHTML("author", "", "All Authors", null, !f.author);
            for (const a of data.authors) h += makeItemHTML("author", a.id, a.name, a.books_count || null, f.author == a.id);
            return h + "</div>";
        },
        price(data, f) {
            return `
                <div style="padding: var(--space-1);">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: var(--space-2);">
                        <input type="number" class="dd-min-price" placeholder="Min ${data.priceRange.min}" value="${f.min_price || ""}" 
                               style="width: 100%; min-width: 0; flex: 1; padding: 10px var(--space-2); border: 1.5px solid var(--color-border); border-radius: var(--radius-input); font-size: 13px; background: var(--color-surface); color: var(--color-text); outline: none;">
                        <span style="color: var(--color-text-muted); flex-shrink: 0;">-</span>
                        <input type="number" class="dd-max-price" placeholder="Max ${data.priceRange.max}" value="${f.max_price || ""}" 
                               style="width: 100%; min-width: 0; flex: 1; padding: 10px var(--space-2); border: 1.5px solid var(--color-border); border-radius: var(--radius-input); font-size: 13px; background: var(--color-surface); color: var(--color-text); outline: none;">
                    </div>
                    <button class="dd-apply-price" 
                            style="width: 100%; padding: 10px; background: var(--color-primary); color: #fff; border: none; border-radius: var(--radius-input); cursor: pointer; font-weight: 600; font-size: 13px;">
                        Apply Price
                    </button>
                </div>
            `;
        },
        rating(data, f) {
            let h = '<div style="padding: 2px;">' + makeItemHTML("rating", "", "Any Rating", null, !f.rating);
            for (let s = 4; s >= 2; s--) h += makeItemHTML("rating", s, "★".repeat(s) + " & Up", null, f.rating == s);
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
            let h = '<div style="padding: 2px;">';
            for (const o of options) {
                const isChecked = selected.includes(o.value);
                h += `
                    <label class="filter-checkbox ${isChecked ? "is-active" : ""}">
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
                    <details open><summary style="font-weight: 700; padding: 8px 4px; cursor: pointer; font-size: 13px; color: var(--color-text);">Categories</summary>${DropdownTemplates.category(data, f)}</details>
                    <details><summary style="font-weight: 700; padding: 8px 4px; cursor: pointer; font-size: 13px; color: var(--color-text);">Authors</summary>${DropdownTemplates.author(data, f)}</details>
                    <details><summary style="font-weight: 700; padding: 8px 4px; cursor: pointer; font-size: 13px; color: var(--color-text);">Price</summary>${DropdownTemplates.price(data, f)}</details>
                    <details><summary style="font-weight: 700; padding: 8px 4px; cursor: pointer; font-size: 13px; color: var(--color-text);">Availability</summary>${DropdownTemplates.availability(data, f)}</details>
                    <details><summary style="font-weight: 700; padding: 8px 4px; cursor: pointer; font-size: 13px; color: var(--color-text);">Rating</summary>${DropdownTemplates.rating(data, f)}</details>
                </div>
            `;
        },
        sort(data, f) {
            let h = '<div style="padding: 2px;">';
            for (const [value, label] of Object.entries(data.sortOptions)) {
                h += makeItemHTML("sort", value, label, null, (f.sort || 'featured') === value);
            }
            return h + "</div>";
        }
    };

    function openDropdown(btn, type) {
        if (activeDropdown && activeDropdown.btn === btn) { closeDropdown(); return; }
        closeDropdown();
        if (!DropdownTemplates[type]) return;

        const dd = document.createElement("div");
        dd.id = "activeDropdownPanel";
        dd.className = "filter-dropdown show";
        dd.innerHTML = DropdownTemplates[type](serverData(), getFilters());

        const br = btn.getBoundingClientRect();
        dd.style.top = `${br.bottom + window.scrollY + 6}px`;
        dd.style.left = `${Math.max(0, Math.min(br.left + window.scrollX, window.innerWidth + window.scrollX - 310))}px`;

        const ov = document.createElement("div");
        ov.id = "activeDropdownOverlay";
        ov.className = "filter-dropdown-overlay show";
        ov.addEventListener("click", closeDropdown);

        // Scope targeted interactive handlers to the newly mounted dropdown shell
        dd.addEventListener("click", function (e) {
            const item = e.target.closest(".filter-radio");
            if (item) {
                applyFilter(item.dataset.name, item.dataset.value);
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
            }
        });

        dd.addEventListener("change", function (e) {
            const availCb = e.target.closest(".availability-cb");
            if (availCb) {
                const checked = Array.from(dd.querySelectorAll(".availability-cb:checked")).map((cb) => cb.value);
                const f2 = getFilters();
                delete f2.page;
                if (checked.length > 0) f2.availability = checked.join(","); else delete f2.availability;
                fetchBooks(f2);
            }
        });

        document.body.appendChild(ov);
        document.body.appendChild(dd);
        activeDropdown = { dd, ov, btn };
    }

    document.addEventListener("click", function (e) {
        const dBtn = e.target.closest("[data-dropdown-type]");
        if (dBtn) { e.preventDefault(); openDropdown(dBtn, dBtn.dataset.dropdownType); return; }
        
        if (e.target.closest("#clearAllChips")) {
            const currentFilters = getFilters();
            fetchBooks({ sort: currentFilters.sort || "featured" });
            return;
        }

        const chip = e.target.closest(".filter-chip");
        if (chip) {
            const f = getFilters(); delete f.page;
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

    const searchInput = $("#bookSearchInput");
    const clearSearchBtn = $("#searchClearBtn");
    let searchDebounceTimeout = null;

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            if (clearSearchBtn) clearSearchBtn.style.display = this.value ? "flex" : "none";
            clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => { applyFilter("search", this.value); }, 400);
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener("click", function () {
            if (searchInput) { searchInput.value = ""; this.style.display = "none"; applyFilter("search", ""); }
        });
    }

    window.addEventListener("popstate", function (e) {
        fetchBooks((e.state && e.state.filters) || getFilters(), true);
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") closeDropdown();
    });
})();