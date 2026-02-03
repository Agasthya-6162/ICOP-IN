// ===========================
// Scroll-Based Animations
// ===========================
function initScrollAnimations() {
    // Check if Intersection Observer is supported
    if (!('IntersectionObserver' in window)) {
        // If not supported, show all elements immediately
        document.querySelectorAll('[data-animate]').forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'none';
        });
        return;
    }

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const animationType = entry.target.getAttribute('data-animate');

                switch (animationType) {
                    case 'fade-up':
                        entry.target.classList.add('animate-fade-in-up');
                        break;
                    case 'fade':
                        entry.target.classList.add('animate-fade-in');
                        break;
                    case 'slide-left':
                        entry.target.classList.add('animate-slide-left');
                        break;
                    default:
                        entry.target.classList.add('animate-fade-in-up');
                }

                // Unobserve after animation starts
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all elements with data-animate attribute
    document.querySelectorAll('[data-animate]').forEach(section => {
        observer.observe(section);
    });
}

// Initialize animations when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
} else {
    initScrollAnimations();
}
