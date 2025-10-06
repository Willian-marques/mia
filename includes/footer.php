<?php
// Usar configurações globais
$contact_info = [
    'whatsapp' => CONTACT_WHATSAPP,
    'email' => CONTACT_EMAIL,
    'instagram' => CONTACT_INSTAGRAM,
    'address' => CONTACT_ADDRESS
];

// Links rápidos
$quick_links = [
    ['label' => 'Início', 'url' => 'index.php'],
    ['label' => 'Produtos', 'url' => 'produtos.php'],
    ['label' => 'Sobre Nós', 'url' => '#sobre'],
    ['label' => 'Contato', 'url' => '#contato']
];

// Categorias de produtos
$product_categories = [
    ['label' => 'Bolsas', 'url' => 'produtos.php?categoria=bolsas'],
    ['label' => 'Carteiras', 'url' => 'produtos.php?categoria=carteiras'],
    ['label' => 'Acessórios', 'url' => 'produtos.php?categoria=acessorios'],
    ['label' => 'Cases', 'url' => 'produtos.php?categoria=cases']
];
?>

<footer class="site-footer" id="contato">
    <div class="footer-container">
        <!-- Logo e Descrição -->
        <div class="footer-section">
            <div class="footer-logo">
                <img src="img/MiaCourolegitimo 1.svg" alt="Mia Couro Legítimo">
                <h3>Mia Couro Legítimo</h3>
            </div>
            <p class="footer-description">
                Produtos artesanais em couro genuíno, feitos com amor e tradição. 
                Qualidade e elegância em cada detalhe.
            </p>
            <div class="social-media">
                <a href="<?php echo $contact_info['instagram']; ?>" target="_blank" class="social-link">
                    <img src="icon s/instagram.svg" alt="Instagram">
                </a>
            </div>
        </div>

        <!-- Links Rápidos -->
        <div class="footer-section">
            <h4>Links Rápidos</h4>
            <ul class="footer-links">
                <?php foreach ($quick_links as $link): ?>
                    <li><a href="<?php echo $link['url']; ?>"><?php echo $link['label']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Categorias -->
        <div class="footer-section">
            <h4>Categorias</h4>
            <ul class="footer-links">
                <?php foreach ($product_categories as $category): ?>
                    <li><a href="<?php echo $category['url']; ?>"><?php echo $category['label']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Contato -->
        <div class="footer-section">
            <h4>Contato</h4>
            <div class="contact-info">
                <div class="contact-item">
                    <strong>WhatsApp:</strong>
                    <a href="https://wa.me/<?php echo $contact_info['whatsapp']; ?>" target="_blank">
                        +55 41 9733-8289
                    </a>
                </div>
                <div class="contact-item">
                    <strong>E-mail:</strong>
                    <a href="mailto:<?php echo $contact_info['email']; ?>">
                        <?php echo $contact_info['email']; ?>
                    </a>
                </div>
                <div class="contact-item">
                    <strong>Localização:</strong>
                    <span><?php echo $contact_info['address']; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
        <div class="footer-container">
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> Mia Couro Legítimo. Todos os direitos reservados.</p>
            </div>
            <div class="footer-credits">
                <p>Desenvolvido com ❤️ para valorizar o artesanato brasileiro.</p>
            </div>
        </div>
    </div>
</footer>