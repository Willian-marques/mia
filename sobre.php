<?php
require_once 'config.php';

// Configurações da página
$current_page = "sobre";
$site_title = getPageTitle('sobre', 'Sobre Nós');

// Dados da equipe
$team_members = [
    [
        'name' => 'Maria Fernanda',
        'role' => 'Fundadora & Mestra Artesã',
        'description' => 'Com mais de 30 anos de experiência, Maria fundou a empresa com a visão de preservar a tradição do couro artesanal brasileiro.',
        'image' => 'img/mulher1.jpg'
    ],
    [
        'name' => 'Amanda Santos',
        'role' => 'Designer & Estilista',
        'description' => 'Responsável pela criação de novos designs e pela fusão entre tradição e modernidade em cada peça.',
        'image' => 'img/mulher1.jpg'
    ],
    [
        'name' => 'Ana Beatriz',
        'role' => 'Especialista em Qualidade',
        'description' => 'Garante que cada produto atenda aos mais altos padrões de qualidade e acabamento.',
        'image' => 'img/mulher1.jpg'
    ]
];

// Nossos valores
$values = [
    [
        'title' => 'Qualidade Premium',
        'description' => 'Utilizamos apenas couros legítimos de primeira qualidade',
        'icon' => 'quality'
    ],
    [
        'title' => 'Sustentabilidade',
        'description' => 'Práticas responsáveis desde a origem até o produto final',
        'icon' => 'sustainability'
    ],
    [
        'title' => 'Tradição',
        'description' => 'Técnicas artesanais passadas de geração em geração',
        'icon' => 'tradition'
    ],
    [
        'title' => 'Autenticidade',
        'description' => 'Cada peça é única e carrega nossa assinatura',
        'icon' => 'authenticity'
    ],
    [
        'title' => 'Artesanato',
        'description' => 'Trabalho manual cuidadoso em cada detalhe',
        'icon' => 'craftsmanship'
    ],
    [
        'title' => 'Excelência',
        'description' => 'Busca constante pela perfeição em tudo que fazemos',
        'icon' => 'excellence'
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <meta name="description" content="Conheça a história da Mia Couro Legítimo, nossa equipe e os valores que nos guiam na criação de produtos artesanais únicos.">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sobre-styles.css">
    <link rel="stylesheet" href="image-optimize.css">
    <link rel="stylesheet" href="menu-styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content animate-on-scroll">
                <h1>Nossa História</h1>
                <p>Há mais de três décadas, nossa paixão pelo couro legítimo e pela tradição artesanal nos move a criar peças únicas que contam histórias e resistem ao tempo.</p>
            </div>
        </div>
    </section>

    <!-- Nossa História Section -->
    <section class="our-story">
        <div class="container">
            <div class="story-content animate-on-scroll">
                <div class="story-image">
                    <img src="img/homen.jpg" alt="Artesão trabalhando com couro" loading="lazy">
                </div>
                <div class="story-text">
                    <h2><?php echo SITE_NAME; ?></h2>
                    <p>Nossa jornada começou em uma pequena oficina, onde cada peça era cuidadosamente trabalhada à mão. Hoje, mantemos essa mesma dedicação artesanal, utilizando apenas couros legítimos de origem sustentável.</p>
                    
                    <p>Cada produto que criamos carrega consigo a expertise de gerações de artesãos, combinando técnicas tradicionais com design contemporâneo para oferecer peças duráveis e atemporais.</p>
                    
                    <div class="certification">
                        <svg width="18" height="24" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 0L11.09 6.26L18 4.27L15.64 10.14L22 12L15.64 13.86L18 19.73L11.09 17.74L9 24L6.91 17.74L0 19.73L2.36 13.86L-4 12L2.36 10.14L0 4.27L6.91 6.26L9 0Z" fill="#520100"/>
                        </svg>
                        <span>Certificação de Couro Legítimo</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nossos Valores Section -->
    <section class="our-values">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2>Nossos Valores</h2>
                <p>Os princípios que guiam cada decisão e cada peça que criamos</p>
            </div>
            
            <div class="values-grid">
                <?php foreach ($values as $index => $value): ?>
                    <div class="value-card animate-on-scroll" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="value-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L13.09 8.26L20 6L17 12L24 14L17 16L20 22L13.09 19.74L12 26L10.91 19.74L4 22L7 16L0 14L7 12L4 6L10.91 8.26L12 2Z" fill="white"/>
                            </svg>
                        </div>
                        <h3><?php echo $value['title']; ?></h3>
                        <p><?php echo $value['description']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Nossa Equipe Section -->
    <section class="our-team">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2>Nossa Equipe</h2>
                <p>Conheça as pessoas apaixonadas que tornam cada peça especial</p>
            </div>
            
            <div class="team-grid">
                <?php foreach ($team_members as $member): ?>
                    <div class="team-member animate-on-scroll">
                        <div class="member-image">
                            <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" loading="lazy">
                        </div>
                        <h3><?php echo $member['name']; ?></h3>
                        <p class="member-role"><?php echo $member['role']; ?></p>
                        <p class="member-description"><?php echo $member['description']; ?></p>
                    </div>
                <?php endforeach; ?>
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

                        <a href="#" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
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
                            <li><a href="#">Contato</a></li>
                            <li><a href="#">Sobre Nós</a></li>
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
    <script src="app.js"></script>
    <script>
        // Adicionar animações aos elementos
        document.addEventListener('DOMContentLoaded', function() {
            // Animar estatísticas quando aparecem na tela
            const statNumbers = document.querySelectorAll('.stat-number');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const finalValue = target.textContent;
                        const numericValue = parseInt(finalValue.replace(/\D/g, ''));
                        
                        if (numericValue) {
                            animateNumber(target, 0, numericValue, finalValue);
                        }
                        
                        observer.unobserve(target);
                    }
                });
            });

            statNumbers.forEach(stat => {
                observer.observe(stat);
            });

            // Inicializar animações on-scroll
            if (window.miaApp) {
                window.miaApp.animateOnScroll();
            }
        });

        function animateNumber(element, start, end, suffix) {
            const duration = 2000;
            const increment = (end - start) / (duration / 16);
            let current = start;

            const timer = setInterval(() => {
                current += increment;
                if (current >= end) {
                    current = end;
                    clearInterval(timer);
                }
                
                const displayValue = Math.floor(current);
                element.textContent = displayValue + suffix.replace(/\d/g, '');
            }, 16);
        }
    </script>
</body>
</html>
