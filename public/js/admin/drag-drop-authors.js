/**
 * Bookshop Admin — Author Drag & Drop Selection
 * Shared across Book Create/Edit forms
 */
(function () {
    'use strict';

    let draggedId = null;

    // ========== RENDER SELECTED AUTHORS ==========
    window.renderSelectedAuthors = function () {
        const zone = document.getElementById('selectedAuthorsZone');
        const msg = document.getElementById('noAuthorsMsg');
        const inputsContainer = document.getElementById('authorInputs');
        const selected = window.selectedAuthors || [];

        if (!zone || !inputsContainer) return;

        // Clear existing chips in zone
        zone.querySelectorAll('.selected-author-chip').forEach(function (el) { el.remove(); });

        // Clear hidden inputs
        inputsContainer.innerHTML = '';

        if (selected.length === 0) {
            if (msg) msg.style.display = '';
        } else {
            if (msg) msg.style.display = 'none';

            selected.forEach(function (id) {
                const chipEl = document.querySelector('.author-chip[data-author-id="' + id + '"]');
                const name = chipEl ? chipEl.dataset.authorName : 'Author ' + id;

                const tag = document.createElement('span');
                tag.className = 'selected-author-chip';
                tag.innerHTML = name +
                    ' <button type="button" class="selected-author-remove" onclick="removeAuthor(' + id + ')" aria-label="Remove ' + name + '">' +
                    '<i class="fas fa-xmark"></i></button>';
                zone.appendChild(tag);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'author_ids[]';
                input.value = id;
                inputsContainer.appendChild(input);
            });
        }

        // Update chip active states
        document.querySelectorAll('.author-chip').forEach(function (chip) {
            const chipId = parseInt(chip.dataset.authorId);
            chip.classList.toggle('author-chip-selected', selected.includes(chipId));
        });
    };

    // ========== AUTHOR SELECTION ==========
    window.addAuthor = function (id) {
        if (!window.selectedAuthors) window.selectedAuthors = [];
        if (!window.selectedAuthors.includes(id)) {
            window.selectedAuthors.push(id);
            renderSelectedAuthors();
        }
    };

    window.removeAuthor = function (id) {
        if (!window.selectedAuthors) return;
        window.selectedAuthors = window.selectedAuthors.filter(function (a) { return a !== id; });
        renderSelectedAuthors();
    };

    window.toggleAuthorSelect = function (chip, id) {
        if (!window.selectedAuthors) window.selectedAuthors = [];
        if (window.selectedAuthors.includes(id)) {
            removeAuthor(id);
        } else {
            addAuthor(id);
        }
    };

    // ========== DRAG & DROP ==========
    window.handleAuthorDragStart = function (e) {
        draggedId = parseInt(e.target.dataset.authorId);
        e.target.classList.add('author-chip-dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', draggedId);
    };

    window.handleAuthorDragEnd = function (e) {
        e.target.classList.remove('author-chip-dragging');
        const zone = document.getElementById('selectedAuthorsZone');
        if (zone) zone.classList.remove('author-dropzone-active');
        draggedId = null;
    };

    window.handleDragOver = function (e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        const zone = document.getElementById('selectedAuthorsZone');
        if (zone) zone.classList.add('author-dropzone-active');
    };

    window.handleDragLeave = function (e) {
        const zone = document.getElementById('selectedAuthorsZone');
        if (zone) zone.classList.remove('author-dropzone-active');
    };

    window.handleAuthorDrop = function (e) {
        e.preventDefault();
        const zone = document.getElementById('selectedAuthorsZone');
        if (zone) zone.classList.remove('author-dropzone-active');

        const id = draggedId || parseInt(e.dataTransfer.getData('text/plain'));
        if (id && !isNaN(id)) {
            addAuthor(id);
        }
        draggedId = null;
    };

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        if (window.selectedAuthors && window.selectedAuthors.length > 0) {
            renderSelectedAuthors();
        }
    });

})();