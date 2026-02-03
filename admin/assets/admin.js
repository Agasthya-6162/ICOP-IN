// Admin Panel JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Auto-dismiss success alerts only
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Image preview
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    let preview = document.getElementById('imagePreview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'imagePreview';
                        preview.style.maxWidth = '200px';
                        preview.style.marginTop = '10px';
                        preview.style.borderRadius = '5px';
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Prevent double submission
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (this.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                // Add loading state
                const originalText = submitBtn.innerHTML || submitBtn.value;
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = originalText;
                
                if (submitBtn.tagName === 'BUTTON') {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                } else {
                    submitBtn.value = 'Processing...';
                }
            }
            
            this.dataset.submitting = 'true';
        });
    });
});
