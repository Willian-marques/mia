<?php
require_once 'config.php';

$current_page = "sobre";
$site_title = "Sobre Nós - MIA Couro Legítimo";
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="product-card-universal.css">
    <link rel="stylesheet" href="responsive-global.css">
    <style>
        .about-hero {
            background: linear-gradient(135deg, #520100 0%, #8B0000 100%);
            padding: 120px 0 80px;
            color: #FCF8F1;
            text-align: center;
        }

        .about-hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-weight: 300;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .story-section {
            padding: 80px 0;
            background: #FCF8F1;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            margin-bottom: 60px;
        }

        .story-text h2 {
            font-size: 2.5rem;
            color: #520100;
            margin-bottom: 30px;
            font-weight: 300;
        }

        .story-text p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
            margin-bottom: 20px;
        }

        .story-image {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .story-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .highlight-quote {
            background: linear-gradient(135deg, #520100 0%, #8B0000 100%);
            color: #FCF8F1;
            padding: 60px 40px;
            border-radius: 20px;
            text-align: center;
            margin: 80px 0;
        }

        .highlight-quote h3 {
            font-size: 2rem;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .highlight-quote p {
            font-size: 1.3rem;
            font-style: italic;
            font-weight: 300;
        }

        @media (max-width: 1200px) {
            .story-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .about-hero {
                padding: 80px 0 60px;
            }

            .story-text h2 {
                font-size: 2rem;
            }

            .highlight-quote {
                padding: 40px 20px;
                margin: 60px 0;
            }
        }

        @media (max-width: 768px) {
            .about-hero {
                padding: 60px 0 40px;
            }

            .about-hero h1 {
                font-size: 2rem;
            }

            .about-content {
                padding: 0 20px;
            }

            .story-section {
                padding: 60px 0;
            }

            .story-text h2 {
                font-size: 1.8rem;
                margin-bottom: 20px;
            }

            .story-text p {
                font-size: 1rem;
                line-height: 1.6;
                margin-bottom: 15px;
            }

            .story-image img {
                height: 250px;
            }

            .highlight-quote {
                padding: 30px 15px;
                margin: 40px 0;
            }

            .highlight-quote h3 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }

            .highlight-quote p {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .about-hero {
                padding: 40px 0 30px;
            }

            .about-hero h1 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }

            .about-hero p {
                font-size: 0.9rem;
            }

            .about-content {
                padding: 0 15px;
            }

            .story-section {
                padding: 40px 0;
            }

            .story-grid {
                gap: 30px;
                margin-bottom: 40px;
            }

            .story-text h2 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }

            .story-text p {
                font-size: 0.9rem;
                line-height: 1.5;
                margin-bottom: 12px;
            }

            .story-image img {
                height: 200px;
            }

            .highlight-quote {
                padding: 25px 12px;
                margin: 30px 0;
            }

            .highlight-quote h3 {
                font-size: 1.3rem;
                margin-bottom: 12px;
            }

            .highlight-quote p {
                font-size: 1rem;
            }
        }

        /* Orientação landscape para mobile */
        @media (max-width: 767px) and (orientation: landscape) {
            .about-hero {
                padding: 40px 0 30px;
            }

            .story-section {
                padding: 40px 0;
            }

            .story-image img {
                height: 180px;
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
            <a href="produtos?filter=desconto" class="sale-link">Sale</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="about-content">
            <h1>Nossa História e Propósito</h1>
            <p>MIA, do italiano "minha", é mais do que um nome: é um símbolo do que é único, pessoal e exclusivo.</p>
        </div>
    </section>

    <!-- Story Section -->
    <section class="story-section">
        <div class="about-content">
            <div class="story-grid">
                <div class="story-text">
                    <h2>Nossa História e Propósito</h2>
                    <p>MIA, do italiano "minha", é mais do que um nome: é um símbolo do que é único, pessoal e
                        exclusivo. Breve e simples, mas que traduz perfeitamente nossa essência.</p>

                    <p>Em 2024, um novo olhar deu vida a uma MIA mais humana, autêntica e jovem. Em 2025, demos um passo
                        decisivo com o rebranding — um processo carregado de história, arte e propósito. O lançamento da
                        bolsa Zephira simboliza essa nova era, em que cada detalhe foi pensado para transmitir nossa
                        essência sem abrir mão da qualidade e dos diferenciais que sempre nos acompanharam.</p>

                    <p>Na MIA, acreditamos que cada pessoa é especial. O cuidado da personalização transforma cada peça
                        em um gesto de carinho, capaz de transmitir afeto e eternizar memórias. Mais do que acessórios,
                        criamos presentes que acompanham histórias e transformam momentos em lembranças duradouras.</p>

                    <p>Nosso propósito é fazer da MIA mais que uma marca: uma comunidade, um estilo de vida. Queremos
                        estar próximos de nossos clientes, compartilhando conquistas, celebrações e o dia a dia, sempre
                        ressaltando a individualidade e a autenticidade de cada um.</p>
                </div>
                <div class="story-image">
                    <img src="img/sobre/pagina_1_imagem_2.jpeg" alt="História MIA">
                </div>
            </div>

            <div class="highlight-quote">
                <h3>Na MIA, celebramos o que é único.</h3>
                <p>Única, para quem também é.</p>
            </div>

            <div class="story-grid">
                <div class="story-image">
                    <img src="img/sobre/pagina_1_imagem_3.png" alt="Matéria prima - Artesãos em Curitiba">
                </div>
                <div class="story-text">
                    <h2>Matéria Prima</h2>
                    <p>Na MIA, cada criação nasce das mãos de dois artesãos aqui em Curitiba. Todo o processo é manual e
                        acompanhado de perto, reforçando a atenção aos detalhes e a proximidade que mantemos desde a
                        produção até o cliente final.</p>

                    <p>A própria matéria-prima também reflete essa essência: grande parte é adquirida localmente,
                        fortalecendo o comércio nacional e traduzindo nossa identidade em cada peça.</p>

                    <p>Trabalhamos exclusivamente com couro legítimo, valorizando suas texturas e formatos naturais — o
                        que torna cada produto único, já que variações de cor e tamanho fazem parte da autenticidade do
                        material.</p>
                </div>
            </div>

            <div class="story-grid">
                <div class="story-text">
                    <h2>Couro e Sustentabilidade</h2>
                    <p>O couro legítimo é um subproduto da indústria alimentícia, aproveitando peles que seriam
                        descartadas e transformando-as em peças atemporais. Por isso, é um material essencial para a
                        economia circular, reduzindo o desperdício e fortalecendo um consumo consciente.</p>

                    <p>Além de durável, o couro é biodegradável: decompõe-se naturalmente em 25 a 45 anos no ambiente
                        aberto, ou em 10 a 15 anos em aterros. Composto por proteínas e moléculas orgânicas, retorna
                        nutrientes ao solo e pode até ser transformado em adubo por meio da compostagem.</p>

                    <p>Escolher couro é optar por um material que valoriza os recursos naturais, reduz o desperdício e
                        permanece no tempo, criando peças únicas que contam histórias e atravessam gerações.</p>
                </div>
                <div class="story-image">
                    <img src="img/sobre/pagina_1_imagem_4.png" alt="Couro e Sustentabilidade">
                </div>
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
                        <a href="https://www.instagram.com/mia.mianet" target="_blank" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.40s-.644-1.44-1.439-1.40z" />
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
</body>

</html>