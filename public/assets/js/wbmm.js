(function () {
    'use strict';

    const baseUrl = (document.querySelector('meta[name="base-url"]')?.content || '/').replace(/\/?$/, '/');

    function apiUrl(path) {
        return baseUrl + path.replace(/^\//, '');
    }

    function toggleSqmFields() {
        const typeSelect = document.getElementById('stall_type');
        const sqmGroup = document.getElementById('sqm_group');
        const floorGroup = document.getElementById('floor_group');
        if (!typeSelect || !sqmGroup) return;

        const isInside = typeSelect.value === 'inside';
        const isOutside = typeSelect.value === 'outside';
        const needsSqm = isInside || isOutside;

        sqmGroup.style.display = needsSqm ? '' : 'none';
        if (floorGroup) floorGroup.style.display = isInside ? '' : 'none';

        const sqmInput = document.getElementById('sqm');
        if (sqmInput) sqmInput.required = needsSqm;
    }

    function filterSelectOptions(selectEl, query) {
        if (!selectEl) return;
        const q = query.toLowerCase();
        Array.from(selectEl.options).forEach((opt, idx) => {
            if (idx === 0) return;
            const text = opt.textContent.toLowerCase();
            opt.hidden = q !== '' && !text.includes(q);
        });
    }

    function suggestPeriodDates() {
        const paymentType = document.getElementById('payment_type');
        const periodStart = document.getElementById('period_start');
        const periodEnd = document.getElementById('period_end');
        if (!paymentType || !periodStart || !periodEnd) return;

        const today = new Date();
        const fmt = (d) => d.toISOString().split('T')[0];

        if (paymentType.value === 'daily') {
            periodStart.value = fmt(today);
            periodEnd.value = fmt(today);
        } else if (paymentType.value === 'monthly') {
            const first = new Date(today.getFullYear(), today.getMonth(), 1);
            const last = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            periodStart.value = fmt(first);
            periodEnd.value = fmt(last);
        }
    }

    async function computePaymentAmount() {
        const stallType = document.getElementById('stall_type_val')?.value;
        const paymentType = document.getElementById('payment_type')?.value;
        const sqm = document.getElementById('sqm_val')?.value || 0;
        const computedEl = document.getElementById('computed_amount');
        const rateUsedEl = document.getElementById('rate_used');
        const amountPaidEl = document.getElementById('amount_paid');

        if (!stallType || !paymentType || !computedEl) return;

        try {
            const params = new URLSearchParams({ stall_type: stallType, payment_type: paymentType, sqm });
            const res = await fetch(apiUrl('payments/ajax/compute?' + params.toString()));
            const data = await res.json();
            if (data.computed_amount !== undefined) {
                computedEl.value = parseFloat(data.computed_amount).toFixed(2);
                if (rateUsedEl) rateUsedEl.value = data.rate_used;
                if (amountPaidEl && (amountPaidEl.hasAttribute('readonly') || !amountPaidEl.dataset.touched)) {
                    amountPaidEl.value = parseFloat(data.computed_amount).toFixed(2);
                }
                checkUnderpayment();
            }
        } catch (e) {
            console.warn('Compute failed', e);
        }
    }

    function checkUnderpayment() {
        const computed = parseFloat(document.getElementById('computed_amount')?.value || 0);
        const paid = parseFloat(document.getElementById('amount_paid')?.value || 0);
        const warn = document.getElementById('underpayment_warning');
        if (!warn) return;
        if (paid > 0 && paid < computed) {
            warn.classList.remove('d-none');
        } else {
            warn.classList.add('d-none');
        }
    }

    async function loadVendorStalls(vendorId) {
        const stallSelect = document.getElementById('stall_id');
        const vendorTypeBadge = document.getElementById('vendor_type_badge');
        const stallSection = document.getElementById('stall_section');
        if (!vendorId || !stallSelect) return;

        try {
            const res = await fetch(apiUrl('payments/ajax/vendor/' + vendorId));
            const data = await res.json();
            if (data.error) return;

            if (vendorTypeBadge) {
                vendorTypeBadge.textContent = data.vendor.type.toUpperCase();
                vendorTypeBadge.className = 'badge badge-' + data.vendor.type;
            }

            stallSelect.innerHTML = '<option value="">— Select stall —</option>';
            if (data.vendor.type === 'ambulant') {
                if (stallSection) stallSection.style.display = 'none';
                document.getElementById('stall_type_val').value = 'ambulant';
                document.getElementById('sqm_val').value = 0;
                const pt = document.getElementById('payment_type');
                if (pt) { pt.value = 'daily'; pt.disabled = true; }
                computePaymentAmount();
                suggestPeriodDates();
                return;
            }

            if (stallSection) stallSection.style.display = '';
            const pt = document.getElementById('payment_type');
            if (pt) {
                if (data.vendor.type === 'inside' || data.vendor.type === 'outside') {
                    pt.value = 'monthly';
                    pt.disabled = true;
                } else {
                    pt.disabled = false;
                }
            }

            data.stalls.forEach((s) => {
                const opt = document.createElement('option');
                opt.value = s.stall_id;
                const sqmLabel = s.sqm ? s.sqm + ' sqm' : s.stall_type;
                opt.textContent = s.stall_code + ' — ' + s.section + ' (' + sqmLabel + ')';
                opt.dataset.type = s.stall_type;
                opt.dataset.sqm = s.sqm || 0;
                stallSelect.appendChild(opt);
            });

            const preStall = document.getElementById('preselect_stall_id')?.value;
            if (preStall) {
                stallSelect.value = preStall;
                onStallSelect();
            }
        } catch (e) {
            console.warn('Vendor load failed', e);
        }
     }
 
     function onStallSelect() {
        const stallSelect = document.getElementById('stall_id');
        const opt = stallSelect?.selectedOptions[0];
        if (!opt || !opt.value) return;
        document.getElementById('stall_type_val').value = opt.dataset.type || '';
        document.getElementById('sqm_val').value = opt.dataset.sqm || 0;

        const pt = document.getElementById('payment_type');
        if (pt) {
            if (opt.dataset.type === 'inside' || opt.dataset.type === 'outside') {
                pt.value = 'monthly';
                pt.disabled = true;
            } else {
                pt.disabled = false;
            }
        }

        computePaymentAmount();
        suggestPeriodDates();
     }

    function filterAssignmentStalls() {
        const vendorSelect = document.getElementById('assignment_vendor_id');
        const stallSelect = document.getElementById('assignment_stall_id');
        if (!vendorSelect || !stallSelect) return;

        const selected = vendorSelect.selectedOptions[0];
        const vendorType = selected?.dataset?.type || '';
        Array.from(stallSelect.options).forEach((opt, idx) => {
            if (idx === 0) return;
            const stallType = opt.dataset?.type || '';
            opt.hidden = vendorType !== '' && stallType !== vendorType;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function () {
                const pt = document.getElementById('payment_type');
                if (pt) pt.disabled = false;
            });
        }

        toggleSqmFields();
        const typeSelect = document.getElementById('stall_type');
        if (typeSelect) typeSelect.addEventListener('change', toggleSqmFields);

        const paymentType = document.getElementById('payment_type');
        if (paymentType) {
            paymentType.addEventListener('change', function () {
                suggestPeriodDates();
                computePaymentAmount();
            });
        }

        const vendorSelect = document.getElementById('vendor_id');
        const vendorSearch = document.getElementById('vendor_search');
        if (vendorSearch && vendorSelect) {
            vendorSearch.addEventListener('input', function () {
                filterSelectOptions(vendorSelect, this.value);
            });
        }
        if (vendorSelect) {
            vendorSelect.addEventListener('change', function () {
                loadVendorStalls(this.value);
            });
            if (vendorSelect.value) loadVendorStalls(vendorSelect.value);
        }

        const stallSelect = document.getElementById('stall_id');
        if (stallSelect) stallSelect.addEventListener('change', onStallSelect);

        const amountPaid = document.getElementById('amount_paid');
        if (amountPaid) {
            amountPaid.addEventListener('input', function () {
                this.dataset.touched = '1';
                checkUnderpayment();
            });

            const toggleBtn = document.getElementById('toggle_amount_paid');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const isReadonly = amountPaid.hasAttribute('readonly');
                    if (isReadonly) {
                        amountPaid.removeAttribute('readonly');
                        amountPaid.focus();
                        toggleBtn.innerHTML = '<i class="fa-solid fa-lock-open text-warning"></i>';
                        toggleBtn.classList.remove('btn-outline-secondary');
                        toggleBtn.classList.add('btn-warning');
                    } else {
                        amountPaid.setAttribute('readonly', 'readonly');
                        const computed = document.getElementById('computed_amount')?.value || 0;
                        amountPaid.value = computed;
                        amountPaid.dataset.touched = '';
                        toggleBtn.innerHTML = '<i class="fa-solid fa-lock"></i>';
                        toggleBtn.classList.remove('btn-warning');
                        toggleBtn.classList.add('btn-outline-secondary');
                        checkUnderpayment();
                    }
                });
            }
        }

        suggestPeriodDates();

        document.querySelectorAll('[data-confirm]').forEach((el) => {
            el.addEventListener('click', function (e) {
                if (!confirm(this.dataset.confirm)) e.preventDefault();
            });
        });

        const rateInputs = document.querySelectorAll('[data-rate-preview]');
        if (rateInputs.length) {
            const updatePreview = () => {
                const sqm = 2.5;
                const inside = parseFloat(document.getElementById('inside_rate_per_sqm')?.value || 45);
                const insideDaily = (sqm * inside).toFixed(2);
                const insideMonthly = (sqm * inside * 30).toFixed(2);

                const outside = parseFloat(document.getElementById('outside_monthly_rate')?.value || 50);
                const outsideDaily = (sqm * outside).toFixed(2);
                const outsideMonthly = (sqm * outside * 30).toFixed(2);

                const preview = document.getElementById('rate_preview');
                if (preview) {
                    preview.innerHTML = 'A ' + sqm + ' sqm inside stall pays ₱' + insideDaily + '/day (₱' + insideMonthly + '/month) (' + sqm + ' sqm × ₱' + inside.toFixed(2) + '/sqm daily)<br>' +
                                      'A ' + sqm + ' sqm outside stall pays ₱' + outsideDaily + '/day (₱' + outsideMonthly + '/month) (' + sqm + ' sqm × ₱' + outside.toFixed(2) + '/sqm daily)';
                }
            };
            rateInputs.forEach((inp) => inp.addEventListener('input', updatePreview));
            updatePreview();
        }

        const assignVendor = document.getElementById('assignment_vendor_id');
        if (assignVendor) {
            assignVendor.addEventListener('change', filterAssignmentStalls);
            filterAssignmentStalls();
        }
    });
})();
