(function(){
    // Lightweight shim: dynamically load the real auth script if an old page requests auth-modal.js
    try {
        var s = document.createElement('script');
        s.src = '/js/customer/auth.js';
        s.defer = true;
        document.head.appendChild(s);
    } catch (e) {
        console.error('Failed to load auth.js via shim:', e);
    }
})();
