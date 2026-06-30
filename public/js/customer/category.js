/**
 * Category Page — AJAX Filters + Sticky Sort
 */
(function () {
    'use strict';

    let isUpdating = false;

    window.toggleCategoryFilters = function () {
        const filters = document.getElementById('categoryFilters');
        const overlay = document.getElementById('categoryFiltersOverlay');
        if (filters) filters.classList.toggle('open');
        if (overlay) overlay.classList.toggle('show');
        document.body.style.overflow = filters && filters.classList.contains('open') ? 'hidden' : '';
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const filters = document.getElementById('categoryFilters');
            if (filters && filters.classList.contains('open')) {
                window.toggleCategoryFilters();
            }
        }
    });

    // AJAX fetch
    async function fetchCategoryBooks(params) {
        if (isUpdating) return;
        const grid = document.querySelector('.category-books-grid');
        const pagination = document.querySelector('.category-books-pagination');
        const countEl = document.querySelector('.category-books-found');
        if (!grid) return;

        try {
            isUpdating = true;
            grid.style.opacity = '0.5';
            grid.style.pointerEvents = 'none';

            const url = new URL(window.location.href);
            const searchParams = new URLSearchParams();
            for (const [k, v] of Object.entries(params)) {
                if (v) searchParams.set(k, v);
            }
            url.search = searchParams.toString();

            const resp = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (!resp.ok) throw new Error('Network error');
            const data = await resp.json();

            grid.innerHTML = new DOMParser().parseFromString(data.html, 'text/html')
                .querySelector('.category-books-grid').innerHTML;

            if (countEl && data.count !== undefined) {
                countEl.textContent = data.count + ' ' + (data.count === 1 ? 'book' : 'books') + ' found';
            }

            if (data.hasPages) {
                if (pagination) {
                    pagination.innerHTML = new DOMParser().parseFromString(data.pagination, 'text/html')
                        .querySelector('.category-books-pagination').innerHTML;
                } else {
                    const newPag = document.createElement('div');
                    newPag.className = 'category-books-pagination';
                    newPag.innerHTML = new DOMParser().parseFromString(data.pagination, 'text/html')
                        .querySelector('.category-books-pagination').innerHTML;
                    grid.parentNode.appendChild(newPag);
                }
            } else if (pagination) {
                pagination.remove();
            }

            window.history.pushState({ params }, '', url.toString());

            if (typeof window.initBookCards === 'function') {
                setTimeout(window.initBookCards, 100);
            }

        } catch (err) {
            console.error('Error:', err);
            window.location.href = window.location.href;
        } finally {
            grid.style.opacity = '1';
            grid.style.pointerEvents = 'auto';
            isUpdating = false;
        }
    }

    // Apply filters button
    const form = document.getElementById('categoryFilterForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = {};
            formData.forEach((v, k) => { if (v) params[k] = v; });
            params.sort = document.querySelector('.category-sort-select')?.value || 'latest';
            fetchCategoryBooks(params);
            // Close mobile sidebar
            if (window.innerWidth <= 768) {
                window.toggleCategoryFilters();
            }
        });
    }

    // Sort change
    window.sortCategoryBooks = function (value) {
        const formData = new FormData(document.getElementById('categoryFilterForm'));
        const params = {};
        formData.forEach((v, k) => { if (v) params[k] = v; });
        params.sort = value;
        fetchCategoryBooks(params);
    };

    // Pagination clicks
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.category-books-pagination a');
        if (!link) return;
        e.preventDefault();
        const url = new URL(link.href);
        const params = {};
        url.searchParams.forEach((v, k) => params[k] = v);
        fetchCategoryBooks(params);
        window.scrollTo({ top: document.querySelector('.category-books-content').getBoundingClientRect().top + window.scrollY - 100, behavior: 'smooth' });
    });
})();
