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

    if (!form) return;

    // ========== PHONE INPUT RESTRICTION ==========
    const phoneInput = document.getElementById('newPhone');
    if (phoneInput) {
        // Prevent non-digit input
        phoneInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 11 digits
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });

        // Prevent pasting non-digits
        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text');
            var digits = pasted.replace(/[^0-9]/g, '').slice(0, 11);
            this.value = digits;
        });

        // Prevent non-digit keys
        phoneInput.addEventListener('keypress', function(e) {
            // Allow only digits, backspace, delete, arrows
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                e.preventDefault();
            }
            
            // Prevent typing if already 11 digits
            if (this.value.length >= 11 && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }

    // ========== STEP NAVIGATION ==========
    function showStep(step) {
        steps.forEach(function (el) { el.classList.remove('active'); });
        const target = document.querySelector('[data-step="' + step + '"]');
        if (target) target.classList.add('active');

        indicators.forEach(function (i) {
            i.classList.toggle('active', parseInt(i.dataset.stepIndicator) === step);
        });

        if (prevBtn) {
            prevBtn.style.display = step === 1 ? 'none' : 'inline-flex';
        }

        if (nextBtn) {
            if (step === totalSteps) {
                nextBtn.innerHTML = '<i class="fas fa-lock"></i> Place Order';
                nextBtn.type = 'submit';
            } else {
                nextBtn.innerHTML = 'Continue <i class="fas fa-arrow-right"></i>';
                nextBtn.type = 'button';
            }
        }

        if (step === 3) {
            updateConfirmSection();
        }

        if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Next button click
    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            if (currentStep === totalSteps) return;
            
            e.preventDefault();
            
            if (currentStep === 1) {
                const selectedAddress = document.querySelector('input[name="address_id"]:checked');
                const newName = document.getElementById('newReceiverName');
                const newPhone = document.getElementById('newPhone');
                const newAddress = document.getElementById('newAddress');
                
                if (!selectedAddress) {
                    var errors = [];
                    
                    if (!newName || !newName.value.trim()) {
                        errors.push('Please enter receiver name');
                    }
                    
                    if (!newPhone || !newPhone.value.trim()) {
                        errors.push('Please enter phone number');
                    } else if (!/^09[0-9]{9}$/.test(newPhone.value.trim())) {
                        errors.push('Phone must be 11 digits starting with 09 (e.g. 09xxxxxxxxx)');
                    }
                    
                    if (!newAddress || !newAddress.value.trim()) {
                        errors.push('Please enter full address');
                    }
                    
                    if (errors.length > 0) {
                        alert(errors.join('\n'));
                        return;
                    }
                }
            }
            
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }

    // ========== ADDRESS SELECTION ==========
    const addressCards = document.querySelectorAll('.address-card');
    const newAddressInputs = document.querySelectorAll('#newReceiverName, #newPhone, #newAddress');
    const newAddressHint = document.getElementById('newAddressHint');

    addressCards.forEach(function (card) {
        card.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            addressCards.forEach(function (c) { c.classList.remove('address-card-selected'); });
            card.classList.add('address-card-selected');
            
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            
            newAddressInputs.forEach(function(input) { input.value = ''; });
            if (newAddressHint) newAddressHint.style.display = 'none';
        });
    });

    newAddressInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            var anyFilled = Array.from(newAddressInputs).some(function(inp) { return inp.value.trim() !== ''; });
            if (anyFilled) {
                addressCards.forEach(function(c) { c.classList.remove('address-card-selected'); });
                document.querySelectorAll('input[name="address_id"]').forEach(function(r) { r.checked = false; });
                if (newAddressHint) newAddressHint.style.display = 'block';
            } else {
                if (newAddressHint) newAddressHint.style.display = 'none';
            }
        });
    });

    // ========== PAYMENT METHOD SELECTION ==========
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(function (option) {
        option.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            paymentOptions.forEach(function (o) { o.classList.remove('selected'); });
            option.classList.add('selected');
            
            const radio = option.querySelector('input');
            if (radio) radio.checked = true;
        });
    });

    // ========== PREVENT EARLY FORM SUBMISSION ==========
    form.addEventListener('submit', function(e) {
        if (currentStep !== totalSteps) {
            e.preventDefault();
            alert('Please complete all steps before placing your order.');
            showStep(currentStep);
            return false;
        }
        
        // Final validation
        const selectedAddress = document.querySelector('input[name="address_id"]:checked');
        const newName = document.getElementById('newReceiverName');
        const newPhone = document.getElementById('newPhone');
        const newAddress = document.getElementById('newAddress');
        
        if (!selectedAddress) {
            if (!newName || !newName.value.trim() || 
                !newPhone || !newPhone.value.trim() || 
                !newAddress || !newAddress.value.trim()) {
                e.preventDefault();
                alert('Please provide a shipping address.');
                showStep(1);
                return false;
            }
            
            if (!/^09[0-9]{9}$/.test(newPhone.value.trim())) {
                e.preventDefault();
                alert('Phone must be 11 digits starting with 09 (e.g. 09xxxxxxxxx)');
                showStep(1);
                return false;
            }
        }
        
        return true;
    });

    // ========== CONFIRM SECTION ==========
    function updateConfirmSection() {
        const confirmAddr = document.getElementById('confirmAddress');
        const confirmAddrText = document.getElementById('confirmAddressText');
        
        if (confirmAddr && confirmAddrText) {
            const selectedAddress = document.querySelector('input[name="address_id"]:checked');
            if (selectedAddress) {
                const card = selectedAddress.closest('.address-card');
                if (card) {
                    const name = card.querySelector('.address-card-receiver').textContent;
                    const phone = card.querySelector('.address-card-phone').textContent;
                    const addr = card.querySelector('.address-card-line').textContent;
                    confirmAddrText.textContent = name + ' • ' + phone + ' • ' + addr;
                    confirmAddr.style.display = 'block';
                }
            } else {
                const newName = document.getElementById('newReceiverName');
                const newPhone = document.getElementById('newPhone');
                const newAddr = document.getElementById('newAddress');
                if (newName && newName.value && newPhone && newPhone.value && newAddr && newAddr.value) {
                    confirmAddrText.textContent = newName.value + ' • ' + newPhone.value + ' • ' + newAddr.value;
                    confirmAddr.style.display = 'block';
                }
            }
        }
    }

    // ========== INIT ==========
    showStep(1);

})();
