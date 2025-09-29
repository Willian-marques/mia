<?php
// Incluir base de dados de produtos
require_once 'config/produtos.php';

// Obter produto pela URL (ID ou slug)
$produtoId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$produtoSlug = isset($_GET['produto']) ? $_GET['produto'] : null;

$product = null;
if ($produtoId) {
    $product = getProdutoById($produtoId);
} elseif ($produtoSlug) {
    $product = getProdutoBySlug($produtoSlug);
}

// Se não encontrou produto, redirecionar
if (!$product) {
    header('Location: produtos');
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
            <a href="./">Início</a>
            <a href="produtos">Produtos</a>
            <a href="sobre">Sobre nós</a>
            <a href="contato">Contato</a>
            <a href="produtos?filter=desconto" class="sale-link">Sale</a>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <a href="./">Início</a>
            <span class="separator">></span>
            <a href="produtos"><?php echo htmlspecialchars($product['category']); ?></a>
            <span class="separator">></span>
            <span><?php echo htmlspecialchars($product['title']); ?></span>
        </div>
    </div>

    <!-- Produto Principal -->
    <section class="product-detail" style="background: #FCF8F1; padding: 56px 0;">
        <div style="display: flex; gap: 145px; max-width: none; margin: 0; padding: 0 223px;">
                <!-- Imagens do Produto -->
                <div class="product-images">
                    <div class="main-image">
                        <img src="<?php echo htmlspecialchars($product['images'][0]); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" id="mainImage">
                    </div>
                    <div class="thumbnail-images">
                        <?php foreach ($product['images'] as $index => $image): ?>
                            <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage('<?php echo htmlspecialchars($image); ?>', this)">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Imagem <?php echo $index + 1; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Informações do Produto -->
                <div class="product-info" style="width: 544px;">
                    <h1 style="color: #520100; font-size: 48px; font-weight: 400; line-height: 56px; margin-bottom: 16px;"><?php echo htmlspecialchars($product['title']); ?></h1>
                    <div style="color: #8A4D99; font-size: 18px; font-weight: 400; margin-bottom: 32px;">Em estoque</div>
                    
                    <div class="product-price" style="display: flex; align-items: center; gap: 16px; margin-bottom: 40px;">
                        <span style="color: #8A4D99; font-size: 48px; font-weight: 700;"><?php echo formatPrice($product['price']); ?></span>
                        <?php if ($product['oldPrice']): ?>
                            <span style="color: #520100; font-size: 24px; font-weight: 400; text-decoration: line-through;"><?php echo formatPrice($product['oldPrice']); ?></span>
                            <span style="background: #8A4D99; color: white; padding: 8px 16px; border-radius: 20px; font-size: 16px; font-weight: 600;"><?php echo $product['discount']; ?>% OFF</span>
                        <?php endif; ?>
                    </div>

                    <?php if (count($product['colors']) > 1): ?>
                    <div class="color-selector" style="margin-bottom: 32px;">
                        <label style="color: #262523; font-size: 18px; font-weight: 500; display: block; margin-bottom: 16px;">Cor:</label>
                        <div class="color-options" style="display: flex; gap: 12px;">
                            <?php foreach ($product['colors'] as $index => $color): ?>
                                <div class="color-option <?php echo $index === 0 ? 'selected' : ''; ?>" 
                                     style="width: 40px; height: 40px; border-radius: 50%; cursor: pointer; border: 3px solid <?php echo $index === 0 ? '#520100' : 'transparent'; ?>; background-color: <?php echo $color['color']; ?>;" 
                                     data-color="<?php echo $color['name']; ?>" 
                                     title="<?php echo $color['title']; ?>"
                                     onclick="selectColor(this)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (count($product['sizes']) > 1): ?>
                    <div class="size-selector" style="margin-bottom: 40px;">
                        <label style="color: #262523; font-size: 18px; font-weight: 500; display: block; margin-bottom: 16px;">Tamanho:</label>
                        <div class="size-options" style="display: flex; gap: 12px;">
                            <?php foreach ($product['sizes'] as $index => $size): ?>
                                <button class="size-option <?php echo $index === 0 ? 'selected' : ''; ?>" 
                                        style="padding: 12px 24px; border: 2px solid <?php echo $index === 0 ? '#520100' : '#E5E7EB'; ?>; background: <?php echo $index === 0 ? '#520100' : 'white'; ?>; color: <?php echo $index === 0 ? 'white' : '#262523'; ?>; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 500;" 
                                        data-size="<?php echo strtolower($size); ?>"
                                        onclick="selectSize(this)">
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
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516"/>
                        </svg>
                        Encaminhar para WhatsApp
                    </button>
                </div>
        </div>
    </section>

    <!-- Abas de Descrição -->
    <section style="width: 100%; background: #FCF8F1;">
        <div style="display: flex; border-bottom: 1px solid #E5E7EB;">
            <div style="width: 230px; height: 54px; background: transparent; border-bottom: 2px solid #520100; display: flex; align-items: center; justify-content: center;">
                <span style="color: #520100; font-size: 24px; font-weight: 500;">Descrição</span>
            </div>
        </div>
        <div style="padding: 81px 56px; border: 1px solid #E5E7EB; border-top: none; min-height: 690px;">
            <div style="color: black; font-size: 18px; font-weight: 400; line-height: 26px; text-align: justify; margin-bottom: 32px;">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
            
            <div style="color: black; font-size: 18px; font-weight: 400; line-height: 26px; text-align: justify;">
                <strong>Especificações:</strong><br/>
                <?php 
                $specs = explode('|', $product['specifications']);
                foreach ($specs as $spec): ?>
                    • <?php echo htmlspecialchars($spec); ?><br/>
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
                <a href="produto-unico.php?id=<?php echo $prod['id']; ?>" class="related-card">
                    <img src="<?php echo htmlspecialchars($prod['images'][0]); ?>" alt="<?php echo htmlspecialchars($prod['title']); ?>">
                    <div class="related-card-content">
                        <h3><?php echo htmlspecialchars($prod['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($prod['description'], 0, 80)); ?>...</p>
                        <div class="price-section">
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
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="icon s/MiaCourolegitimo 1.svg" alt="MIA" class="footer-logo">
                    <p>Perfeita para aqueles que também são.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="WhatsApp">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516"/>
                            </svg>
                        </a>
                        <a href="#" aria-label="LinkedIn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <div class="link-group">
                        <h4>Produtos</h4>
                        <?php foreach (getAllProdutos() as $prod): ?>
                            <a href="produto-unico.php?id=<?php echo $prod['id']; ?>"><?php echo htmlspecialchars($prod['title']); ?></a>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="link-group">
                        <h4>Atendimento</h4>
                        <a href="contato">Contato</a>
                        <a href="sobre">Sobre Nós</a>
                        <a href="#">Trocas</a>
                    </div>
                    
                    <div class="link-group">
                        <h4>Contato</h4>
                        <p>contato@mia.com.br</p>
                        <p>+55 (41) 9999-9999</p>
                        <p>Curitiba, PR</p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2025 Mia. Todos os direitos reservados.</p>
                <p>Desenvolvido por <strong>L&W Digital</strong></p>
            </div>
        </div>
    </footer>

    <script>
        // Funções de interação
        function changeImage(src, thumbnailElement) {
            document.getElementById('mainImage').src = src;
            
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            
            thumbnailElement.classList.add('active');
        }

        function selectColor(element) {
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.style.border = '3px solid transparent';
            });
            element.classList.add('selected');
            element.style.border = '3px solid #520100';
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
