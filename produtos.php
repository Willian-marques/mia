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
    <link rel="stylesheet" href="responsive-global.css">
    <link rel="stylesheet" href="menu-mobile-fix.css">
    <link rel="stylesheet" href="product-card-universal.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <a href="#" class="sale-link" onclick="applySaleFilter()">Desconto</a>
        </nav>
    </header>

    <!-- Hero Section Produtos -->
    <section class="products-hero">
        <div class="products-hero-content">
            <h1>Catálogo de Produtos</h1>
            <p>Explore Nossa Linha Completa de Produtos</p>

            <!-- Filtros -->
            <div class="filters-container">
                <div class="filter-dropdown">
                    <select id="productFilter">
                        <option value="">Todas as Categorias</option>
                        <option value="bolsas">Bolsas</option>
                        <option value="carteiras">Carteiras</option>
                        <option value="cases-capas">Cases & Capas</option>
                        <option value="acessorios">Acessórios</option>
                    </select>
                </div>
                <div class="filter-dropdown">
                    <select id="priceFilter">
                        <option>Faixa de Preço</option>
                        <option>R$ 0 - R$ 50</option>
                        <option>R$ 51 - R$ 100</option>
                        <option>R$ 101 - R$ 200</option>
                        <option>R$ 201 - R$ 500</option>
                        <option>Acima de R$ 500</option>
                    </select>
                </div>
                <div class="filter-dropdown">
                    <select id="typeFilter">
                        <option value="">Tipo de Produto</option>
                        <option value="desconto">Desconto</option>
                        <option value="viagem">Viagem</option>
                        <option value="dia-a-dia">Dia a dia</option>
                    </select>
                </div>
                <div class="filter-dropdown">
                    <select id="sortFilter">
                        <option>Ordenar: Mais vendidos</option>
                        <option>Menor preço</option>
                        <option>Maior preço</option>
                        <option>Mais recentes</option>
                        <option>A-Z</option>
                        <option>Z-A</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Produtos -->
    <section class="catalog-products">
        <div class="container">
            <div class="products-header">
                <h2 onclick="clearFilters()" class="clear-filters-btn" title="Clique para remover todos os filtros">Todos os Produtos</h2>
            </div>
            <div class="products-grid-catalog">
                <?php if (empty($todos_produtos)): ?>
                    <div class="no-results-message">
                        <div>
                            <h3>Nenhum produto encontrado</h3>
                            <p>Não há produtos disponíveis no momento.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($todos_produtos as $produto): ?>
                        <?php if ($produto['status'] === 'ativo'): ?>
                        <a href="produto-unico?id=<?php echo $produto['id']; ?>" class="product-card-catalog" data-category="<?php echo htmlspecialchars($produto['category']); ?>" data-price="<?php echo $produto['price']; ?>" data-sales="<?php echo $produto['sales'] ?? 0; ?>" data-discount="<?php echo (isset($produto['discount']) && $produto['discount'] !== null && $produto['discount'] > 0) ? $produto['discount'] : '0'; ?>" data-tags="<?php echo isset($produto['tags']) ? implode(',', $produto['tags']) : ''; ?>">
                            <div class="product-image-catalog">
                                <?php 
                                $primeira_imagem = $produto['images'][0];
                                $caminho_completo = __DIR__ . '/' . $primeira_imagem;
                                $imagem_existe = file_exists($caminho_completo);
                                $imagem_final = $imagem_existe ? $primeira_imagem : 'img/default-product.png';
                                ?>
                                <img src="<?php echo htmlspecialchars($imagem_final); ?>" 
                                     alt="<?php echo htmlspecialchars($produto['title']); ?>" 
                                     loading="lazy"
                                     data-original="<?php echo htmlspecialchars($primeira_imagem); ?>"
                                     data-exists="<?php echo $imagem_existe ? 'true' : 'false'; ?>"
                                     onerror="this.onerror=null; this.src='img/default-product.png'; console.error('❌ Erro ao carregar imagem do produto <?php echo $produto['id']; ?>: <?php echo htmlspecialchars($primeira_imagem); ?>');"
                                     onload="console.log('✅ Imagem carregada do produto <?php echo $produto['id']; ?>: <?php echo htmlspecialchars($imagem_final); ?>');">
                                <?php if (!empty($produto['discount'])): ?>
                                    <div class="discount-badge"><?php echo $produto['discount']; ?>% OFF</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info-catalog">
                                <div class="product-title-container">
                                    <h3><?php echo htmlspecialchars($produto['title']); ?></h3>
                                </div>
                                <div class="product-description-container">
                                    <p><?php echo htmlspecialchars(substr($produto['description'], 0, 50) . '...'); ?></p>
                                </div>
                                <div class="product-category-container">
                                    <div class="product-category"><?php 
                                        $categoryNames = [
                                            'bolsas' => 'Bolsas',
                                            'carteiras' => 'Carteiras', 
                                            'cases-capas' => 'Cases & Capas',
                                            'escritorio' => 'Escritório',
                                            'viagem' => 'Viagem',
                                            'acessorios' => 'Acessórios'
                                        ];
                                        echo htmlspecialchars($categoryNames[$produto['category']] ?? ucfirst($produto['category'])); 
                                    ?></div>
                                </div>
                                <div class="product-price-container">
                                    <div class="price-catalog">
                                        <?php if (!empty($produto['oldPrice'])): ?>
                                            <span class="old-price"><?php echo formatPrice($produto['oldPrice']); ?></span>
                                        <?php endif; ?>
                                        <span class="current-price"><?php echo formatPrice($produto['price']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="icon s/MiaCourolegitimo 1.svg" alt="Mia Couro Legítimo" class="footer-logo">
                    <p>Única, para quem também é</p>
                    <div class="social-links">
                        <a href="#" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.80 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.40s-.644-1.44-1.439-1.44z" />
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
                            <li><a href="produtos?categoria=bolsas">Bolsas</a></li>
                            <li><a href="produtos?categoria=carteiras">Carteiras</a></li>
                            <li><a href="produtos?categoria=cases-capas">Cases & Capas</a></li>
                            <li><a href="produtos?categoria=escritorio">Escritório</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Atendimento</h4>
                        <ul>
                            <li><a href="contato">Contato</a></li>
                            <li><a href="sobre">Sobre Nós</a></li>
                            <li><a href="#">Trocas</a></li>
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
                <p>Desenvolvido por <strong>L&W Digital</strong></p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script src="produtos-script.js"></script>
    <script>
    // Aplicar filtro inicial se necessário (chamado pelo PHP)
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($filtroInicial === 'desconto'): ?>
        setTimeout(() => {
            applySaleFilter();
            console.log('🏷️ Filtro de desconto aplicado automaticamente');
        }, 500);
        <?php endif; ?>
    });
    </script>
</body>

</html>
