// ===================================
// FILTROS DA PÁGINA DE PRODUTOS - VERSÃO CORRIGIDA
// ===================================

// Variáveis globais
let pageLoaded = false;
let allProducts = [];

// Inicialização quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando página de produtos...');
    initializeProductPage();
});

// Função principal de inicialização
function initializeProductPage() {
    // Coletar todos os produtos
    allProducts = Array.from(document.querySelectorAll('.products-grid-catalog > a'));
    console.log(`📦 Total de produtos encontrados: ${allProducts.length}`);
    
    // Mostrar todos os produtos inicialmente
    showAllProducts();
    
    // Configurar event listeners dos filtros
    setupFilterListeners();
    
    // Aplicar filtro da URL se houver
    applyURLFilter();
    
    // Configurar interações dos produtos
    setupProductInteractions();
    
    // Marcar página como carregada
    setTimeout(() => {
        pageLoaded = true;
        console.log('✅ Página carregada completamente');
    }, 500);
}

// Mostrar todos os produtos (estado inicial)
function showAllProducts() {
    allProducts.forEach(product => {
        product.style.display = 'block';
        product.style.visibility = 'visible';
        product.style.opacity = '1';
        product.style.pointerEvents = 'auto';
        product.style.position = 'relative';
    });
    hideNoResultsMessage();
}

// Configurar event listeners dos filtros
function setupFilterListeners() {
    const categoryFilter = document.getElementById('productFilter');
    const priceFilter = document.getElementById('priceFilter');
    const typeFilter = document.getElementById('typeFilter');
    const sortFilter = document.getElementById('sortFilter');

    if (categoryFilter) {
        categoryFilter.addEventListener('change', () => {
            console.log('🔄 Filtro categoria alterado:', categoryFilter.value);
            applyAllFilters();
        });
    }

    if (priceFilter) {
        priceFilter.addEventListener('change', () => {
            console.log('💰 Filtro preço alterado:', priceFilter.value);
            applyAllFilters();
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener('change', () => {
            console.log('🏷️ Filtro tipo alterado:', typeFilter.value);
            applyAllFilters();
        });
    }

    if (sortFilter) {
        sortFilter.addEventListener('change', () => {
            console.log('📊 Ordenação alterada:', sortFilter.value);
            handleSortChange(sortFilter.value);
        });
    }
}

// Aplicar todos os filtros
function applyAllFilters() {
    if (!pageLoaded) {
        console.log('⏳ Aguardando página carregar...');
        return;
    }

    const categoryFilter = document.getElementById('productFilter');
    const priceFilter = document.getElementById('priceFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    const selectedCategory = categoryFilter ? categoryFilter.value : '';
    const selectedPriceRange = priceFilter ? priceFilter.value : '';
    const selectedType = typeFilter ? typeFilter.value : '';
    
    console.log(`🔍 Aplicando filtros - Categoria: "${selectedCategory}" | Preço: "${selectedPriceRange}" | Tipo: "${selectedType}"`);
    
    let visibleCount = 0;
    
    allProducts.forEach(productLink => {
        const card = productLink.querySelector('.product-card-catalog');
        if (!card) return;
        
        let shouldShow = true;
        
        // Filtro por categoria
        if (selectedCategory && selectedCategory !== '' && selectedCategory !== 'Todas as Categorias') {
            const productCategory = card.getAttribute('data-category');
            if (productCategory !== selectedCategory) {
                shouldShow = false;
            }
        }
        
        // Filtro por preço
        if (selectedPriceRange && selectedPriceRange !== 'Faixa de Preço') {
            const productPrice = parseFloat(card.getAttribute('data-price'));
            
            let priceInRange = false;
            switch (selectedPriceRange) {
                case 'R$ 0 - R$ 50':
                    priceInRange = productPrice >= 0 && productPrice <= 50;
                    break;
                case 'R$ 51 - R$ 100':
                    priceInRange = productPrice >= 51 && productPrice <= 100;
                    break;
                case 'R$ 101 - R$ 200':
                    priceInRange = productPrice >= 101 && productPrice <= 200;
                    break;
                case 'R$ 201 - R$ 500':
                    priceInRange = productPrice >= 201 && productPrice <= 500;
                    break;
                case 'Acima de R$ 500':
                    priceInRange = productPrice > 500;
                    break;
                default:
                    priceInRange = true;
            }
            
            if (!priceInRange) {
                shouldShow = false;
            }
        }
        
        // Filtro por tipo (desconto, viagem, dia-a-dia)
        if (selectedType && selectedType !== '') {
            const productTags = card.getAttribute('data-tags') || '';
            const discountAttr = card.getAttribute('data-discount');
            const productDiscount = discountAttr ? parseInt(discountAttr) : 0;
            
            let typeMatches = false;
            switch (selectedType) {
                case 'desconto':
                    console.log(`🔍 Produto: ${card.querySelector('.product-title-catalog')?.textContent}, Desconto attr: "${discountAttr}", Desconto parsed: ${productDiscount}`);
                    // Verificar se tem desconto (maior que 0) E se tem a classe de desconto
                    const hasDiscountBadge = card.querySelector('.discount-badge');
                    typeMatches = (productDiscount > 0) || (hasDiscountBadge !== null);
                    console.log(`   Tem badge desconto: ${hasDiscountBadge !== null}, Resultado: ${typeMatches}`);
                    break;
                case 'viagem':
                    typeMatches = productTags.includes('viagem');
                    break;
                case 'dia-a-dia':
                    typeMatches = productTags.includes('dia-a-dia');
                    break;
            }
            
            if (!typeMatches) {
                shouldShow = false;
            }
        }
        
        // Mostrar/Ocultar produto
        if (shouldShow) {
            productLink.style.display = 'block';
            productLink.style.visibility = 'visible';
            productLink.style.opacity = '1';
            productLink.style.pointerEvents = 'auto';
            visibleCount++;
        } else {
            productLink.style.display = 'none';
            productLink.style.visibility = 'hidden';
            productLink.style.opacity = '0';
            productLink.style.pointerEvents = 'none';
        }
    });
    
    console.log(`📊 Resultado: ${visibleCount} produtos visíveis`);
    
    // Mostrar mensagem se nenhum produto encontrado
    if (visibleCount === 0) {
        showNoResultsMessage();
    } else {
        hideNoResultsMessage();
    }
}

// Tratar mudança de ordenação
function handleSortChange(sortValue) {
    if (!pageLoaded) return;
    
    // Primeiro aplicar filtros normais
    applyAllFilters();
    // Depois ordenar os produtos visíveis
    if (sortValue !== 'Ordenar: Mais vendidos') {
        sortVisibleProducts(sortValue);
    }
}



// Ordenar produtos visíveis
function sortVisibleProducts(sortOption) {
    const productsGrid = document.querySelector('.products-grid-catalog');
    if (!productsGrid) return;
    
    // Pegar apenas produtos visíveis
    const visibleProducts = allProducts.filter(product => 
        product.style.display !== 'none'
    );
    
    if (visibleProducts.length === 0) return;
    
    console.log(`🔄 Ordenando ${visibleProducts.length} produtos por: ${sortOption}`);
    
    // Ordenar array
    visibleProducts.sort((linkA, linkB) => {
        const cardA = linkA.querySelector('.product-card-catalog');
        const cardB = linkB.querySelector('.product-card-catalog');
        
        if (!cardA || !cardB) return 0;
        
        const nameA = cardA.querySelector('h3').textContent.trim();
        const nameB = cardB.querySelector('h3').textContent.trim();
        
        const priceA = parseFloat(cardA.getAttribute('data-price'));
        const priceB = parseFloat(cardB.getAttribute('data-price'));
        
        switch (sortOption) {
            case 'Menor preço':
                return priceA - priceB;
            case 'Maior preço':
                return priceB - priceA;
            case 'A-Z':
                return nameA.localeCompare(nameB);
            case 'Z-A':
                return nameB.localeCompare(nameA);
            case 'Maior desconto':
                const discountA = parseInt(cardA.getAttribute('data-discount')) || 0;
                const discountB = parseInt(cardB.getAttribute('data-discount')) || 0;
                return discountB - discountA;
            case 'Mais recentes':
                return 0; // Manter ordem original
            default: // Mais vendidos
                const salesA = parseInt(cardA.getAttribute('data-sales')) || 0;
                const salesB = parseInt(cardB.getAttribute('data-sales')) || 0;
                return salesB - salesA;
        }
    });
    
    // Reorganizar no DOM
    visibleProducts.forEach(product => {
        productsGrid.appendChild(product);
    });
}

// Limpar todos os filtros
function clearFilters() {
    console.log('🧹 Limpando todos os filtros...');
    
    // Resetar selects
    const categoryFilter = document.getElementById('productFilter');
    const priceFilter = document.getElementById('priceFilter');
    const typeFilter = document.getElementById('typeFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    if (categoryFilter) categoryFilter.value = '';
    if (priceFilter) priceFilter.selectedIndex = 0;
    if (typeFilter) typeFilter.value = '';
    if (sortFilter) sortFilter.selectedIndex = 0;
    
    // Mostrar todos os produtos
    showAllProducts();
    
    console.log('✅ Filtros limpos - todos os produtos visíveis');
}

// Função para aplicar filtro da URL
function applyURLFilter() {
    const urlParams = new URLSearchParams(window.location.search);
    const categoria = urlParams.get('categoria');
    
    if (categoria) {
        console.log(`🔗 Aplicando filtro da URL: ${categoria}`);
        const categoryFilter = document.getElementById('productFilter');
        if (categoryFilter) {
            categoryFilter.value = categoria;
            setTimeout(() => {
                applyAllFilters();
                // Scroll para produtos
                const catalogSection = document.querySelector('.catalog-products');
                if (catalogSection) {
                    catalogSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100);
        }
    }
}

// Mostrar mensagem de "sem resultados"
function showNoResultsMessage(title = 'Nenhum produto encontrado', description = 'Tente ajustar os filtros para encontrar o que procura.') {
    let noResultsMessage = document.querySelector('.no-results-message');
    const productsGrid = document.querySelector('.products-grid-catalog');
    
    if (!noResultsMessage) {
        noResultsMessage = document.createElement('div');
        noResultsMessage.className = 'no-results-message';
        productsGrid.appendChild(noResultsMessage);
    }
    
    noResultsMessage.innerHTML = `
        <div style="text-align: center; padding: 60px 20px; color: #666; background: #f9f9f9; border-radius: 10px; margin: 20px 0;">
            <h3 style="color: #333; margin-bottom: 10px; font-size: 1.4em;">${title}</h3>
            <p style="margin-bottom: 25px; font-size: 1.1em;">${description}</p>
            <button onclick="clearFilters()" style="padding: 12px 24px; background: #520100; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1em; font-weight: bold; transition: all 0.3s;">
                Limpar Filtros
            </button>
        </div>
    `;
    noResultsMessage.style.display = 'block';
}

// Esconder mensagem de "sem resultados"
function hideNoResultsMessage() {
    const noResultsMessage = document.querySelector('.no-results-message');
    if (noResultsMessage) {
        noResultsMessage.style.display = 'none';
    }
}

// Configurar interações dos produtos (hover, animações)
function setupProductInteractions() {
    allProducts.forEach(productLink => {
        const card = productLink.querySelector('.product-card-catalog');
        if (!card) return;
        
        // Hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
        
        // Click animation
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
}

// Função de teste para debug
function testFilters() {
    console.log('=== 🧪 TESTE DE FILTROS ===');
    console.log(`Total de produtos: ${allProducts.length}`);
    
    let visible = 0, hidden = 0;
    
    allProducts.forEach((product, index) => {
        const card = product.querySelector('.product-card-catalog');
        const name = card.querySelector('h3').textContent;
        const category = card.getAttribute('data-category');
        const price = card.getAttribute('data-price');
        const discount = card.getAttribute('data-discount');
        const isVisible = product.style.display !== 'none';
        
        if (isVisible) visible++;
        else hidden++;
        
        console.log(`${index + 1}. ${name} | Cat: ${category} | R$ ${price} | Desc: ${discount}% | Visível: ${isVisible}`);
    });
    
    console.log(`\n📊 Resumo: ${visible} visíveis, ${hidden} ocultos`);
    
    const filters = {
        categoria: document.getElementById('productFilter')?.value || 'nenhum',
        preco: document.getElementById('priceFilter')?.value || 'nenhum',
        tipo: document.getElementById('typeFilter')?.value || 'nenhum',
        ordem: document.getElementById('sortFilter')?.value || 'nenhum'
    };
    
    console.log('🎛️ Filtros ativos:', filters);
}

// Expor funções globais necessárias
window.clearFilters = clearFilters;
window.testFilters = testFilters;

console.log('✅ Script de filtros carregado com sucesso!');