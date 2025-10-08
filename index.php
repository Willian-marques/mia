<?php
// Carregar produtos
require_once 'config/produtos.php';

function getFeaturedProducts()
{
    $produtos = loadProductsFromJson();
    $featuredProducts = [];

    foreach ($produtos as $produto) {
        // Verificar se está ativo E em destaque
        $isActive = !isset($produto['status']) || $produto['status'] === 'ativo';
        $isFeatured = isset($produto['isFeatured']) && $produto['isFeatured'] === true;

        if ($isActive && $isFeatured) {
            $featuredProducts[] = $produto;
        }
    }

    // Limita a apenas 4 produtos em destaque
    return array_slice($featuredProducts, 0, 4);
}

$featuredProducts = getFeaturedProducts();

// Carregar dados da seção destacada (Bolsa Sienna)
$secao_destacada = null;
$arquivo_destaque = 'data/produto-destaque.json';
if (file_exists($arquivo_destaque)) {
    $conteudo_destaque = file_get_contents($arquivo_destaque);
    $config_destaque = json_decode($conteudo_destaque, true);

    if ($config_destaque && $config_destaque['ativo'] && !empty($config_destaque['produto_id'])) {
        // Buscar o produto específico para pegar a imagem
        $todos_produtos = getAllProdutos('ativo');
        foreach ($todos_produtos as $produto) {
            if ($produto['id'] == $config_destaque['produto_id']) {
                $secao_destacada = [
                    'titulo' => $config_destaque['titulo'],
                    'descricao' => $config_destaque['descricao'],
                    'imagem' => $produto['images'][0],
                    'produto_id' => $produto['id']
                ];
                break;
            }
        }
    }
}

// Função para obter produtos mais recentes para a seção "All Products"
function getRecentProducts($limit = 5)
{
    $produtos = getAllProdutos('ativo');

    // Ordenar por ID (mais recentes primeiro) ou por data se tiver
    usort($produtos, function ($a, $b) {
        return $b['id'] - $a['id'];  // Ordem decrescente (mais recente primeiro)
    });

    return array_slice($produtos, 0, $limit);
}

$recentProducts = getRecentProducts(4);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mia Couro Legítimo - Produtos Artesanais em Couro Genuíno</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Mia Couro Legítimo - Produtos artesanais em couro genuíno, feitos à mão por artesãos. Bolsas, carteiras, necessaires e acessórios em couro de alta qualidade.">
    <meta name="keywords" content="couro legítimo, produtos artesanais, bolsas de couro, carteiras de couro, necessaire de couro, artesanato em couro, couro genuíno, acessórios de couro, Mia Couro">
    <meta name="author" content="Mia Couro Legítimo">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Portuguese">
    <meta name="revisit-after" content="7 days">
    <meta name="rating" content="general">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://miamianet.com.br/">
    <meta property="og:title" content="Mia Couro Legítimo - Produtos Artesanais em Couro Genuíno">
    <meta property="og:description" content="Produtos artesanais em couro genuíno, feitos à mão por artesãos. Qualidade e exclusividade em cada peça.">
    <meta property="og:image" content="https://miamianet.com.br/img/MiaCourolegitimo 1.svg">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Mia Couro Legítimo">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://miamianet.com.br/">
    <meta name="twitter:title" content="Mia Couro Legítimo - Produtos Artesanais em Couro">
    <meta name="twitter:description" content="Produtos artesanais em couro genuíno, feitos à mão por artesãos. Qualidade e exclusividade.">
    <meta name="twitter:image" content="https://miamianet.com.br/img/MiaCourolegitimo 1.svg">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://miamianet.com.br/">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="icon s/logotipo.svg">
    <link rel="alternate icon" type="image/png" href="icon s/logotipo.svg">
    <link rel="apple-touch-icon" href="icon s/logotipo.svg">
    
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="hero-styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="responsive-global.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
    /* Garantir que o header seja visível - igual página contato */
    .header {
        display: flex !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1000 !important;
    }

    /* Container principal com padding para header fixo */
    * {
        box-sizing: border-box !important;
    }

    html,
    body {
        width: 100% !important;
        max-width: 100vw !important;
        overflow-x: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    }

    body {
        padding-top: 68px;
        /* Apenas altura do header, sem espaço extra */
    }

    /* FORÇAR COR DOS CARDS - PRIORIDADE MÁXIMA */
    .product-card,
    .featured-products .product-card,
    .all-products .product-card,
    div.product-card,
    * .product-card {
        background: #fcf8f1 !important;
        background-color: #fcf8f1 !important;
    }

    .product-info,
    .product-card .product-info,
    .featured-products .product-info,
    .all-products .product-info {
        background: #fcf8f1 !important;
        background-color: #fcf8f1 !important;
    }

    /* Garantir que o hero section cole no header */
    .hero {
        margin-top: 0 !important;
        padding-top: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background: linear-gradient(135deg, rgba(82, 1, 0, 0.3) 0%, rgba(82, 1, 0, 0.1) 100%),
            url('img/hero-new.JPG') center/cover no-repeat !important;
        background-size: cover !important;
        background-position: center !important;
        width: 100% !important;
        height: 100vh !important;
        min-height: 600px !important;
    }

    /* Ajustar layout do hero para colocar botão embaixo */
    .hero-content {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        height: 100% !important;
    }

    .hero-text {
        margin-bottom: auto !important;
    }

    .hero .cta-button {
        margin-top: auto !important;
        margin-bottom: 30px !important;
        align-self: center !important;
    }

    @media (max-width: 768px) {
        .hero .cta-button {
            margin-bottom: 20px !important;
        }
    }

    /* Usar CSS padrão do styles.css - SEM customizações */
    /* Fix para imagens dos produtos em destaquee */
    .featured-products .product-image {
        background: #f5f5f5 !important;
        border: 1px solid #eee !important;
    }

    .featured-products .product-image img {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        image-rendering: auto !important;
    }

    .featured-products .product-image img::before,
    .featured-products .product-image img::after {
        content: none !important;
        display: none !important;
    }

    /* Padronização dos produtos em destaque - tamanhos uniformes */
    .featured-products .products-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 30px !important;
        justify-content: center !important;
        justify-items: center !important;
        align-items: stretch !important;
        max-width: 1300px !important;
        margin: 0 auto !important;
        padding: 0 20px !important;
    }

    .featured-products .product-card {
        width: 100% !important;
        height: 450px !important;
        /* Altura fixa para uniformidade */
        display: flex !important;
        flex-direction: column !important;
        background: #fcf8f1 !important;
        border-radius: 20px !important;
        overflow: hidden !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.3s ease !important;
    }

    .featured-products .product-card:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .featured-products .product-image {
        width: 100% !important;
        height: 250px !important;
        /* Altura fixa para imagens */
        overflow: hidden !important;
        background: #f8f8f8 !important;
    }

    .featured-products .product-info {
        flex: 1 !important;
        padding: 20px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
    }

    .featured-products .product-info h3 {
        font-size: 18px !important;
        font-weight: 600 !important;
        color: #520100 !important;
        margin-bottom: 8px !important;
        line-height: 1.3 !important;
        height: 48px !important;
        /* Altura fixa para títulos */
        overflow: hidden !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
    }

    .featured-products .product-info p {
        font-size: 14px !important;
        color: #666 !important;
        margin-bottom: 12px !important;
        height: 40px !important;
        /* Altura fixa para descrição */
        overflow: hidden !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        line-height: 1.4 !important;
    }

    .featured-products .price {
        margin-top: auto !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }

    .featured-products .old-price {
        font-size: 14px !important;
        color: #999 !important;
        text-decoration: line-through !important;
    }

    .featured-products .new-price,
    .featured-products .current-price {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #520100 !important;
    }

    /* RESET COMPLETO - Seção de produtos dinâmicos */
    .all-products .products-grid-extended {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 20px !important;
        padding: 20px 0 !important;
        max-width: 1200px !important;
        margin: 0 auto !important;
        justify-content: center !important;
        justify-items: center !important;
    }

    .all-products .product-card {
        width: 100% !important;
        height: auto !important;
        background: #fcf8f1 !important;
        border-radius: 15px !important;
        overflow: hidden !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    }

    .all-products .product-card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }

    .all-products .product-image {
        width: 100% !important;
        height: 200px !important;
        overflow: hidden !important;
        background: #fcf8f1 !important;
        display: block !important;
    }

    .all-products .product-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: static !important;
        z-index: 1 !important;
    }

    .all-products .product-info {
        padding: 15px !important;
        background: #fcf8f1 !important;
        display: block !important;
    }

    .all-products .product-info h3 {
        font-size: 18px !important;
        color: #520100 !important;
        margin: 0 0 10px 0 !important;
        font-weight: 600 !important;
    }

    .all-products .product-info p {
        font-size: 14px !important;
        color: #666 !important;
        margin: 0 0 10px 0 !important;
    }

    .all-products .price {
        font-size: 16px !important;
        color: #520100 !important;
        font-weight: bold !important;
        display: block !important;
    }

    /* Responsividade simplificada */
    @media (max-width: 1200px) {
        .all-products .products-grid-extended {
            grid-template-columns: repeat(3, 1fr) !important;
        }
        
        .featured-products .products-grid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 20px !important;
        }
    }

    @media (max-width: 768px) {
        .all-products .products-grid-extended {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
            padding: 20px 15px !important;
        }
        
        .featured-products .products-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
            padding: 0 15px !important;
        }

        .featured-products .product-card {
            height: 420px !important;
            max-width: 100% !important;
        }

        .featured-products .product-image {
            height: 240px !important;
        }

        .featured-products .product-info h3 {
            font-size: 18px !important;
        }

        .featured-products .product-info p {
            font-size: 15px !important;
        }

        .all-products .product-card {
            height: auto !important;
            min-height: 380px !important;
        }

        .all-products .product-image {
            height: 220px !important;
        }

        .all-products .product-info h3 {
            font-size: 17px !important;
        }

        .all-products .product-info p {
            font-size: 14px !important;
        }
    }

    @media (max-width: 480px) {
        .all-products .products-grid-extended {
            grid-template-columns: 1fr !important;
            padding: 20px 15px !important;
            gap: 25px !important;
        }
        
        .featured-products .products-grid {
            grid-template-columns: 1fr !important;
            gap: 25px !important;
            padding: 0 15px !important;
        }

        .featured-products .product-card {
            height: 480px !important;
            max-width: 100% !important;
            margin: 0 auto !important;
        }

        .featured-products .product-image {
            height: 280px !important;
        }

        .featured-products .product-info {
            padding: 20px 15px !important;
        }

        .featured-products .product-info h3 {
            font-size: 20px !important;
            height: auto !important;
            min-height: 50px !important;
        }

        .featured-products .product-info p {
            font-size: 16px !important;
            height: auto !important;
            min-height: 45px !important;
        }

        .featured-products .price {
            margin-top: 15px !important;
        }

        .featured-products .old-price,
        .featured-products .new-price,
        .featured-products .current-price {
            font-size: 20px !important;
        }

        .all-products .product-card {
            height: auto !important;
            min-height: 420px !important;
            max-width: 100% !important;
            margin: 0 auto !important;
        }

        .all-products .product-image {
            height: 260px !important;
        }

        .all-products .product-info {
            padding: 18px 15px !important;
        }

        .all-products .product-info h3 {
            font-size: 19px !important;
        }

        .all-products .product-info p {
            font-size: 15px !important;
        }

        .all-products .price {
            font-size: 18px !important;
        }
    }

    /* Ocultar controles de carousel removidos */
    .carousel-controls {
        display: none !important;
    }

    .carousel-btn {
        display: none !important;
    }

    /* Padronização das categorias - FORÇAR UNIFORMIDADE */
    .categories-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 25px !important;
        padding: 40px 20px !important;
        max-width: 1200px !important;
        margin: 0 auto !important;
        justify-items: stretch !important;
        align-items: stretch !important;
        grid-auto-rows: 1fr !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .category-item {
        background: #FCF8F1 !important;
        border-radius: 20px !important;
        overflow: hidden !important;
        box-shadow: none !important;
        transition: all 0.3s ease !important;
        height: 300px !important;
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        position: relative !important;
        cursor: pointer !important;
    }

    .category-item:hover {
        transform: translateY(-8px) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .category-item img {
        width: 100% !important;
        height: 220px !important;
        object-fit: cover !important;
        object-position: center !important;
        transition: transform 0.3s ease !important;
        display: block !important;
        flex-shrink: 0 !important;
    }

    .category-item:hover img {
        transform: scale(1.05) !important;
    }

    .category-item h3 {
        flex: 1 !important;
        min-height: 80px !important;
        display: flex !important;
        align-items: flex-start !important;
        justify-content: center !important;
        font-size: 18px !important;
        font-weight: 600 !important;
        color: #520100 !important;
        padding: 15px 15px 20px 15px !important;
        text-align: center !important;
        margin: 0 !important;
        background: #FCF8F1 !important;
        line-height: 1.3 !important;
    }

    /* Estilos para links dos cards de categoria */
    a.category-item {
        text-decoration: none !important;
    }

    a.category-item:hover h3 {
        color: #8A4D99 !important;
    }

    a.category-item:active {
        transform: translateY(-5px) scale(0.98) !important;
    }

    /* Responsividade das categorias */
    @media (max-width: 1024px) {
        .categories-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
            padding: 30px 15px !important;
        }

        .category-item {
            height: 280px !important;
        }

        .category-item img {
            height: 200px !important;
        }

        .category-item h3 {
            height: 80px !important;
            font-size: 17px !important;
            padding: 15px 15px 20px 15px !important;
            align-items: flex-start !important;
        }
    }

    @media (max-width: 768px) {
        .categories-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 15px !important;
            padding: 20px 10px !important;
        }

        .category-item {
            height: 250px !important;
        }

        .category-item img {
            height: 170px !important;
        }

        .category-item h3 {
            height: 80px !important;
            font-size: 16px !important;
            padding: 15px 12px 20px 12px !important;
            align-items: flex-start !important;
        }
    }

    @media (max-width: 480px) {
        .categories-grid {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
            padding: 20px 15px !important;
        }

        .category-item {
            height: 220px !important;
        }

        .category-item img {
            height: 140px !important;
        }

        .category-item h3 {
            height: 80px !important;
            font-size: 18px !important;
            padding: 15px 15px 20px 15px !important;
            align-items: flex-start !important;
        }
    }

    /* FORÇA COR DE FUNDO DOS CARDS - REGRAS MAIS ESPECÍFICAS */
    .home-section .product-card,
    .featured-products .product-card,
    .all-products .product-card,
    div.product-card {
        background-color: #fcf8f1 !important;
        background: #fcf8f1 !important;
    }

    /* Garantir que não há sobrescrita */
    * .product-card {
        background: #fcf8f1 !important;
    }

    /* Responsividade das categorias */
    @media (max-width: 1024px) {
        .categories-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px !important;
            padding: 30px 15px !important;
        }

        .category-item {
            height: 280px !important;
        }

        .category-item img {
            height: 200px !important;
        }

        .category-item h3 {
            font-size: 17px !important;
            height: 80px !important;
            padding: 15px !important;
        }
    }

    @media (max-width: 768px) {
        .categories-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 15px !important;
            padding: 20px 10px !important;
        }

        .category-item {
            height: 250px !important;
        }

        .category-item img {
            height: 180px !important;
        }

        .category-item h3 {
            font-size: 16px !important;
            height: 70px !important;
            padding: 12px !important;
        }
    }

    @media (max-width: 480px) {
        .categories-grid {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
            padding: 20px 15px !important;
        }

        .category-item {
            height: 220px !important;
        }

        .category-item img {
            height: 160px !important;
        }

        .category-item h3 {
            font-size: 18px !important;
            height: 60px !important;
            padding: 15px !important;
        }
    }
    </style>
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Mia Couro Legítimo",
      "image": "https://miamianet.com.br/img/MiaCourolegitimo 1.svg",
      "description": "Produtos artesanais em couro genuíno, feitos à mão por artesãos. Bolsas, carteiras, necessaires e acessórios em couro de alta qualidade.",
      "url": "https://miamianet.com.br",
      "telephone": "+55-11-99999-9999",
      "priceRange": "$$",
      "address": {
        "@type": "PostalAddress",
        "addressCountry": "BR",
        "addressLocality": "Brasil"
      },
      "sameAs": [
        "https://www.instagram.com/mia.mianet"
      ],
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday"
        ],
        "opens": "09:00",
        "closes": "18:00"
      }
    }
    </script>
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Mia Couro Legítimo",
      "url": "https://miamianet.com.br",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://miamianet.com.br/produtos?search={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
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
            <a href="index.php" style="cursor: pointer; display: block;">
                <img src="icon s/logotipo.svg" alt="Mia Couro Legítimo">
            </a>
        </div>
        <nav class="nav-menu" id="navMenu">
            <div class="menu-title">Menu</div>
            <a href="index" class="active">Início</a>
            <a href="produtos">Produtos</a>
            <a href="sobre">Sobre nós</a>
            <a href="contato">Contato</a>
            <a href="produtos?filter=desconto" class="sale-link">Sale</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-content">
            <div class="hero-text-wrapper">
                <div class="hero-text">
                    <h1>Mianet</h1>
                    <p>Explore nossa coleção de produtos em couro genuíno, feitos à mão por artesãos.</p>
                    <button class="cta-button" onclick="window.location.href='produtos'">Explorar Produtos</button>
                </div>
            </div>
        </div>
    </section>



    <!-- Produtos em Destaque -->
    <section class="featured-products" id="produtos">
        <div class="container">
            <div class="section-header">
                <h2>Produtos em Destaque</h2>
            </div>
            <div class="products-grid">
                <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $produto): ?>
                <a href="/produto-unico?id=<?php echo $produto['id']; ?>"
                    style="text-decoration: none; color: inherit;">
                    <div class="product-card">
                        <div class="product-image">
                            <?php
                                    $imagePath = $produto['images'][0];
                                    // Verificar se o arquivo existe
                                    if (!file_exists($imagePath)) {
                                        $imagePath = 'img/default-product.png';
                                    }
                                    ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>"
                                alt="<?php echo htmlspecialchars($produto['title']); ?>" loading="lazy"
                                onerror="this.onerror=null; this.src='img/default-product.png'; console.error('Erro ao carregar imagem: <?php echo htmlspecialchars($produto['images'][0]); ?>');">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($produto['title']); ?></h3>
                            <p><?php echo htmlspecialchars($produto['description'] ? substr($produto['description'], 0, 50) . '...' : 'Produto em couro legítimo'); ?>
                            </p>
                            <div class="price">
                                <?php if ($produto['oldPrice']): ?>
                                <span class="old-price">R$
                                    <?php echo number_format($produto['oldPrice'], 2, ',', '.'); ?></span>
                                <span class="new-price">R$
                                    <?php echo number_format($produto['price'], 2, ',', '.'); ?></span>
                                <?php else: ?>
                                <span class="current-price">R$
                                    <?php echo number_format($produto['price'], 2, ',', '.'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
                <?php else: ?>
                <!-- Fallback se não houver produtos em destaque -->
                <div class="no-featured-products">
                    <p>Nenhum produto em destaque no momento.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ($secao_destacada): ?>
    <!-- Bolsa Sienna Section -->
    <section class="sienna-section">
        <div class="container">
            <div class="sienna-content">
                <div class="sienna-image">
                    <img src="<?php echo htmlspecialchars($secao_destacada['imagem']); ?>"
                        alt="<?php echo htmlspecialchars($secao_destacada['titulo']); ?>">
                </div>
                <div class="sienna-text">
                    <h2><?php echo htmlspecialchars($secao_destacada['titulo']); ?></h2>
                    <p><?php echo htmlspecialchars($secao_destacada['descricao']); ?></p>
                    <button class="purchase-button"
                        onclick="window.location.href='produto-unico?id=<?php echo $secao_destacada['produto_id']; ?>'">Compre
                        Já</button>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <div class="categories-grid">
                <a href="produtos?filter=desconto" class="category-item">
                    <img src="img/Carteira_Madrid/carteira_1.JPG" alt="Sale - Produtos com Sale">
                    <h3>SALE</h3>
                </a>
                <a href="produtos?categoria=viagem" class="category-item">
                    <img src="img/Carteira_Madrid/carteira_1.JPG" alt="Viagem - Acessórios para Viajar">
                    <h3>Viagem</h3>
                </a>
                <a href="produtos?categoria=bolsas" class="category-item">
                    <img src="img/Zephira_Preta/bolsa_1.JPG" alt="Bolsas Femininas">
                    <h3>Bolsa</h3>
                </a>
                <a href="produtos?categoria=carteiras" class="category-item">
                    <img src="img/Nece_Preta/nece_1.jpg" alt="Produtos para o Dia a Dia">
                    <h3>Dia a dia</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- All Products Section -->
    <section class="all-products">
        <div class="container">
            <div class="section-header-with-link">
                <h2>Produtos</h2>
                <a href="produtos" class="view-all">Todos os produtos</a>
            </div>
            <div class="products-grid-extended">
                    <?php if (!empty($recentProducts)): ?>
                    <?php foreach ($recentProducts as $produto): ?>
                    <a href="/produto-unico?id=<?php echo $produto['id']; ?>"
                        style="text-decoration: none; color: inherit;">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($produto['images'][0]); ?>"
                                    alt="<?php echo htmlspecialchars($produto['title']); ?>" loading="lazy"
                                    onerror="this.onerror=null; this.src='img/default-product.png'; console.error('Erro ao carregar imagem: <?php echo htmlspecialchars($produto['images'][0]); ?>');">
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($produto['title']); ?></h3>
                                <p><?php echo htmlspecialchars($produto['description'] ? substr($produto['description'], 0, 50) . '...' : 'Produto em couro legítimo artesanal'); ?>
                                </p>
                                <div class="price">
                                    <?php if (isset($produto['oldPrice']) && $produto['oldPrice'] > 0): ?>
                                    <span class="old-price">R$
                                        <?php echo number_format($produto['oldPrice'], 2, ',', '.'); ?></span>
                                    <span class="new-price">R$
                                        <?php echo number_format($produto['price'], 2, ',', '.'); ?></span>
                                    <?php else: ?>
                                    <span class="current-price">R$
                                        <?php echo number_format($produto['price'], 2, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
                        <p>Nenhum produto encontrado.</p>
                    </div>
                    <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Instagram Section -->
    <section class="instagram-section">
        <div class="container">
            <h2>Siga-nos no Instagram</h2>
                <!-- Elfsight Instagram Feed | Untitled Instagram Feed -->
                <script src="https://elfsightcdn.com/platform.js" async></script>
                <div class="elfsight-app-3b2dd460-2d5f-4099-abe7-c67f2c5af34f" data-elfsight-app-lazy></div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="sobre">
        <div class="container">
            <h2>O que nossos clientes dizem</h2>
            <div class="testimonials-grid">
                <?php
                // Carregar avaliações dinâmicas
                $arquivo_avaliacoes = 'data/avaliacoes.json';
                $avaliacoes_ativas = [];

                if (file_exists($arquivo_avaliacoes)) {
                    $conteudo = file_get_contents($arquivo_avaliacoes);
                    $todas_avaliacoes = json_decode($conteudo, true) ?: [];

                    // Filtrar apenas avaliações ativas
                    $avaliacoes_ativas = array_filter($todas_avaliacoes, function ($avaliacao) {
                        return $avaliacao['ativo'] === true;
                    });

                    // Ordenar por ordem definida
                    usort($avaliacoes_ativas, function ($a, $b) {
                        return ($a['ordem'] ?? 999) - ($b['ordem'] ?? 999);
                    });
                }

                if (empty($avaliacoes_ativas)): ?>
                <!-- Fallback caso não haja avaliações -->
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div style="
                                width: 60px; 
                                height: 60px; 
                                border-radius: 50%; 
                                background: #e91e63; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                color: white; 
                                font-weight: bold; 
                                font-size: 22px;
                            ">MIA</div>
                        <div class="testimonial-info">
                            <h4>Equipe MIA</h4>
                            <div class="rating">
                                <span>★★★★★</span>
                            </div>
                        </div>
                    </div>
                    <p>"Trabalhamos com paixão para oferecer os melhores produtos em couro legítimo. Sua satisfação é
                        nossa prioridade!"</p>
                </div>
                <?php else: ?>
                <?php foreach ($avaliacoes_ativas as $avaliacao): ?>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <?php if (($avaliacao['tipo_foto'] ?? 'upload') === 'iniciais'): ?>
                        <!-- Avatar com iniciais -->
                        <div class="testimonial-avatar" style="
                                        width: 60px; 
                                        height: 60px; 
                                        border-radius: 50%; 
                                        background: <?php echo htmlspecialchars($avaliacao['cor_inicial'] ?? '#e91e63'); ?>; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center; 
                                        color: white; 
                                        font-weight: bold; 
                                        font-size: 22px;
                                        border: 2px solid #ddd;
                                    ">
                            <?php echo htmlspecialchars($avaliacao['iniciais'] ?? 'XX'); ?>
                        </div>
                        <?php else: ?>
                        <!-- Foto carregada -->
                        <img src="<?php echo htmlspecialchars($avaliacao['foto']); ?>"
                            alt="<?php echo htmlspecialchars($avaliacao['nome']); ?>" class="testimonial-avatar">
                        <?php endif; ?>

                        <div class="testimonial-info">
                            <h4><?php echo htmlspecialchars($avaliacao['nome']); ?></h4>
                            <div class="rating">
                                <span>
                                    <?php
                                            $estrelas = intval($avaliacao['estrelas'] ?? 5);
                                            echo str_repeat('★', $estrelas) . str_repeat('☆', 5 - $estrelas);
                                            ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <p>"<?php echo htmlspecialchars($avaliacao['avaliacao']); ?>"</p>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
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
                        <a href="https://wa.me/554197338289" target="_blank" aria-label="WhatsApp"
                            title="+55 41 9733-8289">
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
                            <li><a href="produtos?categoria=viagem">Viagem</a></li>
                            <li><a href="produtos?categoria=carteiras">Dia a Dia</a></li>
                            <li><a href="produtos?categoria=bolsas">Bolsa</a></li>
                            <li><a href="produtos?filter=desconto">Sale</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Atendimento</h4>
                        <ul>
                            <li><a href="#">Contato</a></li>
                            <li><a href="#">Sobre Nós</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Contato</h4>
                        <ul>
                            <li>contato@mia.com.br</li>
                            <li><a href="https://wa.me/554197338289" target="_blank"
                                    style="color: #9CA3AF; text-decoration: none;">+55 (41) 9733-8289</a></li>
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

    <script>
    // Menu suave - Versão simplificada e funcional
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script carregado!'); // Debug

        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');

        console.log('MenuToggle encontrado:', menuToggle); // Debug
        console.log('NavMenu encontrado:', navMenu); // Debug

        if (menuToggle && navMenu) {
            // Estado inicial do menu
            navMenu.style.display = 'none';

            // Criar backdrop
            let backdrop = document.createElement('div');
            backdrop.id = 'menu-backdrop';
            backdrop.style.cssText = `
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(3px);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.4s ease;
            `;
            document.body.appendChild(backdrop);

            // Função para fechar o menu
            function closeMenu() {
                console.log('Fechando menu...'); // Debug
                menuToggle.classList.remove('active');
                navMenu.classList.remove('active');
                backdrop.style.opacity = '0';
                
                setTimeout(() => {
                    navMenu.style.display = 'none';
                    backdrop.style.display = 'none';
                }, 400);
            }

            // Event listener para o botão hambúrguer
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log('Clique no menu detectado!'); // Debug

                const isActive = navMenu.classList.contains('active');
                console.log('Menu está ativo:', isActive); // Debug

                if (!isActive) {
                    // Abrir menu
                    console.log('Abrindo menu...'); // Debug
                    navMenu.style.display = 'flex';
                    backdrop.style.display = 'block';

                    setTimeout(() => {
                        menuToggle.classList.add('active');
                        navMenu.classList.add('active');
                        backdrop.style.opacity = '1';

                        // Forçar visibilidade dos elementos internos
                        const menuTitle = navMenu.querySelector('.menu-title');
                        const menuLinks = navMenu.querySelectorAll('a');

                        if (menuTitle) menuTitle.style.opacity = '1';
                        menuLinks.forEach(link => {
                            link.style.opacity = '1';
                            link.style.visibility = 'visible';
                        });
                    }, 10);

                } else {
                    // Fechar menu  
                    closeMenu();
                }
            });

            // Fechar menu ao clicar no backdrop
            backdrop.addEventListener('click', function() {
                console.log('Clique no backdrop, fechando menu...'); // Debug
                closeMenu();
            });

            // Fechar menu ao clicar nos links
            const navLinks = navMenu.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    console.log('Link clicado, fechando menu...'); // Debug
                    closeMenu();
                });
            });

            // Fechar menu ao clicar fora
            document.addEventListener('click', function(e) {
                if (!menuToggle.contains(e.target) && !navMenu.contains(e.target) && !backdrop.contains(e.target) && navMenu.classList
                    .contains('active')) {
                    console.log('Clique fora detectado, fechando menu...'); // Debug
                    closeMenu();
                }
            });

            // Fechar menu com tecla ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                    console.log('ESC pressionado, fechando menu...'); // Debug
                    closeMenu();
                }
            });

        } else {
            console.error('Elementos do menu não encontrados!');
        }
    });

    // Carregar script original para outras funcionalidades
    const script = document.createElement('script');
    script.src = 'script.js';
    script.onload = function() {
        console.log('Script.js carregado como backup');
    };
    document.head.appendChild(script);
    </script>
</body>

</html>