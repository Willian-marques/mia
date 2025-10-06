// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize all interactive elements
    initMobileMenu();
    initProductCarousel();
    initSmoothScrolling();
    initLoadingAnimations();
    initButtonInteractions();
    initActiveNavHighlight();
    initNavigationFix();

    // Debug: Test logo click
    const logoLink = document.querySelector('.navbar-brand a');
    if (logoLink) {
        logoLink.addEventListener('click', function (e) {
            console.log('Logo clicked!', e.target);
            // Remove this line if you want the default behavior
            // e.preventDefault();
        });
    }
});

// Mobile Menu Toggle
function initMobileMenu() {
    const menuToggle = document.getElementById('navbar-toggle');
    const navMenu = document.getElementById('navbar-nav');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function () {
            const isActive = this.classList.contains('active');

            if (!isActive) {
                // Abrir menu
                navMenu.style.display = 'flex';
                // Force reflow
                navMenu.offsetHeight;
                this.classList.add('active');
                navMenu.classList.add('active');

                // Adicionar classe ao body para efeitos adicionais
                document.body.classList.add('menu-open');
            } else {
                // Fechar menu
                this.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('menu-open');

                // Aguardar animação antes de ocultar
                setTimeout(() => {
                    if (!navMenu.classList.contains('active')) {
                        navMenu.style.display = 'none';
                    }
                }, 400);
            }
        });

        // Close menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                closeMenuSmooth();
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
                closeMenuSmooth();
            }
        });

        // Função para fechamento suave do menu
        function closeMenuSmooth() {
            if (menuToggle.classList.contains('active')) {
                menuToggle.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.classList.remove('menu-open');

                setTimeout(() => {
                    if (!navMenu.classList.contains('active')) {
                        navMenu.style.display = 'none';
                    }
                }, 400);
            }
        }
    }
}

// Product Carousel
function initProductCarousel() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const carousel = document.querySelector('.products-grid-extended');

    if (prevBtn && nextBtn && carousel) {
        const scrollAmount = 356; // width of product card + gap

        prevBtn.addEventListener('click', function () {
            carousel.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });

        nextBtn.addEventListener('click', function () {
            carousel.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });

        // Update button states based on scroll position
        carousel.addEventListener('scroll', function () {
            const isAtStart = carousel.scrollLeft <= 0;
            const isAtEnd = carousel.scrollLeft >= (carousel.scrollWidth - carousel.clientWidth);

            prevBtn.style.opacity = isAtStart ? '0.5' : '1';
            nextBtn.style.opacity = isAtEnd ? '0.5' : '1';
            prevBtn.style.pointerEvents = isAtStart ? 'none' : 'auto';
            nextBtn.style.pointerEvents = isAtEnd ? 'none' : 'auto';
        });

        // Initialize button states
        carousel.dispatchEvent(new Event('scroll'));
    }
}

// Smooth Scrolling for Navigation Links
function initSmoothScrolling() {
    // Add CSS smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';

    // Only apply smooth scroll to navigation menu links and hero buttons, not ALL links
    const navLinks = document.querySelectorAll('.nav-menu a, .hero-buttons a');

    console.log('Found navigation links:', navLinks.length); // Debug

    navLinks.forEach((link, index) => {
        console.log(`Link ${index}:`, link.getAttribute('href')); // Debug

        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Check if it's a same-page anchor link or cross-page link
            if (href.startsWith('#')) {
                // Same page scroll
                e.preventDefault();
                console.log('Same page scroll to:', href); // Debug
                scrollToSection(href);
            } else if (href.includes('#')) {
                // Cross-page navigation with anchor
                const [page, anchor] = href.split('#');
                console.log('Cross-page navigation:', page, anchor); // Debug

                // If we're already on the target page, just scroll
                if (window.location.pathname.includes(page) ||
                    (page === 'index.html' && (window.location.pathname === '/' || window.location.pathname.includes('index.html')))) {
                    e.preventDefault();
                    scrollToSection('#' + anchor);
                } else {
                    // Let the browser navigate normally, scroll will happen on page load
                    console.log('Navigating to different page with anchor');
                }
            }
        });
    });

    // Handle hash in URL on page load (including from other pages)
    window.addEventListener('load', handleHashOnLoad);

    // Also handle hash changes (back/forward button)
    window.addEventListener('hashchange', handleHashChange);

    // Initial check when script loads
    setTimeout(handleHashOnLoad, 100);
}

function scrollToSection(targetId) {
    const targetSection = document.querySelector(targetId);

    console.log('Target section:', targetSection); // Debug

    if (targetSection) {
        const header = document.querySelector('.header');
        const headerHeight = header ? header.offsetHeight : 0;
        const targetPosition = targetSection.offsetTop - headerHeight - 20;

        console.log('Scrolling to position:', targetPosition); // Debug

        // Use scrollIntoView for better cross-browser support
        targetSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

        // Close mobile menu after clicking
        closeMobileMenu();
    } else {
        console.log('Target section not found for:', targetId); // Debug
    }
}

function handleHashOnLoad() {
    if (window.location.hash) {
        console.log('Hash found on page load:', window.location.hash);
        // Small delay to ensure page is fully loaded
        setTimeout(() => {
            scrollToSection(window.location.hash);
        }, 300);
    }
}

function handleHashChange() {
    if (window.location.hash) {
        console.log('Hash changed:', window.location.hash);
        scrollToSection(window.location.hash);
    }
}

function closeMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');
    if (menuToggle && navMenu && menuToggle.classList.contains('active')) {
        menuToggle.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.classList.remove('menu-open');

        setTimeout(() => {
            if (!navMenu.classList.contains('active')) {
                navMenu.style.display = 'none';
            }
        }, 400);
    }
}

// Loading Animations and Intersection Observer
function initLoadingAnimations() {
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.product-card, .category-item, .testimonial-card');
    animateElements.forEach(el => {
        observer.observe(el);
    });
}

// Button Interactions
function initButtonInteractions() {
    // CTA Button
    const ctaButton = document.querySelector('.cta-button');
    if (ctaButton) {
        ctaButton.addEventListener('click', function () {
            // Redirecionar para a página de produtos
            window.location.href = 'produtos.php';
        });
    }

    // Purchase Button
    const purchaseButton = document.querySelector('.purchase-button');
    if (purchaseButton) {
        purchaseButton.addEventListener('click', function () {
            // Add loading state
            this.classList.add('loading');
            this.textContent = 'Carregando...';

            // Simulate purchase process
            setTimeout(() => {
                this.classList.remove('loading');
                this.textContent = 'Compre Já';
                alert('Redirecionando para o sistema de compra...');
            }, 2000);
        });
    }

    // Product Cards Click
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('click', function () {
            const productName = this.querySelector('h3').textContent;
            console.log(`Clicou no produto: ${productName}`);
            // Here you would typically redirect to product page
            // window.location.href = `produto.html?nome=${encodeURIComponent(productName)}`;
        });

        // Add hover effect
        card.addEventListener('mouseenter', function () {
            this.style.cursor = 'pointer';
        });
    });

    // Category Items Click
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => {
        item.addEventListener('click', function () {
            const categoryName = this.querySelector('h3').textContent;
            console.log(`Clicou na categoria: ${categoryName}`);
            // Here you would typically redirect to category page
        });

        item.addEventListener('mouseenter', function () {
            this.style.cursor = 'pointer';
        });
    });
}

// Header Scroll Effect - Only Opacity (Optimized)
const headerScrollHandler = throttle(function () {
    const header = document.querySelector('.header');
    if (header) {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
}, 16); // ~60fps

window.addEventListener('scroll', headerScrollHandler, { passive: true });

// Instagram Gallery Hover Effects
function initInstagramGallery() {
    const galleryImages = document.querySelectorAll('.instagram-gallery img');

    galleryImages.forEach((img, index) => {
        img.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05)';
            this.style.zIndex = '10';
        });

        img.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
            this.style.zIndex = '1';
        });

        img.addEventListener('click', function () {
            // Open Instagram post (would need Instagram API integration)
            console.log(`Clicou na imagem do Instagram ${index + 1}`);
        });
    });
}

// Follow Button
function initFollowButton() {
    const followBtn = document.querySelector('.follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', function () {
            // Open Instagram profile
            window.open('https://instagram.com/mia.mianet', '_blank');
        });
    }
}

// Form Validation (if forms are added later)
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    return isValid;
}

// Utility Functions
function throttle(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize additional features when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    initInstagramGallery();
    initFollowButton();
});

// Performance Optimization - Lazy Loading for Images
function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));
}

// Error Handling
window.addEventListener('error', function (e) {
    console.error('JavaScript Error:', e.error);
});

// Active Navigation Highlighting
function initActiveNavHighlight() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]');

    function highlightActiveSection() {
        const scrollY = window.pageYOffset;
        const headerHeight = document.querySelector('.header').offsetHeight;

        sections.forEach(section => {
            const sectionTop = section.offsetTop - headerHeight - 50;
            const sectionBottom = sectionTop + section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollY >= sectionTop && scrollY < sectionBottom) {
                // Remove active class from all nav links
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });

                // Add active class to current section link
                const activeLink = document.querySelector(`.nav-menu a[href="#${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        });
    }

    // Throttle the scroll event for better performance
    const throttledHighlight = throttle(highlightActiveSection, 100);
    window.addEventListener('scroll', throttledHighlight);

    // Initial highlight
    highlightActiveSection();
}

// Correção para problemas de navegação de volta
function initNavigationFix() {
    // Interceptar navegação e melhorar histórico
    window.addEventListener('beforeunload', function () {
        // Limpar cache se necessário
        if (window.performance && window.performance.navigation.type === 1) {
            // Página foi recarregada
            sessionStorage.clear();
        }
    });

    // Gerenciar estado de navegação
    const currentPage = window.location.pathname;
    sessionStorage.setItem('lastPage', currentPage);

    // Corrigir problemas com botão voltar do navegador
    window.addEventListener('popstate', function (event) {
        // Recarregar página se necessário para evitar erros de estado
        if (event.state === null && window.history.length > 1) {
            window.location.reload();
        }
    });

    // Melhorar transições entre páginas
    const allLinks = document.querySelectorAll('a[href]');
    allLinks.forEach(link => {
        const href = link.getAttribute('href');

        // Apenas para links internos (PHP)
        if (href && (href.endsWith('.php') || href === 'index.php' || href === 'produtos.php' || href === 'produto.php')) {
            link.addEventListener('click', function (e) {
                // Adicionar uma pequena animação de loading
                const loadingOverlay = document.createElement('div');
                loadingOverlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255,255,255,0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    font-size: 18px;
                    color: #8B4513;
                `;
                loadingOverlay.innerHTML = 'Carregando...';
                document.body.appendChild(loadingOverlay);

                // Remover overlay após um tempo (caso a página não carregue)
                setTimeout(() => {
                    if (document.body.contains(loadingOverlay)) {
                        document.body.removeChild(loadingOverlay);
                    }
                }, 3000);
            });
        }
    });
}

// Service Worker Registration (for PWA capabilities)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js')
            .then(function (registration) {
                console.log('SW registered: ', registration);
            })
            .catch(function (registrationError) {
                console.log('SW registration failed: ', registrationError);
            });
    });
}