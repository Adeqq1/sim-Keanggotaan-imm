import './bootstrap.js';
import * as bootstrap from 'bootstrap';

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('[data-auto-submit-sort]').forEach((form) => {
    form.querySelectorAll('select[name="sort"], select[name="direction"]').forEach((select) => {
        select.addEventListener('change', () => form.requestSubmit());
    });
});

const generationStatus = document.querySelector('[data-certificate-generation]');
if (generationStatus) {
    const statusUrl = generationStatus.dataset.statusUrl;
    const title = generationStatus.querySelector('[data-generation-title]');
    const message = generationStatus.querySelector('[data-generation-message]');
    const progress = generationStatus.querySelector('[data-generation-progress]');
    const count = generationStatus.querySelector('[data-generation-count]');
    let timerId;
    const startedAt = Date.now();
    const maxPollingMs = 5 * 60 * 1000;

    const poll = async () => {
        if (Date.now() - startedAt > maxPollingMs) {
            title.textContent = 'Proses belum selesai';
            message.textContent = 'Worker queue belum menyelesaikan proses. Silakan muat ulang halaman nanti.';
            generationStatus.classList.add('is-warning');
            return;
        }

        try {
            const response = await fetch(statusUrl, { cache: 'no-store', headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('generation status request failed');
            const status = await response.json();
            const percentage = Number(status.progress || 0);
            progress.style.width = `${percentage}%`;
            progress.setAttribute('aria-valuenow', percentage);
            count.textContent = `${percentage}%`;
            message.textContent = `${status.processed} dari ${status.total} sertifikat selesai diproses.`;

            if (status.finished) {
                title.textContent = status.failed > 0 ? 'Pembuatan selesai dengan catatan' : 'Sertifikat selesai dibuat';
                message.textContent = status.failed > 0
                    ? `${status.processed} berhasil dibuat, ${status.failed} gagal diproses.`
                    : `${status.processed} sertifikat berhasil dibuat.`;
                generationStatus.classList.add(status.failed > 0 ? 'is-warning' : 'is-complete');
                window.clearTimeout(timerId);
                 window.setTimeout(() => {
                     const url = new URL(window.location.href);
                     url.searchParams.delete('generation');
                     window.location.replace(url.toString());
                 }, 1200);
                return;
            }

            timerId = window.setTimeout(poll, 2000);
        } catch (error) {
            title.textContent = 'Status belum tersedia';
            message.textContent = 'Akan mencoba memeriksa kembali secara otomatis.';
            timerId = window.setTimeout(poll, 5000);
        }
    };

    const resumePolling = () => {
        if (document.visibilityState === 'visible') poll();
    };
    document.addEventListener('visibilitychange', resumePolling);
    window.addEventListener('pagehide', () => window.clearTimeout(timerId), { once: true });
    poll();
}

document.querySelectorAll('[data-profile-field]').forEach((field) => {
    const input = field.querySelector('input, textarea, select');
    const editButton = field.querySelector('[data-profile-edit]');

    if (!input || !editButton) return;

    const initialValue = input.value;

    if (input.classList.contains('is-invalid')) {
        input.readOnly = false;
        field.classList.add('is-editing');
        editButton.textContent = 'Batal';
    }

    editButton.addEventListener('click', () => {
        const isEditing = !input.readOnly;

        if (isEditing) {
            input.value = initialValue;
            input.readOnly = true;
            field.classList.remove('is-editing');
            editButton.textContent = 'Edit';
            editButton.setAttribute('aria-label', `Edit ${input.labels?.[0]?.textContent || 'field'}`);
            return;
        }

        input.readOnly = false;
        field.classList.add('is-editing');
        editButton.textContent = 'Batal';
        editButton.setAttribute('aria-label', `Batalkan edit ${input.labels?.[0]?.textContent || 'field'}`);
        input.focus();
    });
});

document.querySelectorAll('[data-profile-photo-edit]').forEach((editButton) => {
    const input = document.querySelector(`#${editButton.getAttribute('aria-controls')}`);
    const avatar = editButton.closest('.profile-summary-avatar-wrap')?.querySelector('.profile-summary-avatar');
    const originalAvatar = avatar?.innerHTML;

    if (!input || !avatar) return;

    editButton.addEventListener('click', () => {
        if (!input.disabled) {
            input.value = '';
            input.disabled = true;
            avatar.innerHTML = originalAvatar;
            editButton.innerHTML = '<i class="bi bi-pencil"></i><span>Edit Foto</span>';
            editButton.setAttribute('aria-label', 'Edit Foto Profil');
            return;
        }

        input.disabled = false;
        input.click();
    });

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) return;

        const previewUrl = URL.createObjectURL(file);
        avatar.innerHTML = `<img src="${previewUrl}" alt="Pratinjau foto profil" width="112" height="112">`;
        editButton.innerHTML = '<i class="bi bi-x-lg"></i><span>Batal</span>';
        editButton.setAttribute('aria-label', 'Batalkan edit Foto Profil');
    });
});

document.querySelectorAll('[data-thumbnail-input]').forEach((input) => {
    const form = input.form;
    const preview = form?.querySelector('[data-thumbnail-preview]');
    const image = preview?.querySelector('img');
    const selected = form?.querySelector('[data-thumbnail-selected]');
    let previewUrl;

    if (!preview || !image || !selected) return;

    input.addEventListener('change', () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl);

        const file = input.files?.[0];
        selected.value = file ? '1' : '0';
        if (!file) return;

        previewUrl = URL.createObjectURL(file);
        image.src = previewUrl;
        preview.hidden = false;
    });
});

document.querySelectorAll('[data-activity-schedule]').forEach((schedule) => {
    const dateInput = schedule.querySelector('[data-schedule-date]');
    const timeInput = schedule.querySelector('[data-schedule-time]');
    const summary = schedule.querySelector('[data-schedule-summary]');

    if (!dateInput || !timeInput || !summary) return;

    const syncSchedule = () => {
        if (!dateInput.value || !timeInput.value) {
            summary.textContent = 'Pilih tanggal dan waktu mulai kegiatan.';
            return;
        }

        const date = new Date(`${dateInput.value}T${timeInput.value}:00`);
        const formattedDate = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
        }).format(date);
        summary.textContent = `Jadwal kegiatan: ${formattedDate} pukul ${timeInput.value.replace(':', '.')} WIB.`;
    };

    const syncTimeMinimum = () => {
        const today = new Date().toISOString().slice(0, 10);
        timeInput.min = dateInput.value === today ? new Date().toTimeString().slice(0, 5) : '';
        syncSchedule();
    };

    dateInput.addEventListener('change', syncTimeMinimum);
    timeInput.addEventListener('change', syncSchedule);
    syncTimeMinimum();
});

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
