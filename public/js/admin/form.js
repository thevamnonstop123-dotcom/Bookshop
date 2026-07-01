/**
 * Bookshop Admin Form — Shared Utilities
 * Image preview
 */
(function () {
    "use strict";

    window.previewImage = function (event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById("imagePreview");
        const placeholder = document.getElementById("imagePlaceholder");

        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = "block";
        }

        if (placeholder) {
            placeholder.style.display = "none";
        }
    };

    function initGenreCheckboxes() {
        document
            .querySelectorAll(".admin-genre-checkbox input")
            .forEach(function (input) {
                input.addEventListener("change", function () {
                    if (this.checked) {
                        this.parentElement.style.background =
                            "var(--color-primary)";
                        this.parentElement.style.color = "var(--color-white)";
                        this.parentElement.style.borderColor =
                            "var(--color-primary)";
                    } else {
                        this.parentElement.style.background =
                            "var(--color-surface)";
                        this.parentElement.style.color =
                            "var(--color-text-secondary)";
                        this.parentElement.style.borderColor =
                            "var(--color-border)";
                    }
                });

                // Initial state
                if (input.checked) {
                    input.parentElement.style.background =
                        "var(--color-primary)";
                    input.parentElement.style.color = "var(--color-white)";
                    input.parentElement.style.borderColor =
                        "var(--color-primary)";
                }
            });
    }

    document.addEventListener("DOMContentLoaded", function () {
        initGenreCheckboxes();
    });

    window.updateGenreSelect = function () {
        const selected = [];
        document
            .querySelectorAll("#genreDropdown input:checked")
            .forEach(function (cb) {
                selected.push(cb.parentElement.textContent.trim());
            });
        const textEl = document.getElementById("genreSelectedText");
        if (textEl) {
            textEl.textContent =
                selected.length > 0 ? selected.join(", ") : "Select genres";
        }
    };

    // Close dropdown on outside click
    document.addEventListener("click", function (e) {
        const select = document.getElementById("genreSelect");
        if (select && !select.contains(e.target)) {
            document.getElementById("genreDropdown").classList.remove("open");
        }
    });

    // Init on load
    document.addEventListener("DOMContentLoaded", function () {
        updateGenreSelect();
    });
})();
