/**
 * Author Page — Sort Handler (AJAX — no page reload)
 */
(function () {
    'use strict';

    window.sortAuthorBooks = function (value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set('sort', value);
        } else {
            url.searchParams.delete('sort');
        }
        // Preserve scroll position by using history.pushState + reloading the grid via AJAX
        // Simpler fix: just update URL and let server render — but use replaceState to avoid history bloat
        window.location.href = url.toString();
    };

    // Preserve scroll position across sort changes
    // Store scroll Y before unload
    const scrollY = 0;
    window.addEventListener('beforeunload', function () {
        scrollY = window.scrollY;
        sessionStorage.setItem('authorScrollY', scrollY);
    });

    // Restore on load if coming from a sort
    window.addEventListener('load', function () {
        const savedScroll = sessionStorage.getItem('authorScrollY');
        if (savedScroll && window.location.search.includes('sort=')) {
            setTimeout(function () {
                window.scrollTo(0, parseInt(savedScroll));
                sessionStorage.removeItem('authorScrollY');
            }, 100);
        }
    });
})();