/**
 * Bookshop Checkout — Step Navigation & Interactions
 */
(function () {
    'use strict';

    let currentStep = 1;
    const totalSteps = 3;
    const form = document.getElementById('checkoutForm');
    const nextBtn = document.getElementById('nextStep');
    const prevBtn = document.getElementById('prevStep');
    const steps = document.querySelectorAll('.checkout-step');
    const indicators = document.querySelectorAll('[data-step-indicator]');

    // ========== STEP NAVIGATION ==========
    function showStep(step) {
        steps.forEach(function (el) { el.classList.remove('active'); });
        const target = document.querySelector('[data-step="' + step + '"]');
        if (target) target.classList.add('active');

        indicators.forEach(function (i) {
            i.classList.toggle('active', parseInt(i.dataset.stepIndicator) === step);
        });

        prevBtn.style.display = step === 1 ? 'none' : 'flex';

        if (step === totalSteps) {
            nextBtn.innerHTML = '<i class="fas fa-lock"></i> Place Order';
            nextBtn.type = 'submit';
        } else {
            nextBtn.innerHTML = 'Continue <i class="fas fa-arrow-right"></i>';
            nextBtn.type = 'button';
        }

        // Scroll to top of form
        if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
            // If step 3, the button type is "submit" — form submits naturally
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }

    // ========== PAYMENT METHOD SELECTOR ==========
    function initPaymentMethodSelector() {
        const options = document.querySelectorAll('.payment-option');
        options.forEach(function (option) {
            option.addEventListener('click', function () {
                options.forEach(function (o) { o.classList.remove('payment-option-selected'); });
                option.classList.add('payment-option-selected');
                const radio = option.querySelector('input');
                if (radio) radio.checked = true;
            });
        });
    }

    // ========== ADDRESS SELECTOR ==========
    function initAddressSelector() {
        const addressCards = document.querySelectorAll('.address-card');
        const newAddressInputs = document.querySelectorAll('input[name="receiver_name"], input[name="phone_number"], textarea[name="address_line"]');

        function toggleNewAddressRequired() {
            const selectedRadio = document.querySelector('input[name="address_id"]:checked');
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
                addressCards.forEach(function (c) { c.classList.remove('address-card-selected'); });
                card.classList.add('address-card-selected');
                const radio = card.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
                toggleNewAddressRequired();
            });
        });

        toggleNewAddressRequired();
    }

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        showStep(currentStep);
        initPaymentMethodSelector();
        initAddressSelector();
    });

})();