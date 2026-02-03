// ===========================
// Main JavaScript for ICOP Website
// ===========================

document.addEventListener('DOMContentLoaded', function () {
    // Initialize all features
    initMobileMenu();
    initFontSizeControls();
    initHeroSlider();
    initScrollTop();
    initStickyNav();
    initSmoothScroll();
});

// ===========================
// Mobile Menu Toggle
// ===========================
function initMobileMenu() {
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');

            // Toggle icon
            const icon = this.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.main-nav')) {
                navMenu.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
}

// ===========================
// Font Size Controls
// ===========================
function initFontSizeControls() {
    const decreaseBtn = document.getElementById('font-decrease');
    const normalBtn = document.getElementById('font-normal');
    const increaseBtn = document.getElementById('font-increase');

    let currentSize = 16; // Default font size in pixels

    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', function () {
            if (currentSize > 14) {
                currentSize -= 2;
                document.documentElement.style.fontSize = currentSize + 'px';
                localStorage.setItem('fontSize', currentSize);
            }
        });
    }

    if (normalBtn) {
        normalBtn.addEventListener('click', function () {
            currentSize = 16;
            document.documentElement.style.fontSize = currentSize + 'px';
            localStorage.setItem('fontSize', currentSize);
        });
    }

    if (increaseBtn) {
        increaseBtn.addEventListener('click', function () {
            if (currentSize < 20) {
                currentSize += 2;
                document.documentElement.style.fontSize = currentSize + 'px';
                localStorage.setItem('fontSize', currentSize);
            }
        });
    }

    // Load saved font size
    const savedSize = localStorage.getItem('fontSize');
    if (savedSize) {
        currentSize = parseInt(savedSize);
        document.documentElement.style.fontSize = currentSize + 'px';
    }
}

// ===========================
// Hero Slider
// ===========================
function initHeroSlider() {
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
    const dotsContainer = document.querySelector('.slider-dots');

    if (!slides.length) return;

    let currentSlide = 0;
    let slideInterval;

    // Create dots
    slides.forEach((slide, index) => {
        const dot = document.createElement('div');
        dot.classList.add('slider-dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });

    const dots = document.querySelectorAll('.slider-dot');

    function showSlide(n) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        currentSlide = (n + slides.length) % slides.length;

        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    function goToSlide(n) {
        showSlide(n);
        resetInterval();
    }

    function startInterval() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    function resetInterval() {
        clearInterval(slideInterval);
        startInterval();
    }

    // Event listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetInterval();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetInterval();
        });
    }

    // Pause on hover
    const sliderContainer = document.querySelector('.hero-slider');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
        sliderContainer.addEventListener('mouseleave', startInterval);
    }

    // Start auto-play
    startInterval();
}

// ===========================
// Scroll to Top Button
// ===========================
function initScrollTop() {
    const scrollTopBtn = document.getElementById('scrollTop');

    if (scrollTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// ===========================
// Sticky Navigation
// ===========================
function initStickyNav() {
    const nav = document.getElementById('mainNav');
    if (!nav) return;

    const navOffset = nav.offsetTop;

    window.addEventListener('scroll', function () {
        if (window.pageYOffset >= navOffset) {
            nav.classList.add('sticky');
        } else {
            nav.classList.remove('sticky');
        }
    });
}

// ===========================
// Smooth Scroll for Anchor Links
// ===========================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Skip if href is just "#"
            if (href === '#') {
                e.preventDefault();
                return;
            }

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const navHeight = document.querySelector('.main-nav').offsetHeight;
                const targetPosition = target.offsetTop - navHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// ===========================
// Search Functionality
// ===========================
function initSearch() {
    const searchForm = document.querySelector('.search-box');
    const searchInput = searchForm?.querySelector('input');
    const searchBtn = searchForm?.querySelector('button');

    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const query = searchInput.value.trim();

            if (query) {
                // Implement search functionality
                // This is a placeholder - implement actual search
                window.location.href = `search.php?q=${encodeURIComponent(query)}`;
            }
        });

        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchBtn.click();
            }
        });
    }
}

// ===========================
// Dropdown Menu Accessibility
// ===========================
function initDropdownAccessibility() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('a');
        const menu = dropdown.querySelector('.dropdown-menu');

        toggle.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                menu.classList.toggle('show');
            }
        });
    });
}

// ===========================
// Gallery Lightbox (if needed)
// ===========================
function initGalleryLightbox() {
    const galleryItems = document.querySelectorAll('.gallery-item');

    galleryItems.forEach(item => {
        item.addEventListener('click', function () {
            const img = this.querySelector('img');
            if (img) {
                // Create lightbox
                const lightbox = document.createElement('div');
                lightbox.classList.add('lightbox');
                lightbox.innerHTML = `
                    <div class="lightbox-content">
                        <span class="lightbox-close">&times;</span>
                        <img src="${img.src}" alt="${img.alt}">
                    </div>
                `;

                document.body.appendChild(lightbox);
                document.body.style.overflow = 'hidden';

                // Close lightbox
                const closeBtn = lightbox.querySelector('.lightbox-close');
                closeBtn.addEventListener('click', closeLightbox);
                lightbox.addEventListener('click', function (e) {
                    if (e.target === lightbox) closeLightbox();
                });

                function closeLightbox() {
                    lightbox.remove();
                    document.body.style.overflow = 'auto';
                }

                // Keyboard navigation
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') closeLightbox();
                });
            }
        });
    });
}

// Add lightbox styles dynamically
const lightboxStyles = `
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }
    
    .lightbox-content img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
    }
    
    .lightbox-close {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 40px;
        cursor: pointer;
        transition: 0.3s;
    }
    
    .lightbox-close:hover {
        color: #FF6B35;
    }
`;

// Inject lightbox styles
const styleSheet = document.createElement('style');
styleSheet.textContent = lightboxStyles;
document.head.appendChild(styleSheet);

// ===========================
// Initialize All
// ===========================
window.addEventListener('load', function () {
    initSearch();
    initDropdownAccessibility();
    initGalleryLightbox();
});

// ===========================
// Performance: Lazy Loading Images
// ===========================
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}
