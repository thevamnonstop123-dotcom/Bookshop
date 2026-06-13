/**
 * Bookshop Checkout — Interactions
 * Payment method selector, address selector, button states
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initPaymentMethodSelector();
        initAddressSelector();
        initAutoRedirect();
    });

    // ========== PAYMENT METHOD SELECTOR ==========
    function initPaymentMethodSelector() {
        var options = document.querySelectorAll('.payment-option');
        var placeOrderBtn = document.getElementById('placeOrderBtn');

        if (!options.length || !placeOrderBtn) return;

        var buttonLabels = {
            stripe: '<i class="fas fa-lock"></i> Pay with Stripe',
            kpay: '<i class="fas fa-lock"></i> Pay with KBZ Pay',
            wave: '<i class="fas fa-lock"></i> Pay with Wave Pay',
            cod: '<i class="fas fa-box"></i> Place Order (COD)',
        };

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                // Remove selected from all
                options.forEach(function (o) {
                    o.classList.remove('payment-option-selected');
                });

                // Add selected to clicked
                option.classList.add('payment-option-selected');

                // Check the radio
                var radio = option.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                // Update button text
                var method = option.dataset.method;
                if (buttonLabels[method] && placeOrderBtn) {
                    placeOrderBtn.innerHTML = buttonLabels[method];
                }
            });
        });
    }

        // ========== ADDRESS SELECTOR ==========
    function initAddressSelector() {
        var addressCards = document.querySelectorAll('.address-card');
        var newAddressInputs = document.querySelectorAll('#receiverName, #phoneNumber, #addressLine');

        // If a saved address is selected, make new address fields optional
        function toggleNewAddressRequired() {
            var selectedRadio = document.querySelector('input[name="address_id"]:checked');
            newAddressInputs.forEach(function (input) {
                if (selectedRadio) {
                    input.removeAttribute('required');
                } else {
                    input.setAttribute('required', 'required');
                }
            });
        }

        addressCards.forEach(function (card) {
            card.addEventListener('click', function () {
                addressCards.forEach(function (c) {
                    c.classList.remove('address-card-selected');
                });
                card.classList.add('address-card-selected');

                var radio = card.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                toggleNewAddressRequired();
            });
        });

        // Run on load
        toggleNewAddressRequired();
    }

    // ========== AUTO-REDIRECT ON SUCCESS PAGE ==========
    function initAutoRedirect() {
        var successCard = document.querySelector('.success-card');
        if (!successCard) return;

        var redirectUrl = '/books';
        var booksLink = document.querySelector('.success-btn-primary');
        if (booksLink) {
            redirectUrl = booksLink.getAttribute('href') || redirectUrl;
        }

        setTimeout(function () {
            window.location.href = redirectUrl;
        }, 8000);
    }

    // Ensure mobile menu/overlay are closed when submitting checkout (prevents invisible overlays blocking clicks)
    var checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function () {
            var mobilePanel = document.getElementById('navbarMobilePanel');
            var mobileToggle = document.getElementById('navbarMobileToggle');
            var mobileOverlay = document.getElementById('navbarMobileOverlay');
            if (mobilePanel && mobilePanel.classList.contains('open')) {
                mobilePanel.classList.remove('open');
            }
            if (mobileToggle && mobileToggle.classList.contains('active')) {
                mobileToggle.classList.remove('active');
            }
            if (mobileOverlay && mobileOverlay.classList.contains('show')) {
                mobileOverlay.classList.remove('show');
            }
            document.body.style.overflow = '';
        });
    }

})();