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
            const meta = document.querySelector('meta[name="csrf-token"]');
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
            const loader = document.getElementById('app-loader');
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
            const config = {
                method: options.method || 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRF(),
                    'Accept': 'application/json',
                },
            };

            // Merge custom headers
            if (options.headers) {
                for (const key in options.headers) {
                    config.headers[key] = options.headers[key];
                }
            }

            if (options.data) {
                config.headers['Content-Type'] = 'application/json';
                config.body = JSON.stringify(options.data);
            }

            try {
                const response = await fetch(url, config);
                const json = await response.json();

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
            let timer;
            return function () {
                const context = this;
                const args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    func.apply(context, args);
                }, delay);
            };
        },
    };

})();