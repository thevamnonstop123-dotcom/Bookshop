// /* =============================================
//    Bookshop - Global AJAX Setup
//    Reusable for all features
//    ============================================= */

// const App = {
//     /**
//      * Get CSRF token from meta tag.
//      */
//     getCSRF() {
//         return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
//     },

//     /**
//      * Show a toast notification.
//      * @param {string} message
//      * @param {string} type - 'success' | 'error' | 'warning'
//      */
//     toast(message, type = 'success') {
//         // Remove existing toasts
//         document.querySelectorAll('.app-toast').forEach(el => el.remove());

//         const toast = document.createElement('div');
//         toast.className = `app-toast app-toast-${type}`;
//         toast.innerHTML = message;
//         document.body.appendChild(toast);

//         // Show
//         setTimeout(() => toast.classList.add('show'), 10);

//         // Hide after 3s
//         setTimeout(() => {
//             toast.classList.remove('show');
//             setTimeout(() => toast.remove(), 300);
//         }, 3000);
//     },

//     /**
//      * Show/hide loading overlay.
//      */
//     loading(show = true) {
//         let loader = document.getElementById('app-loader');
//         if (show) {
//             if (!loader) {
//                 loader = document.createElement('div');
//                 loader.id = 'app-loader';
//                 loader.innerHTML = '<div class="loader-spinner"></div>';
//                 document.body.appendChild(loader);
//             }
//             loader.style.display = 'flex';
//         } else {
//             if (loader) loader.style.display = 'none';
//         }
//     },

//     /**
//      * AJAX request helper.
//      * @param {string} url
//      * @param {object} options - { method, data, headers }
//      * @returns {Promise}
//      */
//     async request(url, options = {}) {
//         const config = {
//             method: options.method || 'POST',
//             headers: {
//                 'X-CSRF-TOKEN': this.getCSRF(),
//                 'Accept': 'application/json',
//                 ...options.headers,
//             },
//         };

//         if (options.data) {
//             config.headers['Content-Type'] = 'application/json';
//             config.body = JSON.stringify(options.data);
//         }

//         try {
//             const response = await fetch(url, config);
//             const json = await response.json();

//             if (!response.ok) {
//                 throw new Error(json.message || 'Something went wrong.');
//             }

//             return json;
//         } catch (error) {
//             this.toast(error.message, 'error');
//             throw error;
//         }
//     },

//     /**
//      * Shorthand methods.
//      */
//     ajax: {
//         get(url) {
//             return App.request(url, { method: 'GET' });
//         },
//         post(url, data) {
//             return App.request(url, { method: 'POST', data });
//         },
//         put(url, data) {
//             return App.request(url, { method: 'PUT', data });
//         },
//         delete(url) {
//             return App.request(url, { method: 'DELETE' });
//         },
//     },

//     /**
//      * Format number as currency (MMK).
//      */
//     formatCurrency(amount) {
//         return new Intl.NumberFormat('en-MM').format(amount) + ' MMK';
//     },

//     /**
//      * Debounce function for search inputs.
//      */
//     debounce(func, delay = 300) {
//         let timer;
//         return function (...args) {
//             clearTimeout(timer);
//             timer = setTimeout(() => func.apply(this, args), delay);
//         };
//     },
// };

