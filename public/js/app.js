/**
 * Bookshop — Global App Utilities
 * Reusable toast, loading, AJAX, currency, debounce
 */
(function () {
    'use strict';

    window.App = {
        /**
         * Get CSRF token from meta tag.
         */
        getCSRF: function () {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        /**
         * Show a toast notification.
         * @param {string} message
         * @param {string} type - 'success' | 'error' | 'warning'
         */
        toast: function (message, type) {
            type = type || 'success';

            // Remove existing toasts
            document.querySelectorAll('.app-toast').forEach(function (el) { el.remove(); });

            var toast = document.createElement('div');
            toast.className = 'app-toast app-toast-' + type;
            toast.innerHTML = message;
            document.body.appendChild(toast);

            // Show
            setTimeout(function () { toast.classList.add('show'); }, 10);

            // Hide after 3s
            setTimeout(function () {
                toast.classList.remove('show');
                setTimeout(function () { toast.remove(); }, 300);
            }, 3000);
        },

        /**
         * Show/hide loading overlay.
         */
        loading: function (show) {
            show = show !== false;
            var loader = document.getElementById('app-loader');
            if (show) {
                if (!loader) {
                    loader = document.createElement('div');
                    loader.id = 'app-loader';
                    loader.innerHTML = '<div class="loader-spinner"></div>';
                    document.body.appendChild(loader);
                }
                loader.style.display = 'flex';
            } else {
                if (loader) loader.style.display = 'none';
            }
        },

        /**
         * AJAX request helper.
         */
        request: async function (url, options) {
            options = options || {};
            var config = {
                method: options.method || 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRF(),
                    'Accept': 'application/json',
                },
            };

            // Merge custom headers
            if (options.headers) {
                for (var key in options.headers) {
                    config.headers[key] = options.headers[key];
                }
            }

            if (options.data) {
                config.headers['Content-Type'] = 'application/json';
                config.body = JSON.stringify(options.data);
            }

            try {
                var response = await fetch(url, config);
                var json = await response.json();

                if (!response.ok) {
                    throw new Error(json.message || 'Something went wrong.');
                }

                return json;
            } catch (error) {
                this.toast(error.message, 'error');
                throw error;
            }
        },

        /**
         * Shorthand AJAX methods.
         */
        ajax: {
            get: function (url) {
                return App.request(url, { method: 'GET' });
            },
            post: function (url, data) {
                return App.request(url, { method: 'POST', data: data });
            },
            put: function (url, data) {
                return App.request(url, { method: 'PUT', data: data });
            },
            delete: function (url) {
                return App.request(url, { method: 'DELETE' });
            },
        },

        /**
         * Format number as currency (MMK).
         */
        formatCurrency: function (amount) {
            return new Intl.NumberFormat('en-MM').format(amount) + ' MMK';
        },

        /**
         * Debounce function for search inputs.
         */
        debounce: function (func, delay) {
            delay = delay || 300;
            var timer;
            return function () {
                var context = this;
                var args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    func.apply(context, args);
                }, delay);
            };
        },
    };

    // ========== WISHLIST TOGGLE ==========
    window.toggleWishlist = async function (btn, bookId) {
        if (!btn || !bookId) return;

        try {
            var resp = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ book_id: bookId })
            });
            var data = await resp.json();

            if (data.added) {
                btn.classList.add('wishlisted');
                if (btn.querySelector('i')) btn.querySelector('i').className = 'fas fa-heart';
            } else {
                btn.classList.remove('wishlisted');
                if (btn.querySelector('i')) btn.querySelector('i').className = 'far fa-heart';
            }

            App.toast(data.message, data.added ? 'success' : 'warning');
        } catch (e) {
            console.error('Wishlist error:', e);
        }
    };

})();