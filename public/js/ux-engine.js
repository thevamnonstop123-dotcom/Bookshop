class UXEngine {
    constructor(config) {
        this.container = document.querySelector(config.container);
        this.gridSelector = config.gridSelector;
        this.urlBuilder = config.urlBuilder;
        this.onUpdate = config.onUpdate || function () {};
        this.isLoading = false;
    }

    getGrid() {
        if (!this.container) return null;
        return this.container.querySelector(this.gridSelector);
    }

    async fetch(filters = {}, silent = false) {
        if (this.isLoading) return;
        this.isLoading = true;

        const grid = this.getGrid();
        if (!grid) return;

        try {
            // =========================
            // LOADING STATE
            // =========================
            grid.classList.add('is-loading');

            if (!silent) {
                grid.innerHTML = this.skeletonHTML();
            }

            // =========================
            // FETCH
            // =========================
            const url = this.urlBuilder(filters);

            const resp = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!resp.ok) throw new Error(resp.statusText);

            const data = await resp.json();

            // =========================
            // TRANSITION OUT
            // =========================
            grid.style.opacity = '0.2';
            grid.style.transform = 'translateY(6px)';

            await this.delay(80);

            // =========================
            // REPLACE CONTENT
            // =========================
            this.container.innerHTML = data.html;

            const newGrid = this.getGrid();

            if (newGrid) {
                newGrid.classList.remove('is-loading');
                newGrid.classList.add('is-ready');

                requestAnimationFrame(() => {
                    newGrid.style.opacity = '1';
                    newGrid.style.transform = 'translateY(0)';
                });
            }

            // =========================
            // CALLBACK HOOK
            // =========================
            this.onUpdate(data, filters);

            // =========================
            // HISTORY
            // =========================
            const newUrl = this.urlBuilder(filters);
            window.history.pushState({ filters }, '', newUrl);

        } catch (err) {
            console.error('UXEngine Error:', err);
            grid.innerHTML = `<p style="padding:20px;">Error loading content</p>`;
        } finally {
            this.isLoading = false;

            const grid = this.getGrid();
            if (grid) {
                grid.classList.remove('is-loading');
                grid.classList.add('is-ready');
            }
        }
    }

    skeletonHTML() {
        return Array(8).fill(`
            <div class="skeleton-card">
                <div class="skeleton skeleton-image"></div>
                <div class="skeleton skeleton-title"></div>
                <div class="skeleton skeleton-text"></div>
            </div>
        `).join('');
    }

    delay(ms) {
        return new Promise(r => setTimeout(r, ms));
    }
}