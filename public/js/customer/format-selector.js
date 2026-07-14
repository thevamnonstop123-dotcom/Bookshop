/**
 * Format Selector - Book Detail Page
 * Handles switching between physical and ebook formats
 */
(function () {
    'use strict';

    function initFormatSelector() {
        var formatOptions = document.querySelectorAll('.format-option');
        if (formatOptions.length <= 1) return;

        function selectFormat(format) {
            formatOptions.forEach(function (opt) {
                opt.classList.toggle('selected', opt.dataset.format === format);
                var radio = opt.querySelector('.format-radio');
                if (radio) radio.checked = (opt.dataset.format === format);
            });

            var priceEl = document.getElementById('formatPrice');
            var availabilityEl = document.getElementById('formatAvailability');
            var qtySelector = document.getElementById('quantitySelector');
            var ebookInfo = document.getElementById('ebookInfo');
            var cartBtns = document.querySelectorAll('.btn-add-cart');
            var buyBtn = document.getElementById('buyNowBtn');
            var trustBadges = document.querySelector('.book-purchase-trust');
            var mobilePrice = document.getElementById('mobileFormatPrice');
            var mobileLabel = document.getElementById('mobileFormatLabel');
            var mobileCartBtn = document.querySelector('.book-mobile-bar-cart');

            if (format === 'ebook') {
                var ebookPrice = getMeta('ebook-price');
                var ebookOnSale = getMeta('ebook-on-sale') === '1';
                var ebookSalePrice = getMeta('ebook-sale-price');
                
                if (priceEl && priceEl.parentElement) {
                    var container = priceEl.parentElement;
                    if (ebookOnSale && ebookSalePrice) {
                        container.innerHTML = 
                            '<span class="purchase-price-original">' + formatCurrency(ebookPrice) + ' MMK</span>' +
                            '<span class="purchase-price-sale" id="formatPrice">' + formatCurrency(ebookSalePrice) + ' MMK</span>';
                    } else {
                        container.innerHTML = 
                            '<span class="purchase-price-current" id="formatPrice">' + formatCurrency(ebookPrice) + ' MMK</span>';
                    }
                }

                if (availabilityEl) {
                    availabilityEl.innerHTML = 
                        '<span class="purchase-availability-badge" style="background:#F0FDF4;color:#166534;">' +
                            '<i class="fas fa-circle-check"></i> Available Instantly' +
                        '</span>';
                }

                if (qtySelector) qtySelector.style.display = 'none';
                if (ebookInfo) ebookInfo.style.display = '';

                if (trustBadges) {
                    trustBadges.innerHTML = 
                        '<span><i class="fas fa-download"></i> Instant Download</span>' +
                        '<span><i class="fas fa-shield-halved"></i> Secure Checkout</span>' +
                        '<span><i class="fas fa-infinity"></i> Lifetime Access</span>';
                }

            } else {
                var physPrice = getMeta('physical-price');
                var physOnSale = getMeta('physical-on-sale') === '1';
                var physSalePrice = getMeta('physical-sale-price');
                var physDiscount = getMeta('physical-discount');
                
                if (priceEl && priceEl.parentElement) {
                    var container = priceEl.parentElement;
                    if (physOnSale && physSalePrice) {
                        container.innerHTML = 
                            '<span class="purchase-price-original">' + formatCurrency(physPrice) + ' MMK</span>' +
                            '<span class="purchase-price-sale" id="formatPrice">' + formatCurrency(physSalePrice) + ' MMK</span>' +
                            '<span class="purchase-price-save">Save ' + physDiscount + '%</span>';
                    } else {
                        container.innerHTML = 
                            '<span class="purchase-price-current" id="formatPrice">' + formatCurrency(physPrice) + ' MMK</span>';
                    }
                }

                var availStatus = getMeta('physical-availability');
                var availLabel = getMeta('physical-availability-label');
                var availIcon = getMeta('physical-availability-icon');
                var availBg = getMeta('physical-availability-bg');
                var availText = getMeta('physical-availability-text');
                var isPurchasable = getMeta('physical-purchasable') === '1';
                
                if (availabilityEl) {
                    availabilityEl.innerHTML = 
                        '<span class="purchase-availability-badge" style="background:' + availBg + ';color:' + availText + ';">' +
                            '<i class="fas ' + availIcon + '"></i> ' + availLabel +
                        '</span>';
                }

                if (qtySelector) qtySelector.style.display = '';
                if (ebookInfo) ebookInfo.style.display = 'none';

                var stockQty = parseInt(getMeta('physical-stock') || '0');
                var qtyInput = document.getElementById('quantity');
                if (qtyInput) {
                    qtyInput.max = stockQty;
                    if (parseInt(qtyInput.value) > stockQty) {
                        qtyInput.value = stockQty;
                    }
                }

                if (trustBadges) {
                    trustBadges.innerHTML = 
                        '<span><i class="fas fa-truck-fast"></i> Free Delivery</span>' +
                        '<span><i class="fas fa-shield-halved"></i> Secure Checkout</span>' +
                        '<span><i class="fas fa-rotate-left"></i> Easy Returns</span>';
                }

                if (!isPurchasable) {
                    if (buyBtn) buyBtn.style.display = 'none';
                } else {
                    if (buyBtn) buyBtn.style.display = '';
                }
            }

            cartBtns.forEach(function (btn) {
                btn.dataset.format = format;
                if (format === 'physical' && getMeta('physical-purchasable') !== '1') {
                    btn.style.display = 'none';
                } else {
                    btn.style.display = '';
                }
            });

            if (mobileCartBtn) mobileCartBtn.dataset.format = format;
            if (mobileLabel) mobileLabel.textContent = format === 'ebook' ? 'eBook' : 'Paperback';
            
            if (buyBtn) {
                if (format === 'physical' && getMeta('physical-purchasable') !== '1') {
                    buyBtn.style.display = 'none';
                } else {
                    buyBtn.style.display = '';
                }
            }
        }

        function getMeta(name) {
            var el = document.querySelector('meta[name="' + name + '"]');
            return el ? el.content : null;
        }

        function formatCurrency(amount) {
            return Number(amount).toLocaleString('en-US');
        }

        formatOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                selectFormat(this.dataset.format);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initFormatSelector);
})();
