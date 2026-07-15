/**
 * Checkout Page — Complete JavaScript
 * Separated from Blade for clean organization
 */

document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // 1. STEP NAVIGATION (2 Steps)
    // ============================================
    const steps = document.querySelectorAll('.checkout-step');
    const stepIndicators = document.querySelectorAll('.step');
    const toStep2Btn = document.getElementById('toStep2');
    const backToStep1Btn = document.getElementById('backToStep1');

    function goToStep(stepNumber) {
        // Update steps
        steps.forEach(function(step) {
            step.classList.toggle('active', parseInt(step.dataset.step) === stepNumber);
        });
        
        // Update indicators
        stepIndicators.forEach(function(indicator) {
            indicator.classList.toggle('active', parseInt(indicator.dataset.step) === stepNumber);
        });
    }

    if (toStep2Btn) {
        toStep2Btn.addEventListener('click', function() {
            if (validateAddressForm()) {
                goToStep(2);
            }
        });
    }

    if (backToStep1Btn) {
        backToStep1Btn.addEventListener('click', function() {
            goToStep(1);
        });
    }

    // ============================================
    // 2. ADDRESS FORM VALIDATION
    // ============================================
    function validateAddressForm() {
        const name = document.getElementById('receiverName');
        const phone = document.getElementById('phoneNumber');
        const address = document.getElementById('addressLine');
        
        let isValid = true;
        let errorMsg = [];

        if (!name.value.trim() || name.value.trim().length < 2) {
            name.classList.add('error');
            errorMsg.push('Please enter your full name');
            isValid = false;
        } else {
            name.classList.remove('error');
        }

        if (!phone.value.trim() || phone.value.trim().length < 9) {
            phone.classList.add('error');
            errorMsg.push('Please enter a valid phone number');
            isValid = false;
        } else {
            phone.classList.remove('error');
        }

        if (!address.value.trim() || address.value.trim().length < 5) {
            address.classList.add('error');
            errorMsg.push('Please enter your full address');
            isValid = false;
        } else {
            address.classList.remove('error');
        }

        if (!isValid) {
            alert('Please fix the following:\n\n• ' + errorMsg.join('\n• '));
        }

        return isValid;
    }

    // ============================================
    // 3. ADDRESS DROPDOWN
    // ============================================
    const addressBtn = document.getElementById('addressSelectBtn');
    const addressDropdown = document.getElementById('addressDropdown');
    const addressDisplay = document.getElementById('addressDisplayText');
    const receiverName = document.getElementById('receiverName');
    const phoneNumber = document.getElementById('phoneNumber');
    const addressLine = document.getElementById('addressLine');

    if (addressBtn) {
        addressBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('open');
            addressDropdown.classList.toggle('open');
        });
    }

    // Select saved address
    document.querySelectorAll('.address-option-item.address-saved').forEach(function(item) {
        item.addEventListener('click', function(e) {
            if (e.target.closest('input[type="radio"]')) return;
            
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            
            // Get data from attributes
            const name = this.dataset.name || '';
            const phone = this.dataset.phone || '';
            const address = this.dataset.address || '';
            
            // Fill form
            if (receiverName) receiverName.value = name;
            if (phoneNumber) phoneNumber.value = phone;
            if (addressLine) addressLine.value = address;
            
            // Update display
            addressDisplay.textContent = name + ' • ' + address;
            
            // Update selected state
            document.querySelectorAll('.address-option-item.address-saved').forEach(function(el) {
                el.classList.remove('selected');
            });
            this.classList.add('selected');
            
            // Update confirm address
            updateConfirmAddress(name, address);
            
            // Close dropdown
            addressDropdown.classList.remove('open');
            addressBtn.classList.remove('open');
        });
    });

    // GPS Result click
    const gpsResultItem = document.getElementById('gpsResultItem');
    if (gpsResultItem) {
        gpsResultItem.addEventListener('click', function() {
            const desc = this.querySelector('.address-option-desc')?.textContent || '';
            const title = this.querySelector('.address-option-title')?.textContent || 'GPS Location';
            
            if (addressLine) addressLine.value = desc;
            addressDisplay.textContent = '📍 ' + desc;
            
            // Deselect saved addresses
            document.querySelectorAll('.address-option-item.address-saved').forEach(function(el) {
                el.classList.remove('selected');
                const radio = el.querySelector('input[type="radio"]');
                if (radio) radio.checked = false;
            });
            
            updateConfirmAddress('GPS Location', desc);
            
            addressDropdown.classList.remove('open');
            addressBtn.classList.remove('open');
        });
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('addressOption');
        if (wrapper && !wrapper.contains(e.target)) {
            if (addressDropdown) addressDropdown.classList.remove('open');
            if (addressBtn) addressBtn.classList.remove('open');
        }
    });

    // Search functionality
    const searchInput = document.getElementById('addressSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.address-option-item.address-saved').forEach(function(item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });
        });
    }

    // ============================================
    // 4. TOGGLE NEW ADDRESS (Manual Entry)
    // ============================================
    window.toggleNewAddress = function() {
        // Just focus on the form fields
        document.getElementById('receiverName').focus();
        addressDropdown.classList.remove('open');
        addressBtn.classList.remove('open');
    };

    // ============================================
    // 5. UPDATE CONFIRM ADDRESS DISPLAY
    // ============================================
    function updateConfirmAddress(name, address) {
        const confirmText = document.getElementById('confirmAddressText');
        if (confirmText) {
            confirmText.textContent = name + ' • ' + address;
        }
    }

    // ============================================
    // 6. PAYMENT CARD SELECTION
    // ============================================
    const paymentCards = document.querySelectorAll('.payment-card');
    
    paymentCards.forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.closest('.payment-card-check')) return;
            
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                const event = new Event('change', { bubbles: true });
                radio.dispatchEvent(event);
            }
            
            paymentCards.forEach(function(c) {
                c.classList.remove('selected');
            });
            this.classList.add('selected');
        });
    });

    // ============================================
    // 7. GPS LOCATION DETECTION
    // ============================================
    let gpsMap = null;

    window.detectLocation = function() {
        const gpsStatusText = document.getElementById('gpsStatusText');
        const gpsStatusDesc = document.getElementById('gpsStatusDesc');
        const gpsSpinner = document.getElementById('gpsSpinner');
        const gpsResultItem = document.getElementById('gpsResultItem');
        const gpsResultDesc = document.getElementById('gpsResultDesc');
        const gpsErrorMsg = document.getElementById('gpsErrorMsg');
        const gpsErrorText = document.getElementById('gpsErrorText');
        const addressDisplay = document.getElementById('addressDisplayText');
        const addressInput = document.getElementById('addressLine');
        
        gpsErrorMsg.style.display = 'none';
        gpsResultItem.style.display = 'none';
        gpsSpinner.style.display = 'inline-block';
        gpsStatusText.textContent = 'Detecting...';
        gpsStatusDesc.textContent = 'Please wait';
        
        if (!navigator.geolocation) {
            gpsSpinner.style.display = 'none';
            gpsStatusText.textContent = 'Use Current Location';
            gpsStatusDesc.textContent = 'GPS not supported';
            gpsErrorText.textContent = 'Geolocation is not supported by your browser';
            gpsErrorMsg.style.display = 'flex';
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            async function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('gpsLat').value = lat;
                document.getElementById('gpsLng').value = lng;
                
                gpsSpinner.style.display = 'none';
                gpsStatusText.innerHTML = '<i class="fas fa-check-circle"></i> Location Found!';
                gpsStatusDesc.textContent = 'Tap to use this address';
                
                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`
                    );
                    const data = await response.json();
                    
                    const address = data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                    document.getElementById('gpsAddressInput').value = address;
                    
                    gpsResultDesc.textContent = address;
                    gpsResultItem.style.display = 'flex';
                    
                    if (addressInput) {
                        addressInput.value = address;
                    }
                    
                    addressDisplay.innerHTML = '<i class="fas fa-location-dot"></i> ' + address;
                    updateConfirmAddress('GPS Location', address);
                    
                    // Deselect saved addresses
                    document.querySelectorAll('.address-option-item.address-saved').forEach(function(el) {
                        el.classList.remove('selected');
                        const radio = el.querySelector('input[type="radio"]');
                        if (radio) radio.checked = false;
                    });
                    
                    setTimeout(function() {
                        addressDropdown.classList.remove('open');
                        addressBtn.classList.remove('open');
                    }, 1500);
                    
                } catch (err) {
                    const fallback = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
                    document.getElementById('gpsAddressInput').value = fallback;
                    gpsResultDesc.textContent = fallback;
                    gpsResultItem.style.display = 'flex';
                    if (addressInput) addressInput.value = fallback;
                    addressDisplay.innerHTML = '<i class="fas fa-location-dot"></i> ' + fallback;
                }
            },
            function(error) {
                gpsSpinner.style.display = 'none';
                gpsStatusText.innerHTML = '<i class="fas fa-exclamation-circle"></i> Use Current Location';
                gpsStatusDesc.textContent = 'Auto-detect via GPS';
                
                let msg = 'Unable to detect location. ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        msg = 'Location permission denied. Please allow location access.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        msg = 'Location unavailable. Please enter manually.';
                        break;
                    case error.TIMEOUT:
                        msg = 'Location request timed out. Please try again.';
                        break;
                }
                gpsErrorText.textContent = msg;
                gpsErrorMsg.style.display = 'flex';
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    };

    // ============================================
    // 8. KEYBOARD SHORTCUTS
    // ============================================
    document.addEventListener('keydown', function(e) {
        // Escape to close dropdown
        if (e.key === 'Escape') {
            if (addressDropdown) addressDropdown.classList.remove('open');
            if (addressBtn) addressBtn.classList.remove('open');
        }
        
        // Enter on Step 1 to go to Step 2
        if (e.key === 'Enter' && document.querySelector('.checkout-step.active[data-step="1"]')) {
            const activeElement = document.activeElement;
            if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA')) {
                // Let the form handle it
            }
        }
    });

    // ============================================
    // 9. INITIALIZE - Load first saved address
    // ============================================
    const firstSaved = document.querySelector('.address-option-item.address-saved.selected');
    if (firstSaved) {
        const name = firstSaved.dataset.name || '';
        const address = firstSaved.dataset.address || '';
        if (addressDisplay) {
            addressDisplay.textContent = name + ' • ' + address;
        }
        updateConfirmAddress(name, address);
    }

});