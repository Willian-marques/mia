<?php
require_once 'config.php';

// Verificar se há parâmetros válidos
$success = isset($_GET['success']) && $_GET['success'] == '1';
$message_id = isset($_GET['id']) ? $_GET['id'] : null;

// Se não há parâmetros válidos, redirecionar para contato
if (!$success || !$message_id) {
    header('Location: contato');
    exit();
}

// Configurações da página
$current_page = "obrigado";
$site_title = "Mensagem Enviada - Mia Couro Legítimo";
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="responsive-global.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #FCF8F1;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .success-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 30px;
            color: #16a34a;
        }

        .success-title {
            font-size: 32px;
            color: #520100;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .success-message {
            font-size: 18px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .message-id {
            background: #f0fdf4;
            color: #16a34a;
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #bbf7d0;
            font-size: 14px;
            margin-bottom: 40px;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #520100;
            color: white;
        }

        .btn-primary:hover {
            background: #741b16;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #520100;
            border: 2px solid #520100;
        }

        .btn-secondary:hover {
            background: #520100;
            color: white;
            transform: translateY(-2px);
        }

        .contact-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
            color: #666;
        }

        .contact-info h4 {
            color: #520100;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .success-card {
                padding: 40px 20px;
            }

            .success-title {
                font-size: 24px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
                justify-content: center;
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

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">✓</div>

            <h1 class="success-title">Mensagem Enviada!</h1>

            <p class="success-message">
                Obrigado por entrar em contato conosco! Recebemos sua mensagem e nossa equipe retornará o contato em
                breve através do email informado.
            </p>

            <div class="message-id">
                <strong>ID da sua mensagem:</strong> <?php echo $message_id; ?><br>
                <small>Guarde este código para referência futura</small>
            </div>

            <div class="action-buttons">
                <a href="produtos" class="btn btn-primary">
                    Ver Produtos
                </a>
                <a href="contato" class="btn btn-secondary">
                    Nova Mensagem
                </a>
            </div>

            <div class="contact-info">
                <h4>Outras formas de contato:</h4>
                <p>
                    📱 WhatsApp: (41) 9733-8289<br>
                    📧 Email: contato@mia.com.br<br>
                    🕘 Horário: Segunda a Sexta, 9h às 18h
                </p>
            </div>
        </div>
    </div>

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
                            <li><a href="https://wa.me/5541973382289" target="_blank" style="color: #9CA3AF; text-decoration: none;">+55 (41) 9733-8289</a></li>
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
        // Função para aplicar filtro Desconto quando clicarem no link
        function applySaleFilter() {
            window.location.href = 'produtos.php?filter=desconto';
            return false; // Prevenir navegação padrão
        }

        // Menu suave
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');

            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (navMenu.classList.contains('active')) {
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    } else {
                        navMenu.style.display = 'flex';
                        setTimeout(() => {
                            menuToggle.classList.add('active');
                            navMenu.classList.add('active');
                        }, 10);
                    }
                });

                window.addEventListener('resize', function () {
                    if (window.innerWidth > 768 && navMenu.classList.contains('active')) {
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    }
                });

                const navLinks = navMenu.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function () {
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    });
                });

                document.addEventListener('click', function (e) {
                    if (!menuToggle.contains(e.target) && !navMenu.contains(e.target) && navMenu.classList.contains('active')) {
                        menuToggle.classList.remove('active');
                        navMenu.classList.remove('active');
                        setTimeout(() => {
                            navMenu.style.display = 'none';
                        }, 400);
                    }
                });
            }
        });

        // Redirecionar automaticamente após 10 segundos (opcional)
        setTimeout(function () {
            // window.location.href = 'produtos.php';
        }, 10000);
    </script>
</body>

</html>