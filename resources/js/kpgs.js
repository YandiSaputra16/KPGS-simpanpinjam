document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            const message = element.dataset.confirm;
            if (message && !window.confirm(message)) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-confirm-submit]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirmSubmit;
            if (message && !window.confirm(message)) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-auto-hide]').forEach((element) => {
        const delay = Number(element.dataset.autoHide || 2000);
        window.setTimeout(() => element.remove(), delay);
    });

    document.querySelectorAll('[data-progress]').forEach((element) => {
        const value = Number(element.dataset.progress || 0);
        element.style.setProperty('--kpgs-progress', `${Math.max(0, Math.min(100, value))}%`);
    });

    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((button) => {
        button.addEventListener('click', () => {
            if (window.bootstrap?.Dropdown) window.bootstrap.Dropdown.getOrCreateInstance(button);
        });
    });

    const loanForm = document.querySelector('[data-kpgs-loan-form]');
    if (loanForm) setupLoanForm(loanForm);

    document.querySelectorAll('[data-kpgs-installment-page]').forEach((installmentPage) => {
        setupInstallmentPage(installmentPage);
    });

    document.querySelectorAll('[data-kpgs-modal-auto-show="true"]').forEach((modal) => {
        if (window.bootstrap?.Modal) window.bootstrap.Modal.getOrCreateInstance(modal).show();
    });

    document.querySelectorAll('[data-kpgs-modal-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.kpgsModalOpen);
            if (target && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(target).show();
            }
        });
    });
});

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', maximumFractionDigits: 0
    }).format(Number(value || 0));
}

function setupLoanForm(root) {
    const paketSelect = root.querySelector('#master_pinjaman_id');
    const jumlahInput = root.querySelector('#jumlah_pinjaman');
    const tenorInput = root.querySelector('#tenor');
    const informasiPaket = root.querySelector('#informasiPaket');
    const infoPlafon = root.querySelector('#infoPlafon');
    const infoTenor = root.querySelector('#infoTenor');
    const infoBunga = root.querySelector('#infoBunga');
    const infoJumlah = root.querySelector('#infoJumlah');
    const infoTenorInput = root.querySelector('#infoTenorInput');
    const estimasiPinjaman = root.querySelector('#estimasiPinjaman');
    const estimasiBunga = root.querySelector('#estimasiBunga');
    const estimasiAngsuran = root.querySelector('#estimasiAngsuran');
    const estimasiTotal = root.querySelector('#estimasiTotal');
    if (!paketSelect || !jumlahInput || !tenorInput) return;

    const getPaket = () => {
        const option = paketSelect.options[paketSelect.selectedIndex];
        if (!option || !option.value) return null;
        return {
            min: Number(option.dataset.min || 0), max: Number(option.dataset.max || 0),
            tenorMin: Number(option.dataset.tenorMin || 0), tenorMax: Number(option.dataset.tenorMax || 0),
            bunga: Number(option.dataset.bunga || 0)
        };
    };

    const updateEstimasi = () => {
        const paket = getPaket();
        if (!paket) { estimasiPinjaman?.classList.add('d-none'); return; }
        const jumlah = Number(jumlahInput.value || 0), tenor = Number(tenorInput.value || 0);
        if (jumlah <= 0 || tenor <= 0) { estimasiPinjaman?.classList.add('d-none'); return; }
        const bunga = jumlah * (paket.bunga / 100);
        const angsuran = (jumlah / tenor) + bunga;
        const total = jumlah + (bunga * tenor);
        estimasiPinjaman?.classList.remove('d-none');
        if (estimasiBunga) estimasiBunga.textContent = formatRupiah(bunga);
        if (estimasiAngsuran) estimasiAngsuran.textContent = formatRupiah(angsuran);
        if (estimasiTotal) estimasiTotal.textContent = formatRupiah(total);
    };

    const updatePaketInfo = () => {
        const paket = getPaket();
        if (!paket) {
            informasiPaket?.classList.add('d-none');
            if (infoJumlah) infoJumlah.textContent = 'Pilih paket terlebih dahulu untuk melihat batas nominal.';
            if (infoTenorInput) infoTenorInput.textContent = 'Pilih paket terlebih dahulu untuk melihat pilihan tenor.';
            estimasiPinjaman?.classList.add('d-none');
            jumlahInput.removeAttribute('min'); jumlahInput.removeAttribute('max');
            tenorInput.removeAttribute('min'); tenorInput.removeAttribute('max');
            return;
        }
        informasiPaket?.classList.remove('d-none');
        if (infoPlafon) infoPlafon.textContent = `${formatRupiah(paket.min)} s/d ${formatRupiah(paket.max)}`;
        if (infoTenor) infoTenor.textContent = `${paket.tenorMin} - ${paket.tenorMax} bulan`;
        if (infoBunga) infoBunga.textContent = `${paket.bunga.toFixed(2)}% per bulan`;
        if (infoJumlah) infoJumlah.textContent = `Nominal harus berada antara ${formatRupiah(paket.min)} dan ${formatRupiah(paket.max)}.`;
        if (infoTenorInput) infoTenorInput.textContent = `Tenor harus berada antara ${paket.tenorMin} dan ${paket.tenorMax} bulan.`;
        jumlahInput.min = paket.min; jumlahInput.max = paket.max;
        tenorInput.min = paket.tenorMin; tenorInput.max = paket.tenorMax;
        updateEstimasi();
    };

    paketSelect.addEventListener('change', updatePaketInfo);
    jumlahInput.addEventListener('input', updateEstimasi);
    tenorInput.addEventListener('input', updateEstimasi);
    updatePaketInfo();
}

function setupInstallmentPage(root) {
    const checkboxes = Array.from(root.querySelectorAll('.angsuran-checkbox'));
    const totalElement = root.querySelector('[data-total-pembayaran]');
    const submitButton = root.querySelector('[data-btn-ajukan-pembayaran]');
    const form = root.querySelector('[data-form-pembayaran]');

    if (!checkboxes.length || !form) return;

    const updateTotal = () => {
        const selectedById = new Map();

        checkboxes.forEach((checkbox) => {
            if (!checkbox.checked) return;

            if (!selectedById.has(checkbox.value)) {
                selectedById.set(
                    checkbox.value,
                    Number(checkbox.dataset.total || 0)
                );
            }
        });

        const total = Array.from(selectedById.values())
            .reduce((sum, nominal) => sum + nominal, 0);

        if (totalElement) {
            totalElement.textContent = formatRupiah(total);
        }

        if (submitButton) {
            submitButton.disabled = selectedById.size === 0;
        }
    };

    const setSelectionThrough = (targetOrder) => {
        checkboxes.forEach((checkbox) => {
            if (Number(checkbox.dataset.angsuranKe) <= targetOrder) {
                checkbox.checked = true;
            }
        });
        updateTotal();
    };

    const clearSelectionFrom = (targetOrder) => {
        checkboxes.forEach((checkbox) => {
            if (Number(checkbox.dataset.angsuranKe) >= targetOrder) {
                checkbox.checked = false;
            }
        });
        updateTotal();
    };

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const installmentOrder = Number(checkbox.dataset.angsuranKe || 0);

            if (checkbox.checked) {
                setSelectionThrough(installmentOrder);
            } else {
                clearSelectionFrom(installmentOrder);
            }
        });
    });

    form.addEventListener('submit', (event) => {
        const selectedById = new Map();

        checkboxes.forEach((checkbox) => {
            if (!checkbox.checked || selectedById.has(checkbox.value)) return;

            selectedById.set(checkbox.value, {
                id: checkbox.value,
                order: Number(checkbox.dataset.angsuranKe || 0),
            });
        });

        const selected = Array.from(selectedById.values())
            .sort((a, b) => a.order - b.order);

        if (!selected.length) {
            event.preventDefault();
            window.alert('Silakan pilih minimal satu angsuran.');
            return;
        }

        form.querySelectorAll('input[data-generated-angsuran="true"]')
            .forEach((input) => input.remove());

        selected.forEach((item) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'angsuran_ids[]';
            input.value = item.id;
            input.dataset.generatedAngsuran = 'true';
            form.appendChild(input);
        });
    });

    const setupToggle = (buttonId, selector) => {
        const button = root.querySelector(`#${buttonId}`);
        if (!button) return;

        const items = root.querySelectorAll(selector);
        if (!items.length) return;

        let expanded = false;
        button.addEventListener('click', () => {
            expanded = !expanded;
            items.forEach((item) => item.classList.toggle('d-none', !expanded));
            button.innerHTML = expanded
                ? '<i class="bi bi-chevron-up me-1"></i> Sembunyikan'
                : '<i class="bi bi-chevron-down me-1"></i> Lihat Lainnya';
        });
    };

    setupToggle('toggleAngsuran', '.angsuran-extra');
    setupToggle('toggleAngsuranMobile', '.angsuran-mobile-item.d-none');
    updateTotal();
}

// Cetak halaman laporan tanpa mencetak navigasi dan kontrol filter.
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-report-print]');
    if (!button) return;
    window.print();
});
