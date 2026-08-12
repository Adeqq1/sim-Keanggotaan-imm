<x-app-layout>
    <x-slot name="header">
        E-KTA Digital
    </x-slot>

    <style data-testid="ekta-print-styles">
        @media print {
            @page {
                margin: 0;
            }

            body {
                background: #ffffff !important;
                padding-bottom: 0 !important;
            }

            .sidebar-desktop,
            .desktop-topbar,
            .navbar-header,
            .app-wrapper > nav,
            .app-wrapper > .toast-container,
            .ekta-print-hide {
                display: none !important;
            }

            .app-wrapper {
                margin-left: 0 !important;
            }

            .app-main-content {
                padding: 0 !important;
            }

            .ekta-page {
                display: block !important;
            }

            .ekta-page > div {
                margin: 0 auto !important;
                max-width: 520px;
                width: 100%;
            }

            .ekta-preview-frame {
                margin-bottom: 0 !important;
            }
        }
    </style>

    <div class="row justify-content-center ekta-page">
        <div class="col-12 col-md-10 col-lg-8 col-xl-7">

            <div class="mb-4 text-center ekta-print-hide">
                <p class="text-muted small">Kartu Tanda Anggota Digital Anda</p>
            </div>

            <div class="ekta-preview-frame mb-4">
                <x-ekta-card
                    :anggota="$anggota"
                    :role-label="$roleLabel"
                    :photo-src="$photoSrc"
                    :logo-src="$logoSrc"
                />
            </div>

            <div class="d-grid gap-3 ekta-print-hide">
                <a href="{{ route('kader.ekta.download') }}" class="btn btn-primary btn-ui py-3">
                    <i class="bi bi-download me-2"></i> Unduh KTA (PDF)
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-ui py-3">
                    <i class="bi bi-printer me-2"></i> Cetak Kartu
                </button>
            </div>

            <div class="mt-4 p-3 bg-light rounded-3 ekta-print-hide">
                <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle me-1"></i> Informasi Kartu</h6>
                <p class="text-muted" style="font-size: 0.75rem; line-height: 1.4;">E-KTA ini adalah identitas resmi anggota Ikatan Mahasiswa Muhammadiyah. Gunakan kartu ini untuk keperluan verifikasi pada kegiatan resmi organisasi.</p>
            </div>

        </div>
    </div>
</x-app-layout>
