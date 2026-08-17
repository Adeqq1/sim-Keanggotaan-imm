import './bootstrap.js';
import * as bootstrap from 'bootstrap';

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();

// --- Preview Dokumen Modal ---
document.addEventListener('shown.bs.modal', (event) => {
    if (event.target.id !== 'previewDocumentModal') return;

    const trigger = event.relatedTarget;
    if (!trigger) return;

    const previewUrl = trigger.dataset.previewUrl;
    const downloadUrl = trigger.dataset.downloadUrl;
    const nama = trigger.dataset.nama;

    const modal = event.target;
    const title = modal.querySelector('#previewDocumentTitle');
    const img = modal.querySelector('#previewDocumentImage');
    const placeholder = modal.querySelector('#previewDocumentPlaceholder');
    const downloadBtn = modal.querySelector('#previewDocumentDownload');
    const downloadFallback = modal.querySelector('#previewDocumentDownloadFallback');

    title.textContent = 'Pratinjau — ' + (nama || 'Dokumen');
    img.alt = 'Dokumen identitas ' + (nama || '');
    downloadBtn.href = downloadUrl || '#';
    downloadFallback.href = downloadUrl || '#';
    placeholder.classList.add('d-none');
    img.classList.remove('d-none');
    img.src = previewUrl;
});

document.addEventListener('error', (event) => {
    const img = event.target;
    if (img.id !== 'previewDocumentImage') return;

    img.classList.add('d-none');
    img.src = '';
    const placeholder = img.closest('.modal-body')?.querySelector('#previewDocumentPlaceholder');
    if (placeholder) placeholder.classList.remove('d-none');
}, true);

document.addEventListener('hidden.bs.modal', (event) => {
    if (event.target.id !== 'previewDocumentModal') return;

    const modal = event.target;
    const img = modal.querySelector('#previewDocumentImage');
    const title = modal.querySelector('#previewDocumentTitle');
    const downloadBtn = modal.querySelector('#previewDocumentDownload');
    const downloadFallback = modal.querySelector('#previewDocumentDownloadFallback');

    img.src = '';
    img.alt = '';
    title.textContent = 'Preview Dokumen';
    downloadBtn.href = '#';
    downloadFallback.href = '#';
});

// --- Flash Toast Auto-Dismiss (5 detik) ---
document.querySelectorAll('[data-auto-dismiss-toast]').forEach((toastNode) => {
    let timerId;
    let disposed = false;
    const toast = bootstrap.Toast.getOrCreateInstance(toastNode, { autohide: false });

    const clearTimer = () => {
        if (timerId !== undefined) {
            window.clearTimeout(timerId);
            timerId = undefined;
        }
    };

    const dispose = () => {
        if (disposed) return;
        disposed = true;
        clearTimer();
        toast.dispose();
    };

    toastNode.addEventListener('shown.bs.toast', () => {
        timerId = window.setTimeout(() => toast.hide(), 5000);
    });

    toastNode.addEventListener('hide.bs.toast', clearTimer);
    toastNode.addEventListener('hidden.bs.toast', () => {
        clearTimer();
        dispose();
    });

    window.addEventListener('pagehide', dispose);

    toast.show();
});

// --- Laporan Date Range Validation ---
document.querySelectorAll('[data-date-range-form]').forEach((form) => {
    const startInput = form.querySelector('[data-date-start]');
    const endInput = form.querySelector('[data-date-end]');

    if (!startInput || !endInput) return;

    const syncEndMin = () => {
        endInput.min = startInput.value;
        validateRange();
    };

    const validateRange = () => {
        if (startInput.value && endInput.value && endInput.value < startInput.value) {
            endInput.setCustomValidity('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        } else {
            endInput.setCustomValidity('');
        }
    };

    startInput.addEventListener('change', syncEndMin);
    endInput.addEventListener('change', validateRange);

    syncEndMin();
});
