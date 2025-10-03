<?php
// Carregar produtos dinamicamente
require_once 'config/produtos.php';

// Obter apenas produtos ativos para o frontend
$todos_produtos = getAllProdutos('ativo');

// Verificar se veio filtro por parâmetro URL
$filtroInicial = isset($_GET['filter']) ? $_GET['filter'] : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Produtos - Mia Couro Legítimo</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="produtos-styles-clean.css">
    <link rel="stylesheet" href="product-card-universal.css">
    <link rel="stylesheet" href="responsive-global.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        /* FORÇAR MENU TOGGLE VISÍVEL - CSS BONITO */
        .header {
            display: flex !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 68px !important;
            z-index: 1000 !important;
            background: url('img/fundoheadercrop.jpg') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: repeat-x !important;
            box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05) !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 0 80px 0 0 !important;
        }

        .menu-toggle {
            display: flex !important;
            width: 98px !important;
            height: 68px !important;
            background: #520100 !important;
            border-radius: 30px !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 5px !important;
            cursor: pointer !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 1100 !important;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
        }

        .menu-toggle:hover {
            background: #891010 !important;
            transform: scale(1.05) !important;
            box-shadow: 0 4px 15px rgba(137, 16, 16, 0.3) !important;
        }

        .menu-toggle span {
            width: 35px !important;
            height: 3px !important;
            background: #FCF8F1 !important;
            border-radius: 2px !important;
            display: block !important;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
            transform-origin: center !important;
        }

        .logo {
            position: absolute !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            display: flex !important;
            align-items: center !important;
        }

        .logo img {
            height: 55px !important;
            width: auto !important;
            max-width: 250px !important;
        }

        .nav-menu {
            display: none !important;
        }

        /* Ajuste do body */
        body {
            padding-top: 68px !important;
        }

        /* CSS de emergência para garantir visibilidade dos produtos e imagens */
        .product-card-catalog {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            background: #FCF8F1 !important;
            border-radius: 20px !important;
            overflow: hidden !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
            margin-bottom: 20px !important;
        }

        .products-grid-catalog {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
            gap: 20px !important;
            padding: 20px !important;
            max-width: 1200px !important;
            margin: 0 auto !important;
        }

        .product-image-catalog {
            width: 100% !important;
            height: 250px !important;
            overflow: hidden !important;
            position: relative !important;
            background: #f5f5f5 !important;
        }

        .product-image-catalog img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .product-info-catalog {
            padding: 15px !important;
            background: #FCF8F1 !important;
        }

        .product-info-catalog h3 {
            margin: 0 0 10px 0 !important;
            color: #333 !important;
            font-size: 18px !important;
        }

        .price-catalog {
            color: #520100 !important;
            font-weight: bold !important;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="logo">
            <img src="icon s/logotipo.svg" alt="Mia Couro Legítimo">
        </div>
        <nav class="nav-menu" id="navMenu">
            <div class="menu-title">Menu</div>
            <a href="index">Início</a>
            <a href="produtos">Produtos</a>
            <a href="sobre">Sobre nós</a>
            <a href="contato">Contato</a>
            <a href="produtos?filter=desconto" class="sale-link">Sale</a>
        </nav>
    </header>

    <!-- Hero Section Produtos -->
    <section class="products-hero">
        <div class="products-hero-content">
            <h1>Catálogo de Produtos</h1>
            <p>Explore nossa coleção exclusiva de couro legítimo</p>

            <!-- Filtros -->
            <div class="filters-container">
                <div class="filter-dropdown">
                    <select id="categoryFilter">
                        <option value="">Todas as Categorias</option>
                        <option value="bolsas">Bolsas</option>
                        <option value="carteiras">Carteiras</option>
                        <option value="cases-capas">Cases & Capas</option>
                        <option value="escritorio">Escritório</option>
                        <option value="viagem">Viagem</option>
                        <option value="acessorios">Acessórios</option>
                    </select>
                </div>

                <div class="filter-dropdown">
                    <select id="priceFilter">
                        <option value="">Faixa de Preço</option>
                        <option value="desconto">Produtos em Desconto</option>
                        <option value="0-100">Até R$ 100</option>
                        <option value="100-200">R$ 100 - R$ 200</option>
                        <option value="200-300">R$ 200 - R$ 300</option>
                        <option value="300+">Acima de R$ 300</option>
                    </select>
                </div>

                <div class="filter-dropdown">
                    <select id="sortFilter">
                        <option value="">Ordenar por</option>
                        <option value="price-asc">Menor Preço</option>
                        <option value="price-desc">Maior Preço</option>
                        <option value="name">Nome A-Z</option>
                        <option value="newest">Mais Recentes</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Produtos -->
    <section class="catalog-products">
        <div class="products-header">
            <div class="clear-filters-btn" onclick="clearAllFilters()">
                <h2>Nossos Produtos</h2>
            </div>
        </div>

        <div class="products-grid-catalog" id="productsGrid">
            <?php if (!empty($todos_produtos)): ?>
                <?php foreach ($todos_produtos as $produto): ?>
                    <?php if ($produto['status'] === 'ativo'): ?>
                        <a href="produto-unico.php?id=<?php echo $produto['id']; ?>" class="product-card-catalog"
                            data-category="<?php echo $produto['category']; ?>" data-price="<?php echo $produto['price']; ?>"
                            data-sales="<?php echo $produto['sales'] ?? 0; ?>"
                            data-discount="<?php echo (isset($produto['discount']) && $produto['discount'] !== null && $produto['discount'] > 0) ? $produto['discount'] : '0'; ?>"
                            data-tags="<?php echo isset($produto['tags']) ? implode(',', $produto['tags']) : ''; ?>">

                            <div class="product-image-catalog">
                                <?php
                                $primeira_imagem = $produto['images'][0];
                                $caminho_completo = __DIR__ . '/' . $primeira_imagem;
                                $imagem_existe = file_exists($caminho_completo);
                                $imagem_final = $imagem_existe ? $primeira_imagem : 'img/default-product.png';
                                ?>
                                <img src="<?php echo htmlspecialchars($imagem_final); ?>"
                                    alt="<?php echo htmlspecialchars($produto['title']); ?>" loading="lazy"
                                    onerror="this.onerror=null; this.src='img/default-product.png';">>

                                <?php if (!empty($produto['discount'])): ?>
                                    <div class="discount-badge"><?php echo $produto['discount']; ?>% OFF</div>
                                <?php endif; ?>

                                <div class="category-badge">
                                    <?php
                                    $categoryNames = [
                                        'bolsas' => 'Bolsas',
                                        'carteiras' => 'Carteiras',
                                        'cases-capas' => 'Cases & Capas',
                                        'escritorio' => 'Escritório',
                                        'viagem' => 'Viagem',
                                        'acessorios' => 'Acessórios'
                                    ];
                                    echo htmlspecialchars($categoryNames[$produto['category']] ?? ucfirst($produto['category']));
                                    ?>
                                </div>
                            </div>

                            <div class="product-info-catalog">
                                <h3><?php echo htmlspecialchars($produto['title']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($produto['description'], 0, 50) . '...'); ?></p>
                                <div class="price-catalog">
                                    <?php if (!empty($produto['oldPrice'])): ?>
                                        <span class="old-price"><?php echo formatPrice($produto['oldPrice']); ?></span>
                                    <?php endif; ?>
                                    <span class="current-price"><?php echo formatPrice($produto['price']); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results-message">
                    <div>
                        <h3>Nenhum produto encontrado</h3>
                        <p>Tente ajustar os filtros ou volte mais tarde.</p>
                        <button onclick="clearAllFilters()" class="btn-primary">Limpar Filtros</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Mensagem quando filtros não retornam resultados -->
        <div id="noFilterResults" class="no-filter-results" style="display: none;">
            <div class="no-results-content">
                <h3>Nenhum produto encontrado</h3>
                <p>Não encontramos produtos que correspondam aos filtros selecionados.</p>
                <button onclick="clearAllFilters()" class="btn-clear-filters">Limpar Filtros</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contato">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="icon s/MiaCourolegitimo 1.svg" alt="Mia Couro Legítimo" class="footer-logo">
                    <p>Única, para quem também é</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/mia.mianet" target="_blank" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.40s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a href="https://wa.me/5541973382889" target="_blank" aria-label="WhatsApp">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.464 3.488" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Produtos</h4>
                        <ul>
                            <li><a href="produtos">Todos os Produtos</a></li>
                            <li><a href="produtos?category=bolsas">Bolsas</a></li>
                            <li><a href="produtos?category=carteiras">Carteiras</a></li>
                            <li><a href="produtos?category=cases-capas">Cases & Capas</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Atendimento</h4>
                        <ul>
                            <li><a href="contato">Contato</a></li>
                            <li><a href="sobre">Sobre Nós</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Contato</h4>
                        <ul>
                            <li>contato@mia.com.br</li>
                            <li>+55 (41) 9733-8289</li>
                            <li>Curitiba, PR</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 Mia. Todos os direitos reservados.</p>
                <p>Desenvolvido por <strong>Aether Design</strong></p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="script.js"></script>
    <script>
        // Sistema de filtros simplificado
        window.addEventListener('load', function () {
            console.log('INICIANDO FILTROS SIMPLES');

            const categorySelect = document.getElementById('categoryFilter');
            const priceSelect = document.getElementById('priceFilter');
            const sortSelect = document.getElementById('sortFilter');

            // Verificar parâmetros da URL e aplicar filtros automaticamente
            const urlParams = new URLSearchParams(window.location.search);
            const categoryParam = urlParams.get('category');
            const priceParam = urlParams.get('price');
            const sortParam = urlParams.get('sort');
            const filterParam = urlParams.get('filter'); // Para links como ?filter=desconto

            // Aplicar filtros da URL
            if (categoryParam && categorySelect) {
                categorySelect.value = categoryParam;
                console.log('Filtro de categoria aplicado da URL:', categoryParam);
            }
            if (priceParam && priceSelect) {
                priceSelect.value = priceParam;
                console.log('Filtro de preço aplicado da URL:', priceParam);
            }
            // Filtro especial para desconto
            if (filterParam === 'desconto' && priceSelect) {
                priceSelect.value = 'desconto';
                console.log('Filtro de desconto aplicado da URL');
            }
            if (sortParam && sortSelect) {
                sortSelect.value = sortParam;
                console.log('Filtro de ordenação aplicado da URL:', sortParam);
            }

            // Aplicar filtros após definir os valores
            if (categoryParam || priceParam || sortParam || filterParam === 'desconto') {
                filterProducts();
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', filterProducts);
            }
            if (priceSelect) {
                priceSelect.addEventListener('change', filterProducts);
            }
            if (sortSelect) {
                sortSelect.addEventListener('change', filterProducts);
            }

            console.log('Event listeners adicionados');
        });

        function filterProducts() {
            console.log('>>> FILTRANDO PRODUTOS <<<');

            const categoryValue = document.getElementById('categoryFilter').value;
            const priceValue = document.getElementById('priceFilter').value;
            const sortValue = document.getElementById('sortFilter').value;
            const noResultsDiv = document.getElementById('noFilterResults');
            const productsGrid = document.getElementById('productsGrid');

            console.log('Categoria selecionada:', categoryValue);
            console.log('Preço selecionado:', priceValue);
            console.log('Ordenação selecionada:', sortValue);

            const products = Array.from(document.querySelectorAll('.product-card-catalog'));
            console.log('Total de produtos encontrados:', products.length);

            let shown = 0;
            let visibleProducts = [];

            products.forEach((product, index) => {
                let show = true;

                // Filtro categoria
                if (categoryValue && categoryValue !== '') {
                    const productCategory = product.getAttribute('data-category');
                    console.log(`Produto ${index}: categoria=${productCategory}, busca=${categoryValue}`);
                    if (productCategory !== categoryValue) {
                        show = false;
                    }
                }

                // Filtro preço
                if (priceValue && priceValue !== '') {
                    const productPrice = parseFloat(product.getAttribute('data-price'));
                    const productDiscount = parseFloat(product.getAttribute('data-discount')) || 0;
                    console.log(`Produto ${index}: preço=${productPrice}, faixa=${priceValue}, desconto=${productDiscount}%`);

                    let priceOk = false;

                    // Filtro especial para produtos em desconto
                    if (priceValue === 'desconto' && productDiscount > 0) {
                        priceOk = true;
                    }
                    // Filtros de faixa de preço normais
                    else if (priceValue === '0-100' && productPrice <= 100) priceOk = true;
                    else if (priceValue === '100-200' && productPrice > 100 && productPrice <= 200) priceOk = true;
                    else if (priceValue === '200-300' && productPrice > 200 && productPrice <= 300) priceOk = true;
                    else if (priceValue === '300+' && productPrice > 300) priceOk = true;

                    if (!priceOk) show = false;
                }

                // Mostrar ou esconder
                if (show) {
                    product.style.display = 'block';
                    product.style.visibility = 'visible';
                    product.style.opacity = '1';
                    shown++;
                    visibleProducts.push(product);
                    console.log(`✓ Produto ${index} VISÍVEL`);
                } else {
                    product.style.display = 'none';
                    console.log(`✗ Produto ${index} OCULTO`);
                }
            });

            // Aplicar ordenação aos produtos visíveis
            if (sortValue && sortValue !== '' && visibleProducts.length > 0) {
                console.log('🔄 Aplicando ordenação:', sortValue);

                visibleProducts.sort((a, b) => {
                    switch (sortValue) {
                        case 'price-asc':
                            return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                        case 'price-desc':
                            return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                        case 'name':
                            const nameA = a.querySelector('h3').textContent.toLowerCase();
                            const nameB = b.querySelector('h3').textContent.toLowerCase();
                            return nameA.localeCompare(nameB);
                        case 'newest':
                            return parseInt(b.getAttribute('data-sales')) - parseInt(a.getAttribute('data-sales'));
                        default:
                            return 0;
                    }
                });

                // Reordenar no DOM
                visibleProducts.forEach(product => {
                    productsGrid.appendChild(product);
                });

                console.log('✅ Produtos reordenados');
            }

            // Mostrar mensagem de "nenhum resultado" se necessário
            if (shown === 0 && (categoryValue !== '' || priceValue !== '')) {
                noResultsDiv.style.display = 'block';
                console.log('📝 Mostrando mensagem de nenhum resultado');
            } else {
                noResultsDiv.style.display = 'none';
            }

            console.log(`RESULTADO: ${shown} produtos visíveis`);
        }

        function clearAllFilters() {
            console.log('>>> LIMPANDO FILTROS <<<');
            document.getElementById('categoryFilter').value = '';
            document.getElementById('priceFilter').value = '';
            document.getElementById('sortFilter').value = '';

            const products = document.querySelectorAll('.product-card-catalog');
            products.forEach(product => {
                product.style.display = 'block';
                product.style.visibility = 'visible';
                product.style.opacity = '1';
            });

            // Ocultar mensagem de nenhum resultado
            const noResultsDiv = document.getElementById('noFilterResults');
            if (noResultsDiv) {
                noResultsDiv.style.display = 'none';
            }

            console.log('Todos os filtros limpos');
        }
    </script>
</body>

</html>