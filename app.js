// app.js - Funcionalidades JavaScript da aplicação PHP

class MiaApp {
    constructor() {
        this.apiBase = './api.php';
        this.cart = [];
        this.favorites = [];
        this.init();
    }

    init() {
        // Inicializar quando o DOM estiver carregado
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }
        
        // Carregar dados iniciais
        this.loadCart();
        this.loadFavorites();
    }

    setupEventListeners() {
        // Menu mobile
        this.setupMobileMenu();
        
        // Busca
        this.setupSearch();
        
        // Smooth scroll para links internos
        this.setupSmoothScroll();
        
        // Lazy loading de imagens
        this.setupLazyLoading();
    }

    // Setup do menu mobile
    setupMobileMenu() {
        const toggle = document.getElementById('navbar-toggle');
        const nav = document.getElementById('navbar-nav');
        
        if (toggle && nav) {
            toggle.addEventListener('click', () => {
                nav.classList.toggle('active');
                toggle.classList.toggle('active');
            });

            // Fechar menu ao clicar em link
            nav.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    nav.classList.remove('active');
                    toggle.classList.remove('active');
                });
            });
        }
    }

    // Setup da busca
    setupSearch() {
        const searchForm = document.querySelector('.search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                const searchInput = searchForm.querySelector('.search-input');
                if (searchInput && searchInput.value.trim() === '') {
                    e.preventDefault();
                    this.showNotification('Digite algo para buscar', 'warning');
                }
            });
        }
    }

    // Setup do smooth scroll
    setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Setup do lazy loading
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // Métodos da API
    async apiRequest(endpoint, method = 'GET', data = null) {
        try {
            const config = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                }
            };

            if (data) {
                config.body = JSON.stringify(data);
            }

            const response = await fetch(`${this.apiBase}?action=${endpoint}`, config);
            const result = await response.json();

            return result;
        } catch (error) {
            console.error('Erro na API:', error);
            this.showNotification('Erro de conexão', 'error');
            return { success: false, message: 'Erro de conexão' };
        }
    }

    // Carrinho
    async addToCart(productId, quantity = 1, color = '', size = '') {
        const result = await this.apiRequest('add_to_cart', 'POST', {
            product_id: productId,
            quantity: quantity,
            color: color,
            size: size
        });

        if (result.success) {
            this.showNotification('Produto adicionado ao carrinho!', 'success');
            this.updateCartUI();
        } else {
            this.showNotification(result.message || 'Erro ao adicionar ao carrinho', 'error');
        }

        return result;
    }

    async loadCart() {
        const result = await this.apiRequest('get_cart');
        if (result.success) {
            this.cart = result.data.items;
            this.updateCartUI();
        }
    }

    async removeFromCart(cartKey) {
        const result = await this.apiRequest('remove_from_cart', 'POST', {
            cart_key: cartKey
        });

        if (result.success) {
            this.showNotification('Produto removido do carrinho', 'info');
            this.loadCart();
        }

        return result;
    }

    // Favoritos
    async addToFavorites(productId) {
        const result = await this.apiRequest('add_to_favorites', 'POST', {
            product_id: productId
        });

        if (result.success) {
            this.showNotification('Produto adicionado aos favoritos!', 'success');
            this.updateFavoritesUI();
        }

        return result;
    }

    async removeFromFavorites(productId) {
        const result = await this.apiRequest('remove_from_favorites', 'POST', {
            product_id: productId
        });

        if (result.success) {
            this.showNotification('Produto removido dos favoritos', 'info');
            this.updateFavoritesUI();
        }

        return result;
    }

    async loadFavorites() {
        const result = await this.apiRequest('get_favorites');
        if (result.success) {
            this.favorites = result.data;
            this.updateFavoritesUI();
        }
    }

    // Atualizar UI
    updateCartUI() {
        // Atualizar contador do carrinho
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const totalItems = Object.keys(this.cart).length;
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'inline' : 'none';
        }
    }

    updateFavoritesUI() {
        // Atualizar botões de favorito
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            const productId = parseInt(btn.dataset.productId);
            const isFavorite = this.favorites.some(fav => fav.id === productId);
            
            const icon = btn.querySelector('.btn-icon');
            if (icon) {
                icon.textContent = isFavorite ? '♥' : '♡';
                btn.classList.toggle('favorited', isFavorite);
            }
        });
    }

    // Busca
    async searchProducts(term, category = 'todos') {
        const result = await this.apiRequest(`search_products&term=${encodeURIComponent(term)}&category=${category}`);
        return result;
    }

    // WhatsApp
    openWhatsApp(productName, price, color = '', size = '') {
        let message = `Olá! Tenho interesse no produto: ${productName}`;
        
        if (price) {
            message += ` - ${this.formatPrice(price)}`;
        }
        
        if (color) {
            message += `\nCor: ${color}`;
        }
        
        if (size) {
            message += `\nTamanho: ${size}`;
        }

        const whatsappUrl = `https://wa.me/5511999999999?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    }

    // Utilitários
    formatPrice(price) {
        return `R$ ${price.toFixed(2).replace('.', ',')}`;
    }

    showNotification(message, type = 'info') {
        // Criar ou atualizar elemento de notificação
        let notification = document.querySelector('.notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.className = 'notification';
            document.body.appendChild(notification);
        }

        notification.textContent = message;
        notification.className = `notification ${type} show`;

        // Auto-remover após 3 segundos
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    // Animações
    animateOnScroll() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }
}

// Funções globais para compatibilidade com HTML existente
function viewProduct(productId) {
    window.location.href = `produto-unico.php?id=${productId}`;
}

function contactWhatsApp(productName, price, color = '', size = '') {
    if (window.miaApp) {
        window.miaApp.openWhatsApp(productName, price, color, size);
    }
}

function toggleFavorite(productId) {
    if (window.miaApp) {
        const btn = document.querySelector(`[data-product-id="${productId}"]`);
        const isFavorite = btn && btn.classList.contains('favorited');
        
        if (isFavorite) {
            window.miaApp.removeFromFavorites(productId);
        } else {
            window.miaApp.addToFavorites(productId);
        }
    }
}

// Inicializar aplicação
window.miaApp = new MiaApp();

// CSS para notificações (inserir dinamicamente)
const notificationCSS = `
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    z-index: 10000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 300px;
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    background-color: #28a745;
}

.notification.error {
    background-color: #dc3545;
}

.notification.warning {
    background-color: #ffc107;
    color: #212529;
}

.notification.info {
    background-color: #17a2b8;
}

.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.animate-on-scroll.animate-in {
    opacity: 1;
    transform: translateY(0);
}
`;

// Inserir CSS
const style = document.createElement('style');
style.textContent = notificationCSS;
document.head.appendChild(style);