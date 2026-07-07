/**
 * Author Page — AJAX Sort + Sticky Dropdown
 */
(function () {
    'use strict';

    let isUpdating = false;

    window.sortAuthorBooks = async function (value) {
        if (isUpdating) return;
        
        const booksGrid = document.querySelector('.author-books-grid');
        const paginationContainer = document.querySelector('.author-books-pagination');
        const selectElement = document.getElementById('authorSortSelect');
        
        if (!booksGrid) return;
        
        try {
            isUpdating = true;
            booksGrid.style.opacity = '0.5';
            booksGrid.style.pointerEvents = 'none';
            if (selectElement) selectElement.disabled = true;
            
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', value);
            currentUrl.searchParams.delete('page');
            
            const response = await fetch(currentUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) throw new Error('Network error');
            const data = await response.json();
            
            booksGrid.innerHTML = new DOMParser()
                .parseFromString(data.html, 'text/html')
                .querySelector('.author-books-grid').innerHTML;
            
            if (data.hasPages) {
                if (paginationContainer) {
                    paginationContainer.innerHTML = new DOMParser()
                        .parseFromString(data.pagination, 'text/html')
                        .querySelector('.author-books-pagination').innerHTML;
                } else {
                    const container = document.getElementById('authorBooksContainer');
                    if (container) {
                        const newPagination = document.createElement('div');
                        newPagination.className = 'author-books-pagination';
                        newPagination.innerHTML = new DOMParser()
                            .parseFromString(data.pagination, 'text/html')
                            .querySelector('.author-books-pagination').innerHTML;
                        container.appendChild(newPagination);
                    }
                }
            } else if (paginationContainer) {
                paginationContainer.remove();
            }
            
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('sort', value);
            newUrl.searchParams.delete('page');
            window.history.pushState({ sort: value }, '', newUrl.toString());
            
            if (typeof window.initBookCards === 'function') {
                window.initBookCards();
            }
            
        } catch (error) {
            console.error('Sort error:', error);
            const url = new URL(window.location.href);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        } finally {
            booksGrid.style.opacity = '1';
            booksGrid.style.pointerEvents = 'auto';
            if (selectElement) selectElement.disabled = false;
            isUpdating = false;
        }
    };

    // Sticky sort
    function initSticky() {
        const sortWrapper = document.querySelector('.author-sort-wrapper');
        if (!sortWrapper) return;
        
        const navbarHeight = 68;
        const offset = navbarHeight + 12;
        
        function handleScroll() {
            const section = document.querySelector('.author-books-section');
            const container = section?.querySelector('.container');
            if (!section || !container) return;
            
            const sectionRect = section.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();
            const shouldStick = sectionRect.top <= offset && (sectionRect.bottom - sortWrapper.offsetHeight) > offset;
            
            if (shouldStick) {
                sortWrapper.style.position = 'fixed';
                sortWrapper.style.top = "65px";
                sortWrapper.style.right = (window.innerWidth - containerRect.right) + 'px';
                sortWrapper.style.zIndex = '50';
                sortWrapper.style.background = "transparent";
                sortWrapper.style.padding = '4px 8px';
                sortWrapper.style.borderRadius = '8px';
            } else {
                sortWrapper.style.cssText = '';
            }
        }
        
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => { handleScroll(); ticking = false; });
                ticking = true;
            }
        });
        window.addEventListener('resize', handleScroll);
        handleScroll();
    }

    // Pagination clicks
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.author-books-pagination a');
        if (!link) return;
        e.preventDefault();
        const url = new URL(link.href);
        const sortValue = url.searchParams.get('sort') || 'latest';
        const select = document.getElementById('authorSortSelect');
        if (select) select.value = sortValue;
        window.history.pushState({ sort: sortValue }, '', url.toString());
        window.sortAuthorBooks(sortValue);
    });

    // Init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSticky);
    } else {
        initSticky();
    }

})();

window.toggleAuthorDropdown = function() {
    var dd = document.getElementById('authorSortDropdown');
    if (!dd) return;
    dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
};

window.selectAuthorSort = function(value, label) {
    document.getElementById('authorSortLabel').textContent = label;
    document.getElementById('authorSortDropdown').style.display = 'none';
    window.sortAuthorBooks(value);
};

document.addEventListener('click', function(e) {
    var wrapper = document.getElementById('authorSortWrapper');
    var dd = document.getElementById('authorSortDropdown');
    if (wrapper && dd && !wrapper.contains(e.target)) {
        dd.style.display = 'none';
    }
});
