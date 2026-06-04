document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[action*="/klaim"]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const fileInput = form.querySelector('input[type="file"]');
            if (!fileInput) return;
            const file = fileInput.files[0];
            if (!file) return;

            // Compress only images larger than 500KB
            if (file.type.startsWith('image/') && file.size > 500 * 1024) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengompres...';

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function (event) {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = function () {
                        const maxDim = 1200;
                        let width = img.width;
                        let height = img.height;

                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(function (blob) {
                            const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });

                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);
                            fileInput.files = dataTransfer.files;

                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                            form.submit();
                        }, 'image/jpeg', 0.7);
                    };
                };
            }
        });
    });
});
