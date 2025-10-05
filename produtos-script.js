// Script específico para página de produtos
console.log('🚀 Script de produtos carregado');

// Variáveis globais
let allProducts = [];
let filteredProducts = [];

// Inicialização quando DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('📦 Inicializando página de produtos...');
    
    // Coletar todos os produtos
    collectProducts();
    
    // Inicializar filtros
    initializeFilters();
    
    // Aplicar filtro inicial se houver
    applyInitialFilter();
    
    // Disponibilizar função de debug globalmente
    window.debugFilters = debugFilters;
    console.log('🔧 Digite debugFilters() no console para ver informações detalhadas');
    
    console.log('✅ Página de produtos inicializada');
});

// Coletar todos os produtos da página
function collectProducts() {
    allProducts = Array.from(document.querySelectorAll('.product-card-catalog'));
    filteredProducts = [...allProducts];
    console.log(`📊 ${allProducts.length} produtos encontrados`);
}

// Inicializar todos os filtros
function initializeFilters() {
    const categoryFilter = document.getElementById('productFilter');
    const priceFilter = document.getElementById('priceFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    // Adicionar eventos de mudança
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
        console.log('✅ Filtro de categoria inicializado');
    }
    
    if (priceFilter) {
        priceFilter.addEventListener('change', applyFilters);
        console.log('✅ Filtro de preço inicializado');
    }
    
    if (sortFilter) {
        sortFilter.addEventListener('change', applyFilters);
        console.log('✅ Filtro de ordenação inicializado');
    }
}

// Aplicar todos os filtros
function applyFilters() {
    console.log('🔍 Aplicando filtros...');
    
    const categoryFilter = document.getElementById('productFilter');
    const priceFilter = document.getElementById('priceFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    let filtered = [...allProducts];
    
    // Filtro por categoria
    if (categoryFilter && categoryFilter.value) {
        if (categoryFilter.value === 'desconto') {
            // Filtrar produtos com desconto
            filtered = filtered.filter(product => {
                const hasOldPrice = product.querySelector('.old-price') !== null;
                const discountData = product.dataset.discount;
                const hasDiscount = discountData && parseFloat(discountData) > 0;
                return hasOldPrice || hasDiscount;
            });
            console.log(`💰 Filtro desconto aplicado`);
        } else {
            // Filtrar por categoria normal
            filtered = filtered.filter(product => {
                const category = product.dataset.category || '';
                return category === categoryFilter.value;
            });
            console.log(`📂 Filtro categoria: ${categoryFilter.value}`);
        }
    }
    
    // Filtro por preço
    if (priceFilter && priceFilter.value && priceFilter.value !== 'Faixa de Preço') {
        filtered = filterByPrice(filtered, priceFilter.value);
        console.log(`💰 Filtro preço: ${priceFilter.value}`);
    }
    
    // Ordenação
    if (sortFilter && sortFilter.value && sortFilter.value !== 'Ordenar: Mais vendidos') {
        filtered = sortProducts(filtered, sortFilter.value);
        console.log(`📊 Ordenação: ${sortFilter.value}`);
    }
    
    // Mostrar/ocultar produtos
    showFilteredProducts(filtered);
    
    console.log(`✅ ${filtered.length} produtos exibidos`);
}

// Filtrar por preço
function filterByPrice(products, priceRange) {
    return products.filter(product => {
        const priceElement = product.querySelector('.current-price, .new-price, .price');
        if (!priceElement) return false;
        
        const priceText = priceElement.textContent;
        const price = parseFloat(priceText.replace(/[^0-9,]/g, '').replace(',', '.'));
        
        switch (priceRange) {
            case 'R$ 0 - R$ 50':
                return price <= 50;
            case 'R$ 51 - R$ 100':
                return price > 50 && price <= 100;
            case 'R$ 101 - R$ 200':
                return price > 100 && price <= 200;
            case 'R$ 201 - R$ 500':
                return price > 200 && price <= 500;
            case 'Acima de R$ 500':
                return price > 500;
            default:
                return true;
        }
    });
}

// Ordenar produtos
function sortProducts(products, sortType) {
    return products.sort((a, b) => {
        switch (sortType) {
            case 'Menor preço':
                return getProductPrice(a) - getProductPrice(b);
            case 'Maior preço':
                return getProductPrice(b) - getProductPrice(a);
            case 'A-Z':
                return getProductName(a).localeCompare(getProductName(b));
            case 'Z-A':
                return getProductName(b).localeCompare(getProductName(a));
            default:
                return 0;
        }
    });
}

// Obter preço do produto
function getProductPrice(product) {
    const priceElement = product.querySelector('.current-price, .new-price, .price');
    if (!priceElement) return 0;
    
    const priceText = priceElement.textContent;
    return parseFloat(priceText.replace(/[^0-9,]/g, '').replace(',', '.')) || 0;
}

// Obter nome do produto
function getProductName(product) {
    const nameElement = product.querySelector('h3');
    return nameElement ? nameElement.textContent : '';
}

// Mostrar produtos filtrados
function showFilteredProducts(filtered) {
    // Ocultar todos os produtos
    allProducts.forEach(product => {
        product.style.display = 'none';
    });
    
    // Mostrar produtos filtrados
    filtered.forEach(product => {
        product.style.display = 'block';
    });
    
    filteredProducts = filtered;
    
    // Atualizar contador de produtos
    updateProductCount(filtered.length);
}

// Atualizar contador de produtos
function updateProductCount(count) {
    const header = document.querySelector('.products-header h2');
    if (header) {
        if (count === allProducts.length) {
            header.textContent = 'Todos os Produtos';
        } else {
            header.textContent = `${count} produto${count !== 1 ? 's' : ''} encontrado${count !== 1 ? 's' : ''}`;
        }
    }
}

// Limpar todos os filtros
function clearFilters() {
    console.log('🧹 Limpando filtros...');
    
    const filters = [
        'productFilter',
        'priceFilter',
        'sortFilter'
    ];
    
    filters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.selectedIndex = 0;
        }
    });
    
    // Mostrar todos os produtos
    showFilteredProducts(allProducts);
    
    console.log('✅ Filtros limpos');
}

// Aplicar filtro inicial (ex: quando vem da URL)
function applyInitialFilter() {
    // Esta função será chamada pelo PHP se necessário
    console.log('🎯 Verificando filtro inicial...');
}

// Função para aplicar filtro Desconto (chamada pelo link no menu)
function applySaleFilter() {
    console.log('🏷️ Aplicando filtro Desconto...');
    
    const categoryFilter = document.getElementById('productFilter');
    if (categoryFilter) {
        categoryFilter.value = 'desconto';
        applyFilters();
    }
    
    return false; // Prevenir navegação
}

// Função para depuração
function debugFilters() {
    console.log('🔧 Debug dos filtros:');
    console.log('- Produtos totais:', allProducts.length);
    console.log('- Produtos filtrados:', filteredProducts.length);
    
    const filters = ['productFilter', 'priceFilter', 'sortFilter'];
    filters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        console.log(`- ${filterId}:`, filter ? filter.value : 'NÃO ENCONTRADO');
    });
    
    // Debug específico para produtos com desconto
    console.log('🏷️ Debug produtos com desconto:');
    const productsWithDiscount = allProducts.filter(product => {
        const hasOldPrice = product.querySelector('.old-price') !== null;
        const discountData = product.dataset.discount;
        const hasDiscount = discountData && parseFloat(discountData) > 0;
        const discountBadge = product.querySelector('.discount-badge') !== null;
        
        console.log(`Produto ${product.querySelector('h3')?.textContent}:`, {
            hasOldPrice,
            discountData,
            hasDiscount,
            discountBadge
        });
        
        return hasOldPrice || hasDiscount || discountBadge;
    });
    
    console.log(`- Total de produtos com desconto encontrados: ${productsWithDiscount.length}`);
}