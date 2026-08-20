<x-app-layout>
    <x-slot name="header">
        E-KTA Digital
    </x-slot>

    <style data-testid="ekta-print-styles">
        @media print {
            @page {
                margin: 8mm;
                size: A4 portrait;
            }

            body {
                background: #ffffff !important;
                padding-bottom: 0 !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
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
                margin: 0 !important;
                padding: 0 !important;
            }

            .ekta-page > div {
                margin: 0 auto !important;
                max-width: 520px !important;
                padding: 0 !important;
                width: 100%;
            }

            .ekta-flip-wrapper {
                display: block !important;
                margin: 0 auto !important;
                max-width: 520px !important;
                perspective: none !important;
                width: 100% !important;
            }

            .ekta-flip-card {
                display: block !important;
                height: auto !important;
                transform: none !important;
                width: 100% !important;
            }

            .ekta-flip-front,
            .ekta-flip-back {
                backface-visibility: visible !important;
                display: block !important;
                margin: 0 auto 16px !important;
                max-width: 520px !important;
                position: static !important;
                transform: none !important;
                visibility: visible !important;
                width: 100% !important;
            }

            .ekta-card,
            .ekta-card-back {
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
        }

        .ekta-flip-wrapper { max-width: 520px; margin: 0 auto 1.5rem; perspective: 1000px; }
        .ekta-flip-card { position: relative; transform-style: preserve-3d; transition: transform .6s ease; }
        .ekta-flip-card.is-flipped { transform: rotateY(180deg); }
        .ekta-flip-front, .ekta-flip-back { backface-visibility: hidden; width: 100%; }
        .ekta-flip-back { left: 0; position: absolute; top: 0; transform: rotateY(180deg); }
    </style>

    <div class="row justify-content-center ekta-page">
        <div class="col-12 col-md-10 col-lg-8 col-xl-7">

            <div class="mb-4 text-center ekta-print-hide">
                <p class="text-muted small">Kartu Tanda Anggota Digital Anda</p>
            </div>

            <div class="ekta-flip-wrapper" data-testid="ekta-flip-container">
                <div class="ekta-flip-card" id="ektaCardFlipper" role="button" tabindex="0" aria-label="Balik Kartu E-KTA">
                    <div class="ekta-flip-front" data-testid="ekta-front-side">
                <x-ekta-card
                    :anggota="$anggota"
                    :role-label="$roleLabel"
                    :photo-src="$photoSrc"
                    :logo-src="$logoSrc"
                />
                    </div>
                    <div class="ekta-flip-back" data-testid="ekta-back-side">
                        <x-ekta-card-back :anggota="$anggota" :role-label="$roleLabel" :qr-code-src="$qrCodeSrc" :logo-src="$logoSrc" />
                    </div>
                </div>
            </div>

            <div class="text-center mb-4 ekta-print-hide"><button type="button" class="btn btn-sm btn-outline-secondary" id="btnFlipCard">Balik Sisi Kartu</button></div>

            <div class="d-grid ekta-print-hide">
                <button onclick="window.print()" class="btn btn-primary btn-ui py-3 ekta-print-button">
                    <i class="bi bi-printer me-2"></i> Cetak Kartu
                </button>
            </div>

            <div class="mt-4 p-3 bg-light rounded-3 ekta-print-hide">
                <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle me-1"></i> Informasi Kartu</h6>
                <p class="text-muted" style="font-size: 0.75rem; line-height: 1.4;">E-KTA ini adalah identitas resmi anggota Ikatan Mahasiswa Muhammadiyah. Gunakan kartu ini untuk keperluan verifikasi pada kegiatan resmi organisasi.</p>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const card = document.getElementById('ektaCardFlipper');
            const toggle = () => card?.classList.toggle('is-flipped');
            card?.addEventListener('click', toggle);
            card?.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggle(); }
            });
            document.getElementById('btnFlipCard')?.addEventListener('click', toggle);
        });
    </script>
</x-app-layout>
