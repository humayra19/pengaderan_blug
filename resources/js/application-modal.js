/**
 * application-modal.js — US-11
 *
 * Mengisi modal detail pengajuan dari atribut data-* tombol "Lihat".
 * Memakai textContent (BUKAN innerHTML) agar aman dari XSS.
 */

/** Peta status → label teks + kelas CSS badge. */
const STATUS_MAP = {
    pending:  { label: 'Menunggu', cls: 'status-pending' },
    approved: { label: 'Diterima', cls: 'status-approved' },
    rejected: { label: 'Ditolak',  cls: 'status-rejected' },
};

/**
 * Isi elemen berteks dengan nilai; bila kosong tampilkan fallback "—".
 * @param {HTMLElement|null} el
 * @param {string|undefined|null} value
 */
function setText(el, value) {
    if (!el) return;
    el.textContent = (value !== null && value !== undefined && value !== '')
        ? value
        : '—';
}

/**
 * Inisialisasi listener modal saat DOM siap.
 * Menggunakan event standar Bootstrap 5 'show.bs.modal'.
 */
function initApplicationModal() {
    const modal = document.getElementById('applicationModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;

        const d = btn.dataset;

        // ---- Data pendaftar ----
        setText(modal.querySelector('[data-modal="name"]'),          d.name);
        setText(modal.querySelector('[data-modal="email"]'),         d.email);
        setText(modal.querySelector('[data-modal="nim"]'),           d.nim);
        setText(modal.querySelector('[data-modal="study-program"]'), d.studyProgram);
        setText(modal.querySelector('[data-modal="batch"]'),         d.batch);
        setText(modal.querySelector('[data-modal="whatsapp"]'),      d.whatsapp);

        // ---- Isi pengajuan ----
        setText(modal.querySelector('[data-modal="interest"]'),    d.interest);
        setText(modal.querySelector('[data-modal="motivation"]'),  d.motivation);
        setText(modal.querySelector('[data-modal="experience"]'),  d.experience);
        setText(modal.querySelector('[data-modal="created-at"]'),  d.createdAt);

        // ---- Status badge ----
        const statusEl  = modal.querySelector('[data-modal="status"]');
        const statusInfo = STATUS_MAP[d.status] ?? { label: d.status || '—', cls: '' };
        if (statusEl) {
            statusEl.textContent = statusInfo.label;
            statusEl.className   = 'status ' + statusInfo.cls;
        }

        // ---- Catatan admin (sembunyikan baris bila kosong) ----
        const noteRow = modal.querySelector('[data-modal-row="admin-note"]');
        const noteEl  = modal.querySelector('[data-modal="admin-note"]');
        const hasNote = Boolean(d.adminNote && d.adminNote.trim() !== '');
        if (noteRow) noteRow.hidden = !hasNote;
        if (noteEl) noteEl.textContent = hasNote ? d.adminNote : '';
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApplicationModal);
} else {
    initApplicationModal();
}
