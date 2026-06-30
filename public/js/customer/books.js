(function () {
    'use strict';

    let isUpdating = false;
    let searchTimeout = null;
    let activeDropdown = null;
    let ignorePopState = false;

    const $ = (s) => document.querySelector(s);

    function getFilters() {
        const p = new URLSearchParams(window.location.search);
        const f = {};
        for (const [k, v] of p) f[k] = v;
        return f;
    }

    function buildUrl(filters) {
        const u = new URL(window.location.pathname, window.location.origin);
        for (const [k, v] of Object.entries(filters)) {
            if (v && v !== '0') u.searchParams.set(k, v);
        }
        return u;
    }

    function serverData() {
        const el = $('#filterData');
        if (!el) return { categories: [], authors: [], priceRange: { min: 0, max: 100000 } };
        try {
            return {
                categories: JSON.parse(el.dataset.categories || '[]'),
                authors: JSON.parse(el.dataset.authors || '[]'),
                priceRange: JSON.parse(el.dataset.priceRange || '{"min":0,"max":100000}'),
            };
        } catch (e) {
            return { categories: [], authors: [], priceRange: { min: 0, max: 100000 } };
        }
    }

    async function fetchBooks(filters, silent = false) {
        if (isUpdating) return;
        const c = $('#booksContainer');
        if (!c) return;
        isUpdating = true;
        try {
            if (!silent) {
                c.innerHTML = Array(8).fill('<div class="skeleton" style="aspect-ratio:3/4;border-radius:18px;background:#e2e8f0;"></div>').join('');
            }
            const resp = await fetch(buildUrl(filters), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!resp.ok) throw new Error('Status ' + resp.status);
            const data = await resp.json();
            
            c.innerHTML = data.html;
            
            const cnt = $('#booksCount');
            if (cnt && data.count !== undefined) {
                cnt.textContent = data.count + ' ' + (data.count === 1 ? 'book' : 'books') + ' found';
            }
            
            if (data.activeFilters) {
                const af = $('#activeFilters');
                if (af) {
                    let h = data.activeFilters.map(c => '<span class="filter-chip" data-remove="' + c.param + '">' + c.label + ' &times;</span>').join('');
                    if (data.activeFilters.length > 1) h += '<button class="filter-chip-clear" id="clearAllChips">Clear All</button>';
                    af.innerHTML = h;
                }
            }
            
            if (data.filterGroups) {
                for (const g of data.filterGroups) {
                    const btn = $('.filter-bar-btn[data-dropdown-type="' + g.key + '"]');
                    if (btn) btn.classList.toggle('is-active', !!g.isActive);
                }
            }
            
            const badge = $('#filterBadge');
            if (badge) {
                const f2 = data.filters || filters;
                let count = 0;
                if (f2.category) count++;
                if (f2.author) count++;
                if (f2.rating) count++;
                if (f2.min_price || f2.max_price) count++;
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
            
            const newUrl = buildUrl(filters).toString();
            if (window.location.href !== newUrl) {
                ignorePopState = true;
                window.history.pushState({ filters: filters }, '', newUrl);
            }
            
            if (typeof window.initBookCards === 'function') setTimeout(window.initBookCards, 100);
        } catch (e) {
            console.error('Fetch error:', e);
            if (!silent) c.innerHTML = '<p>Error loading books.</p>';
        } finally {
            isUpdating = false;
        }
    }

    function applyFilter(name, value) {
        const f = getFilters();
        delete f.page;
        if (!value || value === '') delete f[name];
        else f[name] = value;
        if (!f.sort) f.sort = ($('#bookSortSelect') && $('#bookSortSelect').value) || 'featured';
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
        const bg = active ? 'background:#f1f5f9;font-weight:600;' : '';
        const cnt = count ? '<span style="font-size:11px;color:#94a3b8;background:#e2e8f0;padding:1px 7px;border-radius:99px;pointer-events:none;">' + count + '</span>' : '';
        return '<div class="filter-dropdown-item" data-name="' + name + '" data-value="' + value + '" style="display:flex;align-items:center;gap:8px;padding:10px 12px;cursor:pointer;border-radius:8px;font-size:13px;color:#475569;' + bg + '" onmouseover="this.style.background=\'#f1f5f9\'" onmouseout="this.style.background=\'' + (active ? '#f1f5f9' : 'transparent') + '\'">' + text + cnt + '</div>';
    }

    // Encapsulated Layout Composition Strategies
    const DropdownTemplates = {
        category(data, f) {
            let h = '<div style="padding:4px">' + makeItemHTML('category', '', 'All Categories', null, !f.category);
            for (const c of data.categories) h += makeItemHTML('category', c.id, c.name, c.books_count || null, f.category == c.id);
            return h + '</div>';
        },
        author(data, f) {
            let h = '<div style="max-height:240px;overflow-y:auto;padding:4px">' + makeItemHTML('author', '', 'All Authors', null, !f.author);
            for (const a of data.authors) h += makeItemHTML('author', a.id, a.name, a.books_count || null, f.author == a.id);
            return h + '</div>';
        },
        price(data, f) {
            return '<div style="padding:4px">' +
                '<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">' +
                    '<input type="number" class="dd-min-price" placeholder="Min ' + data.priceRange.min + '" value="' + (f.min_price || '') + '" style="width:100%;min-width:0;flex:1;padding:10px 8px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;box-sizing:border-box;">' +
                    '<span style="color:#94a3b8;flex-shrink:0;">-</span>' +
                    '<input type="number" class="dd-max-price" placeholder="Max ' + data.priceRange.max + '" value="' + (f.max_price || '') + '" style="width:100%;min-width:0;flex:1;padding:10px 8px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;box-sizing:border-box;">' +
                '</div>' +
                '<button class="dd-apply-price" style="width:100%;padding:10px;background:var(--color-primary,#0f172a);color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;">Apply Price</button>' +
            '</div>';
        },
        rating(data, f) {
            let h = '<div style="padding:4px">' + makeItemHTML('rating', '', 'Any Rating', null, !f.rating);
            for (let s = 4; s >= 2; s--) h += makeItemHTML('rating', s, Array(s + 1).join('★') + ' & Up', null, f.rating == s);
            return h + '</div>';
        },
        all(data, f) {
            return '<div style="padding:8px">' +
                '<details open><summary style="font-weight:700;padding:8px 0;cursor:pointer;font-size:13px">Categories</summary>' + DropdownTemplates.category(data, f) + '</details>' +
                '<details><summary style="font-weight:700;padding:8px 0;cursor:pointer;font-size:13px">Authors</summary>' + DropdownTemplates.author(data, f) + '</details>' +
                '<details><summary style="font-weight:700;padding:8px 0;cursor:pointer;font-size:13px">Price</summary>' + DropdownTemplates.price(data, f) + '</details>' +
                '<details><summary style="font-weight:700;padding:8px 0;cursor:pointer;font-size:13px">Rating</summary>' + DropdownTemplates.rating(data, f) + '</details>' +
            '</div>';
        }
    };

    function openDropdown(btn, type) {
        if (activeDropdown && activeDropdown.btn === btn) { closeDropdown(); return; }
        closeDropdown();
        
        if (!DropdownTemplates[type]) return;

        const data = serverData();
        const f = getFilters();
        const html = DropdownTemplates[type](data, f);

        const dd = document.createElement('div');
        dd.id = 'activeDropdownPanel';
        
        if (type === 'price' || type === 'rating') {
            dd.style.cssText = 'position:absolute;min-width:280px;max-width:320px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.15);z-index:99999;padding:12px;';
            dd.classList.add('no-scroll');
        } else {
            dd.style.cssText = 'position:absolute;min-width:240px;max-width:320px;max-height:380px;overflow-y:auto;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.15);z-index:99999;padding:8px;';
        }
        
        dd.innerHTML = html;

        const br = btn.getBoundingClientRect();
        dd.style.top = (br.bottom + window.scrollY + 6) + 'px';
        let l = br.left + window.scrollX;
        if (l + 300 > window.innerWidth + window.scrollX) l = window.innerWidth + window.scrollX - 310;
        if (l < 0) l = 0;
        dd.style.left = l + 'px';

        const ov = document.createElement('div');
        ov.id = 'activeDropdownOverlay';
        ov.style.cssText = 'position:fixed;inset:0;z-index:99998;background:rgba(0,0,0,0.05)';
        ov.addEventListener('click', closeDropdown);

        dd.addEventListener('click', function(e) {
            const item = e.target.closest('.filter-dropdown-item');
            if (item) {
                applyFilter(item.dataset.name, item.dataset.value);
                closeDropdown();
                return;
            }
            const priceBtn = e.target.closest('.dd-apply-price');
            if (priceBtn) {
                const container = priceBtn.parentElement;
                const mn = container.querySelector('.dd-min-price').value;
                const mx = container.querySelector('.dd-max-price').value;
                const f2 = getFilters();
                delete f2.page;
                if (mn) f2.min_price = mn; else delete f2.min_price;
                if (mx) f2.max_price = mx; else delete f2.max_price;
                if (!f2.sort) f2.sort = ($('#bookSortSelect') && $('#bookSortSelect').value) || 'featured';
                fetchBooks(f2);
                closeDropdown();
            }
        });

        document.body.appendChild(ov);
        document.body.appendChild(dd);

        activeDropdown = { dd: dd, ov: ov, btn: btn };
    }

    document.addEventListener('click', function (e) {
        const dropdownBtn = e.target.closest('[data-dropdown-type]');
        if (dropdownBtn) {
            e.preventDefault();
            openDropdown(dropdownBtn, dropdownBtn.dataset.dropdownType);
            return;
        }

        if (e.target.closest('#clearAllChips')) {
            fetchBooks({ sort: ($('#bookSortSelect') && $('#bookSortSelect').value) || 'featured' });
            return;
        }
        
        const chip = e.target.closest('.filter-chip');
        if (chip) {
            const p = chip.dataset.remove;
            if (p === 'price') {
                const f = getFilters();
                delete f.min_price; delete f.max_price; delete f.page;
                if (!f.sort) f.sort = ($('#bookSortSelect') && $('#bookSortSelect').value) || 'featured';
                fetchBooks(f);
            } else if (p === 'search') {
                const si = $('#bookSearchInput'); if (si) si.value = '';
                applyFilter('search', '');
            } else {
                applyFilter(p, '');
            }
            return;
        }
        
        if (e.target.closest('#searchClearBtn')) {
            const si = $('#bookSearchInput'); if (si) si.value = '';
            applyFilter('search', '');
            return;
        }
        
        const pl = e.target.closest('.pagination-wrapper a');
        if (pl) {
            e.preventDefault();
            const u = new URL(pl.href), params = {};
            u.searchParams.forEach((v, k) => params[k] = v);
            fetchBooks(params);
            const m = $('#booksMain');
            if (m) window.scrollTo({ top: m.getBoundingClientRect().top + window.scrollY - 100, behavior: 'smooth' });
        }
    });

    const sortSel = $('#bookSortSelect');
    if (sortSel) {
        sortSel.addEventListener('change', function () {
            const f = getFilters(); f.sort = this.value; delete f.page; fetchBooks(f);
        });
    }

    window.addEventListener('popstate', function (e) {
        if (ignorePopState) { ignorePopState = false; return; }
        if (e.state && e.state.filters) {
            fetchBooks(e.state.filters, true);
            if (sortSel) sortSel.value = e.state.filters.sort || 'featured';
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDropdown();
    });


// Re-initialize on Turbo navigation
document.addEventListener("turbo:load", function () {
    const sortSel = document.getElementById("bookSortSelect");
    if (sortSel) {
        sortSel.addEventListener("change", function () {
            const f = getFilters(); f.sort = this.value; delete f.page; fetchBooks(f);
        });
    }
    updateActiveStates();
});
})();