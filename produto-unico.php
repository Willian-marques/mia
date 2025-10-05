<?php
// Incluir base de dados de produtos
require_once 'config/produtos.php';

// Obter produto pela URL (ID ou slug)
$produtoId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$produtoSlug = isset($_GET['produto']) ? $_GET['produto'] : null;

$product = null;
if ($produtoId) {
    $product = getProdutoById($produtoId);
} elseif ($produtoSlug) {
    $product = getProdutoBySlug($produtoSlug);
}

// Se não encontrou produto, redirecionar
if (!$product) {
    header('Location: produtos.php');
    exit;
}

// Obter produtos relacionados (mesma categoria, exceto o atual)
$produtosRelacionados = [];
foreach (getAllProdutos('ativo') as $prod) {
    if ($prod['id'] !== $product['id'] && $prod['category'] === $product['category']) {
        $produtosRelacionados[] = $prod;
    }
}

// Se não há produtos da mesma categoria, pegar produtos aleatórios
if (empty($produtosRelacionados)) {
    foreach (getAllProdutos('ativo') as $prod) {
        if ($prod['id'] !== $product['id']) {
            $produtosRelacionados[] = $prod;
        }
    }
}

// Limitar a 4 produtos relacionados
$produtosRelacionados = array_slice($produtosRelacionados, 0, 4);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - MIA</title>
    <link rel="stylesheet" href="styles.css?v=20250924">
    <link rel="stylesheet" href="produto-premium-styles.css?v=20250924">
    <link rel="stylesheet" href="image-optimize.css?v=20250924">
    <link rel="stylesheet" href="product-card-universal.css?v=20250924">
    <link rel="stylesheet" href="responsive-global.css?v=20250924">
    <style>
    /* Remove sublinhados grossos do menu */
    .nav-menu a {
        text-decoration: none !important;
        border-bottom: none !important;
    }

    /* Garante que o pseudo-elemento seja fino */
    .nav-menu a::after {
        height: 1px !important;
    }

    /* Remove qualquer sublinhado do browser */
    .nav-menu a:link,
    .nav-menu a:visited,
    .nav-menu a:hover,
    .nav-menu a:active {
        text-decoration: none !important;
        border-bottom: none !important;
    }

    /* CSS para galeria de imagens do produto */
    .product-images {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 600px;
    }

    .main-image {
        width: 100%;
        height: 600px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .thumbnail-images {
        display: flex;
        gap: 15px;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
        opacity: 0.7;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thumbnail:hover {
        opacity: 1;
        border-color: #8A4D99;
        transform: scale(1.05);
    }

    .thumbnail.active {
        border-color: #520100;
        opacity: 1;
        box-shadow: 0 2px 10px rgba(82, 1, 0, 0.3);
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Responsividade para galeria */
    @media (max-width: 1400px) {
        .product-detail>div {
            gap: 80px !important;
            padding: 0 60px !important;
        }
    }

    @media (max-width: 1200px) {
        .product-detail>div {
            gap: 60px !important;
            padding: 0 40px !important;
        }
    }

    @media (max-width: 992px) {
        .product-detail>div {
            flex-direction: column !important;
            gap: 40px !important;
            padding: 0 30px !important;
        }

        .product-images {
            width: 100% !important;
        }

        .product-info {
            width: 100% !important;
        }
    }

    @media (max-width: 768px) {
        .product-detail {
            padding: 30px 0 !important;
            margin-top: 30px !important;
        }

        .product-detail>div {
            padding: 0 20px !important;
            gap: 30px !important;
        }

        .product-images {
            width: 100%;
        }

        .main-image {
            height: 400px;
            background: white;
        }

        .main-image img {
            object-fit: cover;
        }

        .thumbnail {
            width: 60px;
            height: 60px;
            background: white;
        }

        .thumbnail img {
            object-fit: cover;
        }

        .product-info h1 {
            font-size: 32px !important;
            line-height: 40px !important;
        }

        .product-price span:first-child {
            font-size: 36px !important;
        }

        .product-price span:nth-child(2) {
            font-size: 20px !important;
        }

        .product-price span:nth-child(3) {
            font-size: 14px !important;
            padding: 6px 12px !important;
        }

        .color-selector label,
        .size-selector label {
            font-size: 16px !important;
        }

        .color-option {
            width: 36px !important;
            height: 36px !important;
        }

        .size-option {
            padding: 10px 20px !important;
            font-size: 14px !important;
        }

        .whatsapp-btn {
            font-size: 16px !important;
            padding: 14px 20px !important;
        }
    }

    @media (max-width: 480px) {
        .product-detail {
            padding: 20px 0 !important;
            margin-top: 20px !important;
        }

        .product-detail>div {
            padding: 0 15px !important;
            gap: 20px !important;
        }

        .main-image {
            height: 300px;
            border-radius: 12px;
            background: white;
        }

        .main-image img {
            object-fit: contain;
        }

        .thumbnail {
            width: 50px;
            height: 50px;
            background: white;
        }

        .thumbnail img {
            object-fit: contain;
        }

        .thumbnail-images {
            gap: 10px;
            flex-wrap: wrap;
        }

        .product-info h1 {
            font-size: 24px !important;
            line-height: 32px !important;
            margin-bottom: 12px !important;
        }

        .product-info>div:nth-child(2) {
            font-size: 16px !important;
            margin-bottom: 24px !important;
        }

        .product-price {
            flex-wrap: wrap;
            margin-bottom: 30px !important;
        }

        .product-price span:first-child {
            font-size: 32px !important;
        }

        .product-price span:nth-child(2) {
            font-size: 18px !important;
        }

        .product-price span:nth-child(3) {
            font-size: 12px !important;
            padding: 4px 10px !important;
        }

        .color-selector,
        .size-selector {
            margin-bottom: 24px !important;
        }

        .color-selector label,
        .size-selector label {
            font-size: 14px !important;
            margin-bottom: 12px !important;
        }

        .color-option {
            width: 32px !important;
            height: 32px !important;
        }

        .size-option {
            padding: 8px 16px !important;
            font-size: 13px !important;
        }

        .whatsapp-btn {
            font-size: 15px !important;
            padding: 12px 16px !important;
        }

        .whatsapp-btn svg {
            width: 20px;
            height: 20px;
        }
    }

    /* CSS para seção de descrição dinâmica */
    .description-section {
        width: 100%;
        background: #FCF8F1;
    }

    .description-tab {
        width: 230px;
        height: 54px;
        background: transparent;
        border-bottom: 2px solid #520100;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .description-content {
        padding: 60px 56px;
        border: 1px solid #E5E7EB;
        border-top: none;
        min-height: auto;
    }

    .description-text {
        color: black;
        font-size: 18px;
        font-weight: 400;
        line-height: 26px;
        text-align: justify;
        margin-bottom: 32px;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .specifications-text {
        color: black;
        font-size: 18px;
        font-weight: 400;
        line-height: 26px;
        text-align: justify;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Responsividade para descrição */
    @media (max-width: 768px) {
        .description-tab {
            width: 180px;
            height: 48px;
        }

        .description-tab span {
            font-size: 20px !important;
        }

        .description-content {
            padding: 40px 30px;
        }

        .description-text,
        .specifications-text {
            font-size: 16px;
            line-height: 24px;
        }
    }

    @media (max-width: 480px) {
        .description-tab {
            width: 150px;
            height: 44px;
        }

        .description-tab span {
            font-size: 18px !important;
        }

        .description-content {
            padding: 30px 20px;
        }

        .description-text,
        .specifications-text {
            font-size: 15px;
            line-height: 22px;
        }
    }

    /* CSS para produtos relacionados - baseado no backup */
    .related-products {
        width: 100%;
        background: #FCF8F1;
        padding: 40px 0 80px;
    }

    .related-products h2 {
        font-size: 36px;
        font-weight: 400;
        color: #520100;
        text-align: center;
        margin-bottom: 60px;
    }

    .related-grid {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 70px;
        flex-wrap: wrap;
        flex-direction: row;
        padding: 0 121px;
    }

    .related-card {
        width: 286px;
        background: #FCF8F1;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        will-change: transform;
        transform: translateZ(0);
        text-decoration: none;
        color: inherit;
    }

    .related-card:hover {
        transform: translateY(-10px);
        box-shadow: 0px 12px 20px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        color: inherit;
    }

    .related-card .product-image {
        width: 100%;
        height: 283px;
        overflow: hidden;
    }

    .related-card .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        will-change: transform;
        transform: translateZ(0);
    }

    .related-card:hover .product-image img {
        transform: scale(1.05);
    }

    .related-card .related-card-content {
        padding: 14px 8px 20px;
    }

    .related-card .related-card-content h3 {
        font-size: 24px;
        font-weight: 500;
        color: #520100;
        margin-bottom: 8px;
    }

    .related-card .related-card-content p {
        font-size: 18px;
        font-weight: 400;
        color: #4B5563;
        margin-bottom: 12px;
    }

    .related-card .price-section {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .related-card .product-info {
        padding: 14px 8px 20px;
    }

    .related-card .product-info h3 {
        font-size: 24px;
        font-weight: 500;
        color: #520100;
        margin-bottom: 8px;
    }

    .related-card .product-info p {
        font-size: 18px;
        font-weight: 400;
        color: #4B5563;
        margin-bottom: 12px;
    }

    .related-card .price {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .related-card .old-price {
        font-size: 18px;
        font-weight: 700;
        color: #520100;
        text-decoration: line-through;
    }

    .related-card .new-price {
        font-size: 18px;
        font-weight: 700;
        color: #8A4D99;
    }

    .related-card .current-price {
        font-size: 18px;
        font-weight: 700;
        color: #520100;
    }

    /* Responsividade para produtos relacionados */
    @media (max-width: 1200px) {
        .related-grid {
            gap: 60px;
        }
    }

    @media (max-width: 768px) {
        .related-grid {
            flex-direction: row !important;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            padding: 0 20px;
        }

        .related-card {
            width: calc(50% - 10px);
            min-width: 280px;
            max-width: 320px;
        }
    }

    @media (max-width: 480px) {
        .related-grid {
            gap: 15px;
            padding: 0 15px;
            flex-direction: row !important;
        }

        .related-card {
            width: calc(50% - 7.5px);
            min-width: 140px;
        }
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
            <a href="produtos?filter=desconto" class="sale-link">Desconto</a>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <a href="index">Início</a>
            <span class="separator">></span>
            <a href="produtos"><?php echo htmlspecialchars($product['category']); ?></a>
            <span class="separator">></span>
            <span><?php echo htmlspecialchars($product['title']); ?></span>
        </div>
    </div>

    <!-- Produto Principal -->
    <section class="product-detail" style="background: #FCF8F1; padding: 56px 0; margin-top: 40px;">
        <div style="display: flex; gap: 145px; max-width: none; margin: 0; padding: 0 223px;">
            <!-- Imagens do Produto -->
            <div class="product-images">
                <div class="main-image">
                    <?php
                    // Se o produto tem colorImages, usar a primeira cor disponível
                    $hasColorImages = isset($product['colorImages']) && !empty($product['colorImages']);
                    $initialImages = $hasColorImages 
                        ? reset($product['colorImages']) 
                        : $product['images'];
                    ?>
                    <img src="<?php echo htmlspecialchars($initialImages[0]); ?>"
                        alt="<?php echo htmlspecialchars($product['title']); ?>" id="mainImage">
                </div>
                <div class="thumbnail-images" id="thumbnailContainer">
                    <?php foreach ($initialImages as $index => $image): ?>
                    <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                        onclick="changeImage('<?php echo addslashes(htmlspecialchars($image)); ?>', this)"
                        data-image="<?php echo htmlspecialchars($image); ?>">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Imagem <?php echo $index + 1; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Informações do Produto -->
            <div class="product-info" style="width: 544px;">
                <h1 style="color: #520100; font-size: 48px; font-weight: 400; line-height: 56px; margin-bottom: 16px;">
                    <?php echo htmlspecialchars($product['title']); ?>
                </h1>
                <div style="color: #8A4D99; font-size: 18px; font-weight: 400; margin-bottom: 32px;">Em estoque</div>

                <div class="product-price" style="display: flex; align-items: center; gap: 16px; margin-bottom: 40px;">
                    <span
                        style="color: #8A4D99; font-size: 48px; font-weight: 700;"><?php echo formatPrice($product['price']); ?></span>
                    <?php if ($product['oldPrice']): ?>
                    <span
                        style="color: #520100; font-size: 24px; font-weight: 400; text-decoration: line-through;"><?php echo formatPrice($product['oldPrice']); ?></span>
                    <span
                        style="background: #8A4D99; color: white; padding: 8px 16px; border-radius: 20px; font-size: 16px; font-weight: 600;"><?php echo $product['discount']; ?>%
                        OFF</span>
                    <?php endif; ?>
                </div>

                <?php if (count($product['colors']) > 1): ?>
                <div class="color-selector" style="margin-bottom: 32px;">
                    <label
                        style="color: #262523; font-size: 18px; font-weight: 500; display: block; margin-bottom: 16px;">Cor:</label>
                    <div class="color-options" style="display: flex; gap: 12px;">
                        <?php foreach ($product['colors'] as $index => $color): ?>
                        <div class="color-option <?php echo $index === 0 ? 'selected' : ''; ?>"
                            style="width: 40px; height: 40px; border-radius: 50%; cursor: pointer; border: 3px solid <?php echo $index === 0 ? '#520100' : 'transparent'; ?>; background-color: <?php echo $color['color']; ?>;"
                            data-color="<?php echo $color['name']; ?>" title="<?php echo $color['title']; ?>"
                            onclick="selectColor(this, '<?php echo $color['name']; ?>')">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (count($product['sizes']) > 1): ?>
                <div class="size-selector" style="margin-bottom: 40px;">
                    <label
                        style="color: #262523; font-size: 18px; font-weight: 500; display: block; margin-bottom: 16px;">Tamanho:</label>
                    <div class="size-options" style="display: flex; gap: 12px;">
                        <?php foreach ($product['sizes'] as $index => $size): ?>
                        <button class="size-option <?php echo $index === 0 ? 'selected' : ''; ?>"
                            style="padding: 12px 24px; border: 2px solid <?php echo $index === 0 ? '#520100' : '#E5E7EB'; ?>; background: <?php echo $index === 0 ? '#520100' : 'white'; ?>; color: <?php echo $index === 0 ? 'white' : '#262523'; ?>; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 500;"
                            data-size="<?php echo strtolower($size); ?>" onclick="selectSize(this)">
                            <?php echo htmlspecialchars($size); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <button class="whatsapp-btn"
                    style="width: 100%; background: #25D366; color: white; border: none; padding: 16px 24px; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px;"
                    onclick="sendToWhatsApp()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516" />
                    </svg>
                    Encaminhar para WhatsApp
                </button>
            </div>
        </div>
    </section>

    <!-- Abas de Descrição -->
    <section class="description-section">
        <div style="display: flex; border-bottom: 1px solid #E5E7EB;">
            <div class="description-tab">
                <span style="color: #520100; font-size: 24px; font-weight: 500;">Descrição</span>
            </div>
        </div>
        <div class="description-content">
            <div class="description-text">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>

            <div class="specifications-text">
                <strong>Especificações:</strong><br />
                <?php
                $specs = explode('|', $product['specifications']);
                foreach ($specs as $spec): ?>
                • <?php echo htmlspecialchars($spec); ?><br />
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Produtos Relacionados -->
    <?php if (!empty($produtosRelacionados)): ?>
    <section class="related-products">
        <h2>Outros Produtos</h2>
        <div class="related-grid">
            <?php foreach ($produtosRelacionados as $prod): ?>
            <a href="/produto-unico.php?id=<?php echo $prod['id']; ?>" class="related-card">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($prod['images'][0]); ?>"
                        alt="<?php echo htmlspecialchars($prod['title']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($prod['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($prod['description'], 0, 80)); ?>...</p>
                    <div class="price">
                        <?php if ($prod['oldPrice']): ?>
                        <span class="old-price"><?php echo formatPrice($prod['oldPrice']); ?></span>
                        <?php endif; ?>
                        <span class="current-price"><?php echo formatPrice($prod['price']); ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer" id="contato">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="icon s/MiaCourolegitimo 1.svg" alt="Mia Couro Legítimo" class="footer-logo">
                    <p>Única, para quem também é</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/mia.mianet" target="_blank" aria-label="Instagram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a href="https://wa.me/5541973382889" target="_blank" aria-label="WhatsApp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Produtos</h4>
                        <ul>
                            <li><a href="produtos">Todos os Produtos</a></li>
                            <li><a href="produtos?categoria=viagem">Viagem</a></li>
                            <li><a href="produtos?categoria=carteiras">Dia a Dia</a></li>
                            <li><a href="produtos?categoria=bolsas">Bolsa</a></li>
                            <li><a href="produtos?filter=desconto">Desconto</a></li>
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
                            <li><a href="https://wa.me/5541973382289" target="_blank"
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
    // Dados do produto em JSON
    const productData = <?php echo json_encode([
            'colorImages' => isset($product['colorImages']) ? $product['colorImages'] : null,
            'images' => $product['images'],
            'colors' => $product['colors']
        ]); ?>;

    const hasColorImages = productData.colorImages !== null && Object.keys(productData.colorImages).length > 0;

    // Funções de interação
    function changeImage(src, thumbnailElement) {
        console.log('Mudando imagem para:', src);
        const mainImage = document.getElementById('mainImage');
        if (mainImage) {
            mainImage.src = src;
        } else {
            console.error('Elemento mainImage não encontrado');
        }

        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });

        thumbnailElement.classList.add('active');
    }

    // Inicializar carrossel quando DOM carregar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado, inicializando carrossel');
        console.log('Has color images:', hasColorImages);
        console.log('Product data:', productData);

        // Adicionar event listeners para thumbnails
        document.querySelectorAll('.thumbnail').forEach((thumbnail, index) => {
            thumbnail.addEventListener('click', function() {
                const imageSrc = this.getAttribute('data-image');
                changeImage(imageSrc, this);
            });
        });
    });

    function selectColor(element, colorName) {
        // Atualizar visual das opções de cor
        document.querySelectorAll('.color-option').forEach(opt => {
            opt.classList.remove('selected');
            opt.style.border = '3px solid transparent';
        });
        element.classList.add('selected');
        element.style.border = '3px solid #520100';

        // Se o produto tem imagens por cor, trocar as imagens
        if (hasColorImages && productData.colorImages[colorName]) {
            const colorImages = productData.colorImages[colorName];
            const thumbnailContainer = document.getElementById('thumbnailContainer');
            const mainImage = document.getElementById('mainImage');

            // Atualizar imagem principal
            if (colorImages.length > 0) {
                mainImage.src = colorImages[0];
            }

            // Atualizar miniaturas
            thumbnailContainer.innerHTML = '';
            colorImages.forEach((image, index) => {
                const thumbnailDiv = document.createElement('div');
                thumbnailDiv.className = 'thumbnail' + (index === 0 ? ' active' : '');
                thumbnailDiv.setAttribute('data-image', image);
                thumbnailDiv.onclick = function() {
                    changeImage(image, this);
                };

                const img = document.createElement('img');
                img.src = image;
                img.alt = 'Imagem ' + (index + 1);

                thumbnailDiv.appendChild(img);
                thumbnailContainer.appendChild(thumbnailDiv);
            });
        }
    }

    function selectSize(element) {
        document.querySelectorAll('.size-option').forEach(opt => {
            opt.classList.remove('selected');
            opt.style.border = '2px solid #E5E7EB';
            opt.style.background = 'white';
            opt.style.color = '#262523';
        });
        element.classList.add('selected');
        element.style.border = '2px solid #520100';
        element.style.background = '#520100';
        element.style.color = 'white';
    }

    function sendToWhatsApp() {
        const productName = '<?php echo addslashes($product['title']); ?>';
        const price = '<?php echo addslashes(formatPrice($product['price'])); ?>';

        const colorElement = document.querySelector('.color-option.selected');
        const sizeElement = document.querySelector('.size-option.selected');

        const color = colorElement ? colorElement.getAttribute('data-color') : 'padrão';
        const size = sizeElement ? sizeElement.getAttribute('data-size') : 'único';

        const message = `Olá! Tenho interesse no(a) ${productName} por ${price}. Cor: ${color}, Tamanho: ${size}.`;
        const whatsappUrl = `https://wa.me/5541973382889?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    }
    </script>

    <script src="script.js"></script>
</body>

</html>