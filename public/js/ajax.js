/**
 * Bookshop AJAX — Reusable fetch wrapper
 * 
 * Usage:
 *   Ajax.get('/url').then(data => ...);
 *   Ajax.post('/url', { name: 'John' }).then(data => ...);
 *   Ajax.delete('/url').then(data => ...);
 *   Ajax.submitForm(formElement).then(data => ...);
 */
(function () {
    'use strict';

    window.Ajax = {
        csrfToken: function () {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        },

        headers: function () {
            return {
                'X-CSRF-TOKEN': this.csrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            };
        },

        async request(url, options = {}) {
            try {
                const resp = await fetch(url, {
                    headers: this.headers(),
                    ...options,
                });

                const data = await resp.json().catch(() => ({}));

                if (!resp.ok) {
                    throw { status: resp.status, message: data.message || 'Request failed', data: data };
                }

                return data;
            } catch (err) {
                if (err.status === 422) {
                    // Validation errors — return them for inline display
                    return err.data;
                }
                console.error('Ajax error:', err);
                throw err;
            }
        },

        get(url) {
            return this.request(url);
        },

        post(url, body = {}) {
            return this.request(url, { method: 'POST', body: JSON.stringify(body) });
        },

        put(url, body = {}) {
            return this.request(url, { method: 'PUT', body: JSON.stringify(body) });
        },

        patch(url, body = {}) {
            return this.request(url, { method: 'PATCH', body: JSON.stringify(body) });
        },

        delete(url) {
            return this.request(url, { method: 'DELETE' });
        },

        // Submit a form as AJAX, return response
        async submitForm(form) {
            const url = form.action;
            const method = form.method.toUpperCase() || 'POST';
            const formData = new FormData(form);
            const body = {};

            formData.forEach(function (val, key) { body[key] = val; });

            return this.request(url, { method: method, body: JSON.stringify(body) });
        },

        // Replace current page content with response HTML
        async loadInto(url, targetSelector) {
            const resp = await fetch(url, { headers: { 'Accept': 'text/html' } });
            const html = await resp.text();
            const target = document.querySelector(targetSelector);
            if (target) {
                target.innerHTML = html;
            }
            return html;
        },

        // Redirect without full reload (pushState)
        navigate(url) {
            history.pushState({}, '', url);
            window.dispatchEvent(new PopStateEvent('popstate'));
        },

        // Reload a specific element's content
        async reloadElement(url, targetSelector) {
            const html = await this.loadInto(url, targetSelector);
            return html;
        }
    };

})();